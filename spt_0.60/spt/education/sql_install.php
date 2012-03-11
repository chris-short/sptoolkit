<?php

/**
 * file:    sql_install.php
 * version: 6.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Education
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

//This is the install script for the education module
//Education Table
$sql = "
    CREATE TABLE `education` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) DEFAULT NULL,
        `description` longtext,
        PRIMARY KEY (`id`)
    )";

mysql_query ( $sql ) or die ( mysql_error () );

////insert default education packages

//first sql statement (prevents some problems)
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Phished 1','Displays content about being phished including a Youtube video from Symantec about phishing.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//remaining sql statements

$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Infected 1','Displays content about being infected with malware including a Youtube video from Symantec about various types of malware.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] APWG Phishing Education Landing Page','Displays the full and unmodified content of the APWG phishing education landing page.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[Internet] Flash game from OnGuardOnline.gov','Displays content about being phished including an embedded Shockwave Flash game from OnGuardOnline.gov about phishing.  [Requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 2','Displays content about being phished.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 2','Displays content about being infected with malware.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Infected 3','Displays content about being infected with malware.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO `education` (name, description) VALUES ('[NO Internet] Phished 3','Displays content about being phished.  [No Internet access required].')";
mysql_query($sql) or die(mysql_error());

//set initial counter values
$install_count = 8;
$folder = 1;
$i = 0;

//move files
do {
    //make directory for files
    mkdir($id);
    //move files
    $sourceDir = "temp_upload/".$folder ."/";
    $targetDir = $id."/";
    if ( $dh = opendir($sourceDir) )
    {
        while(false !== ($fileName = readdir($dh)))
        {
            if (!in_array($fileName, array('.','..')))
            {
                rename($sourceDir.$fileName, $targetDir.$fileName);
            }
        }
    }
    //delete the temp folder
    rmdir('temp_upload/'.$folder);
    //increment counters
    $id++;
    $folder ++;
    $i++;
} while ($i < $install_count);


?>
