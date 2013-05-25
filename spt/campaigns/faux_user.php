<?php

/**
 * file:    faux_user.php
 * version: 7.0
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
//start a background campaign without a valid session, just a cron id 
if(isset($_GET['cron_id']) && isset($_GET['c'])){
    //validate the campaign and cron id
    $campaign_id = $_GET['c'];
    if(!preg_match('/[0-9]/',$campaign_id)){
        exit;
    }
    include '../spt_config/mysql_config.php';
    $cron_id = $_GET['cron_id'];
    $r = mysql_query("SELECT cron_id FROM campaigns WHERE id = '$campaign_id'");
    while ($ra = mysql_fetch_assoc($r)){
        if($ra['cron_id'] == $cron_id){
            $match = 1;
        }
    }
    //if there is a match continue
    if(isset($match) && $match == 1){
        //see what the status is...set scheduled to active
        $r = mysql_query("SELECT status FROM campaigns WHERE id = '$campaign_id'");
        while ($ra = mysql_fetch_assoc($r)){
            if($ra['status'] == 0){
                mysql_query("UPDATE campaigns SET status = 1 WHERE id = '$campaign_id'");
            }
        }
        //ensure campaign status is set to active
        $r = mysql_query ( "SELECT status FROM campaigns WHERE id = '$campaign_id'" );
        while ( $ra = mysql_fetch_assoc ( $r ) ) {
            if ( $ra['status'] != 1 ) {
                exit;
            }
        }
        //see how many targets are left
        $r = mysql_query ( "SELECT * FROM campaigns_responses WHERE campaign_id = '$campaign_id' AND sent = 0" );
        $ra1 = mysql_num_rows ( $r );
        //get the message delay
        $r = mysql_query ( "SELECT message_delay FROM campaigns WHERE id = '$campaign_id'" );
        while ( $ra = mysql_fetch_assoc ( $r ) ) {
            $message_delay = $ra['message_delay'];
        }
        //calculate a counter
        $second_delay = $message_delay / 1000;
        $i = $second_delay * $ra1;
        if($i < 60){
            $counter = $i; 
        }else{
            $counter = 60;
        }
        //prep next faux user session by creating a cron job
        //get path
        $path = 'http://127.0.0.1' . $_SERVER['REQUEST_URI'];
        //get current date and time
        $start_minute = date('i') + 1;
        $start_hour = date('G');
        $start_day = date('j');
        $start_month = date('n');
        //formulate cron date and time
        $cron_start_date = $start_minute.'    '.$start_hour.'   '.$start_day.'    '.$start_month.'    *    ';
        //construct url that needs to be hit based on cronjob
        $cron_url = "'".$path."'";
        //create a cronjob to come back and start the campaign
        $output = shell_exec('crontab -l');
        file_put_contents('/tmp/crontab.txt', $output.$cron_start_date.'curl '.$cron_url.PHP_EOL);
        echo exec('crontab /tmp/crontab.txt');
        echo exec('rm /tmp/crontab.txt');
        $new_path = preg_replace('/faux_user/', 'send_emails', $path);
        //start sending email
        while($counter < 61){
            //curl url with log suppression
            shell_exec('curl \''.$new_path.'\' 2>&1');
            $counter++;
            sleep(1);
        }

    }
}

?>
