<?php
isset( $_GET[1] )
	? wa::call( 'captcha' )->image_output( wa::get_captcha_key( $_GET[1] ) )
	: wa::$buffers = wa::get_captcha_new();