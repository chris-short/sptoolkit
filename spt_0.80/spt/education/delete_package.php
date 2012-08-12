<?php

/**
 * file:    delete_package.php
 * version: 8.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Education
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

//get education id
$education_id = filter_var ( $_REQUEST['t'], FILTER_SANITIZE_NUMBER_INT );


//validate the education id
include "../spt_config/mysql_config.php";
$r = mysql_query ( "SELECT id FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['id'] == $education_id ) {
        $match = 1;
    }
}
if ( ! isset ( $match ) ) {
    $_SESSION['alert_message'] = "you specified an invalid package";
    header ( 'location:./#alert' );
    exit;
}

//verify this education package is not used in an existing campaign
$r = mysql_query ( "SELECT DISTINCT education_id FROM campaigns" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['education_id'] == $education_id ) {
        $match2 = 1;
    }
}
if ( isset ( $match2 ) ) {
    $_SESSION['alert_message'] = "you cannot delete a package that is currently used by a campaign";
    header ( 'location:./#alert' );
    exit;
}

//delete the education directory from the filesystem
$dir = $education_id;

function delTree ( $dir ) {
    foreach ( glob ( $dir ) as $file ) {
        if ( is_dir ( $file ) ) {
            delTree ( "$file/*" );
            rmdir ( $file );
        } else {
            unlink( $file );
        }
    }
}

delTree ( $dir );

//delete the education from the database
mysql_query ( "DELETE FROM education WHERE id = '$education_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//send them back to the education home page
$_SESSION['alert_message'] = "education package deleted successfully";
header ( 'location:./#alert' );
exit;
?>
