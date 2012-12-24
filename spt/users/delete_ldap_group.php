<?php

/**
 * file:    delete_ldap_group.php
 * version: 1.0
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
$ldap_group = $_REQUEST['g'];

//ensure that the group is a number
if(!preg_match('/^[0-9]/',$ldap_group)){
    $_SESSION['alert_message'] = 'please select a valid group';
    header('location:./#tabs-3');
    exit;
}

//ensure the user is attempting to delete a valid username
$r = mysql_query ( 'SELECT id FROM users_ldap_groups' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['id'] == $ldap_group ) {
        $count = 1;
    }
}
if ( $count != 1 ) {
    $_SESSION['alert_message'] = "you are attempting to delete a group that does not exist";
    header ( 'location:./#tabs-3' );
    exit;
}

//delete the specified user
mysql_query ( "DELETE FROM users_ldap_groups WHERE id = '$ldap_group'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//send the user back to the users page with a success message
$_SESSION['alert_message'] = "group deleted successfully";
header ( 'location:./#tabs-3' );
exit;
?>
