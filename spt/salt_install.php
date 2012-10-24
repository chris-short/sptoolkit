<?php

/**
 * file:    salt_install.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Installation
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

$sql = "
    CREATE TABLE `salt` (
        `salt` varchar(50) NOT NULL COMMENT 'Do NOT change this value or your installation will break!'
    )";

mysql_query ( $sql ) or die ( mysql_error () );
?>