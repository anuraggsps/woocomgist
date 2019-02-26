<?php

	global $wpdb;
	$users_events_tables 		=    $wpdb->prefix.'gist_users_events_data';
	$users_data 		        =    $wpdb->prefix.'gist_users_data';
	$user_guest_gist_ids 		=    $wpdb->prefix.'guest_gist_ids';
    // Delete all record that is sent to gist

	$deleterecords  = $wpdb->query("DELETE FROM $users_events_tables WHERE event_id IN (select event_id from $users_events_tables where is_data_sent_to_gist =0 )");
