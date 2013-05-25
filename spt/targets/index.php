<?php
/**
 * file:    index.php
 * version: 42.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Target management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:	GNU/GPL, see license.htm.
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
        <title>spt - targets</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/jquery-ui.css" type="text/css" />
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_targets.css" type="text/css" />
        <!--scripts-->
        <script language="Javascript" type="text/javascript">
            function updateCustom(custom,value) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //send update request
                xmlhttp.open("POST","custom_update.php",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("c="+custom+"&n="+value);					
            }
        </script>
        <script language="Javascript" type="text/javascript">
            function updateTarget(id,column,data) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //send update request
                xmlhttp.open("POST","target_update.php",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("id="+id+"&column="+column+"&data="+data);
					
            }
        </script>
        <script language="Javascript" type="text/javascript">
            function updateMetrics(field_name) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //get checked status
                var check = document.getElementById("checkbox_"+field_name).checked;

                //send update request
                xmlhttp.open("POST","update_metrics.php",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("field_name="+field_name+"&shown="+check);

                if (xmlhttp.responseText != "set")
                {
                    window.location = ".#alert"
                    window.location.reload()
                }								
            }
        </script>
        <script language="Javascript" type="text/javascript">
            function updateMetricsName(field_name,value) 
            { 
                //begin new request
                xmlhttp = new XMLHttpRequest();

                //send update request
                xmlhttp.open("POST","update_metrics_name.php",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("field_name="+field_name+"&value="+value);

                if (xmlhttp.responseText != "set")
                {
                    window.location = ".#alert"
                    window.location.reload()
                }                               
            }
        </script>
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
                if(isset($_GET['add_one']) && $_GET['add_one'] == "true"){
                    echo '
                        <div id="add_one">
                            <div>
                                <form action="target_upload_single.php" method="post" enctype="multipart/form-data">
                                    <table id="add_single">
                                        <tr>
                                            <td colspan="2"><h3>Add Single Target</h3></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter the target\'s name, valid email address and then select an existing or new group to add the new target to.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>First Name</td>
                                            <td colspan="2"><input type="text" name="fname" ';
                    if ( isset ( $_SESSION['temp_fname'] ) ) {
                        echo "value=\"" . $_SESSION['temp_fname'] . "\" ";
                        unset ( $_SESSION['temp_fname'] );
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Last Name</td>
                                            <td colspan="2"><input type="text" name="lname"';
                    if ( isset ( $_SESSION['temp_lname'] ) ) {
                        echo "value=\"" . $_SESSION['temp_lname'] . "\" ";
                        unset ( $_SESSION['temp_lname'] );
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td colspan="2"><input type="text" name="email"';
                    if ( isset ( $_SESSION['temp_email'] ) ) {
                        echo "value=\"" . $_SESSION['temp_email'] . "\" ";
                        unset ( $_SESSION['temp_email'] );
                    }
                    echo '
                                            /></td>
                                        </tr>
                                        <tr>
                                            <td>Existing Group</td>
                                            <td colspan="2">
                                                <select name="group_name">
                                                    <option value="Select an Existing Group...">Select an Existing Group...</option>';
                    //connect to database
                    include "../spt_config/mysql_config.php";

                    //pull in current group names
                    $r = mysql_query ( "SELECT DISTINCT group_name FROM targets ORDER BY group_name" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "<option value=\"" . $ra['group_name'] . "";
                        echo "\" ";
                        if(isset($_SESSION['temp_group_name']) && $_SESSION['temp_group_name'] == $ra['group_name']){
                            echo "\" SELECTED";
                            unset($_SESSION['temp_group_name']);
                        }
                        echo ">" . $ra['group_name'] . "</option>";
                    }
                    echo '
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">OR</td>
                                        </tr>
                                        <tr>
                                            <td>New Group</td>
                                            <td colspan="2"> 
                                            <input type="text" name="group_name_new"';
                    if ( isset ( $_SESSION['temp_group_name_new'] ) ) {
                        echo "value=\"" . $_SESSION['temp_group_name_new'] . "\" ";
                        unset ( $_SESSION['temp_group_name_new'] );
                    }
                    echo '
                                            />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><h3>Metrics (Optional)</h3></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Enter values for any custom metrics you have added.</span></a>
                                            </td>

                                        </tr>';
                    //query for all metrics
                    $r = mysql_query ( "SELECT * FROM targets_metrics" );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "
                                        <tr>
                                            <td>" . $ra['field_name'] . "</td>
                                            <td><input type=\"text\" name=\"" . $ra['field_name'] . "\" /></td>
                                        </tr>";
                    }
                    echo '
                                        <tr>
                                            <td colspan="3" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>';
                }
                if(isset($_GET['add_many']) && $_GET['add_many'] == "true"){
                    echo '
                        <div id="add_many">
                            <div>
                                <form action="target_upload_batch.php" method="post" enctype="multipart/form-data">
                                    <table id="add_bunches">
                                        <tr>
                                            <td style="text-align: left;"><h3>Import Targets From CSV</h3></td>
                                            <td style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Upload a CSV file with a header row that contains a column for the required columns (fname, lname email, group) as well as any additional attributes you have added.  If you do not match the current set of column headings, the upload will fail.<br /><br />Export the current list by clicking on the export button, even if you have no targets it will make a good template.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input type="file"  name="file" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/cancel.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="../images/accept.png" alt="accept" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>';
                }
                if(isset($_GET['metrics']) && $_GET['metrics'] == "true"){
                    echo '
                        <div id="metrics">
                            <div>
                                <form action="add_metric.php" method="post" enctype="multipart/form-data">
                                    <table id="add_metric">
                                        <tr>
                                            <td style="text-align: left;"><h3>Manage Target Metrics</h3></td>
                                            <td colspan="2" style="text-align: right;">
                                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>Adding a metric will create a new column in the database for tracking target metrics.<br /><br />Check the box next to 5 metrics and those will be the 5 that are displayed in the group list pop-over.</span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Add Metric</td>
                                            <td><input type="text" name="metric" /></td>
                                            <td><input type="image" src="../images/add_sm.png" alt="add" /></td>
                                        </tr>
                                    </table>
                                </form>
                                <table id="manage_metrics">
                                    <tr>
                                        <td><h3>Show</h3></td>
                                        <td><h3>Metric</h3></td>
                                        <td><h3>Delete</h3></td>
                                    </tr>
                                    <tr>
                                        <td>-</td>
                                        <td>First Name</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>-</td>
                                        <td>Last Name</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>-</td>
                                        <td>Email</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>-</td>
                                        <td>Group</td>
                                        <td>-</td>
                                    </tr>';
                    //connect to database
                    include "../spt_config/mysql_config.php";
                    //query for all metrics
                    $r = mysql_query ( "SELECT * FROM targets_metrics" );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "
                                    <tr>
                                        <td><input id=\"checkbox_" . $ra['field_name'] . "\"type=\"checkbox\" name=\"" . $ra['field_name'] . "\" onclick=\"updateMetrics('" . $ra['field_name'] . "')\" value=\"" . $ra['field_name'] . "\"";
                        if ( $ra['shown'] == 1 ) {
                            echo "checked";
                        }
                        echo "
                                        ></td>
                                        <td><input id=\"metric_field_" . $ra['field_name'] . "\" onchange=\"updateMetricsName('" . $ra['field_name'] . "',this.value)\" value=\"" . $ra['field_name'] . "\" /></td>
                                        <td><a href=\"delete_metric.php?m=" . $ra['field_name'] . "\"><img src=\"../images/table_delete_sm.png\" alt=\"delete\" /></a></td>
                                    </tr>";
                    }
                    echo '
                                    <tr>
                                        <td colspan="3" style="text-align: center;"><br /><a href=".#tabs-1"><img src="../images/accept.png" alt="accept" /></a></td>
                                    </tr>
                                </table>
                            </div>
                        </div>';                    
                }
                if(isset($_GET['group_list']) && $_GET['group_list']){
                    echo '
                        <div  id="group_list">
                            <div>
                                <table id="group_list_header">
                                    <tr>
                                        <td class="left">
                                            <h1>';
                    if ( isset ( $_REQUEST['g'] ) ) {
                        echo filter_var ( $_REQUEST['g'], FILTER_SANITIZE_STRING );
                    } else {
                        echo "All Targets";
                    }
                    echo '
                                            </h1>
                                        </td>
                                        <td style="text-align: right;">
                                            <a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>You can easily edit any cell by clicking on it and making your changes.  Changes are automatically saved when you click anywhere <strong>outside</strong> of the cell just edited.</span></a>
                                            &nbsp;&nbsp;&nbsp;
                                            <a href=".#tabs-1"><img src="../images/cancel.png" alt="close" /></a>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table id="group_user_list">
                                    <tr>
                                        <td><h3>First Name</h3></td>
                                        <td><h3>Last Name</h3></td>
                                        <td><h3>Email</h3></td>
                                        <td><h3>Group</h3></td>';

                    //get the list of columns that should be shown
                    $r = mysql_query ( "SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC" );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "<td><h3>" . $ra['field_name'] . "</h3></td>";
                    }
                    echo '
                                        <td></td>
                                    </tr>
                                    <tr>
                                    <form action="target_upload_single.php" method="post" enctype="multipart/form-data">
                                        <td class="target_cell"><input type="text" name="fname" class="invisible_input" /></td>
                                        <td class="target_cell"><input type="text" name="lname" class="invisible_input" /></td>
                                        <td class="target_cell"><input type="text" name="email" class="invisible_input" /></td>
                                        <td class="target_cell"><input type="text" name="group_name_new"';
                    if ( isset ( $_REQUEST['g'] ) ) {
                        echo "value=\"" . filter_var ( $_REQUEST['g'], FILTER_SANITIZE_STRING ) . "\"";
                    }
                    echo '
                                         class="invisible_input" /></td>';
                    //get the list of columns that should be shown
                    $r = mysql_query ( "SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC" );
                    while ( $ra = mysql_fetch_assoc ( $r ) ) {
                        echo "<td class=\"target_cell\"><input type=\"text\" name=\"" . $ra['field_name'] . "\"class=\"invisible_input\"  /></td>";
                    }
                    echo '
                                        <td class="submit_cell"><input type="image" src="../images/add_sm.png" alt="add" class="invisible_input" /></td>
                                        <input type="hidden" name="group_list"';
                    if ( isset ( $_REQUEST['g'] ) ) {
                        echo "value=\"" . filter_var ( $_REQUEST['g'], FILTER_SANITIZE_STRING ) . "\"";
                    }
                    echo '
                                        />
                                    </form>
                                    </tr>';
                    //connect to database
                    include "../spt_config/mysql_config.php";
                    if ( isset ( $_REQUEST['g'] ) ) {
                        $group = filter_var ( $_REQUEST['g'], FILTER_SANITIZE_STRING );                
                        //ensure the group name is under 50 characters
                        if ( strlen ( $group ) > 50 ) {
                            $_SESSION['alert_message'] = "group names cannot be over 50 characters";
                            header ( "location:./#alert" );
                            exit;
                        }
                        //ensure that the group name exists in the database
                        $r = mysql_query ( "SELECT DISTINCT group_name FROM targets" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            if ( $ra['group_name'] == $group ) {
                                $match = 1;
                            }
                        }
                        if ( ! isset ( $match ) ) {
                            $_SESSION['alert_message'] = "this group does not exist";
                            header ( "location:./#alert" );
                            exit;
                        }
                        //query for a list of group members ordered alphabetically
                        $r = mysql_query ( "SELECT * FROM targets WHERE group_name = '$group' ORDER BY fname" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            //build a row for each member of the group wrapped in a form that will dynamically edit each entry as changes are made
                            echo "
                                    <tr>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_fname\" onchange=\"updateTarget(" . $ra['id'] . ",'fname',this.value)\" type=\"text\" value=\"" . $ra['fname'] . "\" class=\"invisible_input\"/></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_lname\" onchange=\"updateTarget(" . $ra['id'] . ",'lname',this.value)\" type=\"text\" value=\"" . $ra['lname'] . "\" class=\"invisible_input\"/></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_email\" onchange=\"updateTarget(" . $ra['id'] . ",'email',this.value)\" type=\"text\" value=\"" . $ra['email'] . "\" class=\"invisible_input\" /></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_group\" onchange=\"updateTarget(" . $ra['id'] . ",'group_name',this.value)\" type=\"text\" value=\"" . $ra['group_name'] . "\" class=\"invisible_input\" /></td>\n";

                            //get the list of columns that should be shown
                            $r2 = mysql_query ( "SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC" );
                            while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {
                                $field_name = $ra2['field_name'];

                                echo "
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_" . $ra2['field_name'] . "\" onchange=\"updateTarget(" . $ra['id'] . ",'" . $ra2['field_name'] . "',this.value)\" type=\"text\" value=\"" . $ra[$field_name] . "\" class=\"invisible_input\" /></td>\n";
                            }

                            echo "
                                        <td><a href=\"target_delete.php?g=" . $ra['group_name'] . "&u=" . $ra['id'] . "\"><img src=\"../images/user_delete_sm.png\" alt=\"delete\" /></a></td>\n
                                    </tr>";
                        }
                    } else {
                        //query for a list of group members ordered alphabetically
                        $r = mysql_query ( "SELECT * FROM targets" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            //build a row for each member of the group wrapped in a form that will dynamically edit each entry as changes are made
                            echo "
                                    <tr>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_fname\" onchange=\"updateTarget(" . $ra['id'] . ",'fname',this.value)\" type=\"text\" value=\"" . $ra['fname'] . "\" class=\"invisible_input\"/></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_lname\" onchange=\"updateTarget(" . $ra['id'] . ",'lname',this.value)\" type=\"text\" value=\"" . $ra['lname'] . "\" class=\"invisible_input\"/></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_email\" onchange=\"updateTarget(" . $ra['id'] . ",'email',this.value)\" type=\"text\" value=\"" . $ra['email'] . "\" class=\"invisible_input\" /></td>\n
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_group\" onchange=\"updateTarget(" . $ra['id'] . ",'group_name',this.value)\" type=\"text\" value=\"" . $ra['group_name'] . "\" class=\"invisible_input\" /></td>\n";

                            //get the list of columns that should be shown
                            $r2 = mysql_query ( "SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC" );
                            while ( $ra2 = mysql_fetch_assoc ( $r2 ) ) {

                                $field_name = $ra2['field_name'];

                                echo "
                                        <td class=\"target_cell\"><input id=\"" . $ra['id'] . "_" . $ra2['field_name'] . "\" onchange=\"updateTarget(" . $ra['id'] . ",'" . $ra2['field_name'] . "',this.value)\" type=\"text\" value=\"" . $ra[$field_name] . "\" class=\"invisible_input\" /></td>\n";
                            }

                            echo "
                                        <td><a href=\"target_delete.php?g=" . $ra['group_name'] . "&u=" . $ra['id'] . "\"><img src=\"../images/user_delete_sm.png\" alt=\"delete\" /></a></td>\n
                                    </tr>";
                        }
                    }
                    echo '
                                </table>
                            </div>
                        </div>';
                }
            ?>        
            <!--content-new-->
            <div id="content">
                <br />
                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">Local Targets</a></li>
                        <li><a href="#tabs-2">LDAP Targets</a></li>
                    </ul>
                    <div id="tabs-1">
                        <a href="?metrics=true#tabs-1" id="metrics_button" class="popover_button" ><img src="../images/table_edit_sm.png" alt="metrics" /> Metrics</a>
                        <a href="?add_one=true#tabs-1" id="add_one_button" class="popover_button" ><img src="../images/user_add_sm.png" alt="add" /> One</a>
                        <a href="target_export.php" id="target_export_button" class="popover_button" ><img src="../images/page_white_put_sm.png" alt="template" /> Export</a>
                        <a href="?add_many=true#tabs-1" id="add_many_button" class="popover_button" ><img src="../images/user_add_sm.png" alt="add" /> Import</a>
                        <table class="standard_table" >
                            <tr>
                                <td><h3>Group Name</h3></td>
                                <td><h3>Quantity</h3></td>
                                <td><h3>Delete</h3></td>
                            </tr>
                            <tr>
                                <td><a href="?group_list=true#tabs-1"><strong>All Targets</strong></a></td>
                    <?php
                        //connect to database
                        include "../spt_config/mysql_config.php";

                        //query for total count of targets
                        $r = mysql_query ( "SELECT COUNT(id) AS target_count FROM targets" );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            echo "<td>" . $ra['target_count'] . "</td>";
                        }
                        echo '
                                <td></td>
                            </tr>
                        ';
                        //connect to database
                        include "../spt_config/mysql_config.php";

                        //query for a list of groups ordered alphabetically
                        $r = mysql_query ( "SELECT DISTINCT group_name FROM targets ORDER BY group_name" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                        while ( $ra = mysql_fetch_assoc ( $r ) ) {
                            echo "<tr>";
                            echo "<td><a href=\"?group_list=true&g=" . $ra['group_name'] . "#tabs-1\">" . $ra['group_name'] . "</a></td>";
                            $group_name = filter_var ( $ra['group_name'], FILTER_SANITIZE_STRING );
                            $r1 = mysql_query ( "SELECT COUNT(group_name) FROM targets WHERE group_name = '$group_name'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
                            while ( $ra1 = mysql_fetch_assoc ( $r1 ) ) {
                                echo "<td>" . $ra1['COUNT(group_name)'] . "</td>";
                            }
                            echo "<td><a href=\"group_delete.php?g=" . $ra['group_name'] . "\"><img src=\"../images/group_delete_sm.png\" alt=\"delete\" /></a></td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
            <div id="tabs-2">
                <table class="standard_table" >
                    <div style="width:100%;margin:auto;text-align:center;"><br /><br />Coming Soon...</div>
                </table>
            </div>
        </div>	
    </body>
</html>