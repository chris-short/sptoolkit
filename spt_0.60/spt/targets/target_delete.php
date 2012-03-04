<?php

/**
 * file:    target_delete.php
 * version: 7.0
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

//pull in target
$target_to_delete = filter_var ( $_REQUEST['u'], FILTER_SANITIZE_NUMBER_INT );

//connect to database
include "../spt_config/mysql_config.php";

//make sure target is not part of an active campaign
$r = mysql_query ( "SELECT target_id FROM campaigns_responses WHERE target_id = '$target_to_delete'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
if ( mysql_num_rows ( $r ) ) {
    $_SESSION['alert_message'] = "you cannot delete a target that is part of an active campaign";
    header ( 'location:./#alert' );
    exit;
}

//pull in all target ids and compare to entered data
$r = mysql_query ( "SELECT DISTINCT id FROM targets" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    echo $target_to_delete . "<br />";
    echo $ra['id'] . "<br /><br />";
    if ( $target_to_delete == $ra['id'] ) {
        mysql_query ( "DELETE FROM targets WHERE id = '$target_to_delete'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    }
}

//send user back to targets page with success message
$_SESSION['alert_message'] = "target deleted successfully";
header ( 'location:./#alert' );
exit;
?>