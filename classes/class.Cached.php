<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(CLASS_PATH . 'class.Memcached.php');

class Cached {
	const SIX_HOURS = 21600;
        const ONE_DAY = 86400;

        public static function bans($ip, &$reason = '') {
           global $db;
           
           MCached::connect();
           $ip  = vars::$ip;
           $nip = ip2long($ip);
	   
	   $key = 'banned::' . $ip;
	   $banned = MCached::get($key);
           if ($banned === MCached::NO_RESULT) {
                $res = $db->query("SELECT comment FROM bannedip WHERE '" . $nip . "' >= first AND '" . $nip . "' <= last");
                
                if ($res->num_rows) {
                     $comment = $res->fetch_row();
                     $ban_reason = $comment[0];
                     
                     MCached::add($key, $ban_reason, self::ONE_DAY);
                     return true;
                }
                
                $res->free();
                MCached::add($key, 0, self::ONE_DAY);
                return false;
           }
           elseif (!$banned)
                   return false;
           else {
               $reason = $banned;
               return true;
           }
        }

        public static function genrelist() {
           global $db;

           MCached::connect();
	   $key = 'genre::list';
	   $ret = MCached::get($key);
           if ($ret === MCached::NO_RESULT) {
               $ret = array();
               $res = $db->query("SELECT * FROM categories ORDER BY sort_index, id");

               while ($row = $res->fetch_array(MYSQLI_BOTH))
                   $ret[] = $row;
               MCached::add($key, $ret, self::ONE_DAY);
           }
           return $ret;
        }

        public static function sub_cat($sub) {
           global $db;
           
           MCached::connect();
	   $key = 'sub::categories::' . $sub;
	   $name = MCached::get($key);
           if ($name === MCached::NO_RESULT) {
                $c_q = @$db->query("SELECT name FROM categories WHERE id = '" . $sub . "'");
	        $c_q = @$c_q->fetch_array(MYSQLI_BOTH);
                $name = security::html_safe(unesc($c_q["name"]));
                MCached::add($key, $name, self::ONE_DAY);
           }
           return $name;
        }

        public static function style_list() {
           global $db;
           
           MCached::connect();
	   $key = 'style::list';
	   $ret = MCached::get($key);
           if ($ret === MCached::NO_RESULT) {
                $ret = array();
                $res = $db->query("SELECT * FROM style ORDER BY id");
    
                while ($row = $res->fetch_array(MYSQLI_BOTH))
                        $ret[] = $row;
                MCached::add($key, $ret, self::SIX_HOURS);
           }
           return $ret;
        }

        public static function language_list() {
           global $db;
           
           MCached::connect();
	   $key = 'language::list';
	   $ret = MCached::get($key);
           if ($ret === MCached::NO_RESULT) {
                $ret = array();
                $res = $db->query("SELECT * FROM language ORDER BY language");
    
                while ($row = $res->fetch_array(MYSQLI_BOTH))
                        $ret[] = $row;
                MCached::add($key, $ret, self::SIX_HOURS);
           }
           return $ret;
        }

        public static function flag_list($with_unknown = false) {
           global $db;
           
           MCached::connect();
	   $key = 'flag::list';
	   $ret = MCached::get($key);
           if ($ret === MCached::NO_RESULT) {
                $ret = array();
                $res = $db->query("SELECT * FROM countries " . (!$with_unknown ? "WHERE id <> 100" : "") . " ORDER BY name");
    
                while ($row = $res->fetch_array(MYSQLI_BOTH))
                        $ret[] = $row;
                MCached::add($key, $ret, self::ONE_DAY);
           }
           return $ret;
        }

        public static function timezone_list() {
           global $db;
           
           MCached::connect();
	   $key = 'timezone::list';
	   $ret = MCached::get($key);
           if ($ret === MCached::NO_RESULT) {
                $ret = array();
                $res = $db->query("SELECT * FROM timezone");
    
                while ($row = $res->fetch_array(MYSQLI_BOTH))
                        $ret[] = $row;
                MCached::add($key, $ret, self::ONE_DAY);
           }
           return $ret;
        }

}#End Class...
?>
