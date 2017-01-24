var
	rx_staff_lists = {};

function rx_chat_init_staffs()
{
	var q = $.query( 'td.rx_chat_staff_lists' )[0];
	$.ajax( '?/rx_kiss/chat(1)staff_lists' ).done(function( w )
	{
		var e, r, t, y, u, i;
		q.innerHTML = '';
		w = $.json_decode( w );
		for ( e in w )
		{
			r = $.dom_elem( 'div' );
			r.onclick = function()
			{
				this.nextSibling.style.display = this.nextSibling.style.display === 'none' ? null : 'none';
			}
			r.innerHTML = e;
			t = $.dom_elem( 'span' );
			t.dataset.staff_count = 0;
			t.dataset.online_count = 0;
			r.appendChild( t );
			q.appendChild( r );
			y = $.dom_elem( 'ul' );
			y.style.display = 'none';
			for ( u in w[ e ] )
			{
				++t.dataset.staff_count;
				rx_staff_lists[ u ] = {
					staff_dom : $.dom_elem( 'li' ),
					count_dom : t,
					group : e,
					name : w[ e ][ u ][0],
					team : w[ e ][ u ][1]
				};

				rx_staff_lists[ u ].staff_dom.innerHTML = u + ' - ' + rx_staff_lists[ u ].name + (
					rx_staff_lists[ u ].team ? ' (' + rx_staff_lists[ u ].team + ')' : '' );
				rx_staff_lists[ u ].staff_dom.onclick = rx_chat_open_widnow;
				
				
				//rx_staff_lists[ u ].staff_dom.className = 'online';




				y.appendChild( rx_staff_lists[ u ].staff_dom );
			};
			t.innerHTML = t.dataset.online_count + ' / ' + t.dataset.staff_count;
			q.appendChild( y );
		};
		rx_websocket_init().open( rx_chat_send_online_staffs );
		rx_chat_init_logs();
		setInterval( rx_chat_send_online_staffs, 5000 );
		e = $.query( 'div#wa_htt_id0>img' );
		for ( e = $.query( 'div#wa_htt_id0>img' ), r = e.length, t = 0; t < r; ++t )
		{
			e[ t ].onclick = function()
			{
				rx_chat_editor_insert_caret( $.get( 'table.rx_chat_editor>tfoot>tr>td>textarea' ), '[feel=' + this.src.split( '/' ).pop() + ']' );
			};
		}
	});
}

function rx_chat_send_online_staffs()
{
	rx_websocket && rx_websocket.send( 'online_staffs' );
}

function rx_chat_init_logs( q )
{
	var w = $.query( 'td.rx_chat_public>div' )[0];
	$.ajax( '?/rx_kiss/chat(1)chat_logs' + ( q ? '(2)' + q : '' ) ).done(function( e )
	{
		var r;
		w.innerHTML = '';
		e = $.json_decode( e );
		for ( r = e.length; e[ --r ]; )
		{
			rx_chat_add_m( w, e[ r ] );
		};
	});
}

function rx_chat_open_widnow()
{
	alert( '私聊还在开发。。。' )

}

function rx_chat_add_m( q, w )
{
	var e = $.dom_elem( 'pre' ), r = w.from;
	if ( rx_staff_lists[ r ] )
	{
		r = '<a href="javascript:rx_chat_open_widnow();">' + r + ' &gt; ' + rx_staff_lists[ r ].name + '（' + rx_staff_lists[ r ].group + (
			rx_staff_lists[ r ].team ? '）【' + rx_staff_lists[ r ].team + '】' : '）' ) + '</a>';
	};
	r = $.date( 'Y-m-d H:i:s', w.time ) + ' - ' + r;
	e.innerHTML = '<span>' + r + '</span>\n<span>' + rx_chat_content_filter( $.urldecode( w.content ) ) + '</span>';
	q.appendChild( e );
	r = $.query( 'pre', q );
	r.length > 40 && r[0].remove();
	q.scrollTop = q.scrollHeight;
}

