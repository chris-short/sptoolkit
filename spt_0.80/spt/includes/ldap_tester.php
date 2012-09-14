<html>
	<body>
		<h1>LDAP Tester :)</h1>
		<ul>
			<?php
				if(!$_GET['host']){
					//connect to db
					include '../spt_config/mysql_config.php';
					//get ldap servers
					$r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
					while ($ra = mysql_fetch_assoc($r)){
						$ldap_settings = explode("|",$ra['value']);
						echo "<li><a href=\"?host=".$ldap_settings[0]."&port=".$ldap_settings[1]."&ssl=".$ldap_settings[2]."&username=".$ldap_settings[3]."&password=".$ldap_settings[4]."&basedn=".$ldap_settings[5]."\" >".$ldap_settings[0].'</a></li>';
					}
				}else{
					include 'ldap.php';
					ldap_connection($_GET['host'],$_GET['port'],$_GET['username'],$_GET['password']);
					ldap_group_dump($ldap_r,$_GET['basedn']);
				}
			?>
		</ul>
	</body>
</html>