var
	rx_websocket;

function rx_script_callback( q, w )
{
	$.is_function( window[ q ] ) ? window[ q ].call( this, w ) : alert( '不存在命令：' + q );
}

function rx_websocket_disconnect()
{
	alert( '与实时通讯服务器断开连接！' );
}

function rx_websocket_init( q )
{
	rx_websocket = $.websocket( 'ws://' + document.domain + ':8011' + ( q ? q : '' ) ).message(function( q )
	{
		var w = q.data.indexOf( ' ' );
		if ( w === -1 )
		{
			w = '';
			q = q.data;
		}
		else
		{
			w = q.data.substring( 0, w );
			q = q.data.substring( w.length + 1 );

		};
		rx_script_callback.call( this, w, q );
	}).error( rx_websocket_disconnect );
	return rx_websocket;
}


