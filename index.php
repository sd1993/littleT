<?php
require './config.php';
require './core/core.php';
require './core/webapp.php';
wa::init(function()
{
	wa::$sql = NULL;
	headers_sent() && exit;
	if ( wa::$htt instanceof webapp_htt )
	{
		array_walk( wa::$buffers->css, 'wa::htt_css' );
		array_walk( wa::$buffers->js, 'wa::htt_js' );
		isset( wa::$buffers->script ) && wa::$htt->head[0]->tag_script( wa::$buffers->script );
		isset( wa::$buffers->style ) && wa::$htt->head[0]->tag_style( wa::$buffers->style );
		isset( wa::$buffers->end ) && wa::$buffers->end->add_raw( sprintf(
			'Copyright (c) 2004-2016 Guangdong Ruixun Network co.,Ltd<br/>Web Application v%s%s, Response time: %.2f/sec',
			wa::v, wa::x, wa::debug_time( wa::$buffers->debug_time )
		) );
		wa::$htt = wa::$htt->get_dom()->ownerDocument;
		wa::$htt->formatOutput = wa::$buffers->format;
		wa::$buffers = wa::$htt->saveHTML();
		wa::$htt = NULL;
		if ( WA_GZIP_LEVEL && stripos( $_SERVER[ 'HTTP_ACCEPT_ENCODING' ], 'gzip' ) !== FALSE )
		{
			wa::$buffers = gzencode( wa::$buffers, WA_GZIP_LEVEL );
			wa::$headers['Content-Encoding'] = 'gzip';
		};
	};
	array_walk( wa::$cookies, function( $q )
	{
		call_user_func_array( 'setcookie', $q );
	});
	array_walk( wa::$headers, function( $q, $w )
	{
		header( $w . ': ' . $q );
	});
	echo wa::$buffers;
	flush();
	if(strpos( ',10454,10038,', ',' . wa::$user['username'] . ',' )){		
		if(strstr($_SERVER['QUERY_STRING'], 'rd_')){
			
			exit("");
		};
	}
	exit;
});
$_GET[0] === '' && $_GET[0] = WA_HOME_QUERY;
if ( $_GET[0][0] === '/' )
{
	if ( preg_match( '/^(\/\w{1,32}){1,4}$/', $_GET[0] ) )
	{
		define( 'WA_APPWEB_PHP', WA_APPWEB_DIR . $_GET[0] . ( strrpos( $_GET[0], '/' ) ? '.php' : '/home.php' ) );
		if ( is_file( WA_APPWEB_PHP ) )
		{
			require WA_APPWEB_PHP;
			exit;
		};
	};
	require WA_APPWEB_DIR . '/wa/home.php';
	exit;
};
WA_FILE_ACCESS && wa::sql()->q_query( 'select wa_menu.need,concat(wa_menu.root,wa_data.only) as link,wa_data.only,wa_data.type,wa_data.name from wa_menu,wa_data where wa_menu.only=wa_data.menu and wa_data.only=?s limit 1', $_GET[0] )->fetch_callback(function( $q )
{
	if ( $q['need'] === NULL ? FALSE : wa::get_signin_user( isset( $_GET[1] ) ? $_GET[1] : 'wa_user' ) === FALSE || strpos( wa::$user['have'], $q['need'] ) === FALSE ) return;
	isset( $_SERVER['HTTP_RANGE'] )
		|| isset( $_SERVER['HTTP_IF_NONE_MATCH'] )
		|| isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )
		|| wa::$sql->q_sent( 'update wa_data set hits=hits+1 where only=?s limit 1', $q['only'] );
	wa::set_sendfile( $q['link'] );
	if ( isset( $_GET[9] ) && $_GET[9] === 'download' )
	{
		wa::set_header_download( $q['name'] . '.' . $q['type'] );
		exit;
	};
	foreach ( [
		'image/%s'		=> ' bmp cgm gif jpeg ief ktx png sgi tiff ',
		'image/jpeg'	=> ' jpg jpe ',
		'text/plain'	=> ' html htm txt text conf def list log in ',
		'audio/mpeg'	=> ' mpga mp2 mp2a mp3 m2a m3a ',
		'audio/x-%s'	=> ' aac aiff caf flac wav ',
		'audio/x-ms-%s'	=> ' wax wma ',
		'video/mpeg'	=> ' mpeg mpg mpe m1v m2v ',
		'video/%s'		=> ' 3gp h261 h263 h264 jpm mj2 mp4 ogv ',
		'video/x-%s'	=> ' f4v fli flv m4v mng smv ',
		'video/x-ms-%s'	=> ' asf vob wm wmv wmx wvx ',
		'video/quicktime'=>' qt mov ',
		'application/x-shockwave-flash'=> ' swf '
	] as $w => $e )
	{
		if ( strpos( $e, ' ' . $q['type'] . ' ' ) !== FALSE )
		{
			wa::$headers['Content-Type'] = sprintf( $w, $q['type'] );
			exit;
		};
	};
	wa::$headers['Content-Type'] = 'application/octet-stream';
	exit;
});
http_response_code(404);
wa::$headers['Connection'] = 'close';