<?php

/**
 * file:    ldap.php
 * version: 4.0
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
function ldap_connection($ldap_server,$ldap_port){
    //setup connection
    $ldap_conn = ldap_connect ($ldap_server,$ldap_port);
    //set options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
    //return connection
    return $ldap_conn;
}
//ldap bind function
function ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass){
    //if connected attempt bind
    if($ldap_conn){
        $ldap_bind = ldap_bind($ldap_conn, $ldap_user, $ldap_pass);
    }
    //return bind
    return $ldap_bind;
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
    $filter=array("displayName","objectclass", "cn");
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
    $filter=array("displayName","objectclass", "cn");
    //search
    $ldap_user_dump = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_dump = ldap_get_entries($ldap_conn, $ldap_user_dump);
    //return dump
    return $ldap_user_dump;
}
//ldap user dump for specific group
function ldap_user_of_group($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn,$ldap_user,$ldap_group){
    //call connect function
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    //call bind function
    $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
    //setup search filter for the data you want
    $search = "(&(memberof=CN=".$ldap_group.")(|(objectCategory=user)(ObjectClass=person)))";
    //setup filter for what you want from your data
    $filter=array("displayName","objectclass", "cn");
    //search
    $ldap_user_dump = ldap_search($ldap_conn, $ldap_basedn, $search, $filter);    
    //get data
    $ldap_user_dump = ldap_get_entries($ldap_conn, $ldap_user_dump);
    //return dump
    return $ldap_user_dump;
}

?>