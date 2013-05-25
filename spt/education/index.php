<?php
/**
 * file:    index.php
 * version: 25.0
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
        <link rel="stylesheet" href="../includes/jquery-ui.css" type="text/css" />
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_education.css" type="text/css" />
        <!--[if IE]>
        <link rel="stylesheet" href="../includes/spt_ie.css" type="text/css" />
        <![endif]-->
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
        <script src="../includes/jquery.min.js"></script>
        <script src="../includes/jquery-ui.min.js"></script>
        <script>
            $(function() {
                $( "#tabs" ).tabs();
            });
        </script>
    </head>
    <body>
        <!--alert-->
        <?php include '../includes/alert.php'; ?>                 
        <div id="wrapper">
            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>                 
            <!--popovers-->
            <?php
                if ( isset ( $_REQUEST['editor'] ) && $_REQUEST['editor'] == 1 ) {
                    include "../includes/editor.php";
                }
                if(isset($_GET['add_package']) && $_GET['add_package'] == "true"){
                    echo '
                        <div id="add_package">
                            <div>
                                <form method="post" action="upload_package.php" enctype="multipart/form-data">
                                    <table id="add_package_table">
                                        <tr>
                                            <td colspan="3"><h3>Add Education Package</h3></td>
                                            <td>
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter the new package\'s name and description.  You also have the option to upload a zip file with your package\'s content or leave the upload field blank and a default package will be created for you that you you may then edit to your liking.</span></a>
                                            </td>  
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td colspan="2"><input name="name"';
                    if ( isset ( $_SESSION['temp_name'] ) ) {
                        echo "value=\"" . $_SESSION['temp_name'] . "\"";
                        unset ( $_SESSION['temp_name'] );
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td colspan="2"><textarea name="description" cols=50 rows=4 >';
                   if ( isset ( $_SESSION['temp_description'] ) ) {
                       echo $_SESSION['temp_description'];
                       unset ( $_SESSION['temp_description'] );
                   }
                    echo '
                                            </textarea></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="2"><input type="file"  name="file" /></td>
                                        </tr>';
                    echo '
                                        <tr>
                                            <td colspan="2" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>';
                }
                if(isset($_GET['update_package']) && $_GET['update_package'] == "true"){
                    if(isset($_REQUEST['id'])){
                        //retrieve template id
                        $package_id = $_REQUEST['id'];
                        //connect to database
                        include "../spt_config/mysql_config.php";
                        //query database for existing templates
                        $sql = "SELECT id FROM education";
                        $r = mysql_query($sql);
                        $match = 0;
                        while($ra = mysql_fetch_assoc($r)){
                            if($ra['id'] == $package_id){
                                $match = 1;
                            }
                        }
                        //if template id provided doesn't match existing id, throw alert
                        if($match == 0){
                            $_SESSION['alert_message'] = 'please select an existing package';
                            header ( 'location:./#alert' );
                            exit;
                        }
                        //if it does match then grab information on this id
                        $sql = "SELECT * FROM education WHERE id='$package_id'";
                        $r = mysql_query($sql);
                        while($ra = mysql_fetch_assoc($r)){
                            $package_name = $ra['name'];
                            $package_description = $ra['description'];
                        }
                    }
                    echo '
                        <div id="update_package">
                            <div>
                                <form method="post" action="update_package.php" enctype="multipart/form-data">
                                    <table id="package_details">
                                        <tr>
                                            <td colspan="2" style="text-align: left;"><h3>Package Details</h3></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Update the name and description of the education package or preview the package with the provided link.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name</strong></td>
                                            <td colspan="2" style="text-align: left;"><input name="name" size="50"';
                    if ( isset ( $_SESSION['temp_package_name'] ) ) {
                        echo "value=\"" . $_SESSION['temp_package_name'] . "\"";
                        unset ( $_SESSION['temp_package_name'] );
                    }else{
                        if(isset($_REQUEST['id'])){
                            echo "value=\"" . $package_name . "\"";    
                        }
                    }
                    echo '                                            
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Description</strong></td>
                                            <td colspan="2" style="text-align: left;"><textarea name="description" style="text-align:left;" cols=50 rows=10>';
                    if ( isset ( $_SESSION['temp_package_description'] ) ) {
                        echo $_SESSION['temp_package_description'];
                        unset ( $_SESSION['temp_package_description'] );
                    }else{
                        if(isset($_REQUEST['id'])){
                            echo $package_description;    
                        }
                    }
                    echo '
                                            </textarea></td>
                                        </tr>
                                       <td><strong>Website</strong></td>
                                            <td colspan="2" style="text-align: left;"><a href=';
                    if(isset($_REQUEST['id'])){ 
                        echo "\"".$package_id."\"";
                    }
                    echo '
                                            target="_blank">Click here for preview</a></td>
                                        </tr>';
                    echo '
                                            <input type="hidden" name="packageid" value=';
                    if(isset($_REQUEST['id'])){
                        echo "\"".$package_id."\"";
                    }
                    echo '
                                            />
                                        <tr>
                                            <td colspan="3" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>';
                }
            ?>    
            <!--content-->
            <div id="content">
                <br />
                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">Packages</a></li>
                    </ul>
                    <div id="tabs-1">
                        <a href="?add_package=true#tabs-1" id="add_package_button" class="popover_button" ><img src="../images/package_add_sm.png" alt="add" /> Package</a>
                        <table class="standard_table" >
                            <tr>
                                <td style="width:35%"><h3>Name</h3></td>
                                <td><h3>Description</h3></td>
                                <td style="width:15%"><h3>Actions</h3></td>
                            </tr>
                            <?php
                                //connect to database
                                include "../spt_config/mysql_config.php";

                                //pull in list of all templates
                                $r = mysql_query ( "SELECT * FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                                while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                    echo "
                                        <tr>
                                            <td style=\"vertical-align:text-top; text-align: left;\"><a href=\"?id=" . $ra['id'] . "&update_package=true\">" . $ra['name'] . "</a></td>\n
                                            <td style=\"vertical-align:text-top; text-align: left;\">" . $ra['description'] . "</td>\n
                                            <td><a href=\"?editor=1&type=education&id=" . $ra['id'] . "\"><img src=\"../images/pencil_sm.png\" /></a>&nbsp;&nbsp;<a href=\"copy_package.php?id=".$ra['id']."\"><img src=\"../images/page_copy_sm.png\" alt=\"copy\"/>&nbsp;&nbsp;<a href=\"export_package.php?education_id=".$ra['id']."\"><img src=\"../images/page_white_put_sm.png\" alt=\"download\" /></a>&nbsp;&nbsp;<a href=\"delete_package.php?t=" . $ra['id'] . "\"><img src=\"../images/package_delete_sm.png\" alt=\"delete\" /></a></td>\n
                                        </tr>\n";
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
