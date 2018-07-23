<div id="fre-payment-braintree" class="panel-collapse collapse fre-payment-proccess">
    <div class="form-group">
		<div id="dropin-container"></div>
	</div>
	<form id="checkout-form">
		<div class="content">
			<input type="hidden" id="braintree-payment-nonce" name="braintree-payment-nonce">
		</div>
		<div class="button">
			<button class="btn-sumary orange-dark box-shadow-button-orange btn-submit" type="submit">
				<?php _e("Make Payment",ET_DOMAIN);?>
			</button>
		</div>
	</form>

	<form id="braintree_form" style="display: none;">
		<div class="content">
			<input type="hidden" id="braintree-payment-nonce" name="braintree-payment-nonce">
		</div>
		<div class="button">
			<button class="btn-sumary fre-btn btn-submit" type="submit" id="submit_braintree" >	
				<?php _e('Make Payment',ET_DOMAIN);?>
			</button>
		</div>
	</form>
</div>