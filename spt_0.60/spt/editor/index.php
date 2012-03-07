<?php

/**
 * file:    index.php
 * version: 10.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Editor
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

//turn off PHP error reporting, some platforms report error on form post url, but post url is correct
error_reporting ( 0 );

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
        <title>spt - editor</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_editor.css" type="text/css" />
        <!--script-->
        <script language="Javascript" type="text/javascript">
            function selectTemplate(template_id,file) 
            { 
                //re-direct
                window.location = ".?t="+template_id+"&f="+file;								
            }
        </script>
        <script language="Javascript" type="text/javascript">
            function selectPackage(package_id,file) 
            { 
                //re-direct
                window.location = ".?p="+package_id+"&f="+file;								
            }
        </script>
        <script type="text/javascript" src="../includes/escape.js"></script>
    </head>
    <body>
<?php
//check to see if the alert session is set
if ( isset ( $_SESSION['alert_message'] ) ) {
    //create alert popover
    echo "<div id=\"alert\">";

    //echo the alert message
    echo "<div>" . $_SESSION['alert_message'] . "<br />";

    //close the alert message
    echo "<br /><a href=\"\"><img src=\"../images/accept.png\" alt=\"close\" /></a></div>";

    //close alert popover
    echo "</div>";

    //unset the seession
    unset ( $_SESSION['alert_message'] );
}
?>
        <div id="wrapper">
            <!--sidebar-->
        <?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <form id="editor_form" method="post" action="file_update.php?<?php if ( isset ( $_REQUEST['t'] ) ) {
            echo "t=" . $_REQUEST['t'] . "&f=" . $_REQUEST['f'];
        }if ( isset ( $_REQUEST['p'] ) ) {
            echo "p=" . $_REQUEST['p'] . "&f=" . $_REQUEST['f'];
        } ?>">
                    <?php
                    if ( ! isset ( $_REQUEST['t'] ) && ! isset ( $_REQUEST['p'] ) ) {
                        //connect to database
                        include "../spt_config/mysql_config.php";

                        //pull in all the template id's and names
                        $r = mysql_query ( "SELECT id,name FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        ;

                        //error if there are no templates
                        if ( mysql_num_rows ( $r ) == 0 ) {
                            echo "
                <table id=\"editor_buttons\"></table>
                <div id=\"template_select\">\n\t
                    <div>
                        <span><h1>Templates:</h1></span>\n
                        <table id=\"template_list\">\n\t
                            <tr>
                                <td>There are no templates to edit.</td>
                            </tr>
                        </table>
                    </div>
                </div>";
                        } else {
                            echo "
                <table id=\"editor_buttons\"></table>
                <div id=\"template_select\">\n\t
                    <div>
                        <span><h1>Templates:</h1></span>\n
                        <table id=\"template_list\">\n\t";
                            while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                //start template row
                                echo "<tr><td>" . $ra['name'] . "</td><td><select onchange=\"selectTemplate(" . $ra['id'] . ",this.value)\"><option value=\"\">select file...</option>";

                                //query template directory for files
                                $files = scandir ( '../templates/' . $ra['id'] . '/' );

                                //do this process for each item
                                foreach ( $files as $file ) {
                                    //determine if the item is a directory or file
                                    if ( ! is_dir ( $file ) ) {
                                        //break apart the filename
                                        $file_array = explode ( ".", $file );

                                        //look for htm, php and html files
                                        if ( $file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "css" OR $file_array[1] == "js" ) {
                                            if ( $file == "license.htm" ) {
                                                
                                            } else {
                                                //add the file to the drop-down
                                                echo "<option value=\"" . $file . "\">" . $file . "</option>";
                                            }
                                        }
                                    }
                                }

                                //finish the template row
                                echo "</select></td></tr>\n";
                            }
                            echo "
                        </table>
                </div>
        </div>";
                        }

                        //pull in all the package id's and names
                        $r = mysql_query ( "SELECT id,name FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        ;

                        //error if there are no templates
                        if ( mysql_num_rows ( $r ) == 0 ) {
                            echo "
                        <table id=\"editor_buttons\"></table>
                        <div id=\"template_select\">\n\t
                            <div>
                                <span><h1>Education Packages:</h1></span>\n
                                <table id=\"template_list\">\n\t
                                    <tr>
                                        <td>There are no education packages to edit.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>";
                            exit;
                        } else {
                            echo "
                        <table id=\"editor_buttons\"></table>
                        <div id=\"template_select\">\n\t
                            <div>
                                <span><h1>Education Packages:</h1></span>\n
                                <table id=\"template_list\">\n\t";
                            while ( $ra = mysql_fetch_assoc ( $r ) ) {
                                //start template row
                                echo "<tr><td>" . $ra['name'] . "</td><td><select onchange=\"selectPackage(" . $ra['id'] . ",this.value)\"><option value=\"\">select file...</option>";

                                //query template directory for files
                                $files = scandir ( '../education/' . $ra['id'] . '/' );

                                //do this process for each item
                                foreach ( $files as $file ) {
                                    //determine if the item is a directory or file
                                    if ( ! is_dir ( $file ) ) {
                                        //break apart the filename
                                        $file_array = explode ( ".", $file );

                                        //look for htm, php and html files
                                        if ( $file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "css" OR $file_array[1] == "js" ) {
                                            if ( $file == "license.htm" ) {
                                                
                                            } else {
                                                //add the file to the drop-down
                                                echo "<option value=\"" . $file . "\">" . $file . "</option>";
                                            }
                                        }
                                    }
                                }

                                //finish the template row
                                echo "</select></td></tr>\n";
                            }
                            echo "
                        </table>
                </div>
        </div>";
                            exit;
                        }
                    } else if ( ! isset ( $_REQUEST['f'] ) ) {
                        //send them back to select a template and file
                        $_SESSION['alert_message'] = "Please select a file first.";
                        header ( 'location:./#alert' );
                        exit;
                    }

                    //validate the template id
                    if ( preg_match ( '/[^0-9]/', $_REQUEST['t'] ) ) {
                        $_SESSION['alert_message'] = "Please select a valid template.";
                        header ( 'location:./#alert' );
                        exit;
                    }

                    //validate the template id
                    if ( preg_match ( '/[^0-9]/', $_REQUEST['p'] ) ) {
                        $_SESSION['alert_message'] = "Please select a valid package.";
                        header ( 'location:./#alert' );
                        exit;
                    }


                    //validate the filename
                    $filename_array = explode ( '.', $_REQUEST['f'] );
                    $count = 0;
                    foreach ( $filename_array as $file_part ) {
                        $count ++;
                    }

                    if ( $count > 2 ) {
                        $_SESSION['alert_message'] = "That filename is invalid.";
                        header ( 'location:./#alert' );
                        exit;
                    }

                    //validate that only a template or a package is specified
                    if ( isset ( $_REQUEST['t'] ) && isset ( $_REQUEST['p'] ) ) {
                        $_SESSION['alert_message'] = "Please specify only a template or a package...not both.";
                        header ( 'location:./#alert' );
                        exit;
                    }

                    //pull in data
                    if ( isset ( $_REQUEST['t'] ) ) {
                        $data = file_get_contents ( "../templates/" . $_REQUEST['t'] . "/" . $_REQUEST['f'] );
                    }

                    if ( isset ( $_REQUEST['p'] ) ) {
                        $data = file_get_contents ( "../education/" . $_REQUEST['p'] . "/" . $_REQUEST['f'] );
                    }


                    //show buttons
                    echo "
                    <table id=\"editor_buttons\">
                        <tr>
                            <td>
                                <a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>
                                <input type=\"image\" src=\"../images/accept.png\" alt=\"save\" />	
                            </td>
                        </tr>
                    </table>";

                    //write data into textarea
                    echo "<textarea id=\"editor\" name=\"file\">" . $data . "</textarea>";
                    ?>
                </form>
            </div>
        </div>	
    </body>
</html>
