<?php

	global $wpdb;
	$users_events_tables 		=    $wpdb->prefix.'gist_users_events_data';
	$users_data 		        =    $wpdb->prefix.'gist_users_data';
	$user_guest_gist_ids 		=    $wpdb->prefix.'guest_gist_ids';
	
	//check placed order for completed status if status is completed then that order will be deleted other wise it will remain stored in db
	$sql = $wpdb->get_results("select * from $users_events_tables where event_name = 'Placed Order' and is_data_sent_to_gist =1 ");
	// if found result then check the status of order 
	if(!empty($sql)){
		foreach($sql as $data){
			$event_id = $data->event_id; 
			if(get_post_meta($event_id, 'is_custom_completed', true) == 1){
				// Delete all record that is sent to gist
				$guest_id = $data->guest_id; 
				$created_at = $data->created_at; 
				$deleterecords  = $wpdb->query("DELETE FROM $users_events_tables WHERE guest_id = $guest_id and  is_data_sent_to_gist = 1 and created_at <= $created_at");
			}
			
		}
	}
	
