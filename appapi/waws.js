var
	fs = require( 'fs' ),
	crypto = require( 'crypto' ),
	connects = {},
	max_connect = 600,
	connect_callback = Boolean,
	command_extends = {};

function logs( content )
{
	var e = new Date;
	console.log( '[%s-%s-%s %s:%s:%s] %s',
		e.getFullYear(),
		( '0' + ( e.getMonth() + 1 ) ).slice(-2),
		( '0' + e.getDate() ).slice(-2),
		( '0' + e.getHours() ).slice(-2),
		( '0' + e.getMinutes() ).slice(-2),
		( '0' + e.getSeconds() ).slice(-2),
		content );
};

function disconnect( md5 )
{
	if ( connects.hasOwnProperty( md5 ) )
	{
		connects[ md5 ].res.end(),
		logs( md5 + '@' + connects[ md5 ].ip + ' Disconnect' );
		delete connects[ md5 ];
	};			
};

function connect( md5, req, res )
{
	connects[ md5 ] = this,
	this.md5 = md5,
	this.req = req,
	this.url = req.url,
	this.res = res,
	this.ip = res.remoteAddress,
	this.buffer = new Buffer(0),
	this.logs( req.url ),
	connect_callback.call( this );
};
connect.prototype.logs = function( content )
{
	logs( this.md5 + '@' + this.ip + ' ' + content );
},
connect.prototype.disconnect = function()
{
	disconnect( this.md5 );
},
connect.prototype.read_buffer = function()
{
	var buffer = this.buffer, counter, FIN, Opcode, MASK, frame, Payload_len, Payload_data, i, Masking_key;

	if ( buffer.length < 2 )
	{
		return null;
	};

	counter = 0,
	FIN = buffer[ counter ] >> 7,
	Opcode = buffer[ counter++ ] & 15,
	MASK = buffer[ counter ] >> 7,
	Payload_len = buffer[ counter++ ] & 127;

	if ( Payload_len === 126 )
	{
		Payload_len = buffer.readUInt16BE( counter ),
		counter += 2;
	};

	if ( Payload_len === 127 )
	{
		Payload_len = buffer.readUInt32BE( counter + 4 ),
		counter += 8;
	};

	Payload_data = new Buffer( Payload_len );

	if ( MASK )
	{
		for ( i = 0, Masking_key = buffer.slice( counter, counter += 4 ); i < Payload_len; i++ )
		{
			Payload_data[ i ] = buffer[ counter + i ] ^ Masking_key[ i % 4 ];
		};
	};

	if ( buffer.length < counter + Payload_len )
	{
		return undefined;
	};

	this.buffer = buffer.slice( counter + Payload_len );

	return {
		FIN : FIN,
		Opcode : Opcode,
		MASK : MASK,
		Payload_len : Payload_len,
		Payload_data : Payload_data
	};
},
connect.prototype.send = function( data )
{
	var
	frame = { FIN : 1, Opcode : 1, MASK : 0, Payload_data : '' + data },
	preBytes = [ ( frame.FIN << 7 ) + frame.Opcode ],
	payBytes = new Buffer( frame.Payload_data ),
	dataLength = payBytes.length;

	if ( dataLength < 126 )
	{
		preBytes[1] = ( frame.MASK << 7 ) + dataLength;
	}
	else
	{
		dataLength < Math.pow( 2, 16 )
			? preBytes.push( ( frame.MASK << 7 ) + 126, ( dataLength & 0xFF00 ) >> 8, dataLength & 0xFF )
			: preBytes.push( ( frame.MASK << 7 ) + 127, 0, 0, 0, 0,
				( dataLength & 0xFF000000 ) >> 24,
				( dataLength & 0xFF0000 ) >> 16,
				( dataLength & 0xFF00 ) >> 8,
				dataLength & 0xFF );
	};
	this.res.write( Buffer.concat( [ new Buffer( preBytes ), payBytes ] ) );
},
connect.prototype.connect_md5 = function( q, w )
{
	connects.hasOwnProperty( q ) && w.call( connects[ q ], this );
},
connect.prototype.all_url = function( q )
{
	for ( var w in connects )
	{
		q.call( connects[ w ], this );
	};
},
connect.prototype.in_url = function( q, w )
{
	for ( var e in connects )
	{
		if ( q.indexOf( connects[ e ].url ) !== -1 )
		{
			w.call( connects[ e ], this );
		};
	};
},
connect.prototype.to_url = function( q, w )
{
	for ( var e in connects )
	{
		if ( connects[ e ].url === q )
		{
			w.call( connects[ e ] );
		};
	};
};

function waws( port, timeout )
{
	require( 'http' ).createServer().on( 'upgrade', function( req, res )
	{
		var md5 = crypto.createHash( 'md5' ).update( res.remoteAddress + req.headers['sec-websocket-key'] ).digest( 'hex' ), client;
		if ( connects[ md5 ] === undefined )
		{
			client = new connect( md5, req, res ),
			res.on( 'data', function( chunk, data )
			{
				client.buffer = Buffer.concat( [ client.buffer, chunk ] );
				while ( data = client.read_buffer() )
				{
					if ( data.Opcode === 8 )
					{
						client.disconnect();
					}
					else
					{
						data = data.Payload_data.toString(),
						chunk = data.indexOf( ' ' ),
						chunk = data.substr( 0, chunk === -1 ? data.length : chunk ),
						data = data.substr( chunk.length + 1 );

						if ( command_extends.hasOwnProperty( chunk ) )
						{
							client[ chunk ]( data );
						}
						else
						{
							client.logs( 'Unknown command' );
						};
					};
				};
			}),
			res.on( 'close', function()
			{
				client.disconnect();
			}),
			res.write( [
				'HTTP/1.1 101 Switching Protocols',
				'Upgrade: websocket',
				'Connection: Upgrade',
				'Sec-WebSocket-Accept: ' + crypto.createHash( 'sha1' ).update( req.headers['sec-websocket-key'] + '258EAFA5-E914-47DA-95CA-C5AB0DC85B11' ).digest( 'base64' ),
				'\r\n' ].join( '\r\n' ) );
		}
		else
		{
			res.end();
			logs( md5 + '@' + res.remoteAddress + ' Connect hash table exists!' );
		};
	} ).listen( port, '0.0.0.0' ).timeout = timeout | 0;
	process.title = 'WA WebSocket v1.0',
	logs( 'WA WebSocket Server Startup' ),
	setInterval(function()
	{
		for ( var q in connects )
		{
			connects[ q ].res.readable || connects[ q ].disconnect();
		};
	},10000);
};
waws.connect_callback = function( q )
{
	if ( typeof q === 'function' )
	{
		connect_callback = q;
	};
	return waws;
},
waws.command_extends = function( q )
{
	for ( var w in q )
	{
		if ( typeof q[ w ] === 'function' )
		{
			connect.prototype[ w ] = command_extends[ w ] = q[ w ];
		};
	};
	return waws;
},
module.exports = waws;