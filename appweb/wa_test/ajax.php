<?php
 if (!isset($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != 'asd' || $_SERVER['PHP_AUTH_PW'] != 123 ) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
  };


wa::$buffers = json_encode( $_SERVER );