<?php
wa::locale();
wa::$buffers = wa_unauthorized;
if ( isset( $_GET[1], $_POST['password_old'], $_POST['password_new'] )
	&& preg_match( WA_USER_MATCH, wa::$user = urldecode( $_GET[1] ) )
	&& wa::set_signin_user( wa::$user, $_POST['password_old'] ) ) {
	wa::$sql->q_sent( 'update wa_user set password=?s where username=?s limit 1', md5( $_POST['password_new'] ), wa::$user['username'] );
	wa::$buffers = wa::entime();
	wa::$errors && wa::$buffers = join( "\n", wa::$errors );
};