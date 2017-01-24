function sales_device_filter( q )
{
	var w = $.trim( q[0].value ), e = [ $.trim( q[1].value ), $.trim( q[2].value ), $.trim( q[3].value ) ];
	if ( e[0] || e[1] || e[2] )
	{
		e = 'sb_info.like.' + $.urlencode( '%CPU' + ( e[0] ? '%' + e[0] : '' )
			+ '%内存' + ( e[1] ? '%' + e[1] : '' )
			+ '%硬盘' + ( e[2] ? '%' + e[2] : '' )
			+ '%电源%' );
	}
	else
	{
		e = null;
	};
	return wa.query_act( { 6 : w ? $.urlencode( w ) : null, 7 : e } );
}