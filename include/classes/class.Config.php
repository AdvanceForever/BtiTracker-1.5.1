<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

class config {
	public static $conf = array();
}

$CONFIG =& config::$conf;

require_once(INCL_PATH . 'memcached_settings.php');

?>
