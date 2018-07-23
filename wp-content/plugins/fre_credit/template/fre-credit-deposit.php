<?php
/**
 * Template Name: Recharge Pages
 */
global $user_ID;
$user_wallet = FRE_Credit_Users()->getUserWallet($user_ID);
$project_id = !empty($_GET['project_id']) ? $_GET['project_id'] : '';
et_write_session('project_id',$project_id);
?>
<div class="post-place-warpper" id="upgrade-account">
    <?php
    $bid_id = isset($_GET['bid_id']) ? $_GET['bid_id'] : 0;
	if( $bid_id ){
		// new section from version 1.2.3
		$bid = get_post($bid_id);
        $deposit_info = fre_get_deposit_info($bid);
        $text = '';
		?>
    	<ul class="fre-post-package hide">
            <li data-sku="fix_sku"
                data-id="fix_id"
                data-package-type="fre_credit_fix"
                data-price="<?php echo $deposit_info['total']; ?>"
                data-title="Buy credit for project <?php echo get_the_title($bid->post_ttile);?>"
                data-description="<?php echo $text;?>">
                <label class="fre-radio" >
                    <input name="post-package"  type="radio" checked>
                    <span>Buy credit for <i> Post ab </i> project.</span>
                </label>
            </li>

        </ul>
        <input type="hidden" id="itemCheckoutID" value="<?php echo $bid->ID;?>" data-package-type ="fre_credit_fix" />
        <?php
            include dirname(__FILE__) . '/fre-credit-deposit-step4.php';
        // end new section.

	} else {
		include dirname(__FILE__) . '/fre-credit-deposit-step1.php';
		include dirname(__FILE__) . '/fre-credit-deposit-step4.php';
	}


    ?>
</div>
