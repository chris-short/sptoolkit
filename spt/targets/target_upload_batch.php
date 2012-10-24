<?php

/**
 * file:    target_upload_batch.php
 * version: 24.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Target management
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

//set script timeout value to infinite
set_time_limit(0);

//connect to database
include "../spt_config/mysql_config.php";

//ensure that the file is under 20M
if ( $_FILES["file"]["size"] > 20000000 ) {
    $_SESSION['alert_message'] = 'max file size is 20MB';
    header ( 'location:./?add_many=true#tabs-1' );
    exit;
}

//ensure there are no errors
if ( $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "you either did not select a file or there was a problem with it";
    header ( 'location:./?add_many=true#tabs-1' );
    exit;
}

//set ini to detect line endings to prepare for csv import
ini_set ( "auto_detect_line_endings", true );

//pull in file
$lines = file ( $_FILES["file"]["tmp_name"] );

//ensure there is a comma in every line
foreach ( $lines as $line ) {
    if ( ! preg_match ( '/[,]/', $line ) ) {
        $_SESSION['alert_message'] = "this file is not properly comma delimited, there is not a comma on every line";
        header ( 'location:./?add_many=true#tabs-1' );
        exit;
    }
}

//get the header column
$header_line = explode ( ',', $lines[0] );
$file_column_count = count($header_line);

//get the number of columns in the database
$r = mysql_query ( "SHOW COLUMNS FROM targets" );
$c = 0;
$table_column_count = 0;
while($table_columns_array = mysql_fetch_array($r)){
    if($c == 0){
        $c++;
        continue;
    }else if($c == 1){
        $table_columns = array($table_columns_array['Field']);
    }else{
        array_push($table_columns, $table_columns_array['Field']);
    }
    $c++;
    $table_column_count++;
}

//compare row counts
if($file_column_count != $table_column_count){
    $_SESSION['alert_message'] = "the import file's column count does not match what is in the database";
    header ( 'location:./?add_many=true#tabs-1' );
    exit;
}

$c = 0;
//ensure the column names match
foreach($header_line as $column){
    $column = trim($column);
    if(!in_array($column, $table_columns)){
        $_SESSION['alert_message'] = "Your import file has a column that does not exist in the database";
        header ( 'location:./?add_many=true#tabs-1' );
        exit;
    }
    if($c == 0){
        $header_line_trimmed = array(trim($column));
    }else{
        array_push($header_line_trimmed, trim($column));
    }
    ++$c;
}

//determine which column is which in the imported file
$c = 0;
foreach($table_columns as $table_column){
    $key = array_search($table_column, $header_line_trimmed);
    if($c == 0){
        $table_file_match_array = array($key);
    }else{
        array_push($table_file_match_array, $key);
    }
    $c++;
}

//set error counters
$bad_email_count = 0;
$bad_fname_count = 0;
$bad_lname_count = 0;
$bad_group_name_count = 0;
$total_attempted = 0;
$total_imported = 0;
$header_counter = 0;
$continue = 0;

//validate each column of data and if all columns validate write the entire line to the database
foreach ( $lines as $line ) { 
    //skip first/header row
    if($header_counter == 0){
        ++$header_counter;
        continue;
    }
    //separate each line into an array based on the comma delimiter
    $line_contents = explode ( ',', $line );
    foreach($line_contents as $key => $value){
        //check to see if its the group name and if its valid
        if($key == $table_file_match_array[3]){
            $temp_group_name = filter_var(trim($value), FILTER_SANITIZE_STRING);
            if(strlen($temp_group_name) < 1){
                ++$total_attempted;
                ++$bad_group_name_count; 
                $continue = 1;
                continue;
            }
        }
        //check if it is the email column and if it is valid
        if($key == $table_file_match_array[0]){
            $temp_email = trim($value);
            if(!filter_var(trim($value), FILTER_VALIDATE_EMAIL)){
                ++$total_attempted;
                ++$bad_email_count;
                $continue = 1;
                continue;
            }
        }
        //check if it is the fname column
        if($key == $table_file_match_array[1]){
            $temp_fname = filter_var(trim($value), FILTER_SANITIZE_STRING);
            if(strlen($temp_fname) < 1){
                ++$total_attempted;
                ++$bad_fname_count;
                $continue = 1;
                continue;
            }
        }
        //check if it is the lname column
        if($key == $table_file_match_array[2]){
            $temp_lname = filter_var(trim($value), FILTER_SANITIZE_STRING);
            if(strlen($temp_lname) < 1){
                ++$total_attempted;
                ++$bad_lname_count;
                $continue = 1;
                continue;
            }
        }
    }
    if($continue == 1){
        $continue = 0;
        continue;
    }
    //ensure that this email address is unique within the group
    $r = mysql_query("SELECT email FROM targets WHERE group_name = '$temp_group_name'");
    while($ra = mysql_fetch_assoc($r)){
        if($ra['email']==$temp_email){
            ++$bad_email_count;
            ++$total_attempted;
            $continue = 1;
        }
    }
    if($continue == 1){
        $continue = 0;
        continue;
    }
    //if you make it this far write all values to the database and increment the import count
    $sql = "INSERT INTO targets(";
    $c = 0;
    foreach($table_columns as $table_column){
        if($c != 0){
            $sql .= ", ";
        }
        $sql .= $table_column;
        ++$c;
    }
    $sql .= ") VALUES (";
    $c = 0;
    foreach($table_file_match_array as $key => $value){
        if($c != 0){
            $sql .= ", ";
        }
        $field_value = filter_var(trim($line_contents[$value]), FILTER_SANITIZE_STRING);
        $sql .= "'".$field_value."'";
        ++$c;
    }
    $sql .= ")";
    mysql_query($sql) or die (mysql_error());
    ++$total_attempted;
    ++$total_imported;
}

//send stats back if there were bad rows
if ( $bad_email_count > 0 ) {
    $_SESSION["bad_row_stats"][] = $bad_email_count . " rows excluded due to bad email addresses";
}
if ( $bad_fname_count > 0 ) {
    $_SESSION["bad_row_stats"][] = $bad_fname_count . " rows excluded due to missing first name(s)";
}
if ( $bad_lname_count > 0 ) {
    $_SESSION["bad_row_stats"][] = $bad_lname_count . " rows excluded due to missing last name(s)";
}
if ( $bad_group_name_count > 0 ) {
    $_SESSION["bad_row_stats"][] = $bad_group_name_count . " rows excluded due to missing group name(s)";
}

//send user back to targets page with success message
$_SESSION['alert_message'] = $total_imported . " of " . $total_attempted . " targets uploaded successfully";
header ( 'location:.#tabs-1' );
exit;
?>
