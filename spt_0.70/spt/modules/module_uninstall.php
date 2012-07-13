<?php

/**
 * file:    module_uninstall.php
 * version: 7.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Module management
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

//pull in the passed module to be uninstalled
$module = $_REQUEST['m'];

//connect to database
include "../spt_config/mysql_config.php";


//pull in all module names and their path
$r = mysql_query ( "SELECT name, directory_name FROM modules WHERE core = 0" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {

    //validate that the module is not depended on
    $r2 = mysql_query ( "SELECT * FROM modules_dependencies WHERE depends_on = '$module'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    if ( mysql_num_rows ( $r2 ) > 0 ) {
        $_SESSION['alert_message'] = 'This module is depended on.  You must uninstall the module that depends on this Module first.';
        header ( 'location:./#alert' );
        exit;
    }

    //proceed with the uninstall
    if ( $ra['name'] == $module ) {
        //run the uninstall script if it exists
        if ( file_exists ( "../" . $ra['directory_name'] . "/uninstall.php" ) ) {
            include("../" . $ra['directory_name'] . "/uninstall.php");
        }

        //delete the directory
        $path = "../" . $ra['directory_name'];

        //function with loop to delete the entire directory
        function remove_dir ( $path ) {
            if ( is_dir ( $path ) ) {
                $objects = scandir ( $path );
                foreach ( $objects as $object ) {
                    if ( $object != "." && $object != ".." ) {
                        if ( filetype ( $path . "/" . $object ) == "dir" )
                            remove_dir ( $path . "/" . $object );
                        else
                            unlink ( $path . "/" . $object );
                    }
                }

                reset ( $objects );
                rmdir ( $path );
            }
        }

        //delete the directory
        remove_dir ( $path );

        //get the db name to prepare for table listing
        $db_name = $_SESSION['spt_db_name'];

        $tables = mysql_query("SHOW TABLES FROM $db_name" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

        while ( list($table) = mysql_fetch_row ( $tables ) ) {
            if ( stristr ( $module, $table ) ) {
                mysql_query ( "DROP TABLE $table" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
            }
        }

        //delete all dependency data
        mysql_query ( "DELETE FROM modules_dependencies WHERE module = '$module'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

        //delete module entry in modules table
        mysql_query ( "DELETE FROM modules WHERE name = '$module'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

        //if the uninstall went well send them back
        $_SESSION['alert_message'] = 'uninstall successful';
        header ( 'location:./#alert' );
        exit;
    }
}
//if the uninstall did not happen, send them back with an alert
$_SESSION['alert_message'] = "you must only uninstall valid, non-core modules";
header ( 'location:./#alert' );
exit;
?>