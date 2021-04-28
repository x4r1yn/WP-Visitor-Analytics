<?php
//GET INCLUDES
$a = explode('/',$_SERVER['SCRIPT_FILENAME']);
$b = array_slice($a,0,array_search('wp-content',$a));
$url = implode('/', $b);
include_once $url.'/wp-config.php';
include_once $url.'/wp-load.php';
include_once $url.'/wp-includes/wp-db.php';
