<?php
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<div id="fre-payment-pin" class="panel-collapse collapse fre-payment-proccess">
	<form class="modal-form" id="pin_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
		<div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('Name on card', ET_DOMAIN);?></label>
	        <input tabindex="23" name="" id="cc-name"  value="Roland C Robot" data-pin="name" class="bg-default-input not_empty" type="text" />
	    </div>
	    <div class="row card-number-wrap">
	        <div class="col-lg-8 col-md-7 col-sm-7">
	            <div class="fre-input-field">
	                <label class="fre-field-title" for=""><?php _e('Card number:', ET_DOMAIN);?>Card number</label>
	                <input tabindex="20" id="cc-number" type="text" value="5520000000000000" size="20"  data-pin="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226;" />
	            </div>
	        </div>
	        <div class="col-lg-4 col-md-5 col-sm-5">
	            <div class="fre-input-field fre-card-expiry">
	                <label class="fre-field-title " for=""><?php _e('Expiry date', ET_DOMAIN);?></label>
	                <input tabindex="22" type="text" value="5" size="4" data-pin="exp-year" placeholder="MM"  class="fre-card-expiry-month bg-default-input not_empty" id="cc-expiry-month"/>
			      	<input tabindex="21" type="text" value="16" size="2" data-pin="exp-month" placeholder="YY"  class="fre-card-expiry-year bg-default-input not_empty" id="cc-expiry-year"/>
	            </div>
	        </div>
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('Card code', ET_DOMAIN);?></label>
	        <input tabindex="23" name="" id="cc-name" value="Roland C Robot" data-pin="name" class="bg-default-input not_empty" type="text" />
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('Address line', ET_DOMAIN);?></label>
	        <input tabindex="23" name="" id="address-line" value="gfda"  data-pin="address" class="bg-default-input not_empty" type="text" />
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('City', ET_DOMAIN);?></label>
	        <input tabindex="23" name="" id="address-city" value="bd" data-pin="city" class="bg-default-input not_empty" type="text" />
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('State', ET_DOMAIN);?></label>
	        <input tabindex="24" type="text" size="3" value="db" data-pin="state" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="address-state" />
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('Country', ET_DOMAIN);?></label>
	        <select class="form-control-pin fre-chosen-single" id="address-country" name="address-country">
				<script type="text/javascript" >
                    document.write(getCountryOptionsListHtml("<?php echo htmlentities('GB'); ?>"));
                </script>
            </select>
	    </div>
	    <div class="fre-input-field">
	        <label class="fre-field-title" for=""><?php _e('Postcode', ET_DOMAIN);?></label>
	        <input tabindex="24" type="text" size="3" value="2600" data-pin="postcode" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="address-postcode" />
	    </div>
	    <div class="fre-proccess-payment-btn">
	        <button class="fre-btn"><?php _e('Make Payment', ET_DOMAIN);?></button>
	    </div>
	</form>
</div>