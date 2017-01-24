<?php
wa::$headers['Content-Type'] = 'image/jpeg';
isset( $_GET[1] )
	&& preg_match( WA_ROOT_MATCH, $q = wa::decrypt( $_GET[1] ) )
	&& is_file( $q . WA_FILE_CACHE )
	&& wa::set_sendfile( $q . WA_FILE_CACHE );