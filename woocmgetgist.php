<?php 
/*
Plugin Name: Gist WooCommerce Integration
Description: Sending woocommerce data requests to getgist.
Version: 1.0
Author: Softprodigy
*/

# Plugin activation hook

function woocmgetgist_install() {
	
	global $wpdb;
	$users_table 		    =    $wpdb->prefix.'gist_users_data';
	$users_events_table 		=    $wpdb->prefix.'gist_users_events_data';
	$charset_collate 	    =    $wpdb->get_charset_collate();
	$users_sql 				= "CREATE TABLE $users_table (
        						     id bigint(20) NOT NULL AUTO_INCREMENT,
        						     cookie_id varchar(1255) NOT NULL,
        						     login_id bigint(20) NULL,
        						     created_at datetime NOT NULL,
        						     modified_at datetime NOT NULL,
        						     PRIMARY KEY  (id)
        						    ) $charset_collate;";
  $users_events_sql   = "CREATE TABLE $users_events_table (
                         event_id bigint(20) NOT NULL AUTO_INCREMENT,
                         guest_id bigint(20) NULL,
                         event_name varchar(255) NOT NULL,
                         product varchar(1222) NOT NULL,
                         created_at datetime NOT NULL,
                         modified_at datetime NOT NULL,
                         PRIMARY KEY  (event_id)
                        ) $charset_collate;";
  $wpdb->query($users_sql);
  $wpdb->query($users_events_sql);
  add_option( 'access_token_verification', 'no', '', 'yes' );
}
register_activation_hook( __FILE__, 'woocmgetgist_install' );

# Plugin deactivation hook
function woocmgetgist_deactivation() {
}
register_deactivation_hook( __FILE__, 'woocmgetgist_deactivation' );

# Plugin uninstall hook
function woocmgetgist_uninstall()
{
    global $wpdb;
    $users_table        = $wpdb->prefix.'gist_users_data';
    $users_events_table = $wpdb->prefix.'gist_users_events_data';
    $users_sql 		      = "DROP TABLE IF EXISTS $users_table";
    $users_events_sql   = "DROP TABLE IF EXISTS $users_events_table";
    $wpdb->query($users_sql);
    $wpdb->query($users_events_sql);
    delete_option('access_token_verification');
}
register_uninstall_hook(__FILE__, 'woocmgetgist_uninstall');

