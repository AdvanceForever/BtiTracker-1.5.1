<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/
require_once(CLASS_PATH . 'class.Security.php');

class user {
        const EXPIRE_CURUSER = 300;
        const USER_FIELDS1 = 'users.pid, users.topicsperpage, users.postsperpage, users.torrentsperpage, CAST(users.flag AS SIGNED) AS flag, users.avatar, UNIX_TIMESTAMP(users.lastconnect) AS lastconnect, UNIX_TIMESTAMP(users.joined) AS joined, users.id AS uid, users.username, users.password, users.loginhash, users.random, users.email, users.language, users.style, users.time_offset, users_level.*';
        const USER_FIELDS2 = 'users.topicsperpage, users.postsperpage, users.torrentsperpage, CAST(users.flag AS SIGNED) AS flag, users.avatar, UNIX_TIMESTAMP(users.lastconnect) AS lastconnect, UNIX_TIMESTAMP(users.joined) AS joined, users.id AS uid, users.username, users.password, users.loginhash, users.random, users.email, users.language, users.style, users.time_offset, users_level.*';
        const USER_FIELDS3 = 'users.topicsperpage, users.postsperpage, users.torrentsperpage, CAST(users.flag AS SIGNED) AS flag, users.avatar, UNIX_TIMESTAMP(users.lastconnect) AS lastconnect, UNIX_TIMESTAMP(users.joined) AS joined, users.id AS uid, users.username, users.password, users.loginhash, users.random, users.email, users.language, users.style, users.time_offset, users_level.*';

        public static $current = NULL;

	public static function prepare_user(&$user, $curuser = false) {
		if ($curuser && empty($user))
			die;

		if (isset($user['torrentsperpage']))
		    $user['torrentsperpage'] = (int)$user['torrentsperpage'];
		if (isset($user['uid']))
		    $user['uid'] = (int)$user['uid'];
		if (isset($user['username']))
		    $user['username'] = security::html_safe($user['username']);
		if (isset($user['language']))
		    $user['language'] = (int)$user['language'];
		if (isset($user['style']))
		    $user['style'] = (int)$user['style'];
		if (isset($user['flag']))
		    $user['flag'] = (int)$user['flag'];
		if (isset($user['topicsperpage']))
		    $user['topicsperpage'] = (int)$user['topicsperpage'];
		if (isset($user['postsperpage']))
		    $user['postsperpage'] = (int)$user['postsperpage'];
		if (isset($user['id_level']))
		    $user['id_level'] = (int)$user['id_level'];
		if (isset($user['WT']))
		    $user['WT'] = (int)$user['WT'];
		if (isset($user['random']))
		    $user['random'] = (int)$user['random'];
		if (isset($user['flags']))
		    $user['flags'] = (int)$user['flags'];
	}

        public static function login() {
		global $db, $tpl;
                unset($GLOBALS['CURUSER']);
                require_once(CLASS_PATH . 'class.Cached.php');
    
                $ip  = vars::$ip;
                $nip = ip2long($ip);
                $ipf = vars::$realip;
    
                #Check if User is Banned...
                #if (!($row['flags'] & BIT_26)) -- TO-DO
	        #$banned = false;

                if (Cached::bans($ip, $reason))
                    $banned = true;
                else {
                    if ($ip != $ipf) {
                        if (Cached::bans($ipf, $reason))
                            $banned = true;
                    }
                }

                if ($banned) {
                    header('Content-Type: text/html; charset=utf-8');

                    $banned_message = security::html_safe($reason);
	            $tpl->assign('banned_message', $banned_message);

	            $banned_msg = $tpl->draw('style/base/tpl/banned_message', $return_string = true);
                    echo $banned_msg;

                    die;
                } #End Banned User...
    
                // guest
                if (empty($_COOKIE['uid']) || empty($_COOKIE['pass']))
                    $id = 1;
    
                if (!isset($_COOKIE['uid']) && _string::is_hex($_COOKIE['uid']))
                    $_COOKIE['uid'] = 1;

                $id = max(1, (int)$_COOKIE['uid']);

                // it's guest
                if (!$id)
                    $id = 1;

		$key = 'current::user::' . $id;
		$row = MCached::get($key);
		if ($row === MCached::NO_RESULT) {
                    $res = $db->query("SELECT " . self::USER_FIELDS1 . " FROM users INNER JOIN users_level ON users.id_level = users_level.id WHERE users.id = " . $id);
                    $row = $res->fetch_array(MYSQLI_BOTH);
		    MCached::add($key, $row, self::EXPIRE_CURUSER);
		}
    
                self::prepare_user($row);
    
                if (!$row) {
                    $id  = 1;
                    $res = $db->query("SELECT " . self::USER_FIELDS2 . "FROM users INNER JOIN users_level ON users.id_level = users_level.id WHERE users.id = 1");
                    $row = $res->fetch_array(MYSQLI_BOTH);
                }
	
                if (!isset($_COOKIE['pass']))
                    $_COOKIE['pass'] = '';
	
                if (($_COOKIE['pass'] != md5($GLOBALS['salting'] . $row['random'] . $row['password'] . $row['random'])) && $id != 1) {
                    $id  = 1;
                    $res = $db->query("SELECT " . self::USER_FIELDS3 . " FROM users INNER JOIN users_level ON users.id_level = users_level.id WHERE users.id = 1");
                    $row = $res->fetch_array(MYSQLI_BOTH);
                }

                #Hide Staff IP's by Yupy...
                $hide_ips = array(
                    'Moderator' => 6,
                    'Administrator' => 7,
                    'Owner' => 8
                ); // Staff ID level's 

                $ip = ($row['id_level'] <> $hide_ips['Moderator']) ? $ip : '127.0.0.1';
                $ip = ($row['id_level'] <> $hide_ips['Administrator']) ? $ip : '127.0.0.1';
                $ip = ($row['id_level'] <> $hide_ips['Owner']) ? $ip : '127.0.0.1';

                if ($id > 1)
                    $db->query("UPDATE users SET lastconnect = NOW(), lip = " . $nip . ", cip = '" . AddSlashes($ip) . "' WHERE id = " . $id);
                else
                    $db->query("UPDATE users SET lastconnect = NOW(), lip = 0, cip = NULL WHERE id = 1");
    
                self::$current = $row;
                $GLOBALS['CURUSER'] =& self::$current;
                unset($row);
        }

}

?>
