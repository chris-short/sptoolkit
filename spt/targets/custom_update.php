<?php

/**
 * file:    custom_update.php
 * version: 5.0
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

//make sure parameters are set they are set
if ( isset ( $_POST['c'] ) && isset ( $_POST['n'] ) ) {
    //determine which column we need to change
    $c = $_POST['c'];

    //determine what the new name will be
    $n = $_POST['n'];

    //connect to database
    include "../spt_config/mysql_config.php";

    //change the column name
    mysql_query("ALTER TABLE `targets` CHANGE `$c` `$n` varchar(255);";
    mysql_query("UPDATE targets_metrics SET field_name = '$n' WHERE field_name = '$c'";
}
?>