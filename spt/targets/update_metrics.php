<?php

/**
 * file:   update_metrics.php
 * version: 3.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Target management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
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

//get posted metric
$metric = filter_var ( $_POST['field_name'], FILTER_SANITIZE_STRING );

//get value
$shown_value = filter_var ( $_POST['shown'], FILTER_SANITIZE_STRING );

//connect to database
include "../spt_config/mysql_config.php";

//validate the provided metric is valid
$r = mysql_query ( "SELECT field_name FROM targets_metrics" ) or die ( mysql_error () );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['field_name'] == $metric ) {
        $match = 1;
    }
}

if ( ! isset ( $match ) ) {
    $_SESSION['alert_message'] = "Please provide a valid metric that exists";
    exit;
}

//validate that the value for shown is valid
if ( $shown_value != "true" AND $shown_value != "false" ) {
    $_SESSION['alert_message'] = "Please provide a valid status for the metric";
    exit;
}

//modify the status of the metric
if ( $shown_value == "false" ) {
    mysql_query ( "UPDATE targets_metrics SET shown = 0 WHERE field_name = '$metric'" ) or die ( mysql_error () );
    echo "set";
}

//ensure there are not too many metrics shown
$r = mysql_query ( "SELECT SUM(shown) AS total FROM targets_metrics" ) or die ( mysql_error () );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['total'] > 4 ) {
        $_SESSION['alert_message'] = "You are only allowed to display 5 custom metrics at a time";
        exit;
    }
}

//modify the status of the metric
if ( $shown_value == "true" ) {
    mysql_query ( "UPDATE targets_metrics SET shown = 1 WHERE field_name = '$metric'" ) or die ( mysql_error () );
    echo "set";
}
?>