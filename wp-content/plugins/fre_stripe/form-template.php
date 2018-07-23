<div id="fre-payment-stripe" class="panel-collapse collapse fre-payment-proccess">
	<form class="modal-form" id="stripe_form" autocomplete="on" data-ajax="false">
		<div class="fre-input-field">
            <label class="fre-field-title" for=""><?php _e('Name on card',ET_DOMAIN);?></label>
            <input type="text" name='name' id="name_card" data-stripe="name" placeholder="<?php _e('Name on card',ET_DOMAIN);?>">
        </div>
        <div class="row card-number-wrap">
            <div class="col-lg-8 col-md-7 col-sm-7">
                <div class="fre-input-field">
                    <label class="fre-field-title" for=""><?php _e('Card number',ET_DOMAIN);?></label>
                    <input type="number" name="number" id="stripe_number" data-stripe="number" placeholder="****  ****  ****  ****">
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-sm-5">
                <div class="fre-input-field fre-card-expiry">
                    <label class="fre-field-title " for=""><?php _e('Expiry date',ET_DOMAIN);?></label>
                    <input class="fre-card-expiry-month" name='exp_month' data-stripe="exp-month" id="exp_month" type="text" placeholder="<?php _e('MM',ET_DOMAIN);?>">
                    <input class="fre-card-expiry-year" name='exp_year' data-stripe="exp-year" id="exp_year" type="text" placeholder="<?php _e('YY',ET_DOMAIN);?>">
                </div>
            </div>
        </div>
        <div class="fre-input-field">
            <label class="fre-field-title" for=""><?php _e('Card code',ET_DOMAIN);?></label>
            <input type="text" name='cvc' size="3" data-stripe="cvc" id="cvc" placeholder="<?php _e('CVC',ET_DOMAIN);?>">
        </div>
        <div class="fre-proccess-payment-btn">
            <button class="fre-btn" id="submit_stripe" ><?php _e('Make Payment',ET_DOMAIN);?></button>
        </div>
	</form>
</div>
