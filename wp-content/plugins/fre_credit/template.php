<?php
/**
 * Plugin  template
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Jack Bui
*/

if( !function_exists('fre_credit_template_payToSubmitProject_button') ){
    /**
      * html template for pay to submit project button
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_template_payToSubmitProject_button(){
      global $user_ID;
      $available = FRE_Credit_Users()->getUserWallet($user_ID);
      ?>
        <li class="panel fre-credit-payment-onsite">
          <span class="title-plan fre-credit-payment" data-type="frecredit">
              <?php _e("Credit", ET_DOMAIN); ?>
              <span><?php _e("Your available balance", ET_DOMAIN); ?>: <strong><?php echo fre_price_format($available->balance);?></strong></span>
              <span class="error" style="display:none;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php _e("Your balance is not enough to proceed the payment.", ET_DOMAIN);?></span>
          </span>
          <a data-toggle="collapse" data-type="frecredit" data-parent="#fre-payment-accordion" href="#fre-payment-frecredit" class="btn collapsed other-payment"><?php _e("Select", ET_DOMAIN); ?></a>
          <?php include_once dirname(__FILE__) . '/template/form-template.php'; ?>
        <!--     <a href="#!" class="btn btn-submit-price-plan btn-fre-credit-payment" data-toggle="popover" data-content="<?php _e('Your balance is not enough for this plan', ET_DOMAIN);?>" data-type="frecredit"><?php _e("Select", ET_DOMAIN); ?></a> -->
        </li>
<?php }
}
if( !function_exists('fre_credit_deposit_template') ){
    /**
      * html template for deposit page
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_deposit_template(){
        ob_start();
        include dirname(__FILE__) . '/template/fre-credit-deposit.php';
        return ob_get_clean();

    }
}
add_shortcode( 'fre_credit_deposit', 'fre_credit_deposit_template' );
if( !function_exists('fre_credit_secure_code_field')) {
    /**
     * secure code field
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_secure_code_field(){ ?>
        <div class="secure-code-accept-bid fre-input-field">
            <label class="fre-field-title"><?php _e('Secure code', ET_DOMAIN);?></label>
            <input tabindex="20" id="fre_credit_secure_code" type="password" size="20" name="fre_credit_secure_code"  class="bg-default-input not_empty"/>
        </div>
   <?php }
}
if( !function_exists('fre_credit_add_profile_tab') ){
    /**
      * add tab credit to profile page
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_tab(){ ?>
        <li>
            <a href="<?php echo et_get_page_link( "my-credit" ) ?>"><?php _e('My Credit', ET_DOMAIN) ?></a>
        </li>
   <?php }
}
add_action( 'fre_header_before_notify', 'fre_credit_add_profile_tab');
if( !function_exists('fre_credit_add_profile_tab_content') ){
    /**
      * credit tab content
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_profile_tab_content(){
        global $user_ID,$wpdb;
        $user_role = ae_user_role($user_ID);
        $total = fre_credit_get_user_total_balance($user_ID);
        $available = FRE_Credit_Users()->getUserWallet($user_ID);
        //$freezable = FRE_Credit_Users()->getUserWallet($user_ID, 'freezable');
        $email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
        $banking_info = get_user_meta($user_ID, 'bank-info-credit', true);

	    $tooltip_frozen = __('Your withdrawal request is under admin review.', ET_DOMAIN);
        /*if( $user_role == 'freelancer' ) {
            $tooltip_frozen = __('Your withdraw request is waiting for admin approval.', ET_DOMAIN);
        }elseif( $user_role == 'employer' ){
            $tooltip_frozen = __('Your project commission & fee have been sent to admin under Escrow system. Otherwise, your withdraw request is waiting for admin approval.', ET_DOMAIN);
        }else{
            $tooltip_frozen = '';
        }*/
	    $total_withdraw = credit_get_total_withdraw($user_ID);
	    $total_project_payment = credit_get_total_project_payment($user_ID);
	    $total_project_working = credit_get_total_project_working($user_ID);
        $minimum = ae_get_option('fre_credit_minimum_withdraw', 50);
	    $list_credit_pending = get_list_credit_pending($user_ID);
        ?>
        <div class="fre-page-wrapper tabs-credits" id="credits">
            <div class="fre-page-title">
                <div class="container">
                    <h2><?php _e('My Credit' , ET_DOMAIN) ?></h2>
                </div>
            </div>

            <div class="fre-page-section">
                <div class="container">
                    <div class="fre-credit-wrap">
                        <ul class="fre-tabs">
                            <li class="active"><a data-toggle="tab" href="#fre-credit-balance"><?php _e('Balance',ET_DOMAIN) ?></a></li>
                            <li><a data-toggle="tab" href="#fre-credit-transaction"><?php _e('Transaction',ET_DOMAIN) ?></a></li>
                        </ul>
                        <div class="fre-tab-content">
                            <div id="fre-credit-balance" class="fre-panel-tab active">
                                <div class="fre-credit-box">
                                    <div class="credit-balance-info">
                                        <div class="row">
	                                        <?php if($user_role == 'freelancer'){ ?>
                                                <div class="col-sm-3 col-xs-6">
                                                    <div class="balance-info-item">
                                                        <p><?php _e('Working Project',ET_DOMAIN) ?></p>
                                                        <p><b><?php echo fre_price_format($total_project_working) ?></b></p>
                                                    </div>
                                                </div>
	                                        <?php } ?>

                                            <div class="col-sm-3 col-xs-6">
                                                <div class="balance-info-item">
                                                    <p><?php _e('Available Credit',ET_DOMAIN) ?></p>
                                                    <p><b><?php echo fre_price_format($available->balance) ?></b></p>
                                                </div>
                                            </div>
                                            <?php if($user_role != 'freelancer'){ ?>
                                                <div class="col-sm-3 col-xs-6">
                                                    <div class="balance-info-item">
                                                        <p><?php _e('Current Consumption',ET_DOMAIN) ?></p>
                                                        <p><b><?php echo fre_price_format($total_project_payment) ?></b></p>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="col-sm-3 col-xs-6">
                                                <div class="balance-info-item">
                                                    <p>
                                                        <?php _e('Pending Withdrawal',ET_DOMAIN) ?>
                                                        <span data-toggle="tooltip" data-placement="top" title="<?php echo $tooltip_frozen; ?>">
                                                            <i class="fa fa-info-circle"></i>
                                                        </span>
                                                    </p>
                                                    <p><b id="pending_withdraw_number" data-number="<?php echo $total_withdraw ?>"><?php echo fre_price_format($total_withdraw) ?></b></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-xs-6">
                                                <div class="balance-info-item">
                                                    <p><?php _e('Total Credit',ET_DOMAIN) ?></p>
                                                    <p>
                                                        <b>
			                                                <?php echo fre_price_format($total->balance); ?>
                                                        </b>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if(!empty($list_credit_pending)){
                                    //var_dump($list_credit_pending);
                                    ?>
                                    <div class="fre-credit-box">
                                        <div class="credit-recharge">
                                            <p><?php echo sprintf(__('Your %s package has been checked out by cash.',ET_DOMAIN ) , $list_credit_pending['text_package'])?></p>
                                            <p><?php _e('Your pending credit:',ET_DOMAIN) ?> <b class="credit-pending-price"><?php echo fre_price_format($list_credit_pending['total_amount']) ?></b></p>
                                            <p><?php _e('This pending credit will be available to use when your payment is approved.',ET_DOMAIN) ?></p>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="fre-credit-box">
                                    <div class="credit-balance-wrap">
                                        <div class="credit-recharge">
                                            <h2><?php _e('Top-up Credits',ET_DOMAIN) ?></h2>
                                            <p><?php _e('Deposit to get more credits.',ET_DOMAIN) ?></p>
                                            <div class="credit-recharge-btn-wrap">
                                                <a class="fre-normal-btn-o" href="<?php echo fre_credit_deposit_page_link() ?>"><?php _e('Deposit',ET_DOMAIN) ?></a>
                                            </div>
                                        </div>
                                        <div class="credit-withdraw">
                                            <h2><?php _e('Credit Withdrawal',ET_DOMAIN) ?></h2>
                                            <p><?php _e('Withdraw credit from your available balance. You can use either of two following payment methods:',ET_DOMAIN) ?></p>
                                            <div class="withdraw-account-table fre-table">
                                                <div class="fre-table-row">
                                                    <div class="fre-table-col"><i><?php _e('PayPal account',ET_DOMAIN) ?></i></div>
                                                    <div class="fre-table-col"><span><?php echo $email_paypal ? $email_paypal : '<i>'.__('Not yet update',ET_DOMAIN).'</i>' ?></span>
                                                        <a href="#" class="fre-skin-color btn-edit-email-credit" data-toggle="modal" data-target="#" >
                                                            <?php _e('Update',ET_DOMAIN) ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="fre-table-row">
                                                    <div class="fre-table-col"><i><?php _e('Banking info',ET_DOMAIN) ?></i></div>
                                                    <div class="fre-table-col"><span><?php echo $banking_info ? $banking_info['benficial_owner'] : '<i>'.__('Not yet update',ET_DOMAIN). '</i>'; ?></span>
                                                        <a href="#" class="fre-skin-color btn-update-bank" data-toggle="modal" data-target="#">
                                                            <?php _e('Update',ET_DOMAIN) ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="credit-withdraw-btn-wrap <?php echo ((float)$minimum > $available->balance) ? 'not-money': ''; ?>">
		                                        <?php if((float)$minimum > $available->balance):?>
                                                    <a href="javascript:void(0)" class="fre-normal-btn-disable" data-toggle="tooltip" data-placement="top" data-original-title="<?php printf(__('Cannot withdraw money. Your available credit must be greater than or equal to %s', ET_DOMAIN), ae_get_option('fre_credit_minimum_withdraw', 50)); ?>"><?php _e('Withdraw', ET_DOMAIN); ?></a>
		                                        <?php else:?>
                                                    <a href="javascript:void(0)" class="fre-normal-btn-o btn-withdraw btn-withdraw-action"><?php _e('Withdraw', ET_DOMAIN); ?></a>
		                                        <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="fre-credit-transaction" class="fre-panel-tab fre-credit-history-wrapper">
                                <div class="fre-credit-box fre-credit-filter-box">
                                    <div class="transaction-filter-header visible-xs">
                                        <a class="transaction-filter-title" href=""><?php _e("Filter Transaction", ET_DOMAIN); ?></a>
                                    </div>
                                    <form class="credit-transaction-filter">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="fre-input-field">
                                                    <label class="fre-field-title" for=""><?php _e('Transaction Type',ET_DOMAIN) ?></label>
                                                    <select class="fre-chosen-single fre-credit-history-filter-type" >
                                                        <option value=""><?php _e('All',ET_DOMAIN) ?></option>
                                                        <option value="deposit"><?php _e('Deposit', ET_DOMAIN); ?></option>
                                                        <option value="withdraw"><?php _e('Withdraw', ET_DOMAIN); ?></option>
                                                        <option value="transfer"><?php _e('Receive', ET_DOMAIN); ?></option>
                                                        <option value="charge"><?php _e('Pay', ET_DOMAIN); ?></option>
                                                        <option value="refund"><?php _e('Refund', ET_DOMAIN); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="fre-input-field">
                                                    <label class="fre-field-title" for=""><?php _e('Status',ET_DOMAIN) ?></label>
                                                    <select class="fre-chosen-single fre-credit-history-filter-status">
                                                        <option value=""><?php _e('All status',ET_DOMAIN) ?></option>
                                                        <option value="completed"><?php _e('Completed',ET_DOMAIN) ?></option>
                                                        <option value="cancelled"><?php _e('Cancelled',ET_DOMAIN) ?></option>
                                                        <option value="pending"><?php _e('Pending',ET_DOMAIN) ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="fre-input-field fre-period-field">
                                                    <label class="fre-field-title" for=""><?php _e('Period',ET_DOMAIN) ?></label>
                                                    <div class="period-min-wrap">
                                                        <input class="filter-period-min" id="fre_credit_from" type="text" placeholder="Form">
                                                    </div>
                                                    <span>-</span>
                                                    <div class="period-max-wrap">
                                                        <input class="filter-period-max" id="fre_credit_to" type="text" placeholder="To">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a class="transaction-filter-clear" href="javascript:void(0)"><?php _e('Clear all filters',ET_DOMAIN) ?></a>
                                    </form>
                                </div>
                                <div class="fre-credit-box">
                                    <div class="credit-transaction-wrap change-log-list">
                                        <div class="fre-table list-histories">
                                            <div class="fre-table-head">
                                                <div class="fre-table-col"><?php _e('TYPE', ET_DOMAIN) ?></div>
                                                <div class="fre-table-col"><?php _e('AMOUNT', ET_DOMAIN) ?></div>
                                                <div class="fre-table-col"><?php _e('ACTION' , ET_DOMAIN) ?></div>
                                                <div class="fre-table-col"><?php _e('PAYMENT GATEWAY', ET_DOMAIN) ?></div>
                                                <div class="fre-table-col"><?php _e('STATUS', ET_DOMAIN) ?></div>
                                                <div class="fre-table-col"><?php _e('TIME', ET_DOMAIN) ?></div>
                                            </div>

	                                        <?php
	                                        global $post,$wp_query, $ae_post_factory;
	                                        $args = array(
		                                        'post_type'=> 'fre_credit_history',
		                                        'post_status'=> 'publish',
		                                        'paged'=>1,
		                                        'author'=> $user_ID
	                                        );
	                                        $new_query = new WP_Query($args);
	                                        $post_data = array();
	                                        if( $new_query->have_posts() ):
		                                        while( $new_query->have_posts() ):
			                                        $new_query->the_post();
			                                        $his_obj = $ae_post_factory->get('fre_credit_history');
			                                        $convert = $his_obj->convert($post);
			                                        $post_data[]= $convert;
			                                        include dirname(__FILE__) . '/template/fre-credit-history-item.php';
		                                        endwhile;
	                                        endif;?>
                                        </div>
	                                    <?php if(empty($post_data)){ ?>
                                            <div class='no-transaction'><span><?php _e("There isn't any transaction!",ET_DOMAIN) ?></span></div>
	                                    <?php } ?>
                                    </div>
                                </div>
                                <div class="fre-paginations paginations-wrapper">
		                            <?php
		                            ae_pagination($new_query, get_query_var('paged'), 'page');
		                            ?>
                                </div>
	                            <?php echo '<script type="data/json" class="fre_credit_history_data" >' . json_encode($post_data) . '</script>'; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
 <?php  }
}
add_action( 'fre_profile_tab_content_credit', 'fre_credit_add_profile_tab_content');


if( !function_exists('fre_credit_modal_withdraw') ){
    /**
      * modal withdraw
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_modal_withdraw(){
        global $user_ID;
	    $email_paypal = get_user_meta($user_ID, 'email-paypal-credit', true);
	    $banking_info = get_user_meta($user_ID, 'bank-info-credit', true);
	    $minimum = ae_get_option('fre_credit_minimum_withdraw', 50);
	    ?>
        <!--Show modal-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog <?php if(empty($email_paypal)&&  empty($banking_info)) echo 'modal-sm'; ?>" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <p class="warning"></p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
	                    <?php if(empty($email_paypal)&&  empty($banking_info)) { ?>
                            <h4 class="modal-title" id="myModalLabel"><?php _e('Credit Withdrawal', ET_DOMAIN);?></h4>
	                    <?php }else{ ?>
                            <h4 class="modal-title" id="myModalLabel"><?php _e('Withdrawal Request Form', ET_DOMAIN);?></h4>
	                    <?php } ?>
                    </div>
                    <div class="modal-body">
	                    <?php if(empty($email_paypal)&&  empty($banking_info)) { ?>
                            <form class="fre-modal-form credit-no-available">
                                <div class="fre-content-confirm">
                                    <h2><?php _e('No payment method available?',ET_DOMAIN) ?></h2>
                                    <p><?php _e('You must update at least one payment method before submitting a withdrawal request.',ET_DOMAIN) ?></p>
                                </div>
                                <div class="fre-form-btn">
                                    <button type="button" data-dismiss="modal" class="fre-normal-btn"><?php _e('OK', ET_DOMAIN); ?></button>
                                </div>
                            </form>
	                    <?php } else { ?>
                            <form id="fre_credit_withdraw_form" class="fre-modal-form auth-form" novalidate>
                                <!--<div class="balance-withdraw">
                                    <div class="current">
                                        <span><?php /*_e('Current total credit', ET_DOMAIN) ; */?></span>
                                        <span class="price fre-skin-color fre_credit_total"><?php /*echo  fre_price_format(0); */?></span>
                                    </div>
                                    <div class="available">
                                        <span><?php /*_e('Available credit', ET_DOMAIN); */?></span>
                                        <span class="price text-green-dark fre_credit_available"><?php /*echo  fre_price_format(0); */?></span>
                                    </div>
                                    <div class="frozen">
                                        <span><?php /*_e('Frozen credit', ET_DOMAIN); */?></span>
                                        <span class="price fre_credit_freezable"><?php /*echo  fre_price_format(0); */?></span>
                                    </div>
                                </div>-->

                                <div class="fre-input-field input-amount">
                                    <label class="fre-field-title" for="amount"><?php _e('Amount', ET_DOMAIN) ?></label>
                                    <div class="fre-project-budget">
                                        <input type="number" name="amount" id="amount" step="any" value="" class="input-item text-field"  />
                                        <span><?php echo fre_currency_sign(false);?></span>
                                    </div>
                                    <p style="margin-top: 20px;font-weight: 500;font-size: 15px;margin-bottom: 0;">
                                        <?php _e('Minimum Withdrawal Amounts', ET_DOMAIN) ?>:
                                        <span style="color: #333;font-size: 17px;font-weight: 600"><?php echo fre_price_format($minimum) ?></span>
                                    </p>
                                </div>

                                <div class="fre-input-field">
                                    <label class="fre-field-title" for="payment_method"><?php _e('Payment Method', ET_DOMAIN) ?></label>
                                    <select name="payment_method" id="payment_method" class="fre-chosen-single">
                                        <option disabled selected value=""><?php _e('Select payment method',ET_DOMAIN) ?></option>
	                                    <?php
	                                    if(!empty($email_paypal)){
		                                    echo "<option value='email-paypal-credit'>".__('PayPal', ET_DOMAIN)."</option>";
	                                    }
	                                    if(!empty($banking_info)){
		                                    echo "<option value='bank-info-credit'>".__('Bank', ET_DOMAIN)."</option>";
	                                    }
	                                    ?>
                                    </select>
                                </div>

                                <div class="fre-input-field ">
                                    <label class="fre-field-title"><?php _e('Payment Infomation', ET_DOMAIN) ?></label>
                                    <textarea name="payment_info" cols="30" rows="10"></textarea>
                                </div>

			                    <?php if(ae_get_option('fre_credit_secure_code', true)): ?>
                                    <div class="fre-input-field security-code">
                                        <label class="fre-field-title"><?php _e('Security code', ET_DOMAIN) ?></label>
                                        <input type="password" name="secureCode" step="any" value="" />
                                    </div>
			                    <?php endif; ?>

                                <div class="fre-form-btn">
                                    <button type="submit" class="fre-normal-btn fre-btn">
			                            <?php _e('Withdraw', ET_DOMAIN) ?>
                                    </button>
                                    <span class="fre-form-close" data-dismiss="modal"><?php _e('Cancel', ET_DOMAIN) ?></span>
                                </div>

                            </form>
	                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
<?php }
}

