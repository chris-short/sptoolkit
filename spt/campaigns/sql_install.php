<?php
/**
 * file:		sql_install.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Campaign management
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

  //This is the table install script for campaigns
  
  //Campaigns Table
  $sql = 
    "
      CREATE TABLE `campaigns` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `template_id` int(10) NOT NULL,
        `campaign_name` varchar(255) NOT NULL,
        `domain_name` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      )
    ";

  mysql_query($sql) or die(mysql_error());

  //Campaigns and Groups Table
  $sql =
    "
      CREATE TABLE `campaigns_and_groups` (
        `campaign_id` int(10) NOT NULL,
        `group_name` varchar(255) NOT NULL
      )
    ";

  mysql_query($sql) or die(mysql_error());

  //Campaigns Responses
  $sql = 
    "
      CREATE TABLE `campaigns_responses` (
        `target_id` int(10) NOT NULL,
        `campaign_id` int(10) NOT NULL,
        `link` int(1) NOT NULL DEFAULT '0',
        `post` varchar(255) NOT NULL
      )      
    ";

  mysql_query($sql) or die(mysql_error());

?>
