<?php

/**
 * file:    ldap.php
 * version: 16.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Includes
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

//ldap connect function
function ldap_connection($ldap_server,$ldap_port, $ssl_enc){
    if($ssl_enc == 1){
        $ldap_conn = ldap_connect("ldaps://".$ldap_server, $ldap_port);
    }else{
        //setup connection
        $ldap_conn = ldap_connect($ldap_server,$ldap_port);    
    }
    //set options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
    //return ldap connection
    return $ldap_conn;
}
//ldap bind function
function ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass){
    //protect from anonymous bind
    if(strlen($ldap_pass) > 1 && strlen($ldap_user) > 1){
        //if connected attempt bind
        $ldap_bind = ldap_bind($ldap_conn,$ldap_user,$ldap_pass);        
    }
    //return bind
    return $ldap_bind;
}
//ldap user query
function ldap_user_query($ldap_server,$ldap_port,$ldap_bind_user,$ldap_pass,$ldap_basedn,$ldap_user,$ldap_type, $ssl_enc){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port, $ssl_enc);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_bind_user,$ldap_pass);
    //setup search and filter depending on the authentication directory type
    if($ldap_type == "Active Directory"){
        $search = "(sAMAccountName=".$ldap_user.")";
        $filter=array("dn", "sAMAccountName");    
    }else{
        $search = "(uid=".$ldap_user.")";
        $filter=array("dn", "uid");
    }
    //search
    $ldap_user_query = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_query = ldap_get_entries($ldap_conn, $ldap_user_query);
    //return dump
    return $ldap_user_query;
}
//ldap email to user query
function ldap_user_email_query($ldap_server,$ldap_port,$ldap_bind_user,$ldap_pass,$ldap_basedn, $ssl_enc, $ldap_type, $ldap_email){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port, $ssl_enc);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_bind_user,$ldap_pass);
    //setup search and filter depending on the authentication directory type
    if($ldap_type == "Active Directory"){
        $search = "(mail=".$ldap_email.")";
        $filter=array("dn", "sAMAccountName", "givenName", "sn");    
    }else{
        $search = "(mail=".$ldap_email.")";
        $filter=array("dn", "uid", "givenName", "sn");
    }
    //search
    $ldap_user_query = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_query = ldap_get_entries($ldap_conn, $ldap_user_query);
    //return dump
    return $ldap_user_query;
}
//ldap group user dump
function ldap_group_dump($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(|(objectCategory=group)(ObjectClass=posixGroup)(ObjectClass=groupOfNames))";
    //setup filter for what you want from your data
    $filter=array("displayName","objectclass", "cn", "uid");
    //search
    $ldap_group_dump = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_group_dump = ldap_get_entries($ldap_conn, $ldap_group_dump);
    //return dump
    return $ldap_group_dump;
}
//ldap user dump
function ldap_user_dump($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(|(objectCategory=user)(ObjectClass=person))";
    //setup filter for what you want from your data
    $filter=array("displayName","objectclass", "cn", "uid");
    //search
    $ldap_user_dump = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_dump = ldap_get_entries($ldap_conn, $ldap_user_dump);
    //return dump
    return $ldap_user_dump;
}
//ldap group query
function ldap_group_query($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn,$ldap_type,$ldap_ssl,$ldap_group){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_ssl);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(cn=".$ldap_group.")";
    //setup filter for what you want from your data
    $filter=array("dn");
    //search
    $ldap_group_query = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_group_query = ldap_get_entries($ldap_conn, $ldap_group_query);
    //return dump
    return $ldap_group_query;
}
//ldap user in group check
function ldap_user_of_group($ldap_server,$ldap_port,$ldap_ssl,$ldap_type,$ldap_user,$ldap_pass,$ldap_basedn,$ldap_group){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_ssl);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(&(memberof=".$ldap_group.")(|(objectCategory=user)(ObjectClass=person)))";
    //setup filter for what you want from your data
    $filter=array("displayName","objectclass", "cn", "mail");
    //search
    $ldap_user_dump = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_dump = ldap_get_entries($ldap_conn, $ldap_user_dump);
    //return dump
    return $ldap_user_dump;
}
function ldap_user_group_check($ldap_server,$ldap_port,$ldap_ssl,$ldap_type,$ldap_user,$ldap_pass,$ldap_basedn,$ldap_user,$ldap_group_dn){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(&(uid=".$ldap_user.")(memberof=".$ldap_group_dn.")";
    //setup filter for what you want from your data
    $filter=array("dn", "uid");
    //search
    $ldap_user_of_group = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_of_group = ldap_get_entries($ldap_conn, $ldap_user_of_group);
    //return dump
    return $ldap_user_of_group;
}

?>