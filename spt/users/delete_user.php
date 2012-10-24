<?php

/**
 * file:    delete_user.php
 * version: 5.0
 * package: Simple Phishing Toolkit (spt)
 * component:	User management
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

//connect to database
include "../spt_config/mysql_config.php";

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}

//pull in user from parameter and validate
$username = $_REQUEST['u'];

//validate that the passed username is a valid email address
if ( ! filter_var ( $username, FILTER_VALIDATE_EMAIL ) ) {
    $_SESSION['alert_message'] = "you can only delete a user if you pass a valid email address";
    header ( 'location:./#tabs-1' );
    exit;
}

//ensure the user is not attempting to delete themselves
if ( $_SESSION['username'] == $username ) {
    $_SESSION['alert_message'] = "you cannot delete yourself";
    header ( 'location:./#tabs-1' );
    exit;
}

//ensure the user is attempting to delete a valid username
$r = mysql_query ( 'SELECT username FROM users' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['username'] == $username ) {
        $count = 1;
    }
}
if ( $count != 1 ) {
    $_SESSION['alert_message'] = "you are attempting to delete a user that does not exist";
    header ( 'location:./#tabs-1' );
    exit;
}

//delete the specified user
mysql_query ( "DELETE FROM users WHERE username = '$username'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//send the user back to the users page with a success message
$_SESSION['alert_message'] = "user deleted successfully";
header ( 'location:./#tabs-1' );
exit;
?>
