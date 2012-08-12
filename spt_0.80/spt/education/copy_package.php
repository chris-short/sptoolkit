<?php

/**
 * file:    copy_package.php
 * version: 1.0
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

//set values
if(!filter_var($_REQUEST['id'], FILTER_VALIDATE_INT)){
    $_SESSION['alert_message'] = 'please provide a valid package id';
    header('location:./#alert');
    exit;
}
$package_id = $_REQUEST['id'];

//validate the template id is valid
//connect to database
include "../spt_config/mysql_config.php";
//query database for existing templates
$sql = "SELECT id, name, description FROM education";
$r = mysql_query($sql);
$match = 0;
while($ra = mysql_fetch_assoc($r)){
    if($ra['id'] == $package_id){
        $match = 1;
        $name = $ra['name'];
        $description = $ra['description'];
    }
}
//if template id provided doesn't match existing id, throw alert
if($match == 0){
    $_SESSION['alert_message'] = 'this package does not exist';
    header ( 'location:./#alert' );
    exit;
}

//append copy to name
$name = $name." [copy]";

//add new copied entry into database
include "../spt_config/mysql_config.php";
mysql_query ( "INSERT INTO education (name, description) VALUES ('$name','$description')") or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//get new id
$r = mysql_query("SELECT MAX(id) FROM education") or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
$ra = mysql_fetch_array($r);

//get id of new template
$new_package_id = $ra[0];

//copy directory function
function copy_directory( $source, $destination ) {
    if ( is_dir( $source ) ) {
        @mkdir( $destination );
        $directory = dir( $source );
        while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
            if ( $readdirectory == '.' || $readdirectory == '..' ) {
                continue;
            }
            $PathDir = $source . '/' . $readdirectory; 
            if ( is_dir( $PathDir ) ) {
                copy_directory( $PathDir, $destination . '/' . $readdirectory );
                continue;
            }
            copy( $PathDir, $destination . '/' . $readdirectory );
        }
 
        $directory->close();
    }else {
        copy( $source, $destination );
    }
}
//initiate the copy of the directory
copy_directory($package_id,$new_package_id);

header ( 'location:./?id='.$new_package_id.'#update_package' );
exit;
?>