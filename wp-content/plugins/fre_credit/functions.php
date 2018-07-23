<?php
/**
 * Plugin  function
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Jack Bui
*/
if( !function_exists('FRE_Credit_Users')){
    /**
      * get instance of class FRE_Credit_Users
      *
      * @param void
      * @return FRE_Credit_Users $instance
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function FRE_Credit_Users(){
        return FRE_Credit_Users::getInstance();
    }
}

if( !function_exists('FRE_Credit_Wallet')){
    /**
     * get instance of class FRE_Credit_Wallet
     *
     * @param void
     * @return FRE_Credit_Wallet $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Wallet(){
        return FRE_Credit_Wallet::getInstance();
    }
}
if( !function_exists('FRE_Credit_Currency')){
    /**
     * get instance of class FRE_Credit_Currency
     *
     * @param void
     * @return FRE_Credit_Currency $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Currency(){
        return FRE_Credit_Currency::getInstance();
    }
}
if( !function_exists('FRE_Credit_Currency_Exchange')){
    /**
     * get instance of class FRE_Credit_Currency_Exchange
     *
     * @param void
     * @return FRE_Credit_Currency_Exchange $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Currency_Exchange(){
        return FRE_Credit_Currency_Exchange::getInstance();
    }
}
if( !function_exists('FRE_Credit_Plan_Posttype')){
    /**
     * get instance of class FRE_Credit_Plan_Posttype
     *
     * @param void
     * @return FRE_Credit_Currency $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Plan_Posttype(){
        return FRE_Credit_Plan_Posttype::getInstance();
    }
}
if( !function_exists('FRE_Credit_Escrow')){
    /**
     * get instance of class FRE_Credit_Escrow
     *
     * @param void
     * @return FRE_Credit_Escrow $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Escrow(){
        return FRE_Credit_Escrow::getInstance();
    }
}
if( !function_exists('FRE_Credit_History')){
    /**
     * get instance of class FRE_Credit_History
     *
     * @param void
     * @return FRE_Credit_History $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_History(){
        return FRE_Credit_History::getInstance();
    }
}
if( !function_exists('FRE_Credit_Withdraw')){
    /**
     * get instance of class FRE_Credit_Withdraw
     *
     * @param void
     * @return FRE_Credit_Withdraw $instance
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function FRE_Credit_Withdraw(){
        return FRE_Credit_Withdraw::getInstance();
    }
}
if( !function_exists('fre_credit_get_payment_currency') ){
    /**
     * get site payment currency
     *
     * @param void
     * @return FRE_Credit_Currency $currency
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_payment_currency(){
        $currency = ae_get_option('currency', false);
        $code = 'usd';
        $signal = '$';
        $rate_exchange = 1;
        if( $currency ){
           $code = $currency['code'];
           $signal = $currency['icon'];
        }
        if(isset($currency['rate_exchange']) ){
            $rate_exchange = $currency['rate_exchange'];
        }
        $currency = new FRE_Credit_Currency($code, $signal, true, $rate_exchange);
        return $currency;
    }

}
if( !function_exists('fre_credit_convert_wallet') ){
    /**
      * convert a number to wallet
      *
      * @param float $number
      * @return FRE_Credit_Wallet $wallet
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_convert_wallet( $number = 0 ){
        if( null == $number || empty($number) ){
            $number = 0;
        }
        $currency = fre_credit_get_payment_currency();
        $wallet = new FRE_Credit_Wallet($number, $currency);
        return $wallet;
    }
}
if( !function_exists('is_use_credit_escrow') ){
    /**
     * Check if use credit escrow
     * @param void
     * @return bool true/false, true if use stripe escrow and false if don't
     * @since 1.0
     * @package AE_ESCROW
     * @category STRIPE
     * @author Jack Bui
     */
    function  is_use_credit_escrow(){
        $credit_api = ae_get_option( 'escrow_credit_settings' );
        return apply_filters( 'use_credit_escrow', $credit_api['use_credit_escrow'] );
    }
}
if( !function_exists('fre_parse_form_data') ) {
    /**
     * description
     *
     * @param string $data
     * @return array $data
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_parse_form_data($data){
        $array = array();
        if( empty($data) ){
            return $array;
        }
        $data = explode('&', $data);
        foreach( $data as $key => $value ){
            $data_arr = explode('=', $value);
            $array[$data_arr['0']] = $data_arr['1'];
        }
        return $array;
    }
}
if( !function_exists('fre_credit_get_user_total_balance') ){
    /**
     * get user balance
     *
     * @param integer $user_id
     * @return FRE_Credit_Wallet $available
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_user_total_balance($user_id){
        $available = FRE_Credit_Users()->getUserWallet($user_id);
	    //$freezable = FRE_Credit_Users()->getUserWallet($user_id, 'freezable');
	    //$available->balance = $available->balance + $freezable->balance;
	    $total_withdraw = credit_get_total_withdraw($user_id);
	    $user_role = ae_user_role($user_id);
	    if($user_role == 'freelancer'){
		    $total_project_working = credit_get_total_project_working($user_id);
		    $available->balance = $available->balance + $total_withdraw + $total_project_working;
	    }else{
	    	$total_project_payment = credit_get_total_project_payment($user_id);
		    $available->balance = $available->balance + $total_withdraw + $total_project_payment;
	    }

        return $available;
    }
}
if( !function_exists('fre_credit_balance_info') ){
    /**
     * render json about balance infor
     *
     * @param integer $user_id
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_balance_info($user_id){
        $total = fre_credit_get_user_total_balance($user_id);
        $available = FRE_Credit_Users()->getUserWallet($user_id);
        $freezable = FRE_Credit_Users()->getUserWallet($user_id, 'freezable');
        $minimum = ae_get_option('fre_credit_minimum_withdraw', 50);
        $balance_info = array(
            'total_text'=>  fre_price_format($total->balance),
            'available_text'=>fre_price_format($available->balance),
            'freezable_text'=> fre_price_format($freezable->balance),
            'total'=> $total,
            'available'=> $available,
            'freezable'=> $freezable,
            'min_withdraw'=> $minimum,
            'min_withdraw_text'=> fre_price_format($minimum)
        );
        return $balance_info;
    }
}
/**
  * deposit page link
  *
  * @param void
  * @return string link of deposit page
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_deposit_page_link(){
    $page = ae_get_option('fre_credit_deposit_page_slug', false);
    if( $page ){
        if(is_numeric($page)){
            $link = get_permalink( get_page( $page ) );
        }else{
            $link = get_permalink( get_page_by_title( $page ) );
        }
        return $link;
    }
    return home_url();
}
/**
  * get admin email
  *
  * @param void
  * @return string $email
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_get_admin_email(){
    $email = ae_get_option('fre_credit_admin_emails', get_option('admin_email'));
    if( !$email ){
        $email = get_option('admin_email');
    }
    return apply_filters('fre_credit_admin_email', $email);
}


if( !function_exists('fre_credit_get_withdraw_email_content') ) {
    /**
     * get email content
     *
     * @param integer $amount
     * @param string $msg
     * @return $message
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_get_withdraw_email_content($amount, $msg)
    {
        if(ae_get_option('fre_credit_withdraw_mail_template')){
            $message = ae_get_option('fre_credit_withdraw_mail_template');
        }else{
            $message = "<p>Hi,</p>
                        <p>There is a withdraw request on your site.</p>
                        <p>Name: [display_name]</p>
                        <p>Amount: [amount]</p>
                        <p>Node: [message]</p>
                        <p>Sincerely,</p>
                        <p>[blogname]</p>";
        }
        $number = fre_price_format($amount);
        $message = str_ireplace('[amount]', $number, $message);
        $message = str_ireplace('[message]', $msg, $message);
        return $message;
    }
}
if( !function_exists('fre_credit_request_secure_code_mail_content') ) {
    /**
     * get email content
     *
     * @param string $number
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_request_secure_code_mail_content($code)
    {
        if(ae_get_option('fre_credit_request_secure_mail_template')){
            $message = ae_get_option('fre_credit_request_secure_mail_template');
        }else{
            $message = "<p>Hi [display_name],</p>
                                    <p>You have requested a secure code.</p>
                                    <p>You can use this code for credit transaction. You should keep your code secret. </p>
                                    <p>Your code: [code]</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>";
        }

        $message = str_ireplace('[code]', $code, $message);
        return $message;
    }
}
/**
  * check if enable option prevent access to deposit page
  *
  * @param void
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category FRE CREDIT
  * @author Jack Bui
  */
function fre_credit_redirect(){
    if( ae_get_option('prevent_deposit_page', false) ){
        $page = ae_get_option('fre_credit_deposit_page_slug', false);
        if( $page && is_page($page) ){
	        wp_redirect(et_get_page_link('my-credit'));
        }
    }
}
add_action('template_redirect', 'fre_credit_redirect');

/**
 * Get color/icon of transaction
 * @param $type
 * @author ThanhTu
 * @since 1.0
 * @return Array
 */
function get_color_icon_transaction($type = 'deposit'){
    $trans_arr = array(
        'deposit'   => array('color' => 'text-blue-light', 'icon' => 'fa fa-arrow-up'),
        'withdraw'  => array('color' => 'text-green-dark', 'icon' => 'fa fa-arrow-down'),
        'transfer'  => array('color' => 'text-blue-light', 'icon' => 'fa fa-arrow-right'),
        'charge'    => array('color' => 'text-orange-dark', 'icon' => 'fa fa-minus'),
        'refund'    => array('color' => 'text-orange-light', 'icon' => '')
    );

    if(!isset($trans_arr[$type]))
        return array('color' => '', 'icon' => '');
    else
        return $trans_arr[$type];
}


/**
 * Get list credit pending
 *
 * vosydao
 * @param $user_ID
 * @return array
 */
function get_list_credit_pending($user_ID){
	global $ae_post_factory;
	$list_credit_pending = get_posts(array(
		'author'=> $user_ID,
		'post_type'  => 'fre_credit_history',
		'post_status'  => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key'   => 'history_type',
				'value' => 'deposit',
			),
			array(
				'key'   => 'history_status',
				'value' => 'pending',
			)
		)
	));
	$data = array();
	$total_amount = 0;
	$text_package = '';
	if(!empty($list_credit_pending)){
		foreach ($list_credit_pending as $k =>$v){
			$his_obj = $ae_post_factory->get('fre_credit_history');
			$convert = $his_obj->convert($v);
			if(!empty($convert->package_name)){
				if($k + 1 == count($list_credit_pending)){
					$text_package  .= $convert->package_name;
				}else{
					$text_package  .= $convert->package_name . ', ';
				}
			}

			$total_amount += $convert->amount;
			$data['text_package'] = $text_package;
			$data['total_amount'] = $total_amount;
			$data['list'][] = $convert;
		}
	}

	return $data;
}


