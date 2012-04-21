<?php

/**
 * file:    editor.php
 * version: 6.0
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
if($_POST){
    //validate that the type is specified
    if(!isset($_POST['type'])){
        $_SESSION['alert_message'] =  "please specify a type when saving content from the editor";
        header ('location:.#alert');
        exit;
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
    if ( !isset($match) OR $match != 1 ) {
        $_SESSION['alert_message'] = "please select an existing filename";
        header ( 'location:.#alert' );
        exit;
    }
    $match = 0;
}
//editor options
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "template" && ! isset ( $_REQUEST['filename'] ) && ! isset ( $_REQUEST['web'] ) ) {
    //get the name of the template
    $r = mysql_query("SELECT name FROM templates WHERE id = '$id'");
    while($ra = mysql_fetch_assoc ( $r)){
        $name = $ra['name'];
    }
    echo "
            <div>
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - ".$name."</h3></td>
                        <td class=\"td_right\">
                            <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Editor help content to go here.</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=\"4\" class=\"td_left\"><img src=\"../images/email_edit_sm.png\" alt=\"email\" />&nbsp<span class=\"thick_underline\">Email</span>&nbsp&nbsp<a href=\"?editor=1&type=" . $_REQUEST['type'] . "&id=" . $id . "&web=1\"><img src=\"../images/world_edit_sm.png\" alt=\"web\" />&nbspWebsite</a></td>
                    </tr>
                </table>
            </div>";
}
if ( isset ( $_REQUEST['type'] ) && $_REQUEST['type'] == "template" && isset ( $_REQUEST['web'] ) ) {
    //get the name of the template
    $r = mysql_query("SELECT name FROM templates WHERE id = '$id'");
    while($ra = mysql_fetch_assoc ( $r)){
        $name = $ra['name'];
    }
    echo "
        <div>
            <form id=\"email_editor\" method=\"GET\" action=\"\">
                <input type=\"hidden\" name=\"editor\" value=\"1\" />
                <input type=\"hidden\" name=\"type\" value=\"template\" />
                <input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />        
                <input type=\"hidden\" name=\"web\" value=\"1\" />
                <table id=\"editor_header\">
                    <tr>
                        <td colspan=\"3\" class=\"td_left\"><h3>Editor - ".$name."</h3></td>
                        <td class=\"td_right\">
                                <a class=\"tooltip\"><img src=\"../images/lightbulb.png\" alt=\"help\" /><span>Editor help content to go here.</span></a>
                                &nbsp;<a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>
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
                            <input type=\"submit\" value=\"load\" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>";
}
//determine if email
if ( $_REQUEST['type'] == "template" && ! isset ( $_REQUEST['web'] ) && ! isset ( $_REQUEST['filename'] ) ) {
    //get the email.php file for this template
    $file = file_get_contents($id."/email.php");
    //get the sender friendly name
    preg_match('#\$sender_friendly\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $sender_friendly = $matches[1];
    //get the sender email address
    preg_match('#\$sender_email\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $sender_email = $matches[1];
    //get the reply to address
    preg_match('#\$reply_to\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $reply_to = $matches[1];
    //get the subject
    preg_match('#\$subject\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $subject = $matches[1];
    //get the fake link
    preg_match('#\$fake_link\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $fake_link = $matches[1];
    //get the message
    preg_match('#\$message\s=\s[\'|\"](.*?)[\'|\"];#', $file, $matches);
    $message = $matches[1];
    
    //start the form
    echo "
                <form action=\".\" method=\"POST\">
                    <table>
                        <tr>
                            <td class=\"td_left\">Sender's Friendly Name</td>
                            <td><input type=\"text\" name=\"sender_friendly\" value=\"".$sender_friendly."\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Sender's Email Address</td>
                            <td><input type=\"text\" name=\"sender_email\" value=\"".$sender_email."\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Reply To Address</td>
                            <td><input type=\"text\" name=\"reply_to\" value=\"".$reply_to."\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Subject</td>
                            <td><input type=\"text\" name=\"subject\" value=\"".$subject."\" size=100 /></td>
                        </tr>
                        <tr>
                            <td class=\"td_left\">Fake Link</td>
                            <td><input type=\"text\" name=\"fake_link\" value=\"".$fake_link."\" size=100 /></td>
                        </tr>
                    </table><br />
                    <textarea id=\"code\" name=\"code\">
                        ".$message."
                    </textarea>
                    <br />
                    <span class = \"center\"><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type = \"image\" src = \"../images/accept.png\" /></span>
                </form>";
}
//determine if website
else {
    //get the contents of the file
    $file = file_get_contents($id."/".$_REQUEST['filename']);
    //start the textarea
    echo "
                <form action=\"\" method=\"POST\">
                    <textarea id=\"code\" name=\"code\">".$file."</textarea>
                    <br />
                    <span class = \"center\"><a href=\".\"><img src=\"../images/cancel.png\" alt=\"close\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<input type = \"image\" src = \"../images/accept.png\" /></span>
                </form>";
}
echo "
        </div>
    </div>";

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
?>
