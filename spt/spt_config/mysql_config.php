<?php

/**
 * file:    mysql_config.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Core files
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

//this file is used throughout spt to connect to the database
//WARNING: if you change anything below it might break connectivity to the database

//set the database variables
$mysql_host = 'replace_me';
$mysql_user = 'replace_me';
$mysql_password = 'replace_me';
$mysql_db_name = 'replace_me';

//connect to the database
mysql_connect ( $mysql_host, $mysql_user, $mysql_password ) or die ( "Cannot connect to the database" );
mysql_select_db ( $mysql_db_name ) or die ( "Cannot select the database" );

//create a session for the database name
$_SESSION['spt_db_name'] = $mysql_db_name;
?>