function credit_get_total_withdraw($user_ID){
	global $wpdb;
	$total_withdraw = 0;

	$select_withdraw = 'SELECT SUM(pm.meta_value) as total FROM '.$wpdb->posts .' as p
                INNER JOIN '.$wpdb->postmeta .' as pm on p.ID = pm.post_id WHERE p.post_status ="pending"
                AND p.post_author = '.$user_ID.' AND p.post_type = "fre_credit_withdraw" AND pm.meta_key = "amount"';
	$withdraw = $wpdb->get_row($select_withdraw);
	if(!empty($withdraw->total)){
		$total_withdraw = $withdraw->total;
	}

	return $total_withdraw;
}

function credit_get_total_project_payment($user_ID){
	$total_project_payment =  0;
	$list_project_payment  = get_posts(array(
		'post_type' => 'fre_order',
		'author' => $user_ID,
		'post_status' => 'publish',
		'posts_per_page' => -1,
	));
	if( !empty( $list_project_payment) ){
		foreach ($list_project_payment as $v){
			$fre_paykey = get_post_meta($v->ID,'fre_paykey',true);
			$payment_amount =  0;
			if( ! empty( $fre_paykey) ){
				$payment_amount = (float) get_post_meta($fre_paykey,'amount',true);
			}

			$total_project_payment = $total_project_payment + $payment_amount;
		}
	}

	return $total_project_payment;
}

