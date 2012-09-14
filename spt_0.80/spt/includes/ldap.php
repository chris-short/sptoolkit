<?php

/**
 * file:    ldap.php
 * version: 2.0
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
    $ldap_conn = ldap_connect ($ldap_server,$ldap_port);
    //set options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
    //if connected bind
    if($ldap_conn){
        $ldap_r = ldap_bind($ldap_conn, $ldap_user, $ldap_pass);
    }
}

//ldap group dump
function ldap_group_dump($ldap_r,$ldap_basedn){
    $ldap_group_dump = ldap_search($ldap_r, $ldap_basedn, "cn=*");    
    $ldap_group_dump = ldap_get_entries($ldap_r, $ldap_group_dump);
    echo $ldap_group_dump;
    exit;
}

//ldap group user dump
function ldap_group_user_dump($ldap_group){

}

//ldap user/group validate
function ldap_user_group_validate($ldap_user,$ldap_group){

}

?>