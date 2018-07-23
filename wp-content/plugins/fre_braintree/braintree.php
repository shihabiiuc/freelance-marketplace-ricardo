<?php
/**
 * Class Braintree
 * @since 1.0
 * @author ThanhTu
 */
class AE_Braintree extends AE_Base{
	private static $instance;
    public $merchant_id;
    public $public_key;
    public $private_key;
    public $merchant_account_id;
    /**
     * getInstance method
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * The constructor
     * @since 1.0
     * @author ThanhTu
     */
    private function __construct() {
        $this->init();
        
    }
    /**
     * Init for class AE_Braintree
     * @param void
     * @return void
     * @since 1.0
     * @category BRAINTREE
     * @author ThanhTu
     */
    public function init(){
        
        $braintree_api = ae_get_option('braintree_api');
        $this->merchant_account_id = isset($braintree_api['merchant_account_id']) ? $braintree_api['merchant_account_id'] : '';
        $this->merchant_id = isset($braintree_api['merchant_id']) ? $braintree_api['merchant_id'] : '';
        $this->public_key = isset($braintree_api['public_key']) ? $braintree_api['public_key'] : '';
        $this->private_key = isset($braintree_api['private_key']) ? $braintree_api['private_key'] : '';
        $this->init_ajax();
    }
     /**
     * Put all ajax function here
     * @param void
     * @return void
     * @since 1.0
     * @category BRAINTREE
     * @author ThanhTu
     */
    public function init_ajax(){
        $this->add_action( 'after_payment_list', 'ae_braintree_render_button');
        $this->add_action( 'after_payment_list_upgrade_account', 'ae_braintree_render_button');
        $this->add_filter( 'ae_setup_payment', 'ae_braintree_setup_payment', 10, 3);
        $this->add_filter( 'ae_support_gateway', 'ae_braintree_add' );
        $this->add_filter( 'ae_process_payment', 'ae_braintree_process_payment', 10 ,2 );
        $this->add_action('ae_payment_script', 'ae_braintree_add_script');
    }
    /**
     * Add Support Gateway
     * @since 1.0
     * @author ThanhTu
     */
    function ae_braintree_add($gateways){
        $gateways['braintree'] = 'Braintree';
        return $gateways;
    }
    /**
     * Get Braintree Api
     * @since 1.0
     * @author ThanhTu
     */
    public function get_braintree_api(){
    	require_once dirname(__FILE__) . '/lib/Braintree.php';
        // Check test mode
        $testmode = ae_get_option('test_mode');
        $mode = 'sandbox';
        if(!$testmode) $mode = 'production';
        // Check connect
        if(empty($this->merchant_id) && empty($this->public_key) && empty($this->private_key)){
            return false;
        }else{
    		try {
                Braintree_Configuration::environment($mode); // 'production' : 'sandbox'
    			Braintree_Configuration::merchantId($this->merchant_id);
    			Braintree_Configuration::publicKey($this->public_key);
    			Braintree_Configuration::privateKey($this->private_key);
    		} catch (Exception $e) {
    	    	return false;
    		}
    		return true;
        }
    }

