<?php

/**
 * file:    update_setting.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Settings
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
// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}

//check to see if something was posted
if($_POST){
    //twitter
    if(isset($_POST['setting']) && $_POST['setting'] == "twitter"){
        //if value is yes
        if(isset($_POST['setting']) && isset($_POST['value']) && $_POST['value'] == "yes" ){
            $twitter_value = 1;
        }else{
            $twitter_value = 0;
        }
        //connect to database
        include "../spt_config/mysql_config.php";
        //update value in database
        mysql_query("UPDATE settings SET value = '$twitter_value' WHERE setting = 'twitter_enable'");
    }
    //timezone
    if(isset($_POST['setting']) && $_POST['setting'] == "timezone" ){
        //ensure that a valid entry is submitted
        if(isset($_POST['value']) && preg_match('/(-10\.0|-9\.0|-8\.0|-7\.0|-6\.0|-5\.0|-4\.0|-3\.5|-3\.0|-2\.0|-1\.0|0\.0|1\.0|2\.0|3\.0|3\.5|4\.0|4\.5|5\.0|5\.5|5\.75|6\.0|7\.0|8\.0|9\.0|9\.5|10\.0|11\.0|12\.0)/', $_POST['value'])){
            //get tz
            $tz = $_POST['value'];
            //connect to database
            include "../spt_config/mysql_config.php";
            //update value in database
            mysql_query("UPDATE settings SET value = '$tz' WHERE setting = 'timezone'");
        }
    }
    //google api
    if(isset($_POST['setting']) && $_POST['setting'] == "google_api"){
        //ensure that a valid entry is submitted
        if(isset($_POST['value'])){
            //get the value
            $google_api = $_POST['value'];
            //connec to database
            include "../spt_config/mysql_config.php";
            //update value in database
            mysql_query("UPDATE settings SET value='$google_api' WHERE setting = 'google_api'");
        }
    }    
}

?>
