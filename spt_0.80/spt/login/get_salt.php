<?php

/**
 * file:    get_salt.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Login management
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

//this file grabs the salt value from the database that was set during installation
//a consistent and unique salt value is required throughout spt
//WARNING: changing an installations salt value may break authentication to spt globally
//connect to database
include '../spt_config/mysql_config.php';

//get the salt value
$r = mysql_query ( "SELECT salt FROM salt" );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $salt = $ra['salt'];
}

//set the salt session
$_SESSION['salt'] = $salt;
?>
