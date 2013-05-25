<?php

/**
 * file:    response.php
 * version: 11.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Campaign management
 * copyright:   Copyright (C) 2011 The SPT Project. All rights reserved.
 * license: GNU/GPL, see license.htm.
 * 
 * This file is part of the Simple Phishing Toolkit (spt).
 * 
 * spt is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, under version 3 of the License.
 *
 * spt is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with spt.  If not, see <http://www.gnu.org/licenses/>.
 * */
//start a session
session_start ();
//if values are being posted recieve all parameters into an array
if ( $_POST ) {
    $post_keys = array_keys( $_POST );
    foreach($post_keys as $pkey) {
        $post = $post . filter_var ( $pkey , FILTER_SANITIZE_SPECIAL_CHARS ) . "<br />";
    }
    //pull in session variables
    $target_id = $_SESSION['target_id'];
    $campaign_id = $_SESSION['campaign_id'];
    $template_id = $_SESSION['template_id'];
    $education_id = $_SESSION['education_id'];
    $education_timing = $_SESSION['education_timing'];
    $link_time = $_SESSION['link_time'];
    //get the time when the data was posted
    $post_time = date ( 'Y-m-d H:i:s' );
    //connect to database
    include "../spt_config/mysql_config.php";
    //insert post metrics into database
    mysql_query ( "UPDATE campaigns_responses SET post = '$post', post_time = '$post_time' WHERE link_time = '$link_time' AND campaign_id = '$campaign_id' AND target_id = '$target_id'" );
    //if education needs to be done after POST send them there
    if ( $education_id > 0 && $education_timing == 2 ) {
        header ( 'location:../education/' . $education_id . '/' );
        exit;
    }
    //terminate session
    session_destroy();
    //send them back to the template
    header ( 'location:../templates/' . $template_id . '/return.htm' );
    exit;
}
//collect all URL parameters and analytical data from the email link and generate sessions and record the link click to the appropriate target
else {
    //get parameters
    $response_id = filter_var ( $_REQUEST['r'], FILTER_SANITIZE_STRING );
    //check to see if the response id is the right length
    if ( strlen ( $response_id ) != 40 ) {
        header ( 'location:http://127.0.0.1' );
        exit;
    }
    //set response id session
    $_SESSION['response_id'] = $response_id;
    //get the ip address
    $target_ip = $_SERVER['REMOTE_ADDR'];
    //get the time when the link was clicked
    $link_time = date ( 'Y-m-d H:i:s' );
    //get browser info
    //pull in browser script
    include "../includes/browser.php";
    //put browser info into variable
    $browser_info = new Browser();
    //get browser type and version
    $browser_type = $browser_info -> getBrowser ();
    $browser_version = $browser_info -> getVersion ();
    //get OS
    $os = $browser_info -> getPlatform ();
    //connect to the database
    include "../spt_config/mysql_config.php";
    //validate that the response id is legit
    $r = mysql_query ( "SELECT response_id FROM campaigns_responses WHERE response_id = '$response_id'" );
    if ( mysql_num_rows ( $r ) > 0 ) {
        $match = 1;
    }
    //if a match happened record that they clicked the link
    if ( isset ( $match ) && $match == 1 ) {
        //get campaign id for this response
        $r = mysql_query ( "SELECT campaign_id, target_id, link, sent, sent_time, java, flash, url, response_log FROM campaigns_responses WHERE response_id = '$response_id'" );
        while ( $ra = mysql_fetch_assoc ( $r ) ) {
            $campaign_id = $ra['campaign_id'];
            $target_id = $ra['target_id'];
            $link = $ra['link'];
            $sent = $ra['sent'];
            $sent_time = $ra['sent_time'];
            $url = $ra['url'];
            $response_log = $ra['response_log'];
        }
        //get campaign direction on whether java or flash should be checked
        $r = mysql_query("SELECT check_java, check_flash FROM campaigns WHERE id = '$campaign_id'");
        while ($ra=mysql_fetch_assoc($r)){
            $check_java = $ra['check_java'];
            $check_flash = $ra['check_flash'];
        }
        if($link == "0"){
            mysql_query ( "UPDATE campaigns_responses SET link = 1, ip = '$target_ip', os = '$os', browser = '$browser_type', browser_version = '$browser_version', link_time = '$link_time'  WHERE response_id = '$response_id'" );
        }else{
            mysql_query ( "INSERT INTO campaigns_responses (target_id, campaign_id, response_id, link, ip, os, browser, browser_version, link_time, sent, url, response_log) VALUES ('$target_id', '$campaign_id', '$response_id', 1, '$target_ip', '$os', '$browser_type', '$browser_version', '$link_time', '$sent', '$url', '$response_log')" );
        }
        //determine what template and education this campaign is using
        $r = mysql_query ( "SELECT template_id, education_id, education_timing FROM campaigns WHERE id = '$campaign_id'" );
        while ( $ra = mysql_fetch_assoc ( $r ) ) {
            $template_id = $ra['template_id'];
            $education_id = $ra['education_id'];
            $education_timing = $ra['education_timing'];
            //set session variables
            $_SESSION['campaign_id'] = $campaign_id;
            $_SESSION['target_id'] = $target_id;
            $_SESSION['template_id'] = $template_id;
            $_SESSION['education_id'] = $education_id;
            $_SESSION['education_timing'] = $education_timing;
            $_SESSION['link_time'] = $link_time;
            //if additional audits should be done send them to be audited first
            //java only
            if(isset($check_java) && $check_java == 1){
                $_SESSION['check_java'] = 1;
            }
            //flash only
            if(isset($check_flash) && $check_flash == 1){
                $_SESSION['check_flash'] = 1;
            }
            //send to audit if necessary
            if($_SESSION['check_java'] OR $_SESSION['check_flash']){
                header('location:audit.php');
                exit;
            }
            //if the campaign is set to education immediately, send the target to be educated
            if ( $education_id > 0 && $education_timing == 1 ) {
                header ( 'location:../education/' . $education_id . '/' );
                exit;
            }
            //send the user to the appropriate template
            header ( 'location:../templates/' . $template_id . '/' );
            exit;
        }
    } else {
        header ( 'location:http://127.0.0.1' );
        exit;
    }
}
?>
    