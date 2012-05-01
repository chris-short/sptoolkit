<?php

/**
 * file:    index.php
 * version: 13.0
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
?>

<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - education</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_education.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <!--popovers-->
            <form method="post" action="upload_package.php" enctype="multipart/form-data">
                <div id="add_package">
                    <div>
                        <table id="add_package_table">
                            <tr>
                                <td colspan="3"><h3>Add Education Package</h3></td>
                                <td>
                                    <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter the new package's name and description.  You also have the option to upload a zip file with your package's content or leave the upload field blank and a default package will be created for you that you you may then edit to your liking.</span></a>
                                </td>  
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td colspan="2"><input name="name" /></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td colspan="2"><textarea name="description" cols=50 rows=4></textarea></td>
                            </tr>
                            <tr>
                                <td><i>(optional)</i></td>
                                <td colspan="2"><input type="file"  name="file" /></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;"><br /><a href=""><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
<?php
//check to see if the alert session is set
if ( isset ( $_SESSION['alert_message'] ) ) {
    //create alert popover
    echo "<div id=\"alert\">";

    //echo the alert message
    echo "<div>" . $_SESSION['alert_message'] . "<br /><br /><a href=\"\"><img src=\"../images/accept.png\" alt=\"close\" /></a></div>";

    //unset the seession
    unset ( $_SESSION['alert_message'] );

    //close alert popover
    echo "</div>";
}
?>

            <!--sidebar-->
<?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <span class="button"><a href="#add_package"><img src="../images/package_add_sm.png" alt="add" /> Package</a></span>
                <table class="spt_table">
                    <tr>
                        <td style="text-align: left;"><h3>Name</h3></td>
                        <td style="text-align: left;"><h3>Description</h3></td>
                        <td><h3>Actions</h3></td>
                    </tr>

<?php
//connect to database
include "../spt_config/mysql_config.php";

//pull in list of all templates
$r = mysql_query ( "SELECT * FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    echo "
                    <tr>
                        <td style=\"vertical-align:text-top; text-align: left;\"><a href=\"" . $ra['id'] . "\" target=\"_blank\">" . $ra['name'] . "</a></td>\n
                        <td style=\"vertical-align:text-top; text-align: left;\">" . $ra['description'] . "</td>\n
                        <td><a href=\"?editor=1&type=education&id=".$ra['id']."\"><img src=\"../images/pencil_sm.png\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"delete_package.php?t=" . $ra['id'] . "\"><img src=\"../images/package_delete_sm.png\" alt=\"delete\" /></a></td>\n
                    </tr>\n";
}
?>
                </table>
            </div>
        </div>
    </body>
</html>
