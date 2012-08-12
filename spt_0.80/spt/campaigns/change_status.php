<?php

/**
 * file:    change_status.php
 * version: 1.0
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

//get campaign id
$campaign_id = filter_var ( $_REQUEST['c'], FILTER_SANITIZE_NUMBER_INT );

//validate the campaign id
include "../spt_config/mysql_config.php";
$r = mysql_query ( "SELECT id FROM campaigns" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['id'] == $campaign_id ) {
        $match = 1;
    }
}
if ( ! isset ( $match ) ) {
    $_SESSION['alert_message'] = "you can only change the status of valid campaigns";
    header ( 'location:./#alert' );
    exit;
}

//validate the status is set and accurate
if(isset($_REQUEST['s']) AND $_REQUEST['s'] > 0 AND $_REQUEST['s'] < 4 ){
    $status = $_REQUEST['s'];
    mysql_query("UPDATE campaigns SET status = '$status' WHERE id = '$campaign_id'");
    header('location:./?c='.$campaign_id.'#responses');
    exit;
}else{
    $_SESSION['alert_message'] = "please specify a valid status";
    header('location:./#alert');
}
?>
