function td_device_filter( q )
{
	var w = $.trim( q[0].value );
	if ( /^\d{1,3}(\.\d{1,3}){0,3}$/.test( w ) )
	{
		w = { 6 : w, 7 : null };
	}
	else
	{
		w = { 6 : null, 7 : w ? 'only.like.' + $.urlencode( '%' + w + '%' ) : null };
	};
	return wa.query_act( w );
}