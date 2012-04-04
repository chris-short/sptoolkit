<?php

/**
 * file:    sql_install.php
 * version: 6.0
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

//This is the table install script for targets
//Targets Table
$sql = "
    CREATE TABLE `targets` (
        `id` int(6) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `fname` varchar(255) NOT NULL,
        `lname` varchar(255) NOT NULL,
        `group_name` varchar(50) NOT NULL,
        PRIMARY KEY (`id`)
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Targets Metrics Table
$sql = "
    CREATE TABLE `targets_metrics` (
        `field_name` varchar(155) NOT NULL,
        `shown` int(1) NOT NULL
    )";

mysql_query ( $sql ) or die ( mysql_error () );
?>