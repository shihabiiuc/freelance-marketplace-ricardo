<?php
	/**
* @author 		EngineThemes
* @copyright 	2015
* @package 		Appengine - Pin Payment
Plugin Name:	FrE Pin
Plugin URI: 	http://enginethemes.com/
Description:	Integrates the Pin payment gateway to your FreelanceEngine site
Version:		1.1
Author:			Enginethemes
Lisence:		GPLv2
Text Domain:	Enginethemes
*/

/*
* setup admin option
*/
add_filter ('ae_admin_menu_pages', 'ae_pin_add_setting');
function ae_pin_add_setting($pages){
	$sections 	= array();
	$options	= AE_Options::get_instance();

	/**
	* ae fields settings
	*/

	$sections = array(
		'args' =>array(
			'title' => __("Pin KPI", ET_DOMAIN),
			'id'	=> 'pin_field',
			'icon'	=> 'a',
			'icon_class' => 'fa fa-money',
			'class'	=> ''
		),
		'groups' =>array(
			array(
				'args' => array(
					'title' => __("Pin API", ET_DOMAIN),
					'id'	=> 'SECRET_KEY',
					'class'	=>'',
					'desc'	=> __('Testing environment:
								<p>For Pin Checkout:
								<p>Secret key: rrybSGk-eKx-oaBdSFv-CA
								<p>Publishable key: pk_Az3-JIm4U2WK4Z7GQbmuig', ET_DOMAIN),
					'name'	=> 'pin'
				),
				'fields' => array(
					array(
						'id' 	=> 'SECRET_KEY',
						'type' 	=> 'text',
						'label'	=> __('SECRET KEY', ET_DOMAIN),
						'name'	=> 'secret_key',
						'class'	=> ''
					),
					array(
						'id' 	=> 'PUBLISHABLE_KEY',
						'type'	=> 'text',
						'label'	=> __('PUBLISHABLE KEY', ET_DOMAIN),
						'name'	=> 'publishable_key',
						'class'	=> ''
					)
				)
			)
		)
	);

	$temp = new AE_section($sections['args'], $sections['groups'], $options);

	$pin_setting = new AE_container(array(
		'class' => 'field-settings',
        'id' => 'settings',
	), $temp, $options);

	$pages[] = array(
		'args'	=>	array(
			'parent_slug'	=>	'et-overview',
			'page_title'	=>	__('Pin', ET_DOMAIN),
			'menu_title'	=>	__('PIN', ET_DOMAIN),
			'cap'			=>	'administrator',
			'slug'			=>	'ae-pin',
			'icon'			=>	'@',
			'icon_class'	=>	'fa fa-cart-plus',
			'desc'			=> __("Integrate the Pin payment gateway to your site", ET_DOMAIN)
		),
		'container'	=>	$pin_setting
	);

	return $pages;
}
add_filter('ae_support_gateway', 'ae_pin_add_support');
function ae_pin_add_support($gateways){
	$gateways['pin'] = 'Pin';
	return $gateways;
}

/**
* render pin button
*/

add_action('after_payment_list', 'ae_pin_render_button');
add_action('after_payment_list_upgrade_account', 'ae_pin_render_button');
function ae_pin_render_button(){
	$klarna_key = ae_get_option('pin');
	if (!$klarna_key['secret_key'] || !$klarna_key['publishable_key']) return false;
?>
	<li class="panel">
		<span class="title-plan pin-payment" data-type="pin">
			<?php _e("Pin", ET_DOMAIN); ?>
			<span><?php _e("Send your payment to our Pin account", ET_DOMAIN); ?></span>
		</span>
		<!-- <a href="#" class="btn btn-submit-price-plan other-payment" data-type="pin"><?php
			_e("select", ET_DOMAIN); ?>
		</a> -->
		<a data-toggle="collapse" data-type="pin" data-parent="#fre-payment-accordion" href="#fre-payment-pin" class="btn collapsed other-payment"><?php _e("Select", ET_DOMAIN); ?></a>
        <?php include_once dirname(__FILE__) . '/form-template.php'; ?>
	</li>
<?php
}

/*
 * init script
*/
add_action('ae_payment_script', 'ae_pin_add_script'); 
function ae_pin_add_script() {
	global $user_ID, $ae_post_factory;
	$ae_pack = $ae_post_factory->get('pack');
	$packs = $ae_pack->fetch();
	wp_enqueue_style('ae_pin', plugin_dir_url(__FILE__) . 'assets/pin.css', array() , '1.0');
	wp_enqueue_script('ae_country', plugin_dir_url(__FILE__) . 'assets/country.js', '', '1.0');
	wp_enqueue_script('pin.checkout', 'https://cdn.pin.net.au/pin.v2.js');
	wp_enqueue_script('ae_pin', plugin_dir_url(__FILE__) . 'assets/pin.js', array(
		'underscore',
		'backbone',
		'appengine'
	), 	'1.0', true);
	$pin_key = ae_get_option('pin');
	wp_localize_script('ae_pin', 'ae_pin', array(
		'public_key' => $pin_key['publishable_key'],
		'currency' => ae_get_option('currency') ,
		'card_number_msg' => __('The Credit card number is invalid.', ET_DOMAIN) ,
		'name_card_msg' => __('The name on card is invalid.', ET_DOMAIN) ,
		'transaction_success' => __('The transaction completed successfull!.', ET_DOMAIN) ,
		'transaction_false' => __('The transaction was not completed successfull!.', ET_DOMAIN) ,
		'pack' => $packs,
		'ip_address'=>$_SERVER['REMOTE_ADDR']
	));
}
add_action('wp_print_scripts','ae_pin_script');
function ae_pin_script(){
	if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
		global $user_ID, $ae_post_factory;
		$ae_pack = $ae_post_factory->get('pack');
		$packs = $ae_pack->fetch();
		wp_enqueue_style('ae_pin', plugin_dir_url(__FILE__) . 'assets/pin.css', array() , '1.0');
		wp_enqueue_script('ae_country', plugin_dir_url(__FILE__) . 'assets/country.js', '', '1.0');
		wp_enqueue_script('pin.checkout', 'https://cdn.pin.net.au/pin.v2.js');
		wp_enqueue_script('ae_pin', plugin_dir_url(__FILE__) . 'assets/pin.js', array(
			'underscore',
			'backbone',
			'appengine'
		), 	'1.0', true);
		$pin_key = ae_get_option('pin');
		wp_localize_script('ae_pin', 'ae_pin', array(
            'public_key' => $pin_key['publishable_key'],
            'currency' => ae_get_option('currency') ,
            'card_number_msg' => __('The Credit card number is invalid.', ET_DOMAIN) ,
            'name_card_msg' => __('The name on card is invalid.', ET_DOMAIN) ,
            'transaction_success' => __('The transaction completed successfull!.', ET_DOMAIN) ,
            'transaction_false' => __('The transaction was not completed successfull!.', ET_DOMAIN) ,
            'pack' => $packs,
            'ip_address'=>$_SERVER['REMOTE_ADDR']
		));
	}
}
// add_action('wp_footer', 'ae_pin_form_template');
// function ae_pin_form_template() {
// 	if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
// 	    include_once dirname(__FILE__) . '/form-template.php';
// 	}
// }
/*
 * Pin setup payment
*/
add_filter('ae_setup_payment', 'ae_pin_setup_payment', 10,3);
function ae_pin_setup_payment($response, $paymentType, $order){

	if ($paymentType == 'PIN') {

		/*
		 *setup in payment
		 */

		$order_pay = $order->generate_data_to_pay();

		$products = array_pop($order_pay['products']);

		$pin_key = ae_get_option('pin');

		// get token from responses
		$token = $_POST['token'];

		global $user_email;

		$test_mode = ET_Payment::get_payment_test_mode();
		$endpoint = 'https://api.pin.net.au/1/charges';
		if ($test_mode){
			$endpoint = 'https://test-api.pin.net.au/1/charges';
		}

		// Keys
		$publishable 	= $pin_key['publishable_key'];
		$secret 		= $pin_key['secret_key'];

		// encode to base64
		$secretApi = base64_encode($secret);

		// setup data
		$data = array(
		  'email'       => $user_email,
		  'description' => $products['NAME'],
		  'amount'      => $order_pay['total']*100,
		  'currency'    => $order_pay['currencyCodeType'],
		  'card_token'  => $token
		  // 'ip_address'  => $ip_address
		);

        // setup args
		$args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . $secretApi,
			),
			'timeout' => 12,
			'sslverify' => false,
			'body' => $data,
			'method' => 'POST'
		);

		// post data to server
		$httpRequest = wp_remote_post( $endpoint, $args );
		$response_code = wp_remote_retrieve_response_code( $httpRequest );
		$response_body = json_decode( wp_remote_retrieve_body( $httpRequest ), true );

		$status_message = $response_body['response']['status_message'];

		// success URL
		$success_url = et_get_page_link('process-payment', array(
            'paymentType' => 'pin',
            'token' =>$token,
            'status'=>$status_message,
            'order-id'	=> $order_pay['ID']
        ));

        // cancel URL
        $cancelURL = et_get_page_link('process-payment', array(
        	'paymentType' => 'pin'
        ));

		// check status message response
		if (isset($response_body) && $status_message == 'Success') {
            $response = array(
                'success' => true,
                'data' => array(
                    'url' => $success_url,
                    'msg' => __('Transaction completed successfull!', ET_DOMAIN)
                ) ,
                'paymentType' => 'pin'
            );
    	} else {
    		$response = array(
    			'success' => fail,
    			'data' => array(
    				'url' => $cancelURL,
    				'paymentType' => 'pin',
    				'msg' => __('Pin payment method false', ET_DOMAIN)
    			)
    		);
    	}

	}
	return $response;

}
/*
 * Process payment
 */

add_filter('ae_process_payment', 'ae_pin_process_payment', 10 , 2 );
function ae_pin_process_payment($payment_return, $data){

	$payment_type = $data['payment_type'];
	$status_message = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
	if ($payment_type = 'pin' && $status_message =='Success'){
		$order = $data['order'];
		$token = $_REQUEST['token'];
		

		if ( isset( $token ) ) {

			$payment_return = array(
				'ACK'		=> true,
				'payment'	=> 'pin',
				'payment_status' => 'Completed'
			);

			$order->set_status ('publish');
			$order->update_order();

		}else{

			$payment_type = array(
				'ACK'		=> false,
				'payment'	=> 'pin',
				'payment_status'	=> 'Uncompleted',
				'msg'		=> __('Pin payment method false', ET_DOMAIN)
			);

		}

	}

	return $payment_return;

}
