<?php
require 'main.php';
wa::locale();
wa::get_signin_admin() || wa::end_string( wa_unauthorized );