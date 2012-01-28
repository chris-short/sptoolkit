<?php
/**
 * file:		install.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Module Template
 * copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
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

  //This is the install script for the module template
  
  //Update the 'modules' table for the new module (required)
  
  $sql = 
    "
      INSERT INTO `modules` VALUES ('Module Name goes here','Module directory name goes here','Module description goes here.','Date Module created goes here',1,0)  
	";

  mysql_query($sql) or die(mysql_error());

  //Update the 'modules_dependencies' table for new module (required)
  
  $sql = 
  	"
  		INSERT INTO `modules_dependencies` VALUES ('This Modules Name','The Module this module cant exist without')
  	";

  mysql_query($sql) or die(mysql_error());
  
  //Create a table for this module (table name is same as module name) (optional)
  
  //$sql = 
  //  "
  //    CREATE TABLE `module_name` (
  //      `id` int(10) NOT NULL AUTO_INCREMENT,
  //      your_table_schema_here,
  //      your_table_schema_here,
  //	  your_table_schema_here,
  //      PRIMARY KEY (`id`)
  //    )
  //  ";

  //mysql_query($sql) or die(mysql_error());  
  
  //Create additional tables (table name should reference module name, like 'moduleX_data1' (optional)
  
  //$sql = 
  //  "
  //    CREATE TABLE `table_name` (
  //      `id` int(10) NOT NULL AUTO_INCREMENT,
  //      your_table_schema_here,
  //      your_table_schema_here,
  //	  your_table_schema_here,
  //     PRIMARY KEY (`id`)
  //    )
  //  ";

  //mysql_query($sql) or die(mysql_error());   
  
  
  //Modify existing tables...
  
  
  //Copy files outside of the module directory...
  
  
?>