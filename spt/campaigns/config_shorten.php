<?php

/**
 * file:    config_shorten.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Campaign management
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

//get posted api value for google and write to or update database
if ( isset ( $_POST['google'] ) ) {
    $api_key = filter_var ( $_POST['google'], FILTER_SANITIZE_STRING );
    //connect to database
    include '../spt_config/mysql_config.php';
    //check to see if database has an entry for google
    $sql = "SELECT api_key FROM campaigns_shorten WHERE service = 'google'";
    $r = mysql_query ( $sql );
    if ( mysql_num_rows ( $r ) != 1 ) {
        //add the entry
        $sql = "INSERT INTO campaigns_shorten (service, api_key) VALUES ('google', '$api_key')";
        mysql_query ( $sql ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    } else {
        //update entry
        $sql = "UPDATE campaigns_shorten SET api_key = '$api_key' WHERE service = 'google' ";
        mysql_query ( $sql ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    }
    $_SESSION['alert_message'] = "shorten info updated successfully!";
    header ( 'location:./#alert' );
    exit;
} else {
    $_SESSION['alert_message'] = "you must enter an api key";
    header ( 'location:./#shorten' );
    exit;
}
?>