function credit_get_total_project_working($user_ID){
	$total_project_working = 0 ;
	$list_project_working  = get_posts(array(
		'post_type' => 'bid',
		'author' => $user_ID,
		'post_status' => array('disputing','accept','complete'),
		'posts_per_page' => -1,
	));
	if(!empty($list_project_working)){
		foreach ($list_project_working as $v){
			$payment_amount = 0 ;
			$fre_paykey = get_post_meta($v->ID,'fre_paykey',true);
			$history_status_transfer = get_post_meta($fre_paykey,'history_status',true);
			if($history_status_transfer == 'pending'){
				$bid_budget = get_post_meta($v->ID,'bid_budget',true);
				$payer_of_commission = get_post_meta($v->ID,'payer_of_commission',true);
				if($payer_of_commission !='project_owner'){
					$commission_fee = get_post_meta($v->ID,'commission_fee',true);
					$payment_amount = $bid_budget - $commission_fee;
				}else{
					$payment_amount = intval($bid_budget);
				}
			}

			$total_project_working += $payment_amount;
		}
	}

	return $total_project_working;
}
// 1.2.2
// Filter default email receive payment send to buyer - the email auto send to buyer when buy a package.

function fre_credit_deposit_notify_buyer( $content, $pack, $type ){
    et_log($type);
    if ( $type == 'fre_credit_plan' ) {

        if( ae_get_option('fre_credit_deposit_mail_template_buyer') ){
                $content = ae_get_option('fre_credit_deposit_mail_template_buyer');
        } else {
            $content = "<p>Dear [display_name],</p>
                                        <p>
                                            Thank you for your payment.<br />
                                            Here are the details of your transaction:<br />
                                            <strong>Detail</strong>: Purchase the [package_name] package. This package contains [number_credit] credits.
                                        </p>
                                        <p>
                                            <strong> Customer info</strong>:<br />
                                            [display_name] <br />
                                            Email: [user_email]. <br />
                                        </p>
                                        <p>
                                            <strong> Invoice</strong> <br />
                                            Invoice No: [invoice_id]  <br />
                                            Date: [date] <br />
                                            Payment: [payment] <br />
                                            Total: [total] [currency]<br />
                                            [notify_cash]
                                        </p>
                                        <p>Sincerely,<br />[blogname]</p>";
        }
        $amout = $pack->et_number_posts;
        //$amout = fre_price_format($amout);
        $content = str_ireplace('[number_credit]', $amout, $content);
        $content = str_ireplace( '[notify_cash]', __( 'Please send the payment to admin to complete your payment.<br>Your credits are under admin review. It will be available after admin approval.', ET_DOMAIN ), $content );
    }

    return $content;
}
add_filter( 'ae_send_receipt_credit_mail','fre_credit_deposit_notify_buyer',10,3);

if( !function_exists('fre_credit_get_deposit_email_content') ) {
    /**
     * get email content
     *
     * @param integer $number
     * @return string
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author danng
     */
    function fre_mail_notify_admin_has_new_deposit($message, $product, $order)   {

        $type    = $product['TYPE'];

        if( $type == 'fre_credit_plan' ){

            $packs   = AE_Package::get_instance();
            $sku     =  get_post_meta( $order->ID, 'et_order_plan_id', true );
            $pack    = $packs->get_pack( $sku, $type );
            $number_of_post = $pack->et_number_posts;

            if(ae_get_option('fre_credit_deposit_mail_template')){

                $message = ae_get_option('fre_credit_deposit_mail_template');

            } else {
                $message = "<p>Hi Administrator,</p>
                            <p>User [display_name] has been deposited [number_credit] on your site,please check and confirm (if required) this payment.</p>
                            <p>Sincerely,</p>
                            <p>[blogname]</p>";
            }

            $message = str_ireplace('[number_credit]', $number_of_post, $message);
        }

        return $message;
    }
}
add_filter( 'mail_notify_admin_has_new_payment', 'fre_mail_notify_admin_has_new_deposit', 10, 3);