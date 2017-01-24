<?php
require 'main.php';
rx_kiss_signin_home();


$rx_kiss_mode = 'jf';
if ( FALSE )
{


}
else
{

	include $rx_kiss_mode . '/home.php';

	wa::end_action_class( 'rx_' . $rx_kiss_mode );
};