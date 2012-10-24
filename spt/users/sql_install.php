<?php

/**
 * file:    sql_install.php
 * version: 4.0
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

//This is the table install script for users
//Users Table
$sql = "
    CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL DEFAULT '',
        `password` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
        `disabled` int(1) NOT NULL DEFAULT '0' COMMENT '0-enabled; 1-disabled',
        `fname` varchar(50) DEFAULT NULL COMMENT 'first name',
        `lname` varchar(50) DEFAULT NULL COMMENT 'last name',
        `admin` int(1) NOT NULL DEFAULT '0' COMMENT '0-standard; 1-admin',
        `preset_day` date NOT NULL DEFAULT '0001-01-01',
        `preset_key` varchar(40) DEFAULT NULL,
        `preset_enabled` int(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    )";

mysql_query ( $sql ) or die ( mysql_error () );
?>	