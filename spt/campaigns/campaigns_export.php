<?php

/**
 * file:    campaigns_export.php
 * version: 10.0
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

//connect to database
include "../spt_config/mysql_config.php";

//get all the custom metrics from the targets table and into an array
$metrics = mysql_query ( "SHOW COLUMNS FROM targets" );

//formulate sql query
$sql = "SELECT campaigns_responses.browser_version AS browser_version, campaigns_responses.java AS java, campaigns_responses.flash AS flash, campaigns_responses.url AS url, campaigns_responses.trained AS trained, campaigns_responses.trained_time AS trained_time, campaigns.campaign_name AS campaign_name, templates.name AS template_name, education.name AS education_name, campaigns.education_timing AS education_timing, campaigns.date_sent AS date_sent, campaigns_responses.post AS post, targets.email AS email, targets.fname AS fname, targets.lname AS lname, campaigns_responses.ip AS ip, campaigns_responses.browser AS browser, campaigns_responses.browser_version AS browser_version, campaigns_responses.os AS os, campaigns_responses.link_time AS link_time, targets.group_name AS group_name";
while ( $metrics_results = mysql_fetch_array ( $metrics ) ) {
    if ( $metrics_results['Field'] != "id" && $metrics_results['Field'] != "email" && $metrics_results['Field'] != "fname" && $metrics_results['Field'] != "lname" && $metrics_results['Field'] != "group_name" ) {
        $sql .= ", targets." . $metrics_results['Field'] . " AS " . $metrics_results['Field'];
    }
}
$sql .= " FROM campaigns_responses JOIN targets ON campaigns_responses.target_id=targets.id JOIN campaigns ON campaigns.id = campaigns_responses.campaign_id LEFT JOIN education ON campaigns.education_id = education.id JOIN templates ON templates.id = campaigns.template_id";
//get data
$r = mysql_query ( $sql ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//set header
$output = "Campaign,Template,Education,EducationTiming,Date,Year,Time,FirstName,LastName,TargetEmail,URL,LinkTime,Post,Trained,IP,Browser,BrowserVersion,OS,Java,Flash,Group";

//get all the custom metrics from the targets table and into an array
$metrics = mysql_query ( "SHOW COLUMNS FROM targets" );

//add custom metrics to header
while ( $metrics_results = mysql_fetch_array ( $metrics ) ) {
    if ( $metrics_results['Field'] != "id" && $metrics_results['Field'] != "email" && $metrics_results['Field'] != "fname" && $metrics_results['Field'] != "lname" && $metrics_results['Field'] != "group_name" ) {
        $output .= "," . $metrics_results['Field'];
    }
}
$output .= "\n";

//output to data
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $output .= $ra['campaign_name'] . "," . $ra['template_name'] . "," . $ra['education_name'] . ",";
    if($ra['education_timing'] == 0){
        $output .= ",";
    }
    if($ra['education_timing'] == 1){
        $output .= "Immediately,";
    }
    if($ra['education_timing'] == 2){
        $output .= "After Form Submission,";
    }
    $output .= $ra['date_sent'] . "," . $ra['fname'] . "," . $ra['lname'] . "," . $ra['email'] . "," . $ra['url'] . "," . $ra['link_time'] . "," . $ra['post'] . ",";
    if($ra['trained'] == 1){
        $output .= $ra['trained_time'].",";
    }
    else{
        $output .= "N,";
    }
    $output .= $ra['ip'] . "," . $ra['browser'] . "," . $ra['browser_version'] . "," . $ra['os'] . "," .$ra['java'].",".$ra['flash'].",". $ra['group_name'];
    //get all the custom metrics from the targets table and into an array
    $metrics = mysql_query ( "SHOW COLUMNS FROM targets" );
    while ( $metrics_results = mysql_fetch_array ( $metrics ) ) {
        if ( $metrics_results['Field'] != "id" && $metrics_results['Field'] != "email" && $metrics_results['Field'] != "fname" && $metrics_results['Field'] != "lname" && $metrics_results['Field'] != "group_name" ) {
            $temp = $metrics_results['Field'];
            $output .= "," . $ra[$temp];
        }
    }
    $output .= "\n";
}

//setup file
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-disposition: csv" . date ( "Y-m-d" ) . ".csv" );
header ( "Content-disposition: filename=campaign_export.csv" );

print $output;

exit;
?>
