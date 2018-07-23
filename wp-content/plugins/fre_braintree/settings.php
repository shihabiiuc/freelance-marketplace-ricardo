<?php 
/**
 * hook filter to menu page in admin
 * @author ThanhTu
 */
add_filter('ae_admin_menu_pages','ae_braintree_add_settings');
function ae_braintree_add_settings($pages){
	$sections = array();
	$options = AE_Options::get_instance();
	/**
	 * ae fields settings
	 */
	$api_link = " <a class='find-out-more' target='_blank' href='https://articles.braintreepayments.com/control-panel/important-gateway-credentials#api-credentials' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";
	$sections = array(
		'args' => array(
			'title' => __("Braintree API", ET_DOMAIN) ,
			'id' => 'meta_field',
			'icon' => 'F',
			'class' => ''
		) ,
		'groups' => array(
			array(
				'args' => array(
					'title' => __("Braintree API", ET_DOMAIN) ,
					'id' => 'secret-key',
					'class' => '',
					'desc' => __('Set up the exact Braintree API so that this payment gateway can display on the front-end.', ET_DOMAIN) . $api_link,
					'name' => 'braintree_api'
				) ,
				'fields' => array(
					array(
						'id' => 'merchant_account_id',
						'type' => 'text',
						'label' => __("Merchant Account ID", ET_DOMAIN). '<p style="font-weight: 100; margin: 5px 0; cursor: text;">'.__("Merchant Account ID must be a unique account & match with the payment currency on your site.", ET_DOMAIN).'</p>',
						'name' => 'merchant_account_id',
						'class' => ''
					),
					array(
						'id' => 'merchant_id',
						'type' => 'text',
						'label' => __("Merchant ID", ET_DOMAIN) ,
						'name' => 'merchant_id',
						'class' => ''
					),
					array(
						'id' => 'public_key',
						'type' => 'text',
						'label' => __('Public Key', ET_DOMAIN),
						'name'  => 'public_key',
						'class' => ''
					),
					array(
						'id' => 'private_key',
						'type' => 'text',
						'label' => __('Private Key', ET_DOMAIN),
						'name'  => 'private_key',
						'class' => ''
					)
				)
			) 
		)
	);

	$temp = new AE_section($sections['args'], $sections['groups'], $options);

	$braintree_setting = new AE_container(array(
		'class' => 'field-settings',
		'id' => 'settings',
	) , $temp, $options);

	$pages[] = array(
		'args' => array(
			'parent_slug' => 'et-overview',
			'page_title' => __('Braintree', ET_DOMAIN) ,
			'menu_title' => __('BRAINTREE', ET_DOMAIN) ,
			'cap' => 'administrator',
			'slug' => 'ae-braintree',
			'icon' => '$',
			'desc' => __("Integrate the Braintree payment gateway to your site", ET_DOMAIN)
		) ,
		'container' => $braintree_setting
	);
	return $pages;
}