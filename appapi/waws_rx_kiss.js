var userid = /userid\=(\d{5})/, users = {};
require( './waws.js' ).connect_callback(function(){
	var q;
	if ( this.url === '/' )
	{
		q = userid.exec( this.req.headers.cookie );
		if ( q )
		{
			this.userid = q[1];
			users[ this.userid ] && this.connect_md5( users[ this.userid ], function( r )
			{
				this.send( 'rx_chat_out ' + this.userid );
				this.disconnect();
			});
			users[ this.userid ] = this.md5;
			this.logs( this.userid );
			return;
		};
		this.disconnect();
	};
}).command_extends({
	online_staffs : function()
	{
		var q = [];
		this.to_url( '/', function()
		{
			q[ q.length ] = this.userid;
		});
		this.send( 'rx_chat_online_staffs ' + q.join( ',' ) );
	},
	send_public : function( q )
	{
		var w = this.userid;
		this.to_url( '/', function()
		{
			this.send( 'rx_chat_from ' + w + ' ' + q );
		});
	},
	m : function( q )
	{
		var w = q.indexOf( ' ' ), e;
		if ( w === -1 )
		{
			return;
		};
		e = q.substr( 0, w );
		users[ e ] && this.connect_md5( users[ e ], function( r )
		{
			this.send( 'r ' + r.userid + ' ' + q.substr( w + 1 ) );
		});
	},
	regoto_in : function( q )
	{
		//this
		this.logs( q );
		this.send( q );
	}
})(8011);