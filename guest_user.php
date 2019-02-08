<?php
	global $wpdb;
	if(!isset($_COOKIE['guest_user'])) {
		ob_start();
		$cookie_name = "guest_user";
		$cookie_value = md5(microtime().rand());;
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30*12), "/"); // 86400 = 1 day
		// create guest user and save data in wordpress 
		$users_table   =    $wpdb->prefix.'gist_users_data';
		$sql = $wpdb->prepare("INSERT INTO $users_table (cookie_id, login_id, created_at,modified_at)VALUES ($cookie_value,,CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())");
		//$wpdb->query($sql);
	}else{
			
		//if cookie is  set
		echo "Value is: " . $_COOKIE['guest_user'];
	}	


?>



