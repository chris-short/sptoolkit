<?php

/**
 * file:    editor.php
 * version: 2.0
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
if ( ! isset ( $_REQUEST['type'] ) OR ! preg_match ( '#(template|education)#', $_REQUEST['type'] ) ) {
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
if ( $_REQUEST['type'] == "template" ) {
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
    //query template directory for files
    $files = scandir ( '../templates/' . $id . '/' );
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
    if ( $match != 1 ) {
        $_SESSION['alert_message'] = "please select an existing filename";
        header ( 'location:.#alert' );
        exit;
    }
    $match = 0;
}
//start editor header
echo "<div id=\"editor_header\">";
//start editor naviation wrapper
echo "<div id=\"editor_navigation_wrapper\">";
//editor options
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "template" && ! isset ( $_REQUEST['filename'] ) && ! isset ( $_REQUEST['web'] ) ) {
    echo "
            <ul id=\"editor_nav\">
                <li>Email</li>
                <li><a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&web=1\">Website</a></li>
                </li>
            </ul>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "template" && isset ( $_REQUEST['web'] ) ) {
    echo "
        <form method=\"GET\" action\"\">
        <input type=\"hidden\" name=\"editor\" value=\"1\" />
        <input type=\"hidden\" name=\"type\" value=\"template\" />
        <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
        <input type=\"hidden\" name=\"web\" value=\"1\" />
        <ul id=\"editor_nav\">
            <li><a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "\">Email</a></li>
            <li>Website</li>
            <li>
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
            if ( $file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "css" OR $file_array[1] == "js" ) {
                if ( $file == "license.htm" ) {
                    
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
            </li>
            <li><input type=\"submit\" value=\"Select\" /></li>
        </ul>
        </form>";
}
//end editor navigation wrapper
echo "</div>";
//start editor action wrapper
echo "<div id=\"editor_actions\">";
//editor actions
echo "<ul><li><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a></li><li>?</li></ul>";
//end editor action wrapper
echo "</div>";
//end editor header
echo "</div>";
//determine if email and echo tinymce
if ( $_REQUEST['type'] == "template" && ! isset ( $_REQUEST['web'] ) && ! isset ( $_REQUEST['filename'] ) ) {
    //start tinymce
    //start the textarea
    echo "
                <form action=\"\" method=\"POST\">
                    <textarea id=\"email_code\" name=\"email_code\">";
    //get the template id
    //get the filename
    //retrieve and echo the file contents
    //close the textarea & wrapper
    echo "
                    </textarea>
                </form>";
}
//determine if website and echo codemirror
else {
    //start the textarea
    echo "
                <form action=\"\" method=\"POST\">
                    <textarea id=\"code\" name=\"code\">";
    //get the template id
    //get the filename
    //retrieve and echo the file contents
    //close the textarea & wrapper
    echo "
                    </textarea>
                </form>";
}
echo "
        </div>
    </div>";

//included required javascript files
echo "<script src=\"../includes/codemirror/lib/codemirror.js\"></script>";
//include the necessary javascript files based on the edited file
//initialize codemirror & the relevant options
echo "    
    <script>
      var editor = CodeMirror.fromTextArea(document.getElementById(\"code\"), {
        lineNumbers: true,
        extraKeys: {\"Ctrl-Space\": function(cm) {CodeMirror.simpleHint(cm, CodeMirror.javascriptHint);}}
      });
    </script>";
?>
