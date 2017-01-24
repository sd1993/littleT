<?php
require 'main.php';
wa::locale();
wa::htt();
wa::htt_title( 'WA Admin' );
wa::get_signin_admin() || wa::end_htt_signin_admin( '?/wa_admin' );
wa::htt_nav( wa_sign_out, 'javascript:$.setcookie_remove("wa_admin"),$.go("?/wa_admin");', 1 );
wa::htt_nav( 'WA MySQL', '?/wa_mysql' );


wa::htt_nav( 'Class', '?/wa_admin(1)menu' );
wa::htt_nav( 'Files', '?/wa_admin(1)data' );
wa::htt_nav( 'Users', '?/wa_admin(1)user' );
wa::htt_nav( 'OPCache Status', '?/wa_admin(1)opcache_status' );

switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'menu':			goto menu;
	case 'data':			goto data;
	case 'opcache_status':	goto opcache_status;
	default:				goto config;
};

menu:
goto end;

data:
goto end;

opcache_status:
goto end;

config:
goto end;

end: