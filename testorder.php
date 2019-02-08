<?php
$args = array( 'post_type' => 'product', 'post_status' => 'publish', 
				'posts_per_page' => -1 );
				$products = new WP_Query( $args );
				echo $products->found_posts; die;
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
