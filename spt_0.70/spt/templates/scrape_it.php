<?php

/**
 * file:    scrape_it.php
 * version: 21.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Template management
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

//set sessions
if(!empty($_POST['url'])){
    $_SESSION['temp_url'] = $_POST['url'];
}
if(!empty($_POST['name'])){
    $_SESSION['temp_scrape_name'] = $_POST['name'];
}
if(!empty($_POST['description'])){
    $_SESSION['temp_description'] = $_POST['description'];
}
if(!empty($_POST['email_subject'])){
    $_SESSION['temp_email_subject'] = $_POST['email_subject'];
}
if(!empty($_POST['email_from'])){
    $_SESSION['temp_email_from'] = $_POST['email_from'];
}
if(!empty($_POST['email_from_friendly'])){
    $_SESSION['temp_email_from_friendly'] = $_POST['email_from_friendly'];
}
if(!empty($_POST['reply_to'])) {
    $_SESSION['temp_reply_to'] = $_POST['reply_to'];
}
if(!empty($_POST['email_message'])){
    $_SESSION['temp_email_message'] = $_POST['email_message'];
}
if(!empty($_POST['email_fake_link'])){
    $_SESSION['temp_email_fake_link'] = $_POST['email_fake_link'];
}

//get URL from passed parameter
if ( ! isset ( $_POST['url'] ) ) {
    //set error message and send them back to template page
    $_SESSION['alert_message'] = "please enter a URL";
    header ( 'location:./#add_scrape' );
    exit;
}

//validate url
if ( ! filter_var ( $_POST['url'], FILTER_SANITIZE_URL ) ) {
    $_SESSION['alert_message'] = "please enter a valid URL";
    header ( 'location:./#add_scrape' );
    exit;
} else {
    $url = filter_var ( $_POST['url'], FILTER_SANITIZE_URL );
}

//get name from passed parameter
if ( strlen ( $_POST['name'] ) > 0 ) {
    $name = filter_var ( $_POST['name'], FILTER_SANITIZE_STRING );
} else {
    //set error message and send them back to template page
    $_SESSION['alert_message'] = "please enter a name";
    header ( 'location:./#add_scrape' );
    exit;
}

//get description from passed parameter
if ( isset ( $_POST['description'] ) ) {
    $description = filter_var ( $_POST['description'], FILTER_SANITIZE_STRING );
} else {
    //set error message and send them back to template page
    $_SESSION['alert_message'] = "please enter a description";
    header ( 'location:./#add_scrape' );
    exit;
}

//function to get data from URL 
function get_url_contents ( $url, $timeout = 10, $userAgent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10' ) {
    $rawhtml = curl_init ();
    $header[] = "Accept-Language: " . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    curl_setopt ( $rawhtml, CURLOPT_HTTPHEADER, $header );
    curl_setopt ( $rawhtml, CURLOPT_URL, $url );
    curl_setopt ( $rawhtml, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $rawhtml, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $rawhtml, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $rawhtml, CURLOPT_USERAGENT, $userAgent );
    $output = curl_exec ( $rawhtml );
    curl_close ( $rawhtml );
    if ( ! $output ) {
        $_SESSION['alert_message'] = "no output was returned from this URL";
        header ( 'location:./#add_scrape' );
        exit;
    }
    return $output;
}

//get passed URL and turn that URL into raw html
$html_string = get_url_contents ( $url );

$file = "temp_upload/index.htm";
$working = fopen ( $file, 'w' ) or die ( "can't open file" );
fwrite ( $working, $html_string );
fclose ( $working );

//prepare URL for parsing links
$parsed_url = parse_url ( $url );
$url = $parsed_url['scheme'] . "://" . $parsed_url['host'];

//find and replace function
function f_and_r ( $find, $replace, $path ) {
    $globarray = glob ( $path );
    if ( $globarray )
        foreach ( $globarray as $filename ) {
            $source = file_get_contents ( $filename );
            $source = preg_replace ( $find, $replace, $source );
            file_put_contents ( $filename, $source );
        }
}

//fix double relative, absolute paths
f_and_r ( '#(async|src|href)="//#', '$1="http://', 'temp_upload/index.htm' );

//find and replace relative links 	
f_and_r ( '#(async|href|src)="([^:|\#"]*")#', '$1="' . $url . '/$2"', 'temp_upload/index.htm' );

//fix inline css url links
f_and_r ( '#url\(//#', 'url(http://', 'temp_upload/index.htm' );

//fix double backslashes
f_and_r ( '#(http(|s)://.*?)(//)#', '$1/', 'temp_upload/index.htm' );

//replace post destination to spt
f_and_r ( '#action="(.*?)"#', 'action="../../campaigns/response.php"', 'temp_upload/index.htm' );

//connect to database
include('../spt_config/mysql_config.php');

//add information to the database
mysql_query ( "INSERT INTO templates (name, description) VALUES ('$name','$description')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//create a directory for the new template
mkdir ( $id );

//copy scraped file into new template directory
copy ( "temp_upload/index.htm", $id . "/index.htm" );

//copy default email and return files into new template directory
copy ( "temp_upload/return.htm", $id . "/return.htm" );
copy ( "temp_upload/email.php", $id . "/email.php" );
copy ( "temp_upload/screenshot.png", $id . "/screenshot.png" );

//set correct permissions on newly created files
$directory = $id;
$filemode = 0775;

function chmodr ( $directory, $filemode ) {
    if ( ! is_dir ( $directory ) )
        return chmod ( $directory, $filemode );

    $dh = opendir ( $directory );
    while ( ($file = readdir ( $dh )) !== false ) {
        if ( $file != '.' && $file != '..' ) {
            $fullpath = $directory . '/' . $file;
            if ( is_link ( $fullpath ) )
                return FALSE;
            elseif ( ! is_dir ( $fullpath ) && ! chmod ( $fullpath, $filemode ) )
                return FALSE;
            elseif ( ! chmodr ( $fullpath, $filemode ) )
                return FALSE;
        }
    }

    closedir ( $dh );

    if ( chmod ( $directory, $filemode ) )
        return TRUE;
    else
        return FALSE;
}

chmodr ( $directory, $filemode );

//find and replace email subject if set
if ( isset ( $_POST['email_subject'] ) ) {
    f_and_r ( '#Insert Subject Here#', filter_var ( $_POST['email_subject'], FILTER_SANITIZE_MAGIC_QUOTES ), $id . '/email.php' );
}

//find and replace email from address if set
if ( isset ( $_POST['email_from'] ) ) {
    f_and_r ( '#postmaster@domain.com#', filter_var ( $_POST['email_from'], FILTER_SANITIZE_EMAIL ), $id . '/email.php' );
}

//find and replace email from friendly name if set
if ( isset ( $_POST['email_from_friendly'] ) ) {
    f_and_r ( '#sender friendly#', filter_var ( $_POST['email_from_friendly'], FILTER_SANITIZE_STRING ), $id . '/email.php' );
}

//find and replace the reply to address if set
if ( isset ( $_POST['reply_to'] ) ) {
    f_and_r ( '#reply_to@domain.com#', filter_var ( $_POST['reply_to'], FILTER_SANITIZE_STRING ), $id . '/email.php' );
}

//find and replace email message if set
if ( isset ( $_POST['email_message'] ) ) {
    f_and_r ( '#Your message will go here.#', htmlentities ( $_POST['email_message'], ENT_QUOTES ), $id . '/email.php' );
}

//find and replace email fake link if set
if ( isset ( $_POST['email_fake_link'] ) ) {
    f_and_r ( '#https://fake_display_link_goes_here.com/login#', filter_var ( $_POST['email_fake_link'], FILTER_SANITIZE_URL ), $id . '/email.php' );
}

//send them back to template page with a success message
$_SESSION['alert_message'] = "Template installed successfully!";
header ( 'location:./#alert' );
exit;
?>