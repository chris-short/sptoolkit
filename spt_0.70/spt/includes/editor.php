<?php

/**
 * file:    editor.php
 * version: 18.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Core Files
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
//determine if data is posted
if ( $_POST ) {
    // verify user is an admin
    $includeContent = "../includes/is_admin.php";
    if ( file_exists ( $includeContent ) ) {
        require_once $includeContent;
    } else {
        echo "stop";
        exit;
    }
    //validate that the type is specified
    if ( ! isset ( $_POST['type'] ) ) {
        $_SESSION['alert_message'] = "please specify a type when saving content from the editor";
        header ( 'location:.#alert' );
        exit;
    }
    //check to see if type is template
    if ( isset ( $_POST['type'] ) && $_POST['type'] == "templates" ) {
        //validate that the subtype is set
        if ( ! isset ( $_POST['subtype'] ) OR ($_POST['subtype'] != "full" && $_POST['subtype'] != "email" && $_POST['subtype'] != "text") ) {
            $_SESSION['alert_message'] = "please specify a subtype";
            header ( 'location:.#alert' );
            exit;
        }
        //get the template id
        if ( ! isset ( $_POST['id'] ) ) {
            $_SESSION['alert_message'] = "please specify a template id";
            header ( 'location:.#alert' );
            exit;
        } else {
            $id = filter_var ( $_POST['id'], FILTER_SANITIZE_NUMBER_INT );
        }
        //if the subtype is email
        if ( $_POST['subtype'] == "email" ) {
            //validate all the appropriate field are set and
            if ( ! isset ( $_POST['sender_friendly'] ) OR ! isset ( $_POST['sender_email'] ) OR ! isset ( $_POST['reply_to'] ) OR ! isset ( $_POST['subject'] ) OR ! isset ( $_POST['fake_link'] ) OR ! isset ( $_POST['code'] ) ) {
                $_SESSION['alert_message'] = "please enter a value into all fields for the email";
                header ( 'location:.#alert' );
                exit;
            }
            //get the email.php file for this template
            $file = file_get_contents ( $id . "/email.php" );
            //validate sender friendly name and then write to file
            if ( $_POST['sender_friendly'] ) {
                $sender_friendly = filter_var ( $_POST['sender_friendly'], FILTER_SANITIZE_MAGIC_QUOTES );
                //write the changes to the file
                $file = preg_replace ( '#\$sender_friendly\s=\s[\'|\"](.*?)[\'|\"];#', '\$sender_friendly = "' . $sender_friendly . '";', $file, $limit = 1 );
            }
            //validate the sender email and then write to file
            if ( $_POST['sender_email'] ) {
                $sender_email = filter_var ( $_POST['sender_email'], FILTER_SANITIZE_EMAIL );
                //write the changes to the file
                $file = preg_replace ( '#\$sender_email\s=\s[\'|\"](.*?)[\'|\"];#', '\$sender_email = "' . $sender_email . '";', $file );
            }
            //sanitize reply to address and then write to file
            if ( $_POST['reply_to'] ) {
                $reply_to = filter_var ( $_POST['reply_to'], FILTER_SANITIZE_EMAIL );
                //write the changes to the file
                $file = preg_replace ( '#\$reply_to\s=\s[\'|\"](.*?)[\'|\"];#', '\$reply_to = "' . $reply_to . '";', $file );
            }
            //santize subject line then write it to file
            if ( $_POST['subject'] ) {
                $subject = filter_var ( $_POST['subject'], FILTER_SANITIZE_STRING );
                //write the changes to the file
                $file = preg_replace ( '#\$subject\s=\s[\'|\"](.*?)[\'|\"];#', '\$subject = "' . $subject . '";', $file );
            }
            //sanitize fake link and then write to file
            if ( $_POST['fake_link'] ) {
                $fake_link = filter_var ( $_POST['fake_link'], FILTER_SANITIZE_STRING );
                //write the changes to the file
                $file = preg_replace ( '#\$fake_link\s=\s[\'|\"](.*?)[\'|\"];#', '\$fake_link = "' . $fake_link . '";', $file );
            }
            //sanitize message and then write to file
            if ( $_POST['code'] ) {
                $message = str_replace ( "\n", "", $_POST['code'] );
                $message = preg_replace ( "!" . '\x24' . "!", '\\\$', $message );
                //write the changes to the file
                $file = preg_replace ( '#\$message\s=\s\'(.*?)\';#', '\$message = \'' . $message . '\';', $file );
            }
            //write the file back
            file_put_contents ( $id . "/email.php", $file );
            //direct people back to where they came from with success alert message
            $_SESSION['alert_message'] = "changes saved";
            header ( 'location:.?editor=1&type=templates&id=' . $id );
            exit;
        }
        //if the subtype is full
        if ( $_POST['subtype'] == "full" ) {
            //validate that all the apropriate fields are completed
            if ( ! isset ( $_POST['code'] ) ) {
                $_SESSION['alert_message'] = "you must enter something";
                header ( 'location:.#alert' );
                exit;
            }
            //set filename
            $filename = $_REQUEST['filename'];
            //get the contents of the file
            $file = file_get_contents ( $id . "/" . $filename );
            //sanitize message and then write to file
            if ( $_POST['code'] ) {
                $code = str_replace ( "\n", "", $_POST['code'] );
                if($filename == "email.php"){
                    preg_match ( '#\$message\s=\s\'(.*?)\';#', $code, $matches );
                    $message = preg_replace ( "!" . '\x24' . "!", '\\\$', $matches[1] );
                    $code = preg_replace ( '#\$message\s=\s\'(.*?)\';#', $message, $code );
                    //write the changes to the file
                    $file = preg_replace ( '#\$message\s=\s[\'|\"](.*?)[\'|\"];#', '\$message = \'' . $message . '\';', $code );
                }
                else{
                    $file = $code;
                }
            }
            //write the file back
            file_put_contents ( $id . "/" . $filename, $file );
            //direct people back to where they came from with success alert message
            $_SESSION['alert_message'] = "changes saved";
            header ( 'location:.?editor=1&type=templates&id=' . $id . '&web=1&filename=' . $filename );
            exit;
        }
        //if the subtype is text
        if ( $_POST['subtype'] == "text" ) {
            //validate that all the apropriate fields are completed
            if ( ! isset ( $_POST['code'] ) ) {
                $_SESSION['alert_message'] = "you must enter something";
                header ( 'location:.#alert' );
                exit;
            }
            //set filename
            $filename = $_REQUEST['filename'];
            //get the contents of the file
            $file = file_get_contents ( $id . "/" . $filename );
            //sanitize message and then write to file
            if ( $_POST['code'] ) {
                $code = str_replace ( "\n", "", $_POST['code'] );
                $file = $code;
            }
            //write the file back
            file_put_contents ( $id . "/" . $filename, $file );
            //direct people back to where they came from with success alert message
            $_SESSION['alert_message'] = "changes saved";
            header ( 'location:.?editor=1&type=templates&id=' . $id . '&text=1&filename=' . $filename );
            exit;
        }

    }
    //check to see if type is education     
    if ( isset ( $_POST['type'] ) && $_POST['type'] == "education" ) {
        //get the template id
        if ( ! isset ( $_POST['id'] ) ) {
            $_SESSION['alert_message'] = "please specify an education id";
            header ( 'location:.#alert' );
            exit;
        } else {
            $id = filter_var ( $_POST['id'], FILTER_SANITIZE_NUMBER_INT );
        }
        //validate that all the apropriate fields are completed
        if ( ! isset ( $_POST['code'] ) ) {
            $_SESSION['alert_message'] = "you must enter something";
            header ( 'location:.#alert' );
            exit;
        }
        //set filename
        $filename = $_REQUEST['filename'];
        //get the contents of the file
        $file = file_get_contents ( $id . "/" . $filename );
        //sanitize message and then write to file
        if ( $_POST['code'] ) {
            $code = str_replace ( "\n", "", $_POST['code'] );
            $file = $code;
        }
        //write the file back
        file_put_contents ( $id . "/" . $filename, $file );
        //direct people back to where they came from with success alert message
        $_SESSION['alert_message'] = "changes saved";
        if(isset($_REQUEST['text'])){
            header('location:?editor=1&type=education&id='.$id.'&text=1&filename='.$filename );
            exit;
        }else{
            header ( 'location:.?editor=1&type=education&id=' . $id . '&filename=' . $filename );
            exit;
        }
    }
}
//start the editor wrapper
echo "
    <div id=\"editor_wrapper\">
        <div id=\"editor\">";
//validate the editor parameter
if ( ! isset ( $_REQUEST['editor'] ) OR $_REQUEST['editor'] != 1 ) {
    $_SESSION['alert_message'] = "please enter valid parameters when using the editor";
    header ( 'location:.#alert' );
    exit;
}
//validate the type parameter
if ( ! isset ( $_REQUEST['type'] ) OR ! preg_match ( '#(templates|education)#', $_REQUEST['type'] ) ) {
    $_SESSION['alert_message'] = "you must specify a valid type";
    header ( 'location:.#alert' );
    exit;
}
//validate that an id is provided
if ( ! isset ( $_REQUEST['id'] ) ) {
    $_SESSION['alert_message'] = "please provide a valid id";
    header ( 'location:.#alert' );
    exit;
}
//connect to database
include '../spt_config/mysql_config.php';
//validate the id parameter based on the type parameter
if ( $_REQUEST['type'] == "templates" ) {
    //get template id and sanitize
    $id = filter_var ( $_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT );
    //query database for template id
    $r = mysql_query ( "SELECT id FROM templates WHERE id = '$id'" );
    //alert if template id doesn't match an existing template id
    if ( mysql_num_rows ( $r ) < 1 ) {
        $_SESSION['alert_message'] = "please select a valid template id";
        header ( 'location:.#alert' );
        exit;
    }
}
if ( $_REQUEST['type'] == "education" ) {
    //get education id and sanitize
    $id = filter_var ( $_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT );
    //query database for education id
    $r = mysql_query ( "SELECT id FROM education WHERE id = '$id'" );
    //alert if education id doesn't match an existing education id
    if ( mysql_num_rows ( $r ) < 1 ) {
        $_SESSION['alert_message'] = "please select a valid education id";
        header ( 'location:.#alert' );
        exit;
    }
}
//if website is specified ensure its set to 1
if ( isset ( $_REQUEST['web'] ) && $_REQUEST['web'] != 1 ) {
    $_SESSION['alert_message'] = "please provide a valid value for the web parameter";
    header ( 'location:.#alert' );
    exit;
}
//validate the filename
if ( isset ( $_REQUEST['filename'] ) ) {
    //determine whether this is education or templates
    $type = $_REQUEST['type'];
    //query template directory for files
    $files = scandir ( '../'.$type.'/' . $id . '/' );
    //do this process for each item
    foreach ( $files as $file ) {
        //determine if the item is a directory or file
        if ( ! is_dir ( $file ) ) {
            //look for match
            if ( $file == $_REQUEST['filename'] ) {
                $match = 1;
            }
        }
    }
    //send error if there is no match
    if ( ! isset ( $match ) OR $match != 1 ) {
        $_SESSION['alert_message'] = "please select an existing filename";
        header ( 'location:.#alert' );
        exit;
    }
    $match = 0;
}
//editor options
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "templates" && ! isset ( $_REQUEST['filename'] ) && ! isset ( $_REQUEST['web'] ) && ! isset ($_REQUEST['text'])) {
    //get the name of the template
    $r = mysql_query ( "SELECT name FROM templates WHERE id = '$id'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $name = $ra['name'];
    }
    echo "
            <div>
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - " . $name . "</h3></td>
                        <td class=\"td_right\">
                            <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Use the editor to edit your template's emails.<br /><br />You may use the following paramters that will be replaced at runtime:<br /><ul><li>@url - exact link</li><li>@link - will present anchor tag with fake link</li><li>@fname - First Name</li><li>@lname - Last Name</li></ul>Hover over each icon in the editor for a little more information about what each does.<br /><br />Click the green checkmark to save your changes.</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\"><img src=\"../images/email_edit_sm.png\" alt=\"email\" />&nbsp<span class=\"thick_underline\">Email</span>&nbsp&nbsp<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&web=1\"><img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbspWebsite</a>&nbsp&nbsp<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&text=1\"><img src=\"../images/pencil_sm.png\" alt=\"text\" />&nbspText Editor</a></td>
                    </tr>
                </table>
            </div>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "templates" && isset ( $_REQUEST['web'] ) ) {
    //get the name of the template
    $r = mysql_query ( "SELECT name FROM templates WHERE id = '$id'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $name = $ra['name'];
    }
    echo "
        <div>
            <form id=\"email_editor\" method=\"GET\" action=\"\">
                <input type=\"hidden\" name=\"editor\" value=\"1\" />
                <input type=\"hidden\" name=\"type\" value=\"templates\" />
                <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
                <input type=\"hidden\" name=\"web\" value=\"1\" />
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - " . $name . "</h3></td>
                        <td class=\"td_right\">
                                <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Select a file from the template or education you selected, load it and use the editor to edit your file.<br /><br />Hover over each icon in the editor for a little more information about what each does.<br /><br />Click the green checkmark to save your changes.</span></a>";
        if(!isset($_REQUEST['filename'])){
            echo "&nbsp;<a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>";
        }
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\">
                            <a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "\"><img src=\"../images/email_edit_sm.png\" alt=\"email\" />&nbspEmail</a>&nbsp;&nbsp;<img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbsp<span class=\"thick_underline\">Website</span>&nbsp;&nbsp;
                            <select name=\"filename\">";
//query template directory for files
    $files = scandir ( '../templates/' . $id . '/' );
//do this process for each item
    foreach ( $files as $file ) {
        //determine if the item is a directory or file
        if ( ! is_dir ( $file ) ) {
            //break apart the filename
            $file_array = explode ( ".", $file );
            //look for htm, php and html files
            if ( $file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "js" ) {
                if ( $file == "license.htm" OR $file == "email.php" ) {
                    
                } else {
                    //add the file to the drop-down
                    echo "<option value=\"" . $file . "\" ";
                    if ( isset ( $_REQUEST['filename'] ) && $_REQUEST['filename'] == $file ) {
                        echo "SELECTED";
                    }
                    echo " >" . $file . "</option>";
                }
            }
        }
    }
    echo "                
                            </select>
                            <input type=\"submit\" value=\"load\" />&nbsp&nbsp<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&text=1\"><img src=\"../images/pencil_sm.png\" alt=\"text\" />&nbspText Editor</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "templates" && !isset ($_REQUEST['web']) && isset ( $_REQUEST['text'] ) ) {
    //get the name of the template
    $r = mysql_query ( "SELECT name FROM templates WHERE id = '$id'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $name = $ra['name'];
    }
    echo "
        <div>
            <form id=\"email_editor\" method=\"GET\" action=\"\">
                <input type=\"hidden\" name=\"editor\" value=\"1\" />
                <input type=\"hidden\" name=\"type\" value=\"templates\" />
                <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
                <input type=\"hidden\" name=\"text\" value=\"1\" />
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - " . $name . "</h3></td>
                        <td class=\"td_right\">
                                <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Select a file from the template or education you selected, load it and use the editor to edit your file.<br /><br />Hover over each icon in the editor for a little more information about what each does.<br /><br />Click the green checkmark to save your changes.</span></a>";
        if(!isset($_REQUEST['filename'])){
            echo "&nbsp;<a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>";
        }
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\">
                            <a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "\"><img src=\"../images/email_edit_sm.png\" alt=\"email\" />&nbspEmail</a>&nbsp&nbsp<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&web=1\"><img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbspWebsite</a>&nbsp&nbsp<img src=\"../images/pencil_sm.png\" alt=\"text\" />&nbsp<span class=\"thick_underline\">Text Editor</span>&nbsp;&nbsp;
                            <select name=\"filename\">";
//query template directory for files
    $files = scandir ( '../templates/' . $id . '/' );
//do this process for each item
    foreach ( $files as $file ) {
        //determine if the item is a directory or file
        if ( ! is_dir ( $file ) ) {
            //break apart the filename
            $file_array = explode ( ".", $file );
            //look for htm, php and html files
            if ( $file_array[1] == "jpg" OR $file_array[1] == "png" OR $file_array[1] == "gif" ){
                //do nothing for images files
            }else {
                //add the file to the drop-down
                echo "<option value=\"" . $file . "\" ";
                if ( isset ( $_REQUEST['filename'] ) && $_REQUEST['filename'] == $file ) {
                    echo "SELECTED";
                }
                echo " >" . $file . "</option>";
            }
            
        }
    }
    echo "                
                            </select>
                            <input type=\"submit\" value=\"load\" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "education" && !isset($_REQUEST['text']) ) {
    //get the name of the education package
    $r = mysql_query ( "SELECT name FROM education WHERE id = '$id'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $name = $ra['name'];
    }
    echo "
        <div>
            <form id=\"education_editor\" method=\"GET\" action=\"\">
                <input type=\"hidden\" name=\"editor\" value=\"1\" />
                <input type=\"hidden\" name=\"type\" value=\"education\" />
                <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - " . $name . "</h3></td>
                        <td class=\"td_right\">
                                <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Select a file from the template or education you selected, load it and use the editor to edit your file.<br /><br />Hover over each icon in the editor for a little more information about what each does.<br /><br />Click the green checkmark to save your changes.</span></a>";
        if(!isset($_REQUEST['filename'])){
            echo "&nbsp;<a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>";
        }
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\">
                            <img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbsp<span class=\"thick_underline\">Website</span>&nbsp&nbsp<select name=\"filename\">";
    //query education directory for files
    $files = scandir ( '../education/' . $id . '/' );
    //do this process for each item
    foreach ( $files as $file ) {
        //determine if the item is a directory or file
        if ( ! is_dir ( $file ) ) {
            //break apart the filename
            $file_array = explode ( ".", $file );
            //look for htm, php and html files
            if ( $file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "js" ) {
                if ( $file == "license.htm" OR $file == "email.php" ) {
                    
                } else {
                    //add the file to the drop-down
                    echo "<option value=\"" . $file . "\" ";
                    if ( isset ( $_REQUEST['filename'] ) && $_REQUEST['filename'] == $file ) {
                        echo "SELECTED";
                    }
                    echo " >" . $file . "</option>";
                }
            }
        }
    }
    echo "                
                            </select>
                            <input type=\"submit\" value=\"load\" />&nbsp;&nbsp;<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&text=1\"><img src=\"../images/pencil_sm.png\" alt=\"text\" />&nbspText Editor&nbsp;&nbsp;
                        </td>
                    </tr>
                </table>
            </form>
        </div>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "education" && isset($_REQUEST['text']) ) {
    //get the name of the education package
    $r = mysql_query ( "SELECT name FROM education WHERE id = '$id'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $name = $ra['name'];
    }
    echo "
        <div>
            <form id=\"education_editor\" method=\"GET\" action=\"\">
                <input type=\"hidden\" name=\"editor\" value=\"1\" />
                <input type=\"hidden\" name=\"type\" value=\"education\" />
                <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
                <input type=\"hidden\" name=\"text\" value=\"1\" />
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - " . $name . "</h3></td>
                        <td class=\"td_right\">
                                <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Select a file from the template or education you selected, load it and use the editor to edit your file.<br /><br />Hover over each icon in the editor for a little more information about what each does.<br /><br />Click the green checkmark to save your changes.</span></a>";
        if(!isset($_REQUEST['filename'])){
            echo "&nbsp;<a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>";
        }
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\">
                            <a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "\"><img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbspWebsite</a>&nbsp;&nbsp;<img src=\"../images/pencil_sm.png\" alt=\"text\" />&nbsp<span class=\"thick_underline\" />Text Editor</span>&nbsp;&nbsp;<select name=\"filename\">";
    //query education directory for files
    $files = scandir ( '../education/' . $id . '/' );
    //do this process for each item
    foreach ( $files as $file ) {
        //determine if the item is a directory or file
        if ( ! is_dir ( $file ) ) {
            //break apart the filename
            $file_array = explode ( ".", $file );
            //look for htm, php and html files
            if ( $file_array[1] != "png" OR $file_array[1] == "jpg" OR $file_array[1] == "gif" ) {
                //add the file to the drop-down
                echo "<option value=\"" . $file . "\" ";
                if ( isset ( $_REQUEST['filename'] ) && $_REQUEST['filename'] == $file ) {
                    echo "SELECTED";
                }
                echo " >" . $file . "</option>";
            }
        }
    }
    echo "                
                            </select>
                            <input type=\"submit\" value=\"load\" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>";
}
//determine if email
if ( $_REQUEST['type'] == "templates" && ! isset ( $_REQUEST['web'] ) && ! isset ( $_REQUEST['filename']) && ! isset ($_REQUEST['text']) ) {
    //get the email.php file for this template
    $file = file_get_contents ( $id . "/email.php" );
    //get the sender friendly name
    preg_match ( '#\$sender_friendly\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $sender_friendly = $matches[1];
    //get the sender email address
    preg_match ( '#\$sender_email\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $sender_email = $matches[1];
    //get the reply to address
    preg_match ( '#\$reply_to\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $reply_to = $matches[1];
    //get the subject
    preg_match ( '#\$subject\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $subject = $matches[1];
    //get the fake link
    preg_match ( '#\$fake_link\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $fake_link = $matches[1];
    //get the message
    preg_match ( '#\$message\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches );
    $message = $matches[1];

    //start the form
    echo "
                <form id=\"editor_form_email\" action=\"?editor=1&type=templates&id=" . $id . "\" method=\"POST\">
                    <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />
                    <input type=\"hidden\" name=\"type\" value=\"templates\" />
                    <input type=\"hidden\" name=\"subtype\" value=\"email\" />
                    <table>
                        <tr>
                            <td class=\"td_left\">Sender's Friendly Name</td>
                            <td><input type=\"text\" name=\"sender_friendly\" value=\"" . $sender_friendly . "\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Sender's Email Address</td>
                            <td><input type=\"text\" name=\"sender_email\" value=\"" . $sender_email . "\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Reply To Address</td>
                            <td><input type=\"text\" name=\"reply_to\" value=\"" . $reply_to . "\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Subject</td>
                            <td><input type=\"text\" name=\"subject\" value=\"" . $subject . "\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Fake Link</td>
                            <td><input type=\"text\" name=\"fake_link\" value=\"" . $fake_link . "\" size=100 /></td>
                        </tr>
                    </table><br />
                    <textarea id=\"code\" name=\"code\">
                        " . $message . "
                    </textarea>
                    <br />
                    <span class = \"center\"><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type = \"image\" src = \"../images/accept.png\" /></span>";
                    if(isset($_SESSION['alert_message'])){
                        echo "<br /><span id=\"save_message\" class= \"popover_alert_message\" style=\"display:block\">".$_SESSION['alert_message']."</span>";
                    }
    echo "

                </form>";
}
//determine if web
if ( isset ( $_REQUEST['filename'] ) && !isset($_REQUEST['text'])) {
    //set filename
    $filename = $_REQUEST['filename'];
    //get the contents of the file
    $file = file_get_contents ( $id . "/" . $filename );
    //determine type
    $type = $_REQUEST['type'];
    //start the textarea
    echo "
                <form id=\"editor_form\" action=\"?editor=1&type=".$type."&id=" . $id;
    if($type == "templates"){
        echo "&web=1";
    }
    echo "&filename=" . $filename . "\" method=\"POST\">
                    <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />
                    <input type=\"hidden\" name=\"type\" value=\"".$type."\" />
                    <input type=\"hidden\" name=\"subtype\" value=\"full\" />
                    <textarea id=\"code\" name=\"code\">" . $file . "</textarea>
                    <br />
                    <span class = \"center\"><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type = \"image\" src = \"../images/accept.png\" /></span>";
                    if(isset($_SESSION['alert_message'])){
                        echo "<br /><span id=\"save_message\" class= \"popover_alert_message\" style=\"display:block\">".$_SESSION['alert_message']."</span>";
                    }
    echo "

                </form>";
}
//determine if text editor
if(isset($_REQUEST['text']) && isset($_REQUEST['filename'])){
    //get data
    $data = file_get_contents($id."/".$_REQUEST['filename']);
    //start the textarea
    echo "
        <form id=\"editor_form_text\" action=\"?editor=1&type=".$type."&id=".$id."&text=1&filename=".$_REQUEST['filename']."\" method=\"POST\" >
            <input type=\"hidden\" name=\"id\" value=\"".$id."\" />
            <input type=\"hidden\" name=\"type\" value=\"".$type."\" />
            <input type=\"hidden\" name=\"subtype\" value=\"text\" />
            <textarea id=\"text_editor\" name=\"code\">" . $data . "</textarea>
            <br /><br />
            <span class=\"center\"><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type = \"image\" src = \"../images/accept.png\" /></span>";
            if(isset($_SESSION['alert_message'])){
                echo "<br /><span id=\"save_message\" class= \"popover_alert_message\" style=\"display:block\">".$_SESSION['alert_message']."</span>";
            }
}
echo "
        </div>
    </div>";

echo "
    <script type=\"text/javascript\">
    // close the div in 5 secs
    window.setTimeout(\"closeSave();\", 5000);

    function closeSave(){
    document.getElementById(\"save_message\").style.display=\"none\";
    }
    </script>
";

if(!isset($_REQUEST['text'])){
    //initialize tinymce
    echo "    
        <script type=\"text/javascript\" src=\"../includes/tiny_mce/tiny_mce.js\"></script>
        <script type=\"text/javascript\">

        tinyMCE.init({
            // General options
            mode : \"textareas\",
            theme : \"advanced\",
            plugins : \"autolink,lists,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,xhtmlxtras,wordcount,advlist,fullpage\",
                                        height : \"100%\",

            // Theme options
            theme_advanced_buttons1 : \"preview,code,fullscreen,|,link,unlink,image,insertdate,inserttime,|,forecolor,backcolor,bullist,numlist,charmap,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect\",
            theme_advanced_buttons2 : \"\",
            theme_advanced_buttons3 : \"\",
            theme_advanced_buttons4 : \"\",
            theme_advanced_toolbar_location : \"top\",
            theme_advanced_toolbar_align : \"left\",
            theme_advanced_statusbar_location : \"bottom\",
            theme_advanced_resizing : true,

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : \"lists/template_list.js\",
            external_link_list_url : \"lists/link_list.js\",
            external_image_list_url : \"lists/image_list.js\",
            media_external_list_url : \"lists/media_list.js\",

            // Style formats
            style_formats : [
                {title : 'Bold text', inline : 'b'},
                {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
                {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
                {title : 'Example 1', inline : 'span', classes : 'example1'},
                {title : 'Example 2', inline : 'span', classes : 'example2'},
                {title : 'Table styles'},
                {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
            ],

            formats : {
                alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
                aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
                alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
                alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
                bold : {inline : 'span', 'classes' : 'bold'},
                italic : {inline : 'span', 'classes' : 'italic'},
                underline : {inline : 'span', 'classes' : 'underline', exact : true},
                strikethrough : {inline : 'del'}
            },
        });
        </script>";   
}
?>