if(!function_exists('fre_credit_modal_update_paypal')){
    /**
      * modal edit Email Paypal
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    function fre_credit_modal_update_paypal(){
?>
        <div class="modal fade email_paypal" id="modalEditPaypal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                        <h4 class="modal-title"><?php _e('Update Your PayPal Account', ET_DOMAIN);?></h4>
                    </div>
                    <div class="modal-body">
                        <form id="fre_credit_edit_paypal_form" class="fre-modal-form">
                            <div class="fre-input-field no-margin-bottom">
                                <label class="fre-field-title" for="credit_email_paypal"><?php _e('PayPal Account', ET_DOMAIN) ?></label>
                                <input type="text" class="" id="email_paypal" name="email_paypal" placeholder="">
                            </div>
                            <div class="fre-form-btn">
                                <button type="submit" class="fre-normal-btn btn-submit">
                                    <?php _e('Update', ET_DOMAIN); ?>
                                </button>
                                <span class="fre-form-close" data-dismiss="modal"><?php _e('Cancel',ET_DOMAIN) ?></span>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

if(!function_exists('fre_credit_modal_update_bank')){
    /**
      * modal edit Email Paypal
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author ThanhTu
      */
    function fre_credit_modal_update_bank(){
?>
        <div class="modal fade email_paypal" id="modalUpdateBank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                        <h4 class="modal-title small-modal-title" id="myModalLabel"><?php _e('Update Banking Information', ET_DOMAIN);?></h4>
                    </div>
                    <div class="modal-body">
                        <form id="fre_credit_updat_bank_form" class="fre-modal-form">
                          <div class="fre-input-field">
                              <label class="fre-field-title" for="benficial_owner"><?php _e('Benificial Owner', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="benficial_owner" name="benficial_owner" >
                          </div>
                          <div class="fre-input-field">
                              <label class="fre-field-title" for="account_number"><?php _e('Account Number', ET_DOMAIN) ?></label>
                              <input type="text" class="form-control" id="account_number" name="account_number">
                          </div>
                          <div class="fre-input-field no-margin-bottom">
                              <label class="fre-field-title" for="banking_information"><?php _e('Banking Information', ET_DOMAIN) ?></label>
                              <textarea id="banking_information" name="banking_information"></textarea>
                          </div>
                          <?php /*if(ae_get_option('fre_credit_secure_code', true)): */?><!--
                          <div class="fre-input-field form-group">
                              <label class="fre-field-title" for="secure_code"><?php /*_e('Secure Code', ET_DOMAIN) */?></label>
                              <input type="text" class="form-control" id="secure_code" name="secure_code" placeholder="<?php /*_e('Enter secure code', ET_DOMAIN) */?>">
                          </div>
                          --><?php /*endif; */?>
                          <div class="fre-form-btn">
                                <button type="submit" class="fre-normal-btn btn-submit">
                                    <?php _e('Update', ET_DOMAIN); ?>
                                </button>
                                <span class="fre-form-close" data-dismiss="modal">Cancel</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

if( !function_exists('fre_credit_add_template') ){
    /**
      * add template to footer
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    function fre_credit_add_template(){
        fre_credit_modal_update_bank();
        fre_credit_modal_update_paypal();
        fre_credit_modal_withdraw();
        include_once dirname(__FILE__) . '/template/fre-credit-history-item-js.php';
    }
}
add_action('wp_footer', 'fre_credit_add_template');
if( !function_exists('fre_credit_add_request_secure_code') ) {
    /**
     * add secure code
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    function fre_credit_add_request_secure_code(){ ?>
        <li>
            <a href="#" class="request-secure-code">
                <i class="fa fa-key"></i>
                <?php
                    global $user_ID;
                    if( !FRE_Credit_Users()->getSecureCode($user_ID) ) {
                        _e("Request a new Secure Code", ET_DOMAIN);
                    }
                    else{ ?>
                        <?php _e("Reset Secure Code", ET_DOMAIN);
                    }
                ?>
            </a>
        </li>

<?php     }

}
add_action('fre-profile-after-list-setting', 'fre_credit_add_request_secure_code');