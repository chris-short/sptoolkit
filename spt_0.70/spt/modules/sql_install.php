<?php

/**
 * file:    sql_install.php
 * version: 7.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Module management
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
//This is the table install script for modules
//Modules Table
$sql = "
    CREATE TABLE `modules` (
        `name` varchar(50) NOT NULL DEFAULT '' COMMENT 'module name',
        `directory_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'name of the directory',
        `description` longtext NOT NULL,
        `module_date` date NOT NULL,
        `core` int(1) NOT NULL DEFAULT '0'
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Upload Module Data
$sql = "INSERT INTO `modules` VALUES ('Dashboard','dashboard','The dashboard is a landing page for the application that provides a quick look at everything that is going on in spt.  It is a required, core module.  Think of it as your homepage in spt.','2011-08-21',1),('Modules','modules','The modules module manages all other modules.  Yes, that sentence had the word module in it too many times.  All modules depend on this module for installation and uninstallation.  This module is a required, core module.','2011-08-21',1),('Users','users','The users module manages users and permissions regarding access to spt.  It is a required, core module for which all modules depend.','2011-08-21',1),('Targets','targets','This module allows you to manage lists of people and their email addresses.  It also allows you to place people into groups.','2011-08-26',1),('Campaigns','campaigns','This module manages a phishing campaign from start to finish.','2011-08-29',1),('Templates','templates','The Template module manages all aspects of templates that are used in campaigns.','2011-08-29',1),('Education','education','The education module allows you to manage educational packages that can be utilized in campaigns.','2011-12-4',1)";

mysql_query ( $sql ) or die ( mysql_error () );

//Modules Dependency Table
$sql = "
    CREATE TABLE `modules_dependencies` (
        `module` varchar(50) NOT NULL,
        `depends_on` varchar(50) NOT NULL
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Upload Module Dependency Data
$sql = "INSERT INTO `modules_dependencies` VALUES ('Dashboard','Users'),('Modules','Users'),('Campaigns','Targets'),('Targets','Users'),('Campaigns','Users'),('Campaigns','Templates'),('Templates','Users'),('Education','Campaigns'),('Campaigns','Education'),('Education','Users')";

mysql_query ( $sql ) or die ( mysql_error () );
?>