<?php

/**
 * file:    ldap.php
 * version: 3.0
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
function ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass){
    //setup connection
    $ldap_conn = ldap_connect ($ldap_server,$ldap_port) or die("Could not connect");
    //set options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
    //if connected bind
    if($ldap_conn){
        $ldap_r = ldap_bind($ldap_conn, $ldap_user, $ldap_pass);
    }
    return $ldap_conn;
}

//ldap group dump
function ldap_cn_dump($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn){
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    $ldap_cn_dump = ldap_search($ldap_conn, $ldap_basedn, "cn=*");    
    $ldap_cn_dump = ldap_get_entries($ldap_conn, $ldap_cn_dump);
    return $ldap_cn_dump;
}

//ldap group user dump
function ldap_group_dump($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn){
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    $filter=array("displayName","objectclass", "cn");
    $ldap_group_dump = ldap_search($ldap_conn, $ldap_basedn, "(|(objectCategory=group)(ObjectClass=posixGroup)(ObjectClass=groupOfNames))", $filter);    
    $ldap_group_dump = ldap_get_entries($ldap_conn, $ldap_group_dump);
    return $ldap_group_dump;
}
//ldap group user dump
function ldap_user_dump($ldap_server,$ldap_port,$ldap_user,$ldap_pass,$ldap_basedn){
    $ldap_conn = ldap_connection($ldap_server,$ldap_port,$ldap_user,$ldap_pass);
    $filter=array("displayName","objectclass", "cn");
    $ldap_user_dump = ldap_search($ldap_conn, $ldap_basedn, "(|(objectCategory=user)(ObjectClass=person))", $filter);    
    $ldap_user_dump = ldap_get_entries($ldap_conn, $ldap_user_dump);
    return $ldap_user_dump;
}


//ldap user/group validate
function ldap_user_group_validate($ldap_user,$ldap_group){

}

?>