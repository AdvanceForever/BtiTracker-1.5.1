<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

global $db;

if (!user::$current || user::$current["view_users"] == "no") {
    // do nothing
} else {
    block_begin("Online Users");
    
    $curtime = vars::$timestamp;
    $curtime -= 60 * 5;
    $print = '';
    
    if (!isset($regusers))
        $regusers = 0;
    if (!isset($gueststr))
        $gueststr = '';

    $users = '';
    
    $key = 'online::users';
    $users = MCached::get($key);
    if ($users === MCached::NO_RESULT) {
        $res = $db->query("SELECT username, users.id, prefixcolor, suffixcolor FROM users INNER JOIN users_level ON users.id_level = users_level.id WHERE UNIX_TIMESTAMP(lastconnect) >= " . $curtime . " AND users.id > 1");
        
	$print .= ("\n<tr><td class='lista' align='center'>");
		
        if ($res) {
            while ($ruser = $res->fetch_row()) {
                $users .= (($regusers > 0 ? ", " : "") . "\n<a href='userdetails.php?id=" . (int)$ruser[1] . "'>" . StripSlashes($ruser[2] . $ruser[0] . $ruser[3]) . "</a>" . Warn_disabled($ruser[1]));
                $regusers++;
            }
        }
        MCached::add($key, $users, 300);
    }

    // guest code
    $guest_ip = explode('.', vars::$realip);
    $guest_ip = pack("C*", $guest_ip[0], $guest_ip[1], $guest_ip[2], $guest_ip[3]);

    if (!file_exists("addons/guest.dat")) {
        $handle = fopen("addons/guest.dat", "w");
        fclose($handle);
    }

    $handle = fopen("addons/guest.dat", "rb+");
    flock($handle, LOCK_EX);
    $guest_num = intval(filesize("addons/guest.dat") / 8);

    if ($guest_num > 0)
        $data = fread($handle, $guest_num * 8);
    else
        $data = fread($handle, 8);

    $guest   = array();
    $updated = false;

    for ($i = 0; $i < $guest_num; $i++) {
        if ($guest_ip == substr($data, $i * 8 + 4, 4)) {
            $updated   = true;
            $guest[$i] = pack("L", vars::$timestamp) . $guest_ip;
        } elseif (join("", unpack("L", substr($data, $i * 8, 4))) < $curtime)
            $guest_num--;
        else
            $guest[$i] = substr($data, $i * 8, 8);
        
    }
    if ($updated == false) {
        $guest[] = pack("L", vars::$timestamp) . $guest_ip;
        $guest_num++;
    }
    
    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, join('', $guest), $guest_num * 8);
    flock($handle, LOCK_UN);
    fclose($handle);
    $guest_num -= $regusers;

    if ($guest_num < 0)
        $guest_num = 0;

    if ($guest_num > 0)
        $gueststr .= $guest_num + $regusers . " visitor" . ($guest_num + $regusers > 1 ? "s" : "") . " (" . $guest_num . " guest" . ($guest_num > 1 ? "s" : "") . "\n";
    elseif ($guest_num + $regusers == 0)
        $print .= NOBODY_ONLINE . "\n";
    else
        $gueststr .= $guest_num + $regusers . " visitor" . ($guest_num + $regusers > 1 ? "s" : "") . " (";
    
    print($print . $users . "\n</td></tr>");
    block_end();
}

?>
