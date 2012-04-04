<?php

/**
 * file:    target_export.php
 * version: 4.0
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

//connect to database
include "../spt_config/mysql_config.php";

//set counter
$output = "";
$count = 0;

//get target columns
$r = mysql_query ( "SHOW COLUMNS FROM targets" );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['Field'] != 'id' ) {
        if ( $count != 0 ) {
            $output .= ",";
        }
        $output .= $ra['Field'];
        $count ++;
    }
}

//go to next line
$output .= "\n";

//get data
$r2 = mysql_query ( "SELECT * FROM targets" );
while ( $ra2 = mysql_fetch_row ( $r2 ) ) {
    for ( $i = 1; $i <= $count; $i ++  ) {
        $output .= $ra2[$i];
        if ( $i != $count ) {
            $output .= ",";
            }
    }
    $output .= "\n";
}

//setup file
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-disposition: csv" . date ( "Y-m-d" ) . ".csv" );
header ( "Content-disposition: filename=target_export.csv" );

print $output;

exit;
?>