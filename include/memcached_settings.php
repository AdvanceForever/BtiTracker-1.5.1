<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

$CONFIG['memcache_servers'] = array();
$CONFIG['memcache_servers'][] = array(
   'ip'	=> '127.0.0.1',
   'port'	=> 11211, #The Default Port...
   'persistent'	=> true,
   'weight'	=> 1,
   'timeout'	=> 1,
   'retry_interval'	=> 1,
   'status'	=> true
);
# Set a prefix on all keys for memcached (allows for multiple sites on the same memcached server)
$CONFIG['memcache_prefix'] = 'btit::';

?>
