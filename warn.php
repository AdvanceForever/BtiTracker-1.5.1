<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'functions.php');

dbconn();

if (!user::$current || user::$current["admin_access"] == "no" || user::$current["edit_users"] == "no") {
    standardheader("Get a freakin' life and stop trying to hack the tracker !");
    block_begin(ERROR);
    err_msg("Error", "Piss off !!! Staff only !");
    block_end();
    stdfoot();
} else {
    standardheader("User Warning System");
    
    if (isset($_GET["action"]))
        $action = security::html_safe($_GET["action"]);
    else
        $action = '';
    
    function warn_expiration($timestamp = 0) {
        return gmdate("Y-m-d H:i:s", $timestamp);
    }

    #Prevent the Warn of the Owner by Yupy...
    if ((int) $_GET['id'] == 2) {
        block_begin('You are such a fool !');
        err_msg(ERROR, "We are trying to Warn the Owner, aren't we ?");
        write_log(user::$current['username'] . ' has tried to Warn the Owner, lol !', 'delete');
        block_end();
        stdfoot();
    } else {
        if ($action == "warn") {
            if ($_POST["reason"] == "" || $_POST["username"] == "" || $_POST["warnfor"] == "") {
                err_msg("Error", "Missing form data.");
            } else {
                $reason    = $db->real_escape_string($_POST["reason"]);
                $username  = $db->real_escape_string($_POST["username"]);
                $added     = warn_expiration(mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                $warnedfor = $db->real_escape_string($_POST["warnfor"]);
                
                if ($_POST['warnfor'] == 7) {
                    $weekswarn  = $db->real_escape_string($_POST['warnfor']) / 7;
                    $period     = "1 Week";
                    $expiration = warn_expiration(mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7, date("Y")));
                } elseif ($_POST['warnfor'] == 14) {
                    $weekswarn  = $db->real_escape_string($_POST['warnfor']) / 7;
                    $period     = "2 Weeks";
                    $expiration = warn_expiration(mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 14, date("Y")));
                } elseif ($_POST['warnfor'] == 21) {
                    $weekswarn  = $db->real_escape_string($_POST['warnfor']) / 7;
                    $period     = "3 Weeks";
                    $expiration = warn_expiration(mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 21, date("Y")));
                } elseif ($_POST['warnfor'] == 28) {
                    $weekswarn  = $db->real_escape_string($_POST['warnfor']) / 7;
                    $period     = "4 Weeks";
                    $expiration = warn_expiration(mktime(date("H"), date("i"), date("s"), date("m") + 1, date("d"), date("Y")));
                } else {
                    $weekswarn  = "0";
                    $period     = "Unlimited";
                    $expiration = "0000-00-00 00-00-00";
                }
                //Unlimited warning period start
                
                //Prepare the variables for insertion in the DB
                $userid = (int) $_GET["id"];
                $url    = unesc($_GET["returnto"]);
                $active = "yes";
                
                
                $sqluserid    = sqlesc((int) $_GET["id"]);
                $sqladded     = sqlesc($added);
                $sqlexpires   = sqlesc($expiration);
                $sqlwarnedfor = sqlesc($weekswarn);
                $sqlreason    = sqlesc($_POST["reason"]);
                $sqladdedby   = sqlesc(user::$current["uid"]);
                $sqlactive    = sqlesc($active);
                $sqlusername  = sqlesc($_POST["username"]);
                
                $warns    = $db->query("SELECT warns FROM users WHERE id = " . $sqluserid);
                $warnings = $warns->fetch_array(MYSQLI_BOTH);
                $sqlwarns = sqlesc($warnings['warns'] + 1);
                
                
                if (($warnings["warns"] + 1) >= $warntimes) {
                    $disable_reason = "Maximum number of Warnings has been reached !";
                    $auto_reason    = sqlesc($disable_reason);
                    
                    //***********execute the queries***********
                    $warnstats = $db->query("SELECT * FROM warnings WHERE userid = " . $sqluserid . " AND active = 'yes'");
                    if ($warnstats->num_rows >= 1) {
                        //update warnings table
                        $db->query("UPDATE warnings SET active = 'no' WHERE userid = " . $sqluserid . " AND active = 'yes'");
                        //update users table
                        $db->query("UPDATE users SET warns = warns + 1, warnremovedby = " . $sqladdedby . ", disabled = 'yes', disabledby = " . $sqladdedby . ", disabledon = " . $sqladded . ", disabledreason = " . $auto_reason . " WHERE id = " . $sqluserid . " AND username = " . $sqlusername);
                    } else {
                        //update users table
                        $db->query("UPDATE users SET warns = warns + 1, disabled = 'yes', disabledby = " . $sqladdedby . ", disabledon = " . $sqladded . ", disabledreason = " . $auto_reason . " WHERE id = " . $sqluserid . " AND username = " . $sqlusername);
                    }
                    $db->query("INSERT INTO warnings (userid, warns, added, expires, warnedfor, reason, addedby, active) VALUES (" . $sqluserid . ", " . $sqlwarns . ", " . $sqladded . ", '0000-00-00 00-00-00', '0', " . $sqlreason . ", " . $sqladdedby . ", 'no')");
                } else {
                    $staff     = $db->query("SELECT username FROM users WHERE id = " . $sqladdedby);
                    $staffname = $staff->fetch_array(MYSQLI_BOTH);
                    
                    $subj = sqlesc("Avertisment !");
                    $msg  = sqlesc("You have received [b]" . $period . " Warning[/b] from [b]" . security::html_safe($staffname['username']) . "[/b]\n\nBecause: " . $reason);
                    
                    //Executing the queries with the above info
                    $db->query("UPDATE users SET warns = warns + 1 WHERE id = " . $sqluserid . " AND username = " . $sqlusername . " LIMIT 1");
                    $db->query("INSERT INTO warnings (userid, warns, added, expires, warnedfor, reason, addedby, active) VALUES (" . $sqluserid . ", " . $sqlwarns . ", " . $sqladded . ", " . $sqlexpires . ", " . $sqlwarnedfor . ", " . $sqlreason . ", " . $sqladdedby . ", " . $sqlactive . ")");
                    $db->query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(2, " . $sqluserid . ", UNIX_TIMESTAMP(), " . $msg . ", " . $subj . ")");
                }
                redirect($url);
            }
        } elseif ($action == "removewarn") {
            $username = $db->real_escape_string($_GET["username"]);
            $url      = stripslashes($_GET["returnto"]);
            
            $sqluserid   = sqlesc((int) $_GET["id"]);
            $sqlusername = sqlesc($_GET["username"]);
            $sqlremover  = sqlesc((int) $_GET["remover"]);
            $subj        = sqlesc("Warn Removed !");
            $msg         = sqlesc("Your Warning has been removed by [b]" . user::$current['level'] . " " . $username . "[/b].");
            
            $db->query("UPDATE warnings SET active = 'no' WHERE userid = " . $sqluserid . " AND active = 'yes' LIMIT 1");
            $db->query("UPDATE users SET warnremovedby = " . $sqlremover . " WHERE id = " . $sqluserid . " AND username = " . $sqlusername . " LIMIT 1");
            $db->query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(2, " . $sqluserid . ", UNIX_TIMESTAMP(), " . $msg . ", " . $subj . ")");
            redirect($url);
        } elseif ($action == "admincpremovewarn") {
            if (empty($_POST["remwarn"])) {
                $url = 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=warnedu';
                redirect($url);
            } else {
                //get data for queries
                $url        = 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=warnedu';
                $userid     = implode(", ", (int) $_POST['remwarn']);
                $sqluserid  = sqlesc($userid);
                $sqlremover = sqlesc(user::$current['uid']);
                $subj       = sqlesc("Warn Removed !");
                $msg        = sqlesc("Your Warning has been removed by [b]" . user::$current['level'] . " " . user::$current['username'] . "[/b].");
                
                //***********execute the queries***********
                //update warnings table
                $db->query("UPDATE warnings SET active = 'no' WHERE userid IN (" . implode(", ", (int) $_POST['remwarn']) . ")  AND active = 'yes'");
                
                //update users table
                $db->query("UPDATE users SET warnremovedby = " . $sqlremover . " WHERE id IN (" . implode(", ", (int) $_POST[remwarn]) . ") ");
                
                //send a private message to every user that got his warn removed
                $message = $db->query("SELECT username, id FROM users WHERE id IN (" . implode(", ", (int) $_POST['remwarn']) . ") ");
                
                while ($get_id = $message->fetch_array(MYSQLI_BOTH)) {
                    $uid = (int) $get_id['id'];
                    
                    $db->query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(2, " . $uid . ", UNIX_TIMESTAMP(), " . $msg . ", " . $subj . ")");
                }
                //redirect back to admincp
                redirect($url);
            }
        } elseif ($action == "resetwarnlevel") {
            //get data for queries
            $userid = max(0, (int) $_GET['uid']);
            $url    = stripslashes($_GET['returnto']);
            
            //subject & body for the private message
            $subj = sqlesc("Warn Level Reset !");
            $msg  = sqlesc("Your Warning Level has been reset by [b]" . user::$current['level'] . " " . user::$current['username'] . "[/b].");
            
            //***********execute the queries***********
            //update warnings table
            $db->query("DELETE FROM warnings WHERE userid = '" . $userid . "'");
            
            //update users table
            $db->query("UPDATE users SET warns = '0', warnremovedby = '0' WHERE id = '" . $userid . "'");
            
            //send a private message to every user that got his warn removed
            $message = $db->query("SELECT username, id FROM users WHERE id = '" . $userid . "'");
            $get_id  = $message->fetch_array(MYSQLI_BOTH);
            
            $uid = (int) $get_id['id'];
            
            $db->query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(2, " . $uid . ", UNIX_TIMESTAMP(), " . $msg . ", " . $subj . ")");
            
            redirect($url);
        } elseif ($action == "disable") {
            //get data for queries
            $disabled = $db->real_escape_string($_POST['disable']);
            $reason   = $db->real_escape_string($_POST['reason']);
            $username = $db->real_escape_string($_POST['name']);
            $userid   = max(0, (int) $_POST['id']);
            $url      = stripslashes($_GET['returnto']);
            $datetime = gmdate("Y-m-d H:i:s");
            
            #Prevent the disable of the Owner by Yupy...
            if ((int) $_POST['id'] == 2) {
                block_begin('You are such a fool !');
                err_msg(ERROR, "We are trying to Disable the Owner, aren't we ?");
                write_log(user::$current['username'] . ' has tried to Disable the Owner, lol !', 'delete');
                block_end();
                stdfoot();
            } else {
                
                if (!$reason) {
                    redirect($url);
                } else {
                    if ($disabled == "no") {
                        redirect($url);
                    } else {
                        $warnstats = $db->query("SELECT * FROM warnings WHERE userid = '" . $userid . "' AND active = 'yes'");
                        if ($warnstats->num_rows >= 1) {
                            //update warnings table
                            $db->query("UPDATE warnings SET active = 'no' WHERE userid = '" . $userid . "' AND active = 'yes'");
                            //update users table
                            $db->query("UPDATE users SET warnremovedby = '" . user::$current['uid'] . "', disabled = 'yes', disabledby = '" . user::$current['uid'] . "', disabledon = '" . $datetime . "', disabledreason = '" . $reason . "' WHERE id = '" . $userid . "' AND username = '" . $username . "'");
                        } else {
                            //update users table
                            $db->query("UPDATE users SET disabled = 'yes', disabledby = '" . user::$current['uid'] . "', disabledon = '" . $datetime . "', disabledreason = '" . $reason . "' WHERE id = '" . $userid . "' AND username = '" . $username . "'");
                        }
                        //redirect back to userdetails
                        redirect($url);
                    }
                }
            }
        } elseif ($action == "admincpremovedisabled") {
            if (empty($_POST["remdisabled"])) {
                $url = 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=disabledu';
                redirect($url);
            } else {
                //get data for queries
                $url = 'admincp.php?user=' . user::$current['uid'] . '&code=' . user::$current['random'] . '&do=disabledu';
                
                //update users table
                $db->query("DELETE FROM users WHERE id IN (" . implode(", ", $_POST['remdisabled']) . ") AND disabled = 'yes'");
                $db->query("DELETE FROM warnings WHERE userid IN (" . implode(", ", $_POST['remdisabled']) . ")");
                
                //redirect back to admincp
                redirect($url);
            }
        } elseif ($action == "enable") {
            //get data for queries
            $disabled = $db->real_escape_string($_POST['disable']);
            $username = $db->real_escape_string($_POST['name']);
            $userid   = max(0, (int) $_POST['id']);
            $url      = stripslashes($_GET['returnto']);
            
            if ($disabled == "yes") {
                redirect($url);
            } else {
                $db->query("UPDATE users SET warnremovedby = '0', disabled = 'no', disabledby = '0', disabledon = '0000-00-00 00:00:00', disabledreason = NULL WHERE id = '" . $userid . "' AND username = '" . $username . "'");
                
                redirect($url);
            }
        }
    }
}

?>
