<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */

function do_sanity() {
    global $PRIVATE_ANNOUNCE, $TORRENTSDIR, $CURRENTPATH, $LIVESTATS, $LOG_HISTORY, $db, $warntimes, $BASEURL, $clean_interval;
    
    // SANITY FOR TORRENTS
    $results = $db->query("SELECT summary.info_hash, seeds, leechers, dlbytes, namemap.filename FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE namemap.external = 'no'");
    $i       = 0;
    while ($row = $results->fetch_row()) {
        list($hash, $seeders, $leechers, $bytes, $filename) = $row;
        
        $timeout = vars::$timestamp - intval($GLOBALS["report_interval"]);
        
        // for testing purpose -- begin
        $resupd = $db->query("SELECT * FROM peers WHERE lastupdate < " . $timeout . " AND infohash = '" . $hash . "'");
        if ($resupd->num_rows > 0) {
            while ($resupdate = $resupd->fetch_array(MYSQLI_BOTH)) {
                $uploaded   = max(0, (int)$resupdate["uploaded"]);
                $downloaded = max(0, (int)$resupdate["downloaded"]);
                $pid        = $db->real_escape_string($resupdate["pid"]);
                $ip         = $db->real_escape_string($resupdate["ip"]);

                //Golden Torrents by CobraCRK
                if ($GLOBALS['freeleech'] == 'yes') {
                    $q = $db->query("SELECT free FROM namemap WHERE info_hash = '" . $hash . "'");
                    $t = mysqli_result($q, 0, 'free');
                    if ($t == 'yes') {
                        $downloaded = 0;
                    }
                }
				
                // update user->peer stats only if not livestat
                if (!$LIVESTATS) {
                    if ($PRIVATE_ANNOUNCE)
                        quickQuery("UPDATE users SET uploaded = uploaded + " . $uploaded . ", downloaded = downloaded + " . $downloaded . " WHERE pid = '" . $pid . "' AND id > 1 LIMIT 1");
                    else // ip
                        quickQuery("UPDATE users SET uploaded = uploaded + " . $uploaded . ", downloaded = downloaded + " . $downloaded . " WHERE cip = '" . $ip . "' AND id > 1 LIMIT 1");
                }
                
                // update dead peer to non active in history table
                if ($LOG_HISTORY) {
                    $resuser = $db->query("SELECT id FROM users WHERE " . ($PRIVATE_ANNOUNCE ? "pid = '" . $pid . "'" : "cip = '" . $ip . "'") . " ORDER BY lastconnect DESC LIMIT 1");
                    $curu    = @$resuser->fetch_row();
                    quickquery("UPDATE history SET active = 'no' WHERE uid = " . (int)$curu[0] . " AND infohash = '" . $hash . "'");
                }
            }
        }
        // for testing purpose -- end
        
        quickQuery("DELETE FROM peers WHERE lastupdate < " . $timeout . " AND infohash = '" . $hash . "'");
        quickQuery("UPDATE summary SET lastcycle = '" . vars::$timestamp . "' WHERE info_hash = '" . $hash . "'");
        
        $results2 = $db->query("SELECT status, COUNT(status) FROM peers WHERE infohash = '" . $hash . "' GROUP BY status");
        $counts   = array();
        while ($row = $results2->fetch_row())
            $counts[$row[0]] = 0 + (int)$row[1];
        
        quickQuery("UPDATE summary SET leechers = " . (isset($counts["leecher"]) ? $counts["leecher"] : 0) . ", seeds = " . (isset($counts["seeder"]) ? $counts["seeder"] : 0) . " WHERE info_hash = '" . $hash . "'");
        
	if ($bytes < 0) {
            quickQuery("UPDATE summary SET dlbytes = 0 WHERE info_hash = '" . $hash . "'");
        }
    }
    // END TORRENT'S SANITY
    
    //  optimize peers table
    quickQuery("OPTIMIZE TABLE peers");
    
    // delete readposts when topic don't exist or deleted  *** should be done by delete, just in case
    quickQuery("DELETE readposts FROM readposts LEFT JOIN topics ON readposts.topicid = topics.id WHERE topics.id IS NULL");
    
    // delete readposts when users was deleted *** should be done by delete, just in case
    quickQuery("DELETE readposts FROM readposts LEFT JOIN users ON readposts.userid = users.id WHERE users.id IS NULL");
    
    // deleting orphan image in torrent's folder (if image code is enabled)
    $tordir = realpath($CURRENTPATH . "/../" . $TORRENTSDIR);
    if ($dir = @opendir($tordir . "/")); {
        while (false !== ($file = @readdir($dir))) {
            if ($ext = substr(strrchr($file, "."), 1) == "png")
                unlink($tordir . "/" . $file);
        }
        @closedir($dir);
    }

    #Seederbonus for BtitTracker by CobraCRK (original ideea from TvRecall)
    if ($GLOBALS['seed_bonus'] == 'yes') {
        $res = $db->query("SELECT DISTINCT pid FROM peers WHERE status = 'seeder'");
        if ($res->num_rows > 0) {
            while ($arr = $res->fetch_assoc()) {
	           $x = $db->real_escape_string($arr['pid']);
                   $db->query("UPDATE users SET seedbonus = seedbonus + 0.166 WHERE pid = '" . $x . "'");
            }
        }
    }

    #Anti Hit & Run...
    if ($GLOBALS['hit_and_run'] == 'yes') {
        $timenow = vars::$timestamp;
	
        $timeres = $db->query("SELECT last_time FROM anti_hit_run_tasks WHERE task = 'sanity'");
        if ($timeres->num_rows > 0) {
            $timearr = $timeres->fetch_array(MYSQLI_BOTH);
            $lastrecordedtime = intval($timearr['last_time']);
        } else {
            $lastrecordedtime = $timenow - $clean_interval;
        }
	
        $res = $db->query("SELECT pid, infohash FROM peers WHERE status = 'seeder'");
        if ($res->num_rows > 0) {
            while ($arr = $res->fetch_assoc()) {
	           $x = $db->real_escape_string($arr['pid']);
    	           $t = $db->real_escape_string($arr['infohash']);
			
  	           $pl = $db->query("SELECT id FROM users WHERE pid = '" . $x . "'");
                   $ccc = mysqli_result($pl, 0, "id");
	
	           $db->query("UPDATE history SET seed = seed + " . $timenow . " - " . $lastrecordedtime . " WHERE uid = " . $ccc . " AND infohash = '" . $t . "'");
            }
        }
	
        $hunden = $db->query("SELECT last_time FROM anti_hit_run_tasks WHERE task = 'sanity'");
        $manneplutt = $hunden->fetch_row();
	
        if (!$manneplutt)
            $db->query("INSERT INTO anti_hit_run_tasks (task, last_time) VALUES ('sanity', " . $timenow . ")");
        else {
            $ts = (int)$manneplutt[0];
            $db->query("UPDATE anti_hit_run_tasks SET last_time = " . $timenow . " WHERE task = 'sanity' AND last_time = " . $ts);
        }

        $levels = $db->query("SELECT id FROM users_level ORDER BY id");

        while ($SingleLevel = $levels->fetch_array(MYSQLI_BOTH)) {
               $hasAntiHitRecord = $db->query("SELECT id_level FROM anti_hit_run WHERE id_level = " . (int)$SingleLevel["id"]);
		
               if ($hasAntiHitRecord->num_rows == 0) {
  	           @$db->query("UPDATE `history`, `users` SET hitchecked = 2 WHERE history.uid = users.id AND users.id_level = " . (int)$SingleLevel["id"] . " AND completed = 'yes' AND hitchecked = '0'");
               }
        }
	
        $hit_parameters = $db->query("SELECT * FROM anti_hit_run ORDER BY id_level");
	
        while ($hit = $hit_parameters->fetch_array(MYSQLI_BOTH)) {
               $r = $db->query("SELECT DISTINCT uid, infohash FROM history INNER JOIN users ON history.uid = users.id WHERE users.id_level = " . (int)$hit["id_level"] . " AND active = 'no' AND completed = 'yes' AND hit = 'no' AND hitchecked = 0 AND date < ( UNIX_TIMESTAMP( ) - (86400 * " . (int)$hit["tolerance_days_before_punishment"] . ")) AND history.downloaded > (1048576 * " . (int)$hit["min_download_size"] . ") AND seed < ( 3600 * " . (int)$hit["min_seed_hours"] . ") AND (history.uploaded / history.downloaded) < " . (float)$hit["min_ratio"]);
       
	       while ($x = $r->fetch_array(MYSQLI_BOTH)) {
	              @$db->query("UPDATE history SET hit = 'yes' WHERE uid = " . (int)$x['uid'] . " AND infohash = '" . $db->real_escape_string($x['infohash']) . "' AND hitchecked = 0");

	              if ($db->affected_rows > 0) {
                          if ($hit['reward'] == 'yes') {
                              $reward = "\n\n[size=3]PS: If you want to get the lost amount, you must seed for at least " . (int)$hit["min_seed_hours"] . " Hours or until the Torrent Ratio becomes greater than " . (float)$hit["min_ratio"] . " then your Total Upload will incremented by " . (int)$hit["upload_punishment"] . " MiB even if the amount taken is less.[/size]";
                          } else {
                              $reward = ' ';
                          }
	
  	                  @$db->query("UPDATE history SET hitchecked = 1, punishment_amount = " . (int)$hit["upload_punishment"] . " WHERE uid = " . (int)$x['uid'] . " AND infohash = '" . $db->real_escape_string($x['infohash']) . "' AND hitchecked = 0");
  	                  @$db->query("UPDATE users SET uploaded = (case when uploaded - (1048576 * " . (int)$hit["upload_punishment"] . ") < 0 then 0 else uploaded - (1048576 * " . (int)$hit["upload_punishment"] . ") end) WHERE id = " . (int)$x['uid']);
 	                  @$db->query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES (3, " . (int)$x['uid'] . ", UNIX_TIMESTAMP(), 'Hit And Run !', 'You had Downloaded the following Torrent without Helping Seeding it:\n\n [URL]" . $BASEURL . "/details.php?id=" . $db->real_escape_string($x['infohash']) . "[/URL] !\n\nYour Total Upload is now less by " . (int)$hit["upload_punishment"] . " MiB!\n\nIf you stay like that you will be Banned soon! " . $db->real_escape_string($reward) . "')");
	              }
	       }
	       $r->free();

               if ($hit['reward'] == 'yes') {
                   $rr = $db->query("SELECT DISTINCT uid, infohash, punishment_amount FROM history INNER JOIN users ON history.uid = users.id WHERE users.id_level = " . (int)$hit["id_level"] . " AND hit = 'yes' AND completed = 'yes' AND hitchecked = 1 AND (seed >= ( 3600 * " . (int)$hit["min_seed_hours"] . ") OR (history.uploaded / history.downloaded) >= " . (float)$hit["min_ratio"] . ")");
           
	           while ($xr = $rr->fetch_array(MYSQLI_BOTH)) {
	                  @$db->query("UPDATE history SET hitchecked = 3 WHERE uid = " . (int)$xr['uid'] . " AND infohash = '" . $db->real_escape_string($xr['infohash']) . "' AND hitchecked = 1");

		          if ($db->affected_rows > 0) {
      	                      @$db->query("UPDATE users SET uploaded = uploaded + (1048576 * " . (int)$xr["punishment_amount"] . ")  WHERE id = " . (int)$xr['uid']);
 	                      @$db->query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES (3, " . (int)$xr['uid'] .", UNIX_TIMESTAMP(), 'Congratulations (Punishement Removed)', 'Congratulations for Seeding the following Torrent:\n\n [URL]" . $BASEURL . "/details.php?id=" . $db->real_escape_string($xr['infohash']) . "[/URL] !\n\n [size=3]The punishment is now removed and your Total Upload amount Increased by " . (int)$xr["punishment_amount"] . " MiB! [/size]\n\n:) ')");
                          }
	           }
	           $rr->free();
               }

               @$db->query("UPDATE history, users SET hitchecked = 1 WHERE history.uid = users.id AND users.id_level = users.id_level = " . (int)$hit["id_level"] . " AND completed = 'yes' AND date < ( UNIX_TIMESTAMP( ) - (86400 * " . (int)$hit["tolerance_days_before_punishment"] . ")) AND hitchecked = 0");
        }
        $levels->free();
        $hasAntiHitRecord->free();
        $hit_parameters->free();
    }
    #Anti Hit & Run End...

    #Seeding Time By Yupy Start...
    if ($GLOBALS['seed_time'] == 'yes') {
        $res = $db->query("SELECT pid, infohash FROM peers WHERE status = 'seeder'");
        if ($res->num_rows > 0) {
            while ($arr = $res->fetch_assoc()) {
	           $xy = $db->real_escape_string($arr['pid']);
	           $ty = $db->real_escape_string($arr['infohash']);

	           $ply = $db->query("SELECT id FROM users WHERE pid = '" . $xy . "'");
	           $cccy = mysqli_result($ply, 0, 'id');

	           $db->query("UPDATE history SET seedtime = seedtime + " . $clean_interval . " WHERE uid = " . $cccy . " AND infohash = '" . $ty . "'");
            }
        }
    }
    #Seeding Time By Yupy End...

    #AutoRemove User Warnings Upon Expiration Hack Start...
    if ($GLOBALS['warn_system'] == 'yes') {
        $datetime = gmdate('Y-m-d H:i:s');
        $subj = sqlesc('Warn Expired !');
        $msg = sqlesc('Your warning expired and has been removed by the System.');

        $warnstats = $db->query("SELECT * FROM warnings WHERE expires <= '" . $datetime . "' AND active = 'yes'");
        while ($arr = $warnstats->fetch_assoc()) {
	       if ($warnstats->num_rows > 0) {
	           if ($arr['warnedfor'] == '0') {
	           } else {
	               $db->query("UPDATE warnings SET active = 'no' WHERE userid = '" . (int)$arr['userid'] . "' AND active = 'yes'") or sqlerr();
	               $db->query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES('2', '" . (int)$arr['userid'] . "', UNIX_TIMESTAMP(), " . $msg . ", " . $subj . ")") or sqlerr();
	               $db->query("UPDATE users SET warnremovedby = '2' WHERE id = '" . (int)$arr['userid'] . "' AND username = '" . $name . "'") or sqlerr();
	           }
	       }
        }
        #AutoRemove user Warnings Upon Expiration Hack Stop...
        #Auto Delete Account After Disable Hack Start
        if ($GLOBALS['autodeldisabled'] == true) {
            $disable = $GLOBALS['acctdisable'];
            $accountdisable = $disable * 86400;

            $disablestats = $db->query("SELECT disabledon, id FROM users WHERE disabled = 'yes' AND disabledon < DATE_SUB(NOW(), INTERVAL " . $accountdisable . " SECOND)");
            while ($arr2 = $disablestats->fetch_assoc()) {
                   $db->query("DELETE FROM users WHERE id = " . (int)$arr2['id']);
            }
        }
        #Auto Delete Account After Disable Hack Stop...
    } #End Warn/Disabled...

    //timed rank by Diemthuy Adapted by Yupy for BtitTracker
    if ($GLOBALS['timed_ranks'] == 'yes') {
        $datetimedt = date('Y-m-d H:i:s');

        $rankstats = $db->query("SELECT * FROM users WHERE timed_rank < '" . $datetimedt . "' AND rank_switch = 'yes'") or sqlerr(__FILE__, __LINE__);
        while ($arrdt = $rankstats->fetch_assoc()) {
	    if ($rankstats->num_rows > 0) {
                $res6 = $db->query("SELECT level FROM users_level WHERE id = '" . (int)$arrdt['old_rank'] . "'") or sqlerr(__FILE__, __LINE__);
                $arr6 = $res6->fetch_assoc();

                $oldrank = security::html_safe($arr6['level']);

                $subj = sqlesc("Your Timed Rank Expired!");
                $msg = sqlesc("Your Timed Rank Expired!\n\n Your Rank did changed back to " . $oldrank . "\n\n [color=red]This is a automatic system message, Do not reply![/color]");
                $db->query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES(2, " . (int)$arrdt['id'] . ", UNIX_TIMESTAMP(), " . $subj . ", " . $msg . ")") or sqlerr(__FILE__, __LINE__);
                $db->query("UPDATE users SET rank_switch = 'no', id_level = old_rank WHERE id = '" . (int)$arrdt['id'] . "'") or sqlerr(__FILE__, __LINE__);
	    }
        }
     }
    //timed rank end
    
}

?>
