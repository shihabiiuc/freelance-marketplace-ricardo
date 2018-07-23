<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 12/8/2015
 * Time: 1:42 PM
 */
class FRE_Credit_Escrow extends AE_Base{
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
        $this->mail = Fre_Mailing::get_instance();
    }
    /**
     * init for this class
     *
     */
    public function init(){
        $this->add_action( 'ae_escrow_payment_gateway', 'acceptBid' );
        $this->add_action('fre_finish_escrow', 'finishEscrow', 10, 2);
        $this->add_filter('fre_process_escrow', 'processEscrow', 10, 3 );
        $this->add_action('ae_escrow_execute', 'executeEscrow', 10, 2);
        $this->add_action('ae_escrow_refund', 'refundEscrow', 10, 2);
        $this->add_action('fre_after_accept_bid_infor', 'fre_credit_add_more_field');
        $this->add_filter('use_paypal_to_escrow', 'use_fre_credit_to_escrow');
        // action transfer money
        $this->add_action('fre_transfer_money_ajax', 'transferMoneyEscrow', 10, 2);
        $this->add_action('fre_change_status_accept_bid','changeStatusAcceptBid');
    }
    /**
      * start escrow process when employer accept a bid
      * @param array  $escrow_data
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function acceptBid( $escrow_data ){
        if(is_use_credit_escrow()){
            global $user_ID;
            $resp = array(
                'success' => false,
                'msg' => __('Please enter a valid secure code!', ET_DOMAIN)
            );
            if(ae_get_option('fre_credit_secure_code', true)){
                if( !isset($_REQUEST['data']) || empty($_REQUEST['data'] ) ){
                    wp_send_json($resp);
                }
                $data = fre_parse_form_data($_REQUEST['data']);

                if(!isset($data['fre_credit_secure_code']) || empty($data['fre_credit_secure_code'])){
                    wp_send_json($resp);
                }else{
                    $flag = FRE_Credit_Users()->checkSecureCode($user_ID, $data['fre_credit_secure_code']);
                    if( !$flag ){
                        wp_send_json($resp);
                    }
                }
            }
            $bid_id = $escrow_data['bid_id'];
            $bid = get_post($bid_id);
            $charge_obj = array(
                'amount' => (float)$escrow_data['total'],
                'currency' => fre_credit_get_payment_currency(),
                'customer' => $user_ID,
                'post_title'=> 'Paid',
                'project_accept' => $bid->post_parent
            );

            $charge = FRE_Credit_Users()->charge($charge_obj);
            $order_post = array(
                'post_type' => 'fre_order',
                'post_status' => 'pending',
                'post_parent' => $bid_id,
                'post_author' => $user_ID,
                'post_title' => 'Pay for accept bid',
                'post_content' => 'Pay for accept bid ' . $bid_id
            );
            $resp = $charge;

            if ( $charge['success'] && isset($charge['id'])) {
                do_action('fre_accept_bid', $bid_id);
                $order_id = wp_insert_post($order_post);
                update_post_meta($order_id, 'fre_paykey', $charge['id']);
                update_post_meta($order_id, 'gateway', 'stripe');

                update_post_meta($bid_id, 'fre_bid_order', $order_id);
                update_post_meta($bid_id, 'commission_fee', $escrow_data['commission_fee']);
                update_post_meta($bid_id, 'payer_of_commission', $escrow_data['payer_of_commission']);
                update_post_meta($bid_id, 'fre_paykey', $charge['id']);

	            // insert transaction received pending for freelancer from ver 1.8.2
	            $bid_budget = get_post_meta($bid_id, 'bid_budget', true);
	            $args_received_pending = array(
		            'post_title' => 'Received',
		            'post_author' => $bid->post_author,
		            'history_type' => 'transfer',
		            'status' => 'pending',
		            'amount' => $bid_budget,
		            'commission_fee' => $escrow_data['commission_fee'],
		            'payment' => $bid->post_parent,
		            'destination' => $bid->post_author,
		            'currency' => $escrow_data['currency'],
	            );
	            if($escrow_data['payer_of_commission'] =='project_owner'){
		            $args_received_pending['commission_fee'] = 0;
	            }
	            FRE_Credit_History()->saveHistory($args_received_pending);

	            $admin_email = get_option('admin_email');
	            $escrow_credit_settings = ae_get_option('escrow_credit_settings',false);
	            $email_receive_commission  = !empty($escrow_credit_settings['email_receive_commission']) ? $escrow_credit_settings['email_receive_commission'] : $admin_email;
	            $user_admin = get_user_by('email',$email_receive_commission);
	            if(!empty($user_admin) && email_exists($email_receive_commission)){
		            // insert transaction commission fee for admin from ver 1.8.2
		            $args_commission = array(
			            'post_title' => 'Received',
			            'post_author' => $user_admin->data->ID,
			            'history_type' => 'transfer',
			            'status' => 'completed',
			            'amount' => $escrow_data['commission_fee'],
			            'payment' => $bid->post_parent,
			            'destination' => $user_admin->data->ID,
			            'currency' => $escrow_data['currency'],
			            'is_commission' => 1,
		            );
		            FRE_Credit_History()->saveHistory($args_commission);

		            //update credit available + commission for admin from ver 1.8.2
		            $admin_available = FRE_Credit_Users()->getUserWallet($user_admin->data->ID);
		            if(!empty($admin_available->balance)){
			            $new_balance = intval($admin_available->balance) + intval($escrow_data['commission_fee']);
		            }else{
			            $new_balance = $escrow_data['commission_fee'];
		            }
		            FRE_Credit_Users()->updateUserBalance($user_admin->data->ID,$new_balance);
	            }

                et_write_session('payKey', $charge['id']);
                et_write_session('order_id', $order_id);
                et_write_session('bid_id', $bid_id);
                et_write_session('ad_id', $bid->post_parent);
                $resp = array(
                    'success' => true,
                    'msg'=> 'Success!',
                    'redirect_url' => et_get_page_link('process-payment').'/?paymentType=frecredit'
                );
            }
            wp_send_json($resp);
        }
    }
    /**
      * add more secure code to accept bid modal
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_add_more_field(){
        if(ae_get_option('fre_credit_secure_code', true) && is_use_credit_escrow()){
            fre_credit_secure_code_field();
        }
    }
    /**
      * process escrow
      *
      * @param array $payment_return
      * @param string $payment_type
      * @param array $data
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function processEscrow( $payment_return, $payment_type, $data ){
        // Paid for submit project
        if($payment_type == 'frecredit' && isset($_REQUEST['success']) && $_REQUEST['success']){
            $payment_return['ACK'] = true;
            $this->mail->new_payment_notification($data['order_id']);
            //update status order
            wp_update_post(array('ID' => $data['order_id'], 'post_status' => 'publish'));
            //update status project
            if( current_user_can(  'manage_options' ) || !ae_get_option('use_pending', false)){
                wp_update_post(array('ID' => $data['ad_id'], 'post_status' => 'publish'));
            }else{
                wp_update_post(array('ID' => $data['ad_id'], 'post_status' => 'pending'));
            }
            $payment_return['payment_status'] = 'Publish';

            if (isset($data['order_id'])) {
                $order = new AE_Order($data['order_id']);
                $order_pay = $order->get_order_data();
                if(isset($_REQUEST['packageType']) && $_REQUEST['packageType'] == '1'){
                    AE_Payment::update_current_order($order_pay['payer'], $order_pay['payment_package'],$data['order_id']);
                    AE_Package::add_package_data($order_pay['payment_package'], $order_pay['payer']);
                    AE_Package::update_package_data($order_pay['payment_package'], $order_pay['payer']);
                }elseif(isset($_REQUEST['packageType']) && $_REQUEST['packageType'] == 'bid_plan'){
                    // Update credit number for user when freelance buy package bid by credit.
                    $order_credit = get_post($data['order_id']);
                    $meta = get_post_meta($data['order_id'], 'et_order_products', true);
                    $product = array_shift($meta);
                    $packs = AE_Package::get_instance();
                    $pack = $packs->get_pack($product['ID'], 'bid_plan');
                    if( isset( $pack->et_number_posts ) && (int)$pack->et_number_posts > 0 ){
                        update_credit_number( $order_credit->post_author, (int)$pack->et_number_posts );
                        // wp_update_post(array('ID' => $data['order_id'], 'post_status' => 'publish'));
                        // $payment_return['payment_status'] = 'Publish';
                    }
                }
            }
            do_action('ae_member_process_order', $order_pay['payer'], $order_pay);
            $data['order'] = $order;
            $payment_return['order'] = $data['order'];
            return $payment_return;
        }

        // Accept bid
        if ($payment_type == 'frecredit' && isset($data['payKey'])) {
            $response = FRE_Credit_History()->retrieveHistory($data['payKey']);
            $payment_return['payment_status'] = $response->post_status;
            if ($response->history_status == 'completed') {
                $payment_return['ACK'] = true;
                wp_update_post(array(
                    'ID' => $data['order_id'],
                    'post_status' => 'publish'
                ));
                // assign project
                $bid_action = Fre_BidAction::get_instance();
                $bid_action->assign_project($data['bid_id']);
            }
            else{
                $payment_return['msg'] = __('Payment failed!', ET_DOMAIN);
            }
        }
        return $payment_return;
    }
    /**
      * disable paypal to escrow
      *
      * @param bool $flag
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function use_fre_credit_to_escrow($flag){
        return false;
    }
    /**
      * finish project
      *
      * @param integer $project_id
      * @param integer $bid_id_accepted
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function finishEscrow( $project_id, $bid_id_accepted ){
        if ( is_use_credit_escrow() ) {
            // execute payment and send money to freelancer
            $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
            if ( $charge_id ) {
                $charge = FRE_Credit_History()->retrieveHistory($charge_id);
                if ( $charge ) {
                    $bid = get_post($bid_id_accepted);
                    $destination = '';
                    $bid_budget = $charge->amount;
                    if( $bid && !empty($bid)){
                        $destination = $bid->post_author;
                        $bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
                        $payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
                        if( $payer_of_commission != 'project_owner' ) {
                            $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                        }else{
                            $commission_fee = 0;
                        }
                    }
                    $transfer_obj = array(
                        "amount" => (float)$bid_budget, // amount in cents
                        "currency" => $charge->currency,
                        "destination" => $destination,
                        'commission_fee' => (float)$commission_fee,
                        "statement_descriptor" => '',
                        'source_transaction' => $charge,
                        'post_title'=> 'Received',
                        'payment' => $project_id,
                        'post_author' => $destination // freelancer id
                    );
                    $transfer = FRE_Credit_Users()->transfer( $transfer_obj );
                    if( $transfer ) {
                        $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                        if ($order) {
                            wp_update_post(array(
                                'ID' => $order,
                                'post_status' => 'finish'
                            ));
                            $mail = Fre_Mailing::get_instance();
                            $mail->alert_transfer_money($project_id, $bid_id_accepted);
                            $mail->notify_execute($project_id, $bid_id_accepted);
                        }
                    }
                }
            } else {
                $mail = Fre_Mailing::get_instance();
                $mail->alert_transfer_money($project_id, $bid_id_accepted);
            }
        }
    }
    /**
      * execute escrow
      *
      * @param integer $project_id
      * @param integer $bid_id_accepted
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function executeEscrow( $project_id, $bid_id_accepted ){
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $charge = FRE_Credit_History()->retrieveHistory( $charge_id );
        if ( $charge ) {
            $bid = get_post($bid_id_accepted);
            $destination = '';
            $bid_budget = $charge->amount;
            if( $bid && !empty($bid)){
                $destination = $bid->post_author;
                $bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
                $payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
                if( $payer_of_commission != 'project_owner' ) {
                    $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                }
                else{
                    $commission_fee = 0;
                }
            }
            $transfer_obj = array(
                "amount" => (float)$bid_budget, // amount in cents
                "currency" => $charge->currency,
                "destination" => $destination,
                'commission_fee' => (float)$commission_fee,
                "statement_descriptor" =>'',
                'source_transaction' => $charge,
                'post_author' => $destination, // freelancer id
                'post_title'=> __('Received',ET_DOMAIN),
                'payment' => $project_id,
            );
            $transfer = FRE_Credit_Users()->transfer( $transfer_obj );
            if( $transfer ) {
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'completed'
                    ));
                }

                // success update project status
                wp_update_post(array(
                    'ID' => $project_id,
                    'post_status' => 'disputed'
                ));

	            // success update bid status
	            wp_update_post(array(
		            'ID' => $bid_id_accepted,
		            'post_status' => 'disputed'
	            ));

                // update meta when admin arbitrate
                if(isset($_REQUEST['comment']) && isset($_REQUEST['winner'])){
                    $comment = $_REQUEST['comment'];
                    $winner = $_REQUEST['winner'];
                    update_post_meta($project_id, 'comment_of_admin', $comment);
                    update_post_meta($project_id, 'winner_of_arbitrate', $winner);
                }
                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute_payment($project_id, $bid_id_accepted);
                do_action('fre_resolve_project_notification', $project_id);
                wp_send_json(array(
                    'success' => true,
                    'msg' => __("Send payment successful.", ET_DOMAIN)
                ));
            }
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('Send payment failed', ET_DOMAIN)
                ));
            }
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid charge.", ET_DOMAIN)
            ));
        }
    }
    /**
      * transfer money escrow
      *
      * @param integer $project_id
      * @param integer $bid_id_accepted
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function transferMoneyEscrow( $project_id, $bid_id_accepted ){
        $charge_id = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $charge = FRE_Credit_History()->retrieveHistory( $charge_id );
        if ( $charge ) {
            $bid = get_post($bid_id_accepted);
            $destination = '';
            $bid_budget = $charge->amount;
            if( $bid && !empty($bid)){
                $destination = $bid->post_author;
                $bid_budget = get_post_meta( $bid_id_accepted, 'bid_budget', true );
                $payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
                if( $payer_of_commission != 'project_owner' ) {
                    $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
                }
                else{
                    $commission_fee = 0;
                }
            }
            $transfer_obj = array(
                "amount" => (float)$bid_budget, // amount in cents
                "currency" => $charge->currency,
                "destination" => $destination,
                'commission_fee' => (float)$commission_fee,
                "statement_descriptor" =>'',
                'source_transaction' => $charge,
                'post_author' => $destination, // freelancer id
                'post_title'=> __('Received',ET_DOMAIN),
                'payment' => $project_id,
            );
            $transfer = FRE_Credit_Users()->transfer( $transfer_obj );
            if( $transfer ) {
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'finish'
                    ));
                }
                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute($project_id, $bid_id_accepted);

                wp_send_json(array(
                    'success' => true,
                    'msg' => __("The payment has been successfully transferred.", ET_DOMAIN)
                ));
            }
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('Send payment failed', ET_DOMAIN)
                ));
            }
        }
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid charge.", ET_DOMAIN)
            ));
        }
    }
    /**
      * refund money
      *
      * @param integer $project_id
      * @param $bid_id_accepted
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function refundEscrow($project_id, $bid_id_accepted){
        $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        $re = FRE_Credit_Users()->refund($pay_key, $project_id, $bid_id_accepted);
        if( $re['success'] ){
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'refund'
                ));
            }
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));
	        wp_update_post(array(
		        'ID' => $bid_id_accepted,
		        'post_status' => 'disputed'
	        ));

	        //update charge to cancelled from ver 1.8.2
	        update_post_meta($pay_key,'history_status','cancelled');

	        // update transfer of freelancer to cancelled
	        $list_transfer = get_posts(array(
		        'post_type' => 'fre_credit_history',
		        'posts_per_page' => -1,
		        'meta_query' => array(
			        array(
				        'key' => 'history_type',
				        'value' => 'transfer'
			        ),
			        array(
				        'key' => 'history_status',
				        'value' => 'pending'
			        ),
			        array(
				        'key' => 'payment',
				        'value' => $project_id
			        )
		        ),
	        ) );
	        if($list_transfer){
		        foreach ($list_transfer as $tr){
			        update_post_meta($tr->ID,'history_status','cancelled');
		        }
	        }


	        //update credit available - commission for freelancer from ver 1.8.2
	        $payer_of_commission = get_post_meta( $bid_id_accepted, 'payer_of_commission', true );
	        if( $payer_of_commission != 'project_owner' ) {
	        	$bid = get_post($bid_id_accepted);
		        $commission_fee = get_post_meta($bid_id_accepted, 'commission_fee', true);
		        $fre_available = FRE_Credit_Users()->getUserWallet($bid->post_author);
		        if(!empty($fre_available->balance)){
			        $new_balance = intval($fre_available->balance) - intval($commission_fee);
		        }else{
			        $new_balance = 0 - intval($commission_fee);
		        }
		        FRE_Credit_Users()->updateUserBalance($bid->post_author,$new_balance);

		        // insert transaction - commission fee for freelancer from ver 1.8.2
		        $args_commission = array(
			        'post_title' => __('Paid',ET_DOMAIN),
			        'post_author' => $bid->post_author,
			        'history_type' => 'charge',
			        'status' => 'completed',
			        'amount' => $commission_fee,
			        'project_accept' => $bid->post_parent,
			        'currency' => fre_credit_get_payment_currency(),
			        'is_commission' => 1,
		        );
		        FRE_Credit_History()->saveHistory($args_commission);
	        }




            // update meta when admin arbitrate
            if(isset($_REQUEST['comment']) && isset($_REQUEST['winner'])){
                $comment = $_REQUEST['comment'];
                $winner = $_REQUEST['winner'];
                update_post_meta($project_id, 'comment_of_admin', $comment);
                update_post_meta($project_id, 'winner_of_arbitrate', $winner);
            }
            //send mail
            $mail = Fre_Mailing::get_instance();
            $mail->refund($project_id, $bid_id_accepted);
            do_action('fre_resolve_project_notification', $project_id);
            // // send json back
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN) ,
                'data' =>__('Success', ET_DOMAIN)
            ));
        } else {
            wp_send_json($re);
        }
    }


	/**
	 * @param $charge_id
	 */
    public function changeStatusAcceptBid($charge_id){
	    if(!empty($charge_id)){
		    update_post_meta($charge_id, 'history_status', 'pending');
	    }
    }

}