    function ae_braintree_add_script() {
        global $user_ID, $ae_post_factory;
        $ae_pack = $ae_post_factory->get('pack');
        $packs = $ae_pack->fetch();
        wp_enqueue_style('ae_braintree', plugin_dir_url(__FILE__) . 'assets/braintree.css', array() , '1.0');
        wp_enqueue_script('ae_braintree', plugin_dir_url(__FILE__) . 'assets/braintree.js', array(
            'underscore',
            'backbone',
            'appengine'
        ) , '1.0', true);

        // Library support for Braintree
        wp_enqueue_script( 'ae-braintree-client', 'https://js.braintreegateway.com/web/3.6.0/js/client.min.js', array(), null, true );
        wp_enqueue_script( 'ae-braintree-min', 'https://js.braintreegateway.com/js/braintree-2.30.0.min.js', array(), null, true );
        wp_localize_script('ae_braintree', 'ae_braintree', array(
            'currency' => ae_get_option('currency') ,
            'pack' => $packs
        ));
    }
    /**
     * render button
     * @since 1.0
     * @author ThanhTu
     */
    public function ae_braintree_render_button(){
        if($this->get_braintree_api()){
            try {
                $clientToken = Braintree_ClientToken::generate();
            } catch (Exception $e) { }
            if(isset($clientToken) && !empty($clientToken)){
                wp_localize_script( 'ae_braintree', 'Braintree_params', array(
                    'client_token'           =>  $clientToken
                ) );
    ?>
                <li class="panel">
                    <span class="title-plan braintree-payment" data-type="braintree">
                        <?php _e("Braintree", ET_DOMAIN); ?>
                        <span><?php _e("Send your payment to our Braintree account", ET_DOMAIN); ?></span>
                    </span>
                    <a data-toggle="collapse" data-type="braintree" data-parent="#fre-payment-accordion" href="#fre-payment-braintree" class="btn collapsed other-payment"><?php _e("Select", ET_DOMAIN); ?></a>
                    <?php include_once dirname(__FILE__) . '/form-template.php'; ?>
                </li>
    <?php 
            }
        }
    }
    /**
     * Setup payment for Braintree
     * @author ThanhTu
     */
    function ae_braintree_setup_payment($response, $paymentType, $order) {
        $getBraintree = $this->get_braintree_api();
        if ($paymentType == 'BRAINTREE' && $getBraintree) {
            global $user_email;
            $current_user = wp_get_current_user();
            $request = $_POST;
            $order_pay = $order->generate_data_to_pay();
            $amount = $order_pay['total'];
            // Check merchant account ID
            if(!empty($this->merchant_account_id)){
                try{
                    $merchantAccount = Braintree_MerchantAccount::find($this->merchant_account_id);
                    if(isset($merchantAccount->currencyIsoCode) && ($merchantAccount->currencyIsoCode != $order_pay['currencyCodeType'])){
                        $response = array(
                            'success'       => false,
                            'msg'           => __('Invalid currency', ET_DOMAIN),
                            'paymentType'   => 'braintree'
                        );
                        return $response;
                    }
                } catch (Exception $e){
                    $response = array(
                        'success'       => false,
                        'msg'           => $e->getMessage(),
                        'paymentType'   => 'braintree'
                    );
                    return $response;
                }
            }

            //BrainTree payment process
            $result = Braintree_Transaction::sale(array(
                'amount'                => (float) $amount,
                'paymentMethodNonce'    => $_POST["braintree-payment-nonce"],
                'customer'              => array(
                    'firstName'     => $current_user->user_firstname,
                    'lastName'      => $current_user->user_lastname ,
                    "email"         => $user_email,
                ),
                'merchantAccountId' => $this->merchant_account_id, // Currency is also determined by merchant account ID
                'options'               => array(
                    'submitForSettlement' => True
                )
            ));

            if ($result->success) {
                $id = $result->transaction->id;
                $token = md5($id);
                $order->set_payment_code($token);
                $order->set_payer_id($id);
                $order->update_order();

                $returnURL = et_get_page_link('process-payment', array(
                        'paymentType'   => 'braintree',
                        'token'         => $token,
                        'order-id'      => $order_pay['ID']
                    ));
                $response = array(
                    'success'       => true,
                    'data'          => array( 'url' => $returnURL ),
                    'paymentType'   => 'braintree'
                );
            }else if($result->transaction){
                $notice = sprintf( __( 'Payment declined.<br />Error: %s<br />Code: %s', ET_DOMAIN ), 
                                $result->message,
                                $result->transaction->processorResponseCode );
                $response = array(
                    'success'       => false,
                    'msg'           => $notice,
                    'paymentType'   => 'braintree'
                );
            }else{
                $exclude = array( 81725 ); //Credit card must include number, paymentMethodNonce, or venmoSdkPaymentMethodCode.
                $notice = '';
                foreach ( ($result->errors->deepAll() ) as $error ) {
                    if( !in_array( $error->code, $exclude ) ) {
                        $notice = sprintf(__('Error - %s', ET_DOMAIN), $error->message );
                    }
                }
                $response = array(
                    'success'       => false,
                    'msg'           => $notice,
                    'paymentType'   => 'braintree'
                );
            }
        }
        return $response;
    }
    /**
     * Process payment of Braintree
     * @author ThanhTu
     */
    function ae_braintree_process_payment($payment_return, $data){
        $getBraintree = $this->get_braintree_api();
        $payment_type = $data['payment_type'];
        $order = $data['order'];
        if( $payment_type == 'braintree') {
            if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
                $payment_return =   array (
                    'ACK'               => true,
                    'payment'           => 'braintree',
                    'payment_status'    => 'Completed'
                );
                $order->set_status('publish');
                $order->update_order();
            }else{
                $payment_return =   array (
                    'ACK'               => false,
                    'payment'           => 'braintree',
                    'payment_status'    => 'Completed',
                    'msg'               => __('Braintree payment method false.', ET_DOMAIN)
                );
            }
        }
        return $payment_return;
    }
}