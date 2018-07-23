<?php
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<div id="fre-payment-frecredit" class="panel-collapse collapse fre-payment-proccess">
	<form class="modal-form" id="submit_fre_credit_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
		<div class="fre-input-field">
			<label class="title-name"><?php _e('Your balance will be deducted:', ET_DOMAIN);?></label>
			<span class="package_price fee-package">--</span>
		</div>
		<div class="fre-input-field">
			<label class="title-name"><?php _e('Estimated balance after payment:', ET_DOMAIN);?></label>
			<span class="estimate_balance fee-package"></span>
		</div>
		<?php if(ae_get_option('fre_credit_secure_code', true)): ?>
			<div class="fre-input-field">
				<label class="fre-field-title"><?php _e('Your secure code:', ET_DOMAIN);?></label>
				<input tabindex="20" id="fre_credit_secure_code" type="password" size="20" name="fre_credit_secure_code" required />
			</div>
		<?php endif; ?>
        <div class="fre-proccess-payment-btn">
            <button class="fre-btn btn-pay-balance"" id="submit_fre_credit" ><?php _e('PAY TO YOUR BALANCE',ET_DOMAIN);?></button>
        </div>
	</form>
</div>
