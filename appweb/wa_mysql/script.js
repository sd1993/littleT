function wa_mysql_parent_table( q )
{
	while ( q !== undefined && q !== document )
	{
		if ( q.tBodies )
		{
			break;
		};
		q = q.parentNode;
	};
	return q;
}
function wa_mysql_ajax( q )
{
	var w = { 0 : '/wa_mysql/ajax' }, e;
	w[1] = arguments[1];
	if ( arguments.length > 2 )
	{
		for ( e = 2; e < arguments.length; ++e )
		{
			w[ e + 2 ] = $.urlencode( arguments[ e ] );
		};
	};
	return wa.ajax_query( wa.query_act( w, true ), q.dataset );
}
function wa_mysql_checkbox( q, w )
{
	var e = $.query( 'input', q ), r = [];
	for ( q in e )
	{
		if ( e[ q ].type == 'checkbox' && ( w === undefined || e[ q ].checked === w ) )
		{
			r[ r.length ] = e[ q ];
		};
	};
	return r;
}
function wa_mysql_checked_invert( q )
{
	var w = wa_mysql_checkbox( q );
	for ( q in w )
	{
		w[ q ].checked = !w[ q ].checked;
	};
}
function wa_mysql_view_data( q )
{
	var w = wa_mysql_checkbox( q, true ), e = [], r;
	for ( r in w )
	{
		e[ e.length ] = $.urlencode( w[ r ].parentNode.parentNode.dataset.fieldname );
	};
	wa.query_act({ 1 : 'data_table', 6 : e.join( '/' ) });
}
function wa_mysql_data_editor( q )
{
	return wa.query_act({ 1 : 'data_editor', 4 : q.parentNode.parentNode.dataset.field, 5 : q.parentNode.dataset.value });
}
function wa_mysql_data_edits()
{
	for ( var q = $.query( 'tbody.wa_mysql_data_table>tr>td' ), w = 0; w < q.length; ++w )
	{
		if ( q[ w ].onclick === null )
		{
			q[ w ].onclick = wa_mysql_data_edit;
		};
	};
}
function wa_mysql_data_edit()
{
	var q = { prompt : wa_mysql_parent_table( this ).tHead.lastChild.cells[ this.cellIndex - 1 ].childNodes[0].nodeValue },
		w = this.parentNode.parentNode.dataset.field,
		e = this.parentNode.dataset.value,
		r;
	if ( this.className )
	{
		if ( this.className == 'long' )
		{
			if ( r = $.xmlhttp().open( 'get', wa.query_act( { 0 : '/wa_mysql/ajax', 1 : 'data_json', 4 : w, 5 : e }, true ), false ).send().result( 'json' ) )
			{
				r = r[ q.prompt ];
			};
		};
		q.value = r;
	}
	else
	{
		q.value = this.innerHTML;
	}
	wa_mysql_ajax( { dataset : q }, 'data_edit', q.prompt, w, e );
}
function wa_mysql_data_drop( q )
{
	return wa_mysql_ajax( { dataset : { confirm : 'Cannot undo' } }, 'data_drop', q.parentNode.parentNode.dataset.field, q.parentNode.dataset.value, '' );
}
function wa_mysql_sql( q )
{
	q = wa_mysql_parent_table( q ).tBodies[0].rows[0].cells[0].firstChild,
	q.disabled = true,
	$.ajax({
		type : 'post',
		url : '?/wa_mysql/ajax(1)sql',
		sent : 'value=' + $.urlencode( q.value )
	}).done(function( w )
	{
		q.disabled = false,
		wa.status_callback( w );
	});
}