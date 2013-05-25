<?php

/**
 * file:    sql_install.php
 * version: 21.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Campaign management
 * copyright:   Copyright (C) 2011 The SPT Project. All rights reserved.
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

//This is the table install script for campaigns
//Campaigns Table
$sql = "
    CREATE TABLE `campaigns` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `template_id` int(10) NOT NULL,
        `campaign_name` varchar(255) NOT NULL,
        `domain_name` varchar(255) NOT NULL,
        `education_id` int(10) NOT NULL,
        `education_timing` int(10) NOT NULL,
        `date_sent` varchar(255) NOT NULL,
        `date_ended` varchar(255) NOT NULL,
        `message_delay` int(10) NOT NULL,
        `status` int(1) NOT NULL,
        `spt_path` varchar(255) NOT NULL,
        `encrypt` int(1) NOT NULL,
        `shorten` varchar(255) NOT NULL,
        `cron_id` varchar(255) NOT NULL,
        `check_java` int(1) NOT NULL,
        `check_flash` int(1) NOT NULL,
        PRIMARY KEY (`id`)
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Campaigns and Groups Table
$sql = "
    CREATE TABLE `campaigns_and_groups` (
        `campaign_id` int(10) NOT NULL,
        `group_name` varchar(255) NOT NULL
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Campaigns Responses
$sql = "
    CREATE TABLE `campaigns_responses` (
        `target_id` int(10) NOT NULL,
        `campaign_id` int(10) NOT NULL,
        `response_id` varchar(40) DEFAULT NULL,
        `relay_host` varchar(255) NOT NULL,
        `link` int(1) NOT NULL DEFAULT '0',
        `post` varchar(255) DEFAULT NULL,
        `link_time` datetime DEFAULT NULL,
        `post_time` datetime DEFAULT NULL,
        `ip` varchar(255) DEFAULT NULL,
        `os` varchar(255) DEFAULT NULL,
        `browser` varchar(255) DEFAULT NULL,
        `browser_version` varchar(255) DEFAULT NULL,
        `sent` int(1) NOT NULL,
        `sent_time` datetime DEFAULT NULL,
        `response_log` longtext NOT NULL,
        `trained` int(1) NOT NULL,
        `trained_time` datetime DEFAULT NULL,
        `url` longtext NOT NULL,
        `java` varchar(255) NOT NULL,
        `flash` varchar(255) NOT NULL
    )";

mysql_query ( $sql ) or die ( mysql_error () );

//Shorten Table
$sql = "
    CREATE TABLE `campaigns_shorten` (
        `service` varchar(255) NOT NULL,
        `api_key` varchar(255) NOT NULL
    )";

mysql_query ( $sql ) or die ( mysql_error () );

?>
