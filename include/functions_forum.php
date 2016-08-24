<?php
/*
 * BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
 * This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
 * Updated and Maintained by Yupy.
 * Copyright (C) 2004-2015 Btiteam.org
 */

function catch_up() {
    global $db;
    
    $userid = user::$current['uid'];
    
    $res = $db->query('SELECT id, lastpost FROM topics');
    while ($arr = $res->fetch_assoc()) {
        $topicid = (int)$arr['id'];
        $postid  = (int)$arr['lastpost'];
        
        $r = $db->query('SELECT id, lastpostread FROM readposts WHERE userid = ' . $userid . ' AND topicid = ' . $topicid) or sqlerr(__FILE__, __LINE__);
        
        if ($r->num_rows == 0)
            $db->query('INSERT INTO readposts (userid, topicid, lastpostread) VALUES(' . $userid . ', ' . $topicid . ', ' . $postid . ')') or sqlerr(__FILE__, __LINE__);
        else {
            $a = $r->fetch_assoc();
            
            if ($a['lastpostread'] < $postid)
                $db->query('UPDATE readposts SET lastpostread = ' . $postid . ' WHERE id = ' . (int)$a['id']) or sqlerr(__FILE__, __LINE__);
        }
    }
}

function update_topic_last_post($topicid) {
    global $db;
    
    MCached::connect();
    
    $res = $db->query('SELECT id FROM posts WHERE topicid = ' . $topicid . ' ORDER BY id DESC LIMIT 1') or sqlerr(__FILE__, __LINE__);
    $arr = $res->fetch_row() or die('No post found');
    
    $postid = (int)$arr[0];
    
    $db->query('UPDATE topics SET lastpost = ' . $postid . ' WHERE id = ' . $topicid) or sqlerr(__FILE__, __LINE__);
    
    MCached::del('get::topic::info::' . $topicid);
    MCached::del('forum::last::post::' . $topicid);
    MCached::del('quick::jump::topics::' . $topicid);
}

function insert_quick_jump_menu($currentforum = 0) {
    global $db;
    
    print("<p align='center'><form method='get' action='?' name='quickjump'>\n");
    print('&nbsp;' . QUICK_JUMP . ': ');
    print("<select name='forumid' onchange='location.href=this.options[this.selectedIndex].value'>\n");
    
    $res = $db->query('SELECT id, name, minclassread FROM forums ORDER BY sort, name') or sqlerr(__FILE__, __LINE__);
    
    while ($arr = $res->fetch_assoc()) {
        if (user::$current['id_level'] >= $arr['minclassread'])
            print("<option value='forum.php?action=viewforum&forumid=" . (int)$arr['id'] . ($currentforum == (int)$arr['id'] ? " selected'>" : "'>") . security::html_safe(unesc($arr['name'])) . "</option>\n");
    }
    
    print("</select>\n");
    print("</form>\n</p>");
}

function insert_compose_frame($id, $newtopic = true, $quote = false) {
    global $maxsubjectlength, $db, $smarty, $STYLEPATH;
    
    MCached::connect();
    
    if ($newtopic) {
        $arr = MCached::get('forums::name::' . $id);
        if ($arr === MCached::NO_RESULT) {
            $res = $db->query('SELECT name FROM forums WHERE id = ' . $id) or sqlerr(__FILE__, __LINE__);
            $arr = $res->fetch_assoc() or die(BAD_FORUM_ID);
            MCached::add('forums::name::' . $id, $arr, 1800);
        }
        
        $forumname = security::html_safe(unesc($arr['name']));
        
        block_begin(WORD_NEW . " " . TOPIC . " " . IN . " <a href='?action=viewforum&forumid=" . $id . "'>" . $forumname . "</a> " . FORUM);
    } else {
        $arr = MCached::get('quick::jump::topics::' . $id);
        if ($arr === MCached::NO_RESULT) {
            $res = $db->query('SELECT * FROM topics WHERE id = ' . $id) or sqlerr(__FILE__, __LINE__);
            $arr = $res->fetch_assoc() or stderr(ERROR, FORUM_ERROR . TOPIC_NOT_FOUND);
            MCached::add('quick::jump::topics::' . $id, $arr, 1800);
        }
        
        $subject = security::html_safe(unesc($arr['subject']));
        
        block_begin(REPLY . " " . TOPIC . ": <a href='?action=viewtopic&topicid=" . $id . "'>" . $subject . "</a>");
    }
    
    begin_frame();

    $smarty->assign('compose_id', $id);
    $smarty->assign('lang_subject', SUBJECT);
    $smarty->assign('is_new_topic', ($newtopic));
    $smarty->assign('maxsubjectlength', $maxsubjectlength);
    $smarty->assign('begin_table', begin_table());
    
    if ($quote) {
        $postid = 0 + (int)$_GET['postid'];

        if (!is_valid_id($postid))
            die;
        
        $res = $db->query('SELECT posts.*, users.username FROM posts INNER JOIN users ON posts.userid = users.id WHERE posts.id = ' . $postid) or sqlerr(__FILE__, __LINE__);
        
        if ($res->num_rows != 1)
            stderr(ERROR, ERR_NO_POST_WITH_ID . '' . $postid);
        
        $arr = $res->fetch_assoc();
    }

    $smarty->assign('lang_body', BODY);
    $smarty->assign('compose_body', textbbcode2('compose', 'body', ($quote ? (('[quote=' . security::html_safe($arr['username']) . ']' . security::html_safe(unesc($arr['body'])) . '[/quote]')) : '')));
    $smarty->assign('lang_confirm', FRM_CONFIRM);
    $smarty->assign('end_table', end_table());
    
    $smarty->display($STYLEPATH . '/tpl/forum/forum_compose.tpl');
    
    end_frame();
    
    //------ Get 10 last posts if this is a reply
    
    if (!$newtopic) {
        $postres = $db->query("SELECT * FROM posts WHERE topicid = " . $id . " ORDER BY id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
        
        begin_frame(LAST_10_POSTS, true);
        
        while ($post = $postres->fetch_assoc()) {
            //-- Get poster details
            $userres = $db->query("SELECT avatar, username FROM users WHERE id = " . (int)$post["userid"] . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
            $user = $userres->fetch_assoc();
            
            $avatar = ($user['avatar'] && $user['avatar'] != '' ? security::html_safe($user['avatar']) : 'images/default_avatar.png');
            
            begin_table(true);
            
            print("<tr valign='top'><td width='150' align='center' style='padding: 0px'>#" . (int)$post['id'] . " by " . security::html_safe($user['username']) . "<br />" . get_date_time($post['added']) . ($avatar != "" ? "<br /><img width='80' src='" . $avatar . "'>" : "") . "</td><td class='lista'>" . format_comment(unesc($post['body'])) . "</td></tr><br>\n");
            
            end_table();
        }
        end_frame();
    }
    
    if (!isset($forumid))
        $forumid = 0;
    
    insert_quick_jump_menu($forumid);
    block_end();
}

?>
