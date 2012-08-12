<?php

/**
 * file:    add_metric.php
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

//ensure new metric is set
if ( strlen ( $_POST['metric'] ) < 1 ) {
    $_SESSION['alert_message'] = "You must enter something";
    header ( 'location:./#metrics' );
    exit;
}

//get posted metric
$new_metric = filter_var ( $_POST['metric'], FILTER_SANITIZE_STRING );

//connect to database
include "../spt_config/mysql_config.php";

//validate the column headings are only letters and underscores
if ( preg_match ( '#[^0-9a-zA-Z_]#', $new_metric ) ) {
    $_SESSION['alert_message'] = "Metrics can only have letters, numbers and underscores";
    header ( 'location:./#metrics' );
    exit;
}

//validate the new metric does not already exist and then add it if it doesn't
$r = mysql_query ( "SHOW COLUMNS FROM targets" );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['Field'] == $new_metric ) {
        $_SESSION['alert_message'] = "a metric or column name already exists with this name";
        header ( 'location:./#metrics' );
        exit;
    }
}

//add to targets table
mysql_query ( "ALTER TABLE `targets` ADD $new_metric VARCHAR(255)" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//add to targets metric table
mysql_query ( "INSERT INTO targets_metrics VALUES ('$new_metric',0)" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//send them back
header ( 'location:./#metrics' );
exit;
?>