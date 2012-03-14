<?php

/**
 * file:    sql_install.php
 * version: 5.0
 * package:	Simple Phishing Toolkit (spt)
 * component:	Template management
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

//This is the table install script for templates
//Templates Table
$sql = "
    CREATE TABLE `templates` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` longtext NOT NULL,
        PRIMARY KEY (`id`)
    )";

mysql_query ( $sql ) or die ( mysql_error () );

////insert quick start templates

//first sql statement (prevents some problems)
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Amazon shipping information','An email from Amazon.com with shipping information about a recently order.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());

//figure out the campaign id
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//remaining sql statements
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] ***REMOVED*** security update','An email from ***REMOVED***.com requesting the target to update their security information.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Delta flight information','An email from Delta.com with flight information for an upcoming flight.  When the link is clicked, automatic education about malware will occur using an embedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] UPS package tracking','An email from UPS with tracking information for a package to be delivered.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] DGXT Virus','An email IT Services about a virus found in the targets mailbox.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox quota reached','An email from the Helpdesk about a mailbox over quota situation.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Mailbox migration required','An email from the Helpdesk about actions required to be done for a mailbox migration.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Elavon Merchant Account','An email from Elavon about a merchant account to be closed if no action is taken.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Helpdesk support portal','An email from Helpdesk about a new support and information portal now available.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Woodgrove bank','An email from Woodgrove Bank about online access to your account being closed if no action taken.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] Coho Vineyard','An email from Coho Vineyard & Winery with information for a recent order just shipped.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('[QS] 419 scam','An email from a Scottish lawyer wanting help in moving millions of dollars...legally of course.  When the link is clicked, automatic education about malware will occur using anembedded YouTube video.  [Video requires Internet access to YouTube]')";
mysql_query($sql) or die(mysql_error());
$sql = "INSERT INTO templates (name, description) VALUES ('OWA 2010 login','A hand crafted copy of the Outlook Web App 2010 login page that uses no content from original OWA login page.  Comes with three different return.htm pages, just rename them to change the return page displayed to the target once they submit the form.  [No Internet access required]')";
mysql_query($sql) or die(mysql_error());

//set initial counter values
$install_count = 13;
$folder = 1;
$i = 0;

//move files
do {
    //make directory for files
    mkdir('templates/'.$id);
    //move files
    $sourceDir = "templates/temp_upload/".$folder ."/";
    $targetDir = "templates/".$id."/";
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
    rmdir('templates/temp_upload/'.$folder);
    //increment counters
    $id++;
    $folder ++;
    $i++;
} while ($i < $install_count);

?>