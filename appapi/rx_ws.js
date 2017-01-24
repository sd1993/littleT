var
	wsguid = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
	decodeFrame = require( './decode.js' ),
	encodeFrame = require( './encode.js' ),
	crypto = require( 'crypto' ),
	fs = require( 'fs' ),
	connects = {};

function say( word, addr )
{
	var
		code = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwx',
		time = new Date;
	console.log( code[ time.getMonth() ]
		+ code[ time.getDate() ]
		+ code[ time.getHours() ]
		+ code[ time.getMinutes() ]
		+ code[ time.getSeconds() ] + ( addr ? '[' + addr + ']' : ' ' ) + word );
};

function stren( q )
{
	return encodeFrame( { FIN : 1, Opcode : 1, MASK : 0, Payload_data : '' + q } );
}

function channel_send( q, w )
{
	var e;
	w = stren( w );
	for ( e in connects )
	{
		connects[ e ].channel == q && connects[ e ].socket.write( w );
	};
}

function command( res, message )
{
	switch ( message.substr( 0, message.indexOf( ' ' ) ) )
	{
		case 'join':
			this.channel = message.substr(5);
			say( '改变频道: ' + this.channel, res.remoteAddress );
			break;
		case 'recode':
			say( '接收消息: ' + message, res.remoteAddress );
			for ( message in connects )
			{
				connects[ message ].socket.write( stren( 'recode ' ) );
			};
			break;
		case 'reload':
			say( '接收消息: ' + message, res.remoteAddress );
			channel_send( this.channel, message );
			break;
		case 'notify_pack':
			say( '接收消息: ' + message, res.remoteAddress );
			for ( message in connects )
			{
				connects[ message ].socket.write( stren( 'notify_pack ' ) );
			};
			break;
		default:
			res.write( stren( +new Date ) );
	};
}

function receive( res, frame, chunk )
{
	var data;
	frame.buffer = Buffer.concat( [ frame.buffer, chunk ] );
	while ( data = decodeFrame( frame.buffer ) )
	{
		frame.buffer = data.frame;
		if ( data.Opcode == 8 )
		{
			res.end();
			delete connects[ this ];
			say( '断开连接!', res.remoteAddress );
		}
		else
		{
			command.call( connects[ this ], res, data.Payload_data.toString() );
		};
	};
}


require( 'https' ).createServer({
	cert : fs.readFileSync( '../../../ca/server.cer' ),
	key : fs.readFileSync( '../../../ca/server.key' )
}).on( 'upgrade', function( req, res )
{
	var ws_md5 = crypto.createHash( 'md5' ).update( res.remoteAddress + req.headers['sec-websocket-key'] ).digest( 'hex' );
	if ( connects[ ws_md5 ] === undefined )
	{
		connects[ ws_md5 ] = {
			channel : req.url,
			socket : res
		},
		res.setTimeout( 60000 * 6, function()
		{
			delete connects[ ws_md5 ];
			say( '超时退出!' );
		});
		res.on( 'data', receive.bind( ws_md5, res, { buffer : new Buffer(0) } ) ),
		res.write( [
			'HTTP/1.1 101 Switching Protocols',
			'Upgrade: websocket',
			'Connection: Upgrade',
			'Sec-WebSocket-Accept: ' + crypto.createHash( 'sha1' ).update( req.headers['sec-websocket-key'] + wsguid ).digest( 'base64' ),
			'\r\n' ].join( '\r\n' ) );
		say( '加入频道: ' + req.url, res.remoteAddress );
	}
	else
	{
		res.end();
		say( '连接哈希表存在: ' + ws_md5, res.remoteAddress );
	};
} ).listen( 8014, '0.0.0.0' );