function rx_chat_online_staffs( q )
{
	var w, e;
	for ( w in rx_staff_lists )
	{
		rx_staff_lists[ w ].staff_dom.className = null;
		e = rx_staff_lists[ w ].count_dom;
		e.dataset.online_count = 0;
	};
	q = q.split( ',' );
	for ( w in q )
	{
		w = q[ w ];
		if ( rx_staff_lists[ w ] )
		{
			rx_staff_lists[ w ].staff_dom.className = 'online';
			e = rx_staff_lists[ w ].count_dom;
			++e.dataset.online_count;
			e.innerHTML = e.dataset.online_count + ' / ' + e.dataset.staff_count;
		};
	};
}

function rx_chat_send_to( q, w )
{
	if ( $.trim( q.value ) === '' )
	{
		return;
	};
	q.disabled = true;
	$.ajax({
		type : 'post',
		url : '?/rx_kiss/chat(1)send_to' + ( w ? '(2)' + w : '' ),
		sent : { value : q.value }
	}).done(function( w )
	{
		 wa.status_callback( w );
		 q.value = '';
		 q.disabled = false;
		 q.focus();
	});
}

function rx_chat_send_callback()
{
	//rx_chat_add_m( $.query( 'td.rx_chat_public>div' )[0], this );
	rx_websocket && rx_websocket.send( this.cbws + ' ' + $.urlencode( this.content ) );
	//alert( this.content )
}

function rx_chat_from( q )
{
	var w = q.indexOf( ' ' );
	rx_chat_add_m( $.query( 'td.rx_chat_public>div' )[0], {
		from : q.substring( 0, w ),
		content : q.substring( w + 1 )
	});
}

function rx_chat_out( q )
{
	$.setcookie( 'rx_signin', 0, 1 );
	alert( q + ' 账号在别的地方登录，断开连接！' );
	$.go();
}

function rx_chat_content_filter( q )
{
	return q.replace( /\</g, '&lt;' ).replace( /\>/g, '&gt;' ).replace( /\[([a-z]+)\=([^\]]+)\]/g, function( q, w, e )
	{
		switch ( w )
		{
			case 'feel'	: return '<img src="appweb/rx_kiss/pack_feel/' + e +'"/>';
			default		: return q;
		};
	});
}

/*
function rx_chat_editor_insert_caret( html )
{
	var sel, range;
	if ( window.getSelection )
	{
		// IE9 and non-IE
		sel = window.getSelection();
		if ( sel.getRangeAt && sel.rangeCount )
		{
			range = sel.getRangeAt(0);
			range.deleteContents();
			// Range.createContextualFragment() would be useful here but is
			// non-standard and not supported in all browsers (IE9, for one)
			var el = document.createElement( 'div' );
			el.innerHTML = html;
			var frag = document.createDocumentFragment(), node, lastNode;
			while ( ( node = el.firstChild ) )
			{
				lastNode = frag.appendChild( node );
			};
			range.insertNode(frag);
			// Preserve the selection
			if ( lastNode )
			{
				range = range.cloneRange();
				range.setStartAfter( lastNode );
				range.collapse( true );
				sel.removeAllRanges();
				sel.addRange( range );
			};
		}
	}
	else if ( document.selection && document.selection.type != 'Control' )
	{
		// IE < 9
		document.selection.createRange().pasteHTML( html );
	};
}
*/

function rx_chat_editor_insert_caret( obj, str )
{
	if ( document.selection )
	{
		var sel = document.selection.createRange();
		sel.text = str;
	}
	else if ( typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number' )
	{
		var
			startPos = obj.selectionStart,
			endPos = obj.selectionEnd,
			cursorPos = startPos,
			tmpStr = obj.value;
		obj.value = tmpStr.substring( 0, startPos ) + str + tmpStr.substring( endPos, tmpStr.length );
		cursorPos += str.length;
		obj.selectionStart = obj.selectionEnd = cursorPos;
	}
	else
	{
		obj.value += str;
	};
}

function rx_chat_editor_move_end( obj )
{
	obj.focus();
	var len = obj.value.length;
	if ( document.selection )
	{
		var sel = obj.createTextRange();
		sel.moveStart( 'character', len );
		sel.collapse();
		sel.select();
	}
	else if ( typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number' )
	{
		obj.selectionStart = obj.selectionEnd = len;
	};
}