// settings page link
function woocmgetgist_action_links( $links ) {
  $links = array_merge( array(
    '<a href="' . esc_url( admin_url( 'options-general.php?page=gist-access-token-settings' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>'
  ), $links );
  return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocmgetgist_action_links' );

// Enqueue style and scripts
function woocmgetgist_scripts() {
    wp_enqueue_style( 'woocmgetgist-style', plugin_dir_url( __FILE__ ).'wocmgetgist.css' );
    wp_enqueue_script( 'jQuery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', null, null, true );
    wp_enqueue_script( 'woocmgetgist-script', plugin_dir_url( __FILE__ ). 'wocmgetgist.js', array(), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'woocmgetgist_scripts' );


// Admin menu link under settings
add_action('admin_menu', 'woocmgetgist_admin_menus');
function woocmgetgist_admin_menus() {

    add_submenu_page('options-general.php', 'Gist Settings', 'Gist Settings', 'manage_options', 'gist-access-token-settings', 'gist_access_token_settings'); 
}

function gist_access_token_settings() {  //is_checkout//echo get_option( 'woocommerce_checkout_page_id' ); ?>

  <div class="form-style-3">
  <?php if(isset($_POST['access_token'])){
          // Verify Access Token Gist Api
            $ch             = curl_init();
            $token          = $_POST['access_token'];
            $authorization  = "Authorization: Bearer ".$token;
            $purl           = 'https://aws-api-testing.getgist.com/verify_authentication';
            curl_setopt($ch, CURLOPT_URL, $purl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $getresponse    = curl_exec($ch);
            $deres          = json_decode($getresponse);  

            if ($deres->errors) {
              echo '<div class="alert alert-danger fade in alert-dismissible">    
                        <strong>Danger! </strong> '. $deres->errors[0]->message .'
                    </div>';
            } else {
              update_option( 'access_token_verification', 'yes', '', 'yes' );
              add_option('saved_access_token_verification',$_POST['access_token']);
              
              echo '<div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                        <strong>Success!</strong> Your entered access token is associated with'. $deres->project->project_name.' and '.$deres->project->domain. ' domain.
                    </div>';
            }
            curl_close($ch);  

        } 

  ?> 
    <form action="" method="post">
      <fieldset>
        <legend>Access Token Settings</legend>
        <label for="field6"><span>Access Token <span class="required">*</span></span>
          <input type="text" name="access_token" class="textarea-field" required="required">
        </label>
        <label><span> </span>
          <input type="submit" value="Submit"/>
        </label>
      </fieldset>
    </form>
  </div>
   
<?php } 

// short code for gist user both for guest user and login user
add_action('wp_head', 'set_cookie');
function set_cookie(){
	global $wpdb;
	//~ if(!is_user_logged_in()){
		if(!isset($_COOKIE['guest_user']) ){
			$cookie_name = "guest_user";
			$cookie_value = md5(microtime());;
			//~ $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
			//~ $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
			$users_table  =    $wpdb->prefix.'gist_users_data';
			$datetime = date("Y-m-d h:i:s");
			setcookie($cookie_name, $cookie_value, time() + (86400 * 365)); 
			$sql = $wpdb->query("INSERT INTO $users_table (cookie_id,created_at,modified_at)VALUES ('$cookie_value','$datetime','$datetime')");
		}
	//~ }
}	
function gist_short_code_func(){
	global $wpdb;
	global $woocommerce;
	if(!is_user_logged_in()){
		if (isset($_COOKIE['guest_user'])) {
			// here we check if cookie is not set if set then all the values will be set in above init hook
			$cookie_id = $_COOKIE['guest_user'];
			// check if user has seen checkout page 
			if(is_checkout()){
					// get all item of cart 
				$items = $woocommerce->cart->get_cart();
				// if item is in the cart that mean add to cart event is trrigered.
				if(count($items)>0){
					$addtocart_array = array();
					foreach($items as $item => $values) { 
						$addtocart_array['external_id'] = $values['data']->get_id();
						$_product =  wc_get_product( $values['data']->get_id());
						$addtocart_array['name'] = $_product->get_title();
						$addtocart_array['price'] = $_product->get_price();
						$addtocart_array['quantity'] = $values['quantity'];
						
						$terms = get_the_terms($values['data']->get_id(), 'product_cat' );
						$product_cat_id =array();
						foreach ($terms as $term) {
							$product_cat_id[] = $term->term_id;
						}
						$type = get_term_by( 'id', $product_cat_id[0], 'product_cat', 'ARRAY_A' );
						$addtocart_array['category'] = $type['name'];
						$data[] = $addtocart_array;
						
					} 
					$serialize = serialize($data);
					$users_events_table 		=    $wpdb->prefix.'gist_users_events_data';
					$datetime = date("Y-m-d h:i:s");
					$users_table  =    $wpdb->prefix.'gist_users_data';
					$sql_get_id =  $wpdb->get_results("select * from $users_table where cookie_id = '$cookie_id'");
					$id = $sql_get_id[0]->id;
					$sql_add_to_cart = $wpdb->query("INSERT INTO $users_events_table (guest_id,event_name,product,created_at,modified_at)VALUES ('$id','viewed_checkoutpage','$serialize','$datetime','$datetime')");
				}
			} 
			// check if user at product page
			if(is_product()){
				$prod = wc_get_product(get_the_ID());
				$viewed =array();
				$viewed['id'] = get_the_ID();
				$viewed['name'] = $prod->get_title();
				$viewed['price'] = $prod->get_price();
				$terms = get_the_terms(get_the_ID(), 'product_cat' );
				$product_cat_id =array();
				foreach ($terms as $term) {
					$product_cat_id[] = $term->term_id;
				}
				$type = get_term_by( 'id', get_the_ID(), 'product_cat', 'ARRAY_A' );
				$viewed['category'] = $type['name'];
				
				$serializev = serialize($viewed);
				$users_events_tablev 		=    $wpdb->prefix.'gist_users_events_data';
				$datetimev = date("Y-m-d h:i:s");
				$users_tablev  =    $wpdb->prefix.'gist_users_data';
				$sql_get_idv =  $wpdb->get_results("select * from $users_tablev where cookie_id = '$cookie_id'");
				$id = $sql_get_idv[0]->id;
				$sql_add_to_cartv = $wpdb->query("INSERT INTO $users_events_tablev (guest_id,event_name,product,created_at,modified_at)VALUES ('$id','viewed_product','$serializev','$datetimev','$datetimev')");
			}
			
			
				
			// for placed order whose paymnet is completed
			function get_placed_order_detail($order_id){
				global $wpdb;
				$order =  new WC_Order($order_id);
					$cookie_ids = $_COOKIE['guest_user'];
					if($order->has_status('processing')){
						$users_tables  =    $wpdb->prefix.'gist_users_data';
						$data['order'] = [];
						$i=0;
						$order_data = $order->get_data(); // The Order data
						$data['order']["order_number"] = $order_data['id'];
						if($order_data['customer_id'] == ''){
							$sql_get_id_process_order =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
							$ids = $sql_get_id_process_order[0]->id;
							$data['order']["customer_id"] = $ids;
							$data['order']["email"] = '' ;
						}else{
							$data['order']["customer_id"] =$order_data['customer_id'];
							$user = get_user_by( 'ID', $order_data['customer_id']);
							$data['order']["email"] = $user->email;
						}
						
						$data['order']["currency"] = $order_data['currency'];
						$data['order']["shipping_method"] = $order_data['payment_method'];
						$data['order']["order_date"] = $order_data['date_created']->date('Y-m-d H:i:s');
						
						$args = array( 'post_type' => 'product', 'post_status' => 'publish', 
						'posts_per_page' => -1 );
						$productsss = new WP_Query( $args );
						$data['order']["total_products"] = $productsss->found_posts;
						$data['order']["total_price"] =	$order->get_total();
						
						foreach ($order->get_items() as $item_key => $item_values){
							$product_id = $item_values->get_product_id(); // the Product id
							$product = $item_values->get_product();
							// Access Order Items data properties (in an array of values) 
							$item_data = $item_values->get_data();
							$data['order']['order_products'][$i]['product_id'] = $product_id;
							$data['order']['order_products'][$i]['order_id'] = $order_id;
							$data['order']['order_products'][$i]['external_id'] = $product->get_sku();
							$data['order']['order_products'][$i]['product_name'] = $item_data['name'];
							$data['order']['order_products'][$i]['price'] = $product->get_price();
							$data['order']['order_products'][$i]['quantity'] = $item_data['quantity'];
							//get product type by its id ;
							$terms = get_the_terms( $product_id, 'product_cat' );
							$product_cat_id =array();
							foreach ($terms as $term) {
								$product_cat_id[] = $term->term_id;
							}
							$type = get_term_by( 'id', $product_cat_id[0], 'product_cat', 'ARRAY_A' );
							$data['order']['order_products'][$i]['category'] = $type['name'];
							$data['order']['order_products'][$i]['timestamp'] = $order_data['date_created']->getTimestamp();
							$i++;
						};
							$users_events_tables 		=    $wpdb->prefix.'gist_users_events_data';
							$datetime = date("Y-m-d h:i:s");
							$serialize = serialize($data);
							$sql_get_id_process_orders =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
							$idss = $sql_get_id_process_orders[0]->id;
							$sql_add_to_cart = $wpdb->query("INSERT INTO $users_events_tables (guest_id,event_name,product,created_at,modified_at)VALUES ('$idss','placeorder_process','$serialize','$datetime','$datetime')");
							add_post_meta($order_data['id'], 'is_custom_completed',0);
							woocommerce_completed_order();	
							woocommerce_cancelled_order();	
					}	
						
			}
			add_action( 'woocommerce_thankyou', 'get_placed_order_detail', 10, 1 );
		}
		
	}else{
		// here all functionality for login user  get_current_user_id()   id', 'user_id' or 'email
		global $wpdb;
		if(is_user_logged_in() && isset($_COOKIE['guest_user'])){
			
			//update login user to custom table
			$user_id =get_current_user_id();
			$cookieid = $_COOKIE['guest_user'];
			$users_tables  =    $wpdb->prefix.'gist_users_data';
			
			$check_sql_login = $wpdb->get_results("select * from $users_tables where cookie_id = '$cookieid' and login_id = '' ");
			if(empty($check_sql_login)){
				$sql_update_user =  $wpdb->query("UPDATE $users_tables SET login_id = $user_id WHERE cookie_id = '$cookieid'");
			}
			
			$access_token = get_option('saved_access_token_verification' );
			$user_regp = array();
			$userdata = get_userdata(get_current_user_id()); 
			// check if user has gist user id 
			$gistid = '';
			if(get_user_meta(get_current_user_id(), 'gist_user_id',true)){
				$gistid = get_user_meta(get_current_user_id(), 'gist_user_id',true);
			}
			$user_regp['id'] = $gistid;
			$user_regp['email'] = $userdata->user_email;
			$user_regp['name'] = $userdata->display_name;
			$user_regp['customer_since'] = get_user_meta(get_current_user_id(),'session_tokens',true);
			$user_regp['username'] = $userdata->user_login;
			$user_regp['phone'] = get_user_meta(get_current_user_id(),'phone_number',true);
			$user_regp['user_id'] = get_current_user_id();
			$user_regp['web_sessions'] = wp_get_session_token(get_current_user_id());
			$user_regp['last_seen'] = get_user_meta(get_current_user_id(),'last_update',true);
			$json_encode = json_encode($user_regp);
			
			//send data to gist
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, 'https://aws-api-testing.getgist.com/users'); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_encode); 
			curl_setopt($ch, CURLOPT_POST, 1);
			$headers = array(); 
			$headers[] = 'Authorization: Bearer '.$access_token; 
			$headers[] = 'Content-Type: application/json'; 
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
			$result = curl_exec($ch); 
			$getdata = (array)json_decode($result);
			//~ print_r($getdata);die;
			if (curl_errno($ch)) { 
				echo 'Error:' . curl_error($ch);
			} 
			curl_close ($ch); 
			
			// add gist id of user
			add_user_meta(get_current_user_id(), 'gist_user_id', $getdata['user']->id);
			
		}
		
		
	}	
		
		
}
add_shortcode( 'gist_short_code', 'gist_short_code_func');
// insert data of completed orders of woocomerce
function woocommerce_completed_order(){
	global $wpdb;
	$args = array('status' => 'completed',);
	$orderssss =  wc_get_orders($args);
	foreach($orderssss as $datass){
		$order_data = $datass->get_data();
		if(get_post_meta($order_data['id'], 'is_custom_completed', true )==0){
			update_post_meta($order_data['id'],'is_custom_completed',1);
			$cookie_ids ='';
			if(!is_user_logged_in()){
				if (isset($_COOKIE['guest_user'])) {
					$cookie_ids = $_COOKIE['guest_user'];
				}
			}	
			$users_tables  =    $wpdb->prefix.'gist_users_data';
			$data['order'] = [];
			$i=0;
			$order_data = $datass->get_data(); // The Order data
			$data['order']["order_number"] = $order_data['id'];
			if($order_data['customer_id'] == ''){
				$sql_get_id_process_order =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
				$ids = $sql_get_id_process_order[0]->id;
				$data['order']["customer_id"] = $ids;
				$data['order']["email"] = '' ;
			}else{
				$data['order']["customer_id"] =$order_data['customer_id'];
				$user = get_user_by( 'ID', $order_data['customer_id']);
				$data['order']["email"] = $user->email;
			}
			
			$data['order']["currency"] = $order_data['currency'];
			$data['order']["shipping_method"] = $order_data['payment_method'];
			$data['order']["order_date"] = $order_data['date_created']->date('Y-m-d H:i:s');
			
			$args = array( 'post_type' => 'product', 'post_status' => 'publish', 
			'posts_per_page' => -1 );
			$productsss = new WP_Query( $args );
			$data['order']["total_products"] = $productsss->found_posts;
			$data['order']["total_price"] =	$datass->get_total();
			
			foreach ($datass->get_items() as $item_key => $item_values){
				$product_id = $item_values->get_product_id(); // the Product id
				$product = $item_values->get_product();
				// Access Order Items data properties (in an array of values) 
				$item_data = $item_values->get_data();
				$data['order']['order_products'][$i]['product_id'] = $product_id;
				$data['order']['order_products'][$i]['order_id'] = $order_data['id'];
				$data['order']['order_products'][$i]['external_id'] = $product->get_sku();
				$data['order']['order_products'][$i]['product_name'] = $item_data['name'];
				$data['order']['order_products'][$i]['price'] = $product->get_price();
				$data['order']['order_products'][$i]['quantity'] = $item_data['quantity'];
				//get product type by its id ;
				$terms = get_the_terms( $product_id, 'product_cat' );
				$product_cat_id =array();
				foreach ($terms as $term) {
					$product_cat_id[] = $term->term_id;
				}
				$type = get_term_by( 'id', $product_cat_id[0], 'product_cat', 'ARRAY_A' );
				$data['order']['order_products'][$i]['category'] = $type['name'];
				$data['order']['order_products'][$i]['timestamp'] = $order_data['date_created']->getTimestamp();
				$i++;
			};
			
			//get shipping method
			foreach( $datass->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
				$data['order']['order_products']['shipping_meathod']    = $shipping_item_obj->get_method_title();
			}
			
			$users_events_tables 		=    $wpdb->prefix.'gist_users_events_data';
			$datetime = date("Y-m-d h:i:s");
			$serialize = serialize($data);
			$sql_get_id_process_orders =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
			$idss = $sql_get_id_process_orders[0]->id;
			$sql_add_to_cart = $wpdb->query("INSERT INTO $users_events_tables (guest_id,event_name,product,created_at,modified_at)VALUES ('$idss','placeorder_completed','$serialize','$datetime','$datetime')");
			
			
		}
		
	}
		
}
function woocommerce_cancelled_order(){
	global $wpdb;
	$args = array('status' => 'cancelled',);
	$orderssss =  wc_get_orders($args);
	if(count($orderssss) > 0){
		foreach($orderssss as $datass){
			$order_data = $datass->get_data();
			if(!get_post_meta($order_data['id'], 'is_custom_cancelled', true )){
				add_post_meta($order_data['id'], 'is_custom_cancelled',0);
				$cookie_ids ='';
			if(!is_user_logged_in()){
				if (isset($_COOKIE['guest_user'])) {
					$cookie_ids = $_COOKIE['guest_user'];
				}
			}	
			$users_tables  =    $wpdb->prefix.'gist_users_data';
			$data['order'] = [];
			$i=0;
			$order_data = $datass->get_data(); // The Order data
			$data['order']["order_number"] = $order_data['id'];
			if($order_data['customer_id'] == ''){
				$sql_get_id_process_order =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
				$ids = $sql_get_id_process_order[0]->id;
				$data['order']["customer_id"] = $ids;
				$data['order']["email"] = '' ;
			}else{
				$data['order']["customer_id"] =$order_data['customer_id'];
				$user = get_user_by( 'ID', $order_data['customer_id']);
				$data['order']["email"] = $user->email;
			}
			
			$data['order']["currency"] = $order_data['currency'];
			$data['order']["shipping_method"] = $order_data['payment_method'];
			$data['order']["order_date"] = $order_data['date_created']->date('Y-m-d H:i:s');
			
			$args = array( 'post_type' => 'product', 'post_status' => 'publish', 
			'posts_per_page' => -1 );
			$productsss = new WP_Query( $args );
			$data['order']["total_products"] = $productsss->found_posts;
			$data['order']["total_price"] =	$datass->get_total();
			
			foreach ($datass->get_items() as $item_key => $item_values){
				$product_id = $item_values->get_product_id(); // the Product id
				$product = $item_values->get_product();
				// Access Order Items data properties (in an array of values) 
				$item_data = $item_values->get_data();
				$data['order']['order_products'][$i]['product_id'] = $product_id;
				$data['order']['order_products'][$i]['order_id'] = $order_data['id'];
				$data['order']['order_products'][$i]['external_id'] = $product->get_sku();
				$data['order']['order_products'][$i]['product_name'] = $item_data['name'];
				$data['order']['order_products'][$i]['price'] = $product->get_price();
				$data['order']['order_products'][$i]['quantity'] = $item_data['quantity'];
				//get product type by its id ;
				$terms = get_the_terms( $product_id, 'product_cat' );
				$product_cat_id =array();
				foreach ($terms as $term) {
					$product_cat_id[] = $term->term_id;
				}
				$type = get_term_by( 'id', $product_cat_id[0], 'product_cat', 'ARRAY_A' );
				$data['order']['order_products'][$i]['category'] = $type['name'];
				$data['order']['order_products'][$i]['timestamp'] = $order_data['date_created']->getTimestamp();
				$i++;
			};
			
			//get shipping method
			foreach( $datass->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
				$data['order']['order_products']['shipping_meathod']    = $shipping_item_obj->get_method_title();
			}
			
			$users_events_tables 		=    $wpdb->prefix.'gist_users_events_data';
			$datetime = date("Y-m-d h:i:s");
			$serialize = serialize($data);
			$sql_get_id_process_orders =  $wpdb->get_results("select * from $users_tables where cookie_id = '$cookie_ids'");
			$idss = $sql_get_id_process_orders[0]->id;
			$sql_add_to_cart = $wpdb->query("INSERT INTO $users_events_tables (guest_id,event_name,product,created_at,modified_at)VALUES ('$idss','placeorder_completed','$serialize','$datetime','$datetime')");
			}
			
			
		}		
	}
}
