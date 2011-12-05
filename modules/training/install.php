<?php
/**
 * file:		install.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Training
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:		GNU/GPL, see license.htm.
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
**/

  //This is the install script for the training module
  
  //Modules Table
  $sql = 
    "
      INSERT INTO `modules` VALUES ('Training','training','--','The training module allows you to manage training packages that can be utilized in campaigns.','1@sptoolkit.com','2011-12-4',1,0)  
	";

  mysql_query($sql) or die(mysql_error());

  //Training Table
  $sql = 
    "
      CREATE TABLE `training` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) DEFAULT NULL,
        `description` longtext,
        PRIMARY KEY (`id`)
      )
  ";

  mysql_query($sql) or die(mysql_error());

  //Add first entry to training table
  $sql = 
    "
        INSERT INTO `training` (name, description) VALUES ('Default','Default training package')
    ";

  mysql_query($sql) or die(mysql_error());

  //Campaigns Table
  $sql = "ALTER TABLE `campaigns` ADD `training_id` int(10)";
  
  mysql_query($sql) or die(mysql_error());

  //Module Dependency Table
  $sql = 
  	"
  		INSERT INTO `modules_dependencies` VALUES ('Training','Campaigns')
  	";

  mysql_query($sql) or die(mysql_error());
    
?>