var
	rx_ws,
	rx_ws_defer = [],
	rx_ws_accept = {
		recode : function()
		{
			notify_pack_ok(function()
			{
				location.reload();
			});
		},
		reload : function()
		{
			notify_pack_ok(function()
			{
				$.go( location.search );
			});
		}
	}, rx_ws_active = true,
	rx_tr_node;

function notify_pack_ok( q )
{
	rx_ws_accept.notify_pack && window.notify_pack.paused === false
		? window.notify_pack.addEventListener( 'ended', q, false ) : q();
}

function rx_tr_resh_bgcolor()
{
	this[1].style.background = this[0].style.background = '#e0e0ff';
}

function rx_tr_resh( q, w )
{
	var e = q.nextSibling;
	if ( e.style.display == 'none' )
	{
		if ( rx_tr_node )
		{
			rx_tr_node[0].style.background = 'white';
			rx_tr_node[1].style.background = null;
			rx_tr_node[0].style.display = 'none';
		};
		( w || rx_tr_resh_bgcolor ).call( rx_tr_node = [ e, q ] );
		e.style.display = null;
	}
	else
	{
		e.style.background = q.style.background = null;
		e.style.display = 'none';
	};
}

function rx_ajax_notice()
{
	var q = this[1], w = $.get( 'td', this[0] );
	if ( w.dataset.open )
	{
		return;
	};
	q = $.query( 'td', q );
	w.innerHTML = '正在打开...',
	wa.ajax( 'get', '?/rx_erp/ajax(1)rx_notice(2)' + w.dataset.only, function( e )
	{
		var r, t, y;
		e = e.split( /[\r\n]+/g );
		if ( e.length == 0 )
		{
			e.innerHTML = '数据读取错误';
			return;
		};
		w.innerHTML = '',
		w.dataset.open = 1,
		r = {
			title : $.dom_elem( 'div' ),
			content : $.dom_elem( 'pre' ),
			from : $.dom_elem( 'div' )
		};
		for ( t = 0; t < e.length; t++ )
		{
			y = r.content.appendChild( $.dom_elem( 'p' ) ),
			y.style.cssText = 'padding:16px 0;text-indent:42px',
			y.innerHTML = '<font style="padding:6px 0;line-height:32px;border-bottom:1px dashed silver">' + e[ t ] + '</font>';
		};
		r.title.innerHTML = q[2].innerHTML,
		w.appendChild( r.title ).style.cssText = 'padding:16px 0;color:maroon;font-size:32px;text-align:center;border-bottom:2px solid red',
		w.appendChild( r.content ).style.cssText = 'width:680px;margin:16px auto;font-size:16px',
		r.from.innerHTML = '<p>' + q[4].innerHTML + ': ' + q[1].innerHTML + '</p><p style="padding:8px">' + q[5].innerHTML + '<p>',
		w.appendChild( r.from ).style.cssText  = 'padding:16px 21px;text-align:right;font-size:16px';
	}).send();
}

function rx_ws_init( q )
{
	window.notify_pack = $.dom_elem( 'audio' );
	window.notify_pack.src = './data/sound/girl_new_msg.mp3';
	$(function()
	{
		$.set_append( window.notify_pack );
		rx_ws_accept.notify_pack = function()
		{
			window.notify_pack.play();
		};
	});
	
	
	return rx_ws = $.websocket( 'wss://' + document.domain + ':8014' + q ).open(function()
	{
		var q = this;
		setInterval( function()
		{
			q.send( 'hey!' );
		}, 60000 * 2 );
	}).message(function( q )
	{
		var w = q.data.substr( 0, q.data.indexOf( ' ' ) );
		rx_ws_active && $.is_function( rx_ws_accept[ w ] ) && rx_ws_accept[ w ]( q.data.substr( w.length + 1 ) );
	}).close(function()
	{
		//alert( '与 Web Socket 服务器失去连接!' );
	});
};

function rx_ws_callback()
{

	var q = this, w = this[0].split( ' ' ), e = 0;
	rx_ws && ( rx_ws_active = false );
	$.websocket( 'wss://' + document.domain + ':8014' + w[0] ).open(function()
	{
		if ( q.send === 'notify_pack' )
		{
			this.send( q.send = 'reload ' );
			this.send( 'notify_pack ' );
		}
		else
		{
			this.send( q.send );
		};
		
		while ( ++e < w.length )
		{
			this.send( 'join ' + w[ e ] );
			this.send( q.send );
		};
		if ( q.come === 'close' )
		{
			window.opener && window.opener.location.reload();
			window.close();
		}
		else
		{
			q.come && $.go( q.come );
		};
	}).error(function(){
		if ( q.come === 'close' )
		{
			window.opener && window.opener.location.reload();
			window.close();
		}
		else
		{
			q.come && $.go( q.come );
		};
	});
}