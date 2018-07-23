<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 3:51 PM
 */
class FRE_Credit_Employer extends FRE_Credit_Users{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct(){
        //-----------------------------
    }
    /**
      * unit function of this class
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function init(){
        //filter gateway to payment method
        $this->add_filter( 'ae_support_gateway', 'fre_credit_support' );
        $this->add_action('after_payment_list', 'fre_credit_render_button');
        $this->add_action('after_payment_list_upgrade_account', 'fre_credit_render_button');
        $this->add_action('wp_footer', 'fre_credit_add_modal');
        $this->add_filter('ae_setup_payment', 'fre_credit_setup_payment', 10, 3);
        $this->add_filter( 'ae_process_payment', 'fre_credit_process_payment', 10 ,2 ); // 1.2.3 will use

        $this->add_filter('ae_nopack_product_info', 'ae_nopack_product_info_data', 10, 2); //@since 1.2.3
    }
    /**
      * add credit gateway
      *
      * @param array $gateways
      * @return array $gateways
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_support($gateways){
        $gateways['frecredit'] = 'frecredit';
        return $gateways;
    }
    /**
      * render button payment
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_render_button(){
        $page = ae_get_option('fre_credit_deposit_page_slug', false);
        if( !$page || !is_page($page) ) {
            fre_credit_template_payToSubmitProject_button();
        }
    }
    /**
      * add modal html
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_add_modal(){
        include_once dirname(__FILE__) . '/template/form-template.php';
    }
    /**
      * submit payment
      *
      * @param array $order
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_submit_project_payment($order){
        global $user_ID;
        $ae_package = AE_Package::get_instance();
        $response = array(
            'success'=> false,
            'msg'=> __("Please select a package plan!", ET_DOMAIN)
        );
        $packType = isset($_REQUEST['packageType']) ? $_REQUEST['packageType'] : 'pack';
        $package = $ae_package->get_pack($order['payment_package'], $packType);
        if( $package ){
            $charge_obj = array(
                'amount' => (float)$package->et_price,
                'currency' => fre_credit_get_payment_currency(),
                'customer' => $user_ID,
                'post_title'=> 'Paid'
            );

            $user_wallet = FRE_Credit_Users()->getUserWallet($user_ID);
            $number = FRE_Credit_Currency_Exchange()->exchange($charge_obj['amount'], $charge_obj['currency'], $user_wallet->currency);
            $wallet = new FRE_Credit_Wallet($number, $user_wallet->currency);
            $result = FRE_Credit_Users()->checkBalance($user_ID, $wallet);

            if( $result >= 0 ){
                $this->updateUserBalance($user_ID, $result);
                $response = array(
                    'success'=> true,
                    'msg'=> __("Payment success!", ET_DOMAIN)
                );
            }
            else{
                $response = array(
                    'success'=> false,
                    'msg'=> __("You don't have enough money in your wallet!", ET_DOMAIN)
                );
            }
        }
        return $response;
    }
    /**
      * filter setup payment
      *
      * @param array $response
     * @param string $paymentType
     * @param array $order
      * @return array $response
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_setup_payment($response, $paymentType, $order){
      // fre_credit_fix or
      // var_dump($paymentType);
      // var_dump($response);
      //var_dump($paymentType);
        if ($paymentType == 'FRECREDIT') {
            $resp = array(
                'success' => false,
                'paymentType' => $paymentType,
                'msg' => __('Please enter a valid secure code!', ET_DOMAIN)
            );
            global $user_ID;
            if( ae_get_option('fre_credit_secure_code', true) ){
                if(!isset($_REQUEST['secureCode']) || empty($_REQUEST['secureCode'])){
                    return $resp;
                }
                else{
                    $flag = $this->checkSecureCode($user_ID, $_REQUEST['secureCode']);
                    if( !$flag ){
                        return $resp;
                    }
                }
            }

            $order_pay = $order->generate_data_to_pay();
            $result = $this->fre_submit_project_payment($order_pay);
            if( $result['success'] ){
                $id = time();
                $token = md5($id);
                $order->set_payment_code($token);
                $order->set_payer_id($id);
                $order->update_order();
                $returnURL = et_get_page_link('process-payment', array(
                    'paymentType' => 'frecredit',
                    'token' => $token,
                    'success' => true,
                    'order-id' => $order_pay['ID'],
                    'packageType' => isset($_REQUEST['packageType']) ? $_REQUEST['packageType'] : true
                ));
                $response = array(
                    'success' => true,
                    'data' => array(
                        'url' => $returnURL
                    ) ,
                    'paymentType' => 'frecredit'
                );
                $history_obj = array(
                    "amount" => (float)$order_pay['total'], // amount in cents
                    "currency" => fre_credit_get_payment_currency(),
                    "destination" => '',
                    'commission_fee' => 0,
                    "statement_descriptor" => __(" to post a project", ET_DOMAIN),
                    'source_transaction' => '',
                    'post_title'=> 'Paid',
                    'history_type'=> 'charge'
                );
                if(isset($_REQUEST['packageType'])){
                    // Payment for Bid Package
                    $product = current($order_pay['products']);
                    $name = $product['NAME'];
                    $history_obj['package_name'] = $name;
                }
                if(isset($_REQUEST['ID'])){
                    $history_obj['payment'] = $_REQUEST['ID'];
                }
                $history_obj['status'] = 'completed';
                FRE_Credit_History()->saveHistory($history_obj);

	            // update set current order for user when payment credit
	            $ae_package = AE_Package::get_instance();
	            $packType = isset($_REQUEST['packageType']) ? $_REQUEST['packageType'] : 'pack';
	            $package = $ae_package->get_pack($order_pay['payment_package'], $packType);
	            $set_current_oder  = AE_Payment::update_current_order($user_ID,$package->sku,$order_pay['ID']);

	            //update package data
	            AE_Package::add_package_data($package->sku,$user_ID);
	            AE_Package::update_package_data($package->sku,$user_ID);
            }
            else{
                $response = array(
                    'success' => false,
                    'paymentType' => $paymentType,
                    'msg' => $result['msg']
                );
            }
        } else if( $paymentType =="fre_credit_fix"){


        }
        return $response;
    }
    /**
     * @author danng
     * @since  version 1.2.3
     * generater
    */
     function ae_nopack_product_info_data($request){
      $packageType = isset($request['packageType']) ? $request['packageType']: 0;
      if( $packageType == 'fre_credit_fix' ){
        $id = $request['ID'];

        $product_info= array(
            'ID' => $id,
            'NAME' => 'buy-credit-fix',
            'AMT' => (int) get_post_meta($id,'bid_budget', true),
            'QTY' => 1,
            'L_DESC' => 'Buy credit for  project',
            'TYPE' => 'fre_credit_plan',
            'post_id' => $id,
            'post_type' => 'fre_credit_fix',
           );
        return $product_info;
      }

    }
    /**
      * filter process payment
      *
      * @param void
      * @return array $paymentReturn
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_process_payment( $payment_return, $data ){
        $payment_type = $data['payment_type'];
        $order = $data['order'];
        if( $payment_type == 'frecredit') {
            if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
                $payment_return	=	array (
                    'ACK' 			=> true,
                    'payment'		=>	'FRE-CREDIT',
                    'payment_status' =>'Completed'

                );
                $order->set_status ('publish');
                $order->update_order();
                update_post_meta($data['ad_id'], 'status', 'completed');
            } else {
                $payment_return	=	array (
                    'ACK' 			=> false,
                    'payment'		=>	'FRE-CREDIT',
                    'payment_status' =>'Completed',
                    'msg' 	=> __('FrE credit payment method false.', ET_DOMAIN)

                );
                update_post_meta($order['order_id'], 'status', 'failed');
            }
        }
        return $payment_return;
    }
}