<?php
/**
 * file:		install.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Editor
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

  //This is the install script for the editor
  
  //Modules Table
  $sql = 
    "
      INSERT INTO `modules` VALUES ('Editor','editor','--','The editor gives you basic web based editing of your template files. Eliminating the need to access files at the command line or attempting to find the files within the templates directory of your spt installation','1@sptoolkit.com','2011-11-23',1,0)  
	";

  mysql_query($sql) or die(mysql_error());

  //Module Dependency Table
  $sql = 
  	"
  		INSERT INTO `modules_dependencies` VALUES ('Editor','Templates')
  	";

  mysql_query($sql) or die(mysql_error());
?>