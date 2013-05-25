<?php

/**
 * file:    start_campaign.php
 * version: 40.0
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
// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}
//pull in all posted values
$campaign_name = $_POST['campaign_name'];
if ( ! empty ( $campaign_name ) ) {
    $_SESSION['temp_campaign_name'] = $campaign_name;
}
$target_groups = $_POST['target_groups'];
if ( ! empty ( $target_groups ) ) {
    $_SESSION['temp_target_groups'] = $target_groups;
}
$template_id = $_POST['template_id'];
if ( ! empty ( $template_id ) ) {
    $_SESSION['temp_template_id'] = $template_id;
}
$message_delay = $_POST['message_delay'];
if ( ! empty ( $message_delay ) ) {
    $_SESSION['temp_message_delay'] = $message_delay;
}
$spt_path = $_POST['spt_path'];
if ( ! empty ( $spt_path ) ) {
    $_SESSION['temp_spt_path'] = $spt_path;
}
$education_id = filter_var ( $_POST['education_id'], FILTER_SANITIZE_NUMBER_INT );
if ( ! empty ( $education_id ) ) {
    $_SESSION['temp_education_id'] = $education_id;
}
$date_sent = date ( "F j, Y, g:i a" );
if ( ! empty ( $date_sent ) ) {
    $_SESSION['temp_date_sent'] = $date_sent;
}
if ( isset ( $_POST['education_timing'] ) ) {
    $education_timing = filter_var ( $_POST['education_timing'], FILTER_SANITIZE_NUMBER_INT );
    $_SESSION['temp_education_timing'] = $education_timing;
}
if ( isset ( $_POST['relay_host'] ) ) {
    $relay_host = $_POST['relay_host'];
    $_SESSION['temp_relay_host'] = $relay_host;
}else{
    $relay_host = "-";
}
if ( isset ( $_POST['shorten_radio'] ) ) {
    $shorten = filter_var ( $_POST['shorten_radio'], FILTER_SANITIZE_STRING );
    $_SESSION['temp_shorten'] = $shorten;
}
if(isset($_POST['start_month']) && $_POST['start_month'] != "-"){
    $start_month = $_POST['start_month'];
    $_SESSION['temp_start_month'] = $start_month; 
}
if(isset($_POST['start_day']) && $_POST['start_day'] != "-"){
    $start_day = $_POST['start_day'];
    $_SESSION['temp_start_day'] = $start_day; 
}
if(isset($_POST['start_hour']) && $_POST['start_hour'] != "-"){
    $start_hour = $_POST['start_hour'];
    $_SESSION['temp_start_hour'] = $start_hour; 
}
if(isset($_POST['start_minute']) && $_POST['start_minute'] != "-"){
    $start_minute = $_POST['start_minute'];
    $_SESSION['temp_start_minute'] = $start_minute; 
}
if(isset($_POST['background'])){
    $background = $_POST['background'];
    $_SESSION['temp_background'] = $background;
}
if(isset($_POST['check_java'])){
    $check_java = $_POST['check_java'];
    $_SESSION['temp_check_java'] = $_POST['check_java'];
}
if(isset($_POST['check_flash'])){
    $check_flash = $_POST['check_flash'];
    $_SESSION['temp_check_flash'] = $_POST['check_flash'];
}
//ensure the campaign name is set
if ( strlen ( $campaign_name ) < 1 ) {
    $_SESSION['alert_message'] = "you must give the campaign a name";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}
//ensure a target group was selected
if ( ! isset ( $target_groups ) ) {
    $_SESSION['alert_message'] = "please select at least one target group";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}
//ensure a template is selected
if ( ! isset ( $template_id ) ) {
    $_SESSION['alert_message'] = "please select a template";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}
//ensure that a message delay is set
if ( ! isset ( $message_delay ) ) {
    $_SESSION['alert_message'] = "please enter a value for message delay";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}
//validate date and time if set
if((isset($start_month) OR isset($start_day) OR isset($start_hour) OR isset($start_minute)) AND (!isset($start_month) OR !isset($start_day) OR !isset($start_hour) OR !isset($start_minute))){
    $_SESSION['alert_message'] = "if you are going to schedule this campaign, please complete all date/time fields";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($start_month) && ($start_month < 1 OR $start_month > 12)){
    $_SESSION['alert_message'] = "please select a valid month";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($start_day) && ($start_day < 1 OR $start_day > 31)){
    $_SESSION['alert_message'] = "please select a valid day of the month";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($start_day) && isset($start_month) && ($start_month == 2 OR $start_month == 4 OR $start_month == 6 OR $start_month == 9 OR $start_month == 11) && $start_day >30){
    $_SESSION['alert_message'] = "the month you selected does not have this many days in it";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($start_hour) && ($start_hour < 0 OR $start_hour > 23)){
    $_SESSION['alert_message'] = "please enter a valid hour 0-23";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($start_minute) && ($start_minute < 0 OR $start_minute > 59)){
    $_SESSION['alert_message'] = "please enter a valid minute 0-59";
    header('location:.?add_campaign=true#tabs-1');
    exit;
}
if(isset($check_java)){
    $check_java = 1;
}else{
    $check_java = 0;
}
if(isset($check_flash)){
    $check_flash = 1;
}else{
    $check_flash = 0;
}
if(isset($background) && $background == 'Yes'){
    $background = 'Y';
}else{
    $background = 'N';
}
//connect to database
include "../spt_config/mysql_config.php";
//take each value in the array and validate that it is a valid group name
foreach ( $target_groups as $group ) {
    $r = mysql_query ( "SELECT DISTINCT group_name FROM targets" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        if ( $group == $ra['group_name'] ) {
            $match = 1;
        }
    }
    if ( ! isset ( $match ) ) {
        $_SESSION['alert_message'] = "invalid group";
        header ( 'location:./?add_campaign=true#tabs-1' );
        exit;
    }
}

//validate the template exists
$r = mysql_query ( "SELECT id FROM templates" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $template_id == $ra['id'] ) {
        $match0 = 1;
    }
}
if ( ! isset ( $match0 ) ) {
    $_SESSION['alert_message'] = "please select a valid template";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}

//validate the education package exists
$r = mysql_query ( 'SELECT id FROM education' ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $education_id == $ra['id'] OR $education_id == 0 ) {
        $match1 = 1;
    }
}
if ( ! isset ( $match1 ) ) {
    $_SESSION['alert_message'] = "please select a valid education package";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}

//validate the education timing if set
if ( isset ( $education_timing ) ) {
    if ( $education_timing == 1 OR $education_timing == 2 ) {
        $match2 = 1;
    }
} else {
    $education_timing = 0;
    $match2 = 1;
}
if ( $match2 != 1 ) {
    $_SESSION['alert_message'] = "please select a valid education timing option";
    header ( 'location:./?add_campaign=true#tabs-1' );
    exit;
}

//if Google shortener is selected validate that their is an API stored in the database
if(isset($shorten) && $shorten == "Google"){
    //query database for a Google API
    $r = mysql_query("SELECT setting, value FROM settings WHERE setting = 'google_api'");
    if(mysql_num_rows($r) != 1){
        $_SESSION['alert_message'] = "you must enter your Google API key before trying to use the Google Shortener";
        header('location:./?add_campaign=true#tabs-1');
        exit;
    }
}

//validate the message delay
if ( isset ( $message_delay ) ) {
    //ensure the message delay is greater than 100 ms
    if ( $message_delay < 100 ) {
        $_SESSION['alert_message'] = "the message delay factor must be greater than 100ms";
        header ( 'location:./?add_campaign=true#tabs-1' );
        exit;
    }
    //ensure the message delay is in incrmeents of 100
    if ( substr ( $message_delay, -2 ) != "00" ) {
        $_SESSION['alert_message'] = "the message delay factor should be in increments of 100ms";
        header ( 'location:./?add_campaign=true#tabs-1' );
        exit;
    }
    //ensure the message delay is not greater than 1 minute
    if ( $message_delay > 60000 ) {
        $_SESSION['alert_message'] = "the message delay factor cannot be more than 1 minute";
        header ( 'location:./?add_campaign=true#tabs-1' );
        exit;
    }
} else {
    //if for some reason the message delay is not set, give it the default value of 1 second
    $message_delay = 1000;
}

//check to see if the campaign has been scheduled and if so schedule a cron job to start it otherwise start it immediately
if (isset($start_month)){
    //formulate cron date and time
    $cron_start_date = $start_minute.'    '.$start_hour.'    '.$start_day.'    '.$start_month.'    *    ';
    //create random cron_id value and store it in the database
    $cron_id = mt_rand(10000000,99999999);
    //get protocol
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        $request_protocol = "https";
    } else {
        $request_protocol = "http";
    }
    //create the campaign
    mysql_query ( "INSERT INTO campaigns (campaign_name, template_id, domain_name, education_id, education_timing, date_sent, message_delay, status, spt_path, cron_id, check_java, check_flash) VALUES ('$campaign_name', '$template_id', '$spt_path', '$education_id', '$education_timing', '$date_sent', '$message_delay', 0, '$spt_path', '$cron_id', '$check_java', '$check_flash')" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    //get the id of this campaign
    $r = mysql_query ( "SELECT MAX(id) as campaign_id FROM campaigns" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $campaign_id = $ra['campaign_id'];
    }
    //get path
    $path = 'http://127.0.0.1' . $_SERVER['REQUEST_URI'];
    //replace start_campaignn with faux_user
    $path = preg_replace('/start_campaign/', 'faux_user', $path);
    //construct url that needs to be hit based on cronjob
    $cron_url = "'".$path."?c=".$campaign_id."&cron_id=".$cron_id."'";
    //create a cronjob to come back and start the campaign
    $output = shell_exec('crontab -l');
    file_put_contents('/tmp/crontab.txt', $output.$cron_start_date.'curl '.$cron_url.PHP_EOL);
    echo exec('crontab /tmp/crontab.txt');
    echo exec('rm /tmp/crontab.txt');
    $scheduled = "Y";
}else{
    //create the campaign
    mysql_query ( "INSERT INTO campaigns (campaign_name, template_id, domain_name, education_id, education_timing, date_sent, message_delay, status, spt_path, check_java, check_flash) VALUES ('$campaign_name', '$template_id', '$spt_path', '$education_id', '$education_timing', '$date_sent', '$message_delay', 1, '$spt_path', '$check_java', '$check_flash')" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );

    //get the id of this campaign
    $r = mysql_query ( "SELECT MAX(id) as campaign_id FROM campaigns" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $campaign_id = $ra['campaign_id'];
    }
}
//update shorten if it is set
if ( isset ( $shorten ) ) {
    mysql_query ( "UPDATE campaigns SET shorten = '$shorten' WHERE id='$campaign_id'" );
}else{
    mysql_query("UPDATE campaigns SET shorten = 'none' WHERE id = '$campaign_id'");
}
//link the campaign id and group name while retrieving all applicable targets
foreach ( $target_groups as $group ) {
    //link campaign id and group names
    mysql_query ( "INSERT INTO campaigns_and_groups (campaign_id, group_name) VALUES ('$campaign_id','$group')" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    //prepare relay host counters
    $relay_host_count = count($relay_host);
    $relay_host_counter = 0;
    //retrieve all targets from group
    $r = mysql_query ( "SELECT id FROM targets WHERE group_name = '$group'" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $target_id = $ra['id'];
        //generate a random key
        $random_number = mt_rand ( 1000000000, 9999999999 );
        $response_id = sha1 ( $random_number );
        //determine relay host
        $relay_host_id = $relay_host[$relay_host_counter];
        //populate the campaign response table with placeholders for when the target clicks the links or posts data
        mysql_query ( "INSERT INTO campaigns_responses (target_id, campaign_id, response_id, sent, relay_host) VALUES ('$target_id', '$campaign_id', '$response_id', 0, '$relay_host_id')" ) or die ( '<!DOCTYPE HTML><html><body><div id="die_error">There is a problem with the database...please try again later</div></body></html>' );
        //adjust relay_host_counter
        if(($relay_host_counter+1) == $relay_host_count){
            $relay_host_counter = 0;
        }else{
            $relay_host_counter++;
        }
    }
}
//unset temp variables
unset($_SESSION['temp_campaign_name']);
unset($_SESSION['temp_target_groups']);
unset($_SESSION['temp_template_id']);
unset($_SESSION['temp_message_delay']);
unset($_SESSION['temp_spt_path']);
unset($_SESSION['temp_education_id']);
unset($_SESSION['temp_date_sent']);
unset($_SESSION['temp_education_timing']);
unset($_SESSION['temp_relay_host']);
unset($_SESSION['temp_relay_port']);
unset($_SESSION['temp_ssl']);
unset($_SESSION['temp_relay_username']);
unset($_SESSION['temp_relay_password']);
unset($_SESSION['temp_shorten']);
unset($_SESSION['temp_start_month']);
unset($_SESSION['temp_start_day']);
unset($_SESSION['temp_start_hour']);
unset($_SESSION['temp_start_minute']);
unset($_SESSION['temp_background']);
unset($_SESSION['temp_check_java']);
unset($_SESSION['temp_check_flash']);
//if scheduled send back to campaign page
if(isset($scheduled) && $scheduled == "Y"){
    $_SESSION['alert_message'] = "your campaign has been scheduled";
    header('location:.#tabs-2');
    exit;
}
//check to see if its a background job and if so send it back through to be run in the background
if($background == 'Y'){
    //create random cron_id value and store it in the database
    $cron_id = mt_rand(10000000,99999999);
    mysql_query ( "UPDATE campaigns SET cron_id = '$cron_id' WHERE id = '$campaign_id'" );
    if(mysql_error()){
        $_SESSION['alert_message'] = mysql_error();
        header('location:.#tabs-1');
        exit;
    }
    //get protocol
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        $request_protocol = "https";
    } else {
        $request_protocol = "http";
    }
    //get current date and time
    $start_minute = date('i') + 1;
    $start_hour = date('G');
    $start_day = date('j');
    $start_month = date('n');
    //formulate cron date and time
    $cron_start_date = $start_minute.'    '.$start_hour.'    '.$start_day.'    '.$start_month.'    *    ';
    //get path
    $path = 'http://127.0.0.1' . $_SERVER['REQUEST_URI'];
    //replace start_campaignn with faux_user
    $path = preg_replace('/start_campaign/', 'faux_user', $path);
    //construct url that needs to be hit based on cronjob
    $cron_url = "'".$path."?c=".$campaign_id."&cron_id=".$cron_id."'";
    //create a cronjob to come back and start the campaign
    $output = shell_exec('crontab -l');
    file_put_contents('/tmp/crontab.txt', $output.$cron_start_date.'curl '.$cron_url.PHP_EOL);
    echo exec('crontab /tmp/crontab.txt');
    $_SESSION['alert_message'] = 'your campaign has been sent to the scheduler and should start in 60 seconds';
    header('location:.#tabs-3');
    exit;
}
//send non background campaigns to the response page for their campaign to begin
header ( 'location:./?c=' . $campaign_id . '&responses=true#tabs-3' );
?>
