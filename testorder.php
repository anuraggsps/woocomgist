<?php
    
    
    
    $var = json_decode({"user":{"type":"user","id":125594,"name":"admin","email":"anugau.123@gmail.com","user_id":"1","phone":"","session_count":null,"avatar":"https://avatar.tobi.sh/anugau.123@gmail.com?size=120\u0026type=svg\u0026text=AD","landing_url":null,"original_referrer":null,"last_seen_ip":null,"last_seen_user_agent":null,"location_data":{"city_name":null,"region_name":null,"country_name":null,"country_code":null,"continent_name":null,"continent_code":null,"latitude":null,"longitude":null,"postal_code":null,"time_zone":null,"utc_offset":null},"tags":[],"social_profiles":[],"custom_properties":{},"unsubscribed_from_emails":"false","created_at":1549615292,"updated_at":1549615292,"signed_up_at":1549615292,"last_seen_at":1549615292,"last_contacted_at":null,"segments":[]}} );
    print_r($var);
	//~ $order =  new WC_Order(238);
		 //~ echo "<pre>";print_r($order); die;
		//~ $data['order'] = [];
		//~ $i=0;

		//~ //get paymnet mode and make checks on it
		//~ $data['order']['order_id'] = $order_id;
		//~ $data['order']['payment_type'] = get_post_meta($order_id, '_billing_myfield23', true );
		//~ $data['order']['total'] = get_post_meta($order_id, '_order_total', true );
		//~ foreach ($order->get_items() as $item_key => $item_values){
			//~ $product_id = $item_values->get_product_id(); // the Product id
			//~ $product = $item_values->get_product();
			//~ // Access Order Items data properties (in an array of values) 
			//~ $item_data = $item_values->get_data();
			//~ $data['order']['order_items'][$i]['product_name'] = $item_data['name'];
			
			//~ //get product type by its id ;
				//~ if($item_data['variation_id'] == 0){
					//~ $terms = get_the_terms( $product_id, 'product_cat' );
					//~ $product_cat_id =array();
					//~ foreach ($terms as $term) {
						//~ $product_cat_id[] = $term->term_id;
					//~ }
					//~ $type = get_term_by( 'id', $product_cat_id[0], 'product_cat', 'ARRAY_A' );
					//~ $data['order']['order_items'][$i]['item_type'] = $type['name'];
					//~ $data['order']['order_items'][$i]['zuora_rplan_id'] = get_post_meta($product_id, 'zuora_rplan_id', true );
					//~ $data['order']['order_items'][$i]['zuora_rplan_charge_id'] = get_post_meta($product_id, 'zuora_rplan_charge_id', true );
				//~ }else{
					//~ $data['order']['order_items'][$i]['item_type'] = 'users'; 
					//~ if('monthly' == get_post_meta($item_data['variation_id'], 'attribute_tenure', true )){    
						//~ $data['order']['order_items'][$i]['zuora_rplan_id'] = get_post_meta($product_id, 'zuora_monthly_rplan_id', true );
						//~ $data['order']['order_items'][$i]['zuora_rplan_charge_id'] = get_post_meta($product_id, 'zuora_rplan_monthly_charge_id', true );
					//~ }else if('annual' == get_post_meta($item_data['variation_id'], 'attribute_tenure', true )){	  
						//~ $data['order']['order_items'][$i]['zuora_rplan_id'] = get_post_meta($product_id, 'zuora_annual_rplan_id', true );
						//~ $data['order']['order_items'][$i]['zuora_rplan_charge_id'] = get_post_meta($product_id, 'zuora_rplan_annual_charge_id', true );
					//~ }
					
				//~ }
			//~ $data['order']['order_items'][$i]['product_variation_id'] = $item_data['variation_id'];
			//~ $data['order']['order_items'][$i]['quantity'] = $item_data['quantity'];
			$data['order']['order_items'][$i]['zuora_rplan_id'] = get_post_meta($product_id, 'zuroa_id', true );;
			//~ $i++;
		//~ };
//~ echo "<pre>";print_r($data);
