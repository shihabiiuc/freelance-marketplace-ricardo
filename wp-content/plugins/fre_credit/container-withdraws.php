<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class AE_WithdrawList
{

    /**
     * construct a user container
     */
    function __construct($args = array(), $roles = '')
    {
        $this->args = $args;
        $this->roles = $roles;
    }

    /**
     * render list of withdraws list
     */
    function render()
    {
        $withdraws = get_withdraws();
        ?>
        <div class="et-main-content order-container fre-credit-withdraw-container" id="">
            <div class="search-box et-member-search">
                <form action="">
				<span class="et-search-role">
					<select name="post_status" id="" class="et-input">
                        <option value=""><?php _e("All", ET_DOMAIN); ?></option>
                        <option value="publish"><?php _e('Publish', ET_DOMAIN); ?></option>
                        <option value="pending"><?php _e('Pending', ET_DOMAIN); ?></option>
                        <option value="draft"><?php _e('Draft', ET_DOMAIN); ?></option>
                    </select>
				</span>
				<span class="et-search-input">
					<input type="text" class="et-input order-search search" name="s" style="height: auto;" placeholder="<?php
                    _e("Search post...", ET_DOMAIN); ?>">
					<span class="icon" data-icon="s"></span>
				</span>
                </form>
            </div>
            <!-- // user search box -->

            <div class="et-main-main no-margin clearfix overview list fre-credit-withdraw-list-wrapper">
                <div class="title font-quicksand"><?php _e('All Withdraws', ET_DOMAIN) ?></div>
                <!-- order list  -->
                <ul class="list-inner list-payment list-withdraws users-list">
                    <?php if ($withdraws->have_posts()) {
                        global $post, $ae_post_factory;
                        $withdraw_obj = $ae_post_factory->get('fre_credit_withdraw');
                        $withdraw_data = array();
                        while ($withdraws->have_posts()) {
                            $withdraws->the_post();
                            $convert = $withdraw_obj->convert($post);
                            $withdraw_data[] = $convert;
                            include dirname(__FILE__) . '/admin-template/withdraw-item.php';
                        }
                    } else {
                        _e('There are no payments yet.', ET_DOMAIN);
                    } ?>
                </ul>

                <div class="paginations-wrapper">
                    <?php
                    ae_pagination($withdraws, get_query_var('paged'), 'load');
                    wp_reset_query();
                    ?>
                </div>

                <?php if ($withdraws->have_posts()) {
                    echo '<script type="data/json" class="fre_credit_withdraw_dta" >' . json_encode($withdraw_data) . '</script>';
                }?>
            </div>
            <!-- //user list -->
        </div>
    <?php }
}
class Fre_Credit_WithdrawAction extends AE_PostAction
{
    function __construct($post_type = 'fre_credit_withdraw')
    {
        $this->post_type = 'fre_credit_withdraw';
        // add action fetch profile
        $this->add_ajax('fre-admin-fetch-withdraw', 'fetch_post');
        $this->add_filter('ae_convert_fre_credit_withdraw', 'fre_credit_convert_withdraw');
        $this->add_ajax('fre-admin-withdraw-sync', 'sync_withdraw');
        $this->add_filter('ae_admin_globals', 'fre_credit_decline_msg');
        $this->add_filter( 'fre_notify_item', 'ae_withdraw_notify_item', 10, 3);

    }
    /**
      * filter query
      *
      * @param array $query_args
      * @return array $query_args after filter
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function filter_query_args($query_args){
        $query_args['post_status'] = array('pending', 'publish', 'draft');
        if( isset($_REQUEST['query']['post_status']) ){
            $query_args['post_status'] = $_REQUEST['query']['post_status'];
        }
        return $query_args;
    }
    /**
      * description
      *
      * @param object $result
      * @return object $result;
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function  fre_credit_convert_withdraw($result){
        $result->withdraw_edit_link = get_edit_post_link($result->ID);
        $result->withdraw_author_url = get_author_posts_url($result->post_author, $author_nicename = '');
        $result->withdraw_author_name = get_the_author_meta('display_name',$result->post_author);
        $history_id = get_post_meta($result->ID,'charge_id', true);
        if($history_id){
            $result->post_status = get_post_meta($history_id,'history_status', true);
        }
        return $result;
    }
    /**
      * sync withdraw
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function sync_withdraw(){
        global $ae_post_factory, $user_ID;
        $request = $_REQUEST;
        $withdraw = $ae_post_factory->get('fre_credit_withdraw');
        if(isset($request['publish']) && $request['publish'] == 1 ){
            $request['post_status'] = 'publish';
            $this->ae_withdraw_notification($request, 'approve_withdraw', __('Approve withdraw request', ET_DOMAIN));
        }
        if( isset($request['archive']) && $request['archive'] == 1 ){
            $request['post_status'] = 'draft';
            $this->ae_withdraw_notification($request, 'cancel_withdraw', __('Cancel withdraw request', ET_DOMAIN));
            unset($request['archive']);
        }
        // sync notify
        if( current_user_can('manage_options') ){
            $status = $request['post_status'];

            $withdraw_id = wp_update_post(array('ID' => $request['ID'],'post_status' => $status));

            $result     = get_post($withdraw_id);
            $history_id = get_post_meta($withdraw_id,'charge_id', true);
            $charge     = FRE_Credit_History()->retrieveHistory($history_id);
            if( $charge ){
                $post_status = $result->post_status;
                $user_id = $charge->post_author;

                $user_freezable_wallet = FRE_Credit_Users()->getUserWallet($user_id, 'freezable');

                $current_frozen     = $user_freezable_wallet->balance ; // amout frozen of this user
                $amout_withdraw     = $charge->amount;
                $new_frozen_balance = $current_frozen - $amout_withdraw;

                if( $new_frozen_balance >= 0 ){
                    if( $post_status == 'publish' ){

                        update_post_meta($charge->ID, 'history_status', 'completed');

                    } else if( $post_status == 'draft' ) {
                        $user_wallet = FRE_Credit_Users()->getUserWallet($user_id);
                        $user_wallet->balance =  $user_wallet->balance + $amout_withdraw;
                        FRE_Credit_Users()->setUserWallet($user_id, $user_wallet);
                        update_post_meta($charge->ID, 'history_status', 'cancelled');
                    }

                    if( in_array( $post_status, array('publish','draft')) ){
                         //update frozen amout
                        $user_freezable_wallet->balance = $new_frozen_balance;
                        FRE_Credit_Users()->setUserWallet($user_id, $user_freezable_wallet, 'freezable');
                    }

                    //update_post_meta($charge->id, 'user_balance', fre_price_format(FRE_Credit_Users()->getUserWallet($charge->post_author)->balance));
                    $response = array(
                        'success' => true,
                        'data' => $result,
                        'msg' => __("Update withdraw successful!", ET_DOMAIN)
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => __("There isn't enough money in your wallet!", ET_DOMAIN)
                    );
                }
            } else {
                $response = array(
                    'success' => false,
                    'msg' => __("There isn't any charge for this withdraw request!", ET_DOMAIN)
                );
            }
        }
        else{
            $response = array(
                'success'=>false,
                'msg'=> __('Please login to your administrator to update withdraw!', ET_DOMAIN)
            );
        }
        wp_send_json($response);
    }
    /**
      * notication
      * @param object $withdraw
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE Credit
      * @author ThanhTu
      */
    public function ae_withdraw_notification($result, $type= '', $title= ''){
        global $user_ID;
        $content = 'type='. $type . '&admin='. $user_ID;
        $notification = array(
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_status' => 'publish',
            'post_author' => $result['post_author'],
            'post_type' => 'notify',
            'post_title' => $title,
            'post_parent' => $result['ID']
        );
        if( class_exists('Fre_Notification') ) {
            $fre_noti = Fre_Notification::getInstance();
            $noti = $fre_noti->insert($notification);
        }
    }
    public function ae_withdraw_notify_item( $content, $notify ){
        $post_excerpt = str_replace('&amp;', '&', $notify->post_excerpt);
        parse_str($post_excerpt);
        if (!isset($type) || !$type) return;
        if (!isset($admin) || !$admin) return;
        switch ($type) {
            case 'approve_withdraw':
                # Text:[Admin] Approved your withdraw request.
                $message = sprintf(__('%s approved your withdraw request.', ET_DOMAIN),
                                '<strong class="author-admin">'.get_the_author_meta('display_name', $admin).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.et_get_page_link('my-credit').'">
                                <span class="notify-avatar">'. get_avatar($admin, 48) .'</span>
                                <span class="notify-info">'.$message.'</span>
                                <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
                break;
            case 'cancel_withdraw':
                # Text:[Admin] Declined your withdraw request.
                $message = sprintf(__('%s declined your withdraw request.', ET_DOMAIN),
                                '<strong class="author-admin">'.get_the_author_meta('display_name', $admin).'</strong>'
                            );
                $content .= '<a class="fre-notify-wrap" href="'.et_get_page_link('my-credit').'">
                                <span class="notify-avatar">'. get_avatar($admin, 48) .'</span>
                                <span class="notify-info">'.$message.'</span>
                                <span class="notify-time">'.sprintf(__("%s on %s", ET_DOMAIN) , get_the_time('', $notify->ID),  get_the_date('', $notify->ID)).'</span>
                            </a>';
                break;
            default:
                # code...
                break;
        }
        return $content;
    }
    /**
      * decline msg
      *
      * @param array $vars
      * @return array $vars
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function fre_credit_decline_msg($vars){
        $vars['confirm_message'] = __('Are you sure to decline this request?', ET_DOMAIN);
        return $vars;
    }
}
/**
 * add footer template
 *
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function fre_credit_admin_footer_function() {
    include_once dirname(__FILE__) . '/admin-template/withdraw-item-js.php';
}
add_action('admin_footer', 'fre_credit_admin_footer_function');
/**
 * get withdraws list
 *
 * @param array $args
 * @return WP_QUERY $withdraw_query
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function get_withdraws($args = array()){
    $default_args = array(
        'paged' => 1,
        'post_status' => array(
            'pending',
            'publish',
            'draft'
        )
    );
    $args = wp_parse_args($args, $default_args);
    $args['post_type'] = 'fre_credit_withdraw';
    $withdraw_query = new WP_Query($args);
    return $withdraw_query;
}
new Fre_Credit_WithdrawAction();