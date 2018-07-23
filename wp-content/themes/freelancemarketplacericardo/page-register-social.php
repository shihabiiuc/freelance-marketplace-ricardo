<?php
/**
 * Template name: Register With Social
 */
get_header();
?>
<div class="fre-page-wrapper">
	<div class="fre-page-section">
		<div class="container">
			<div class="fre-authen-wrapper">
				<div class="fre-authen-social">
					<h2><?php _e('Sign In With Facebook', ET_DOMAIN);?></h2>
					<form role="form" id="register_social_form" class="auth-form forgot_form">
						<div class="fre-input-field">
							<input type="text" placeholder="<?php _e('Your name', ET_DOMAIN);?>">
							<!-- <div class="message">This field is required.</div> -->
						</div>
						<div class="fre-input-field">
							<select class="fre-chosen-single" name="" id="">
								<option selected disabled value=""><?php _e('Choose your role',ET_DOMAIN);?></option>
								<option value="employer"><?php _e('Employer',ET_DOMAIN);?></option>
								<option value="freelancer"><?php _e('Freelancer',ET_DOMAIN);?></option>
							</select>
						</div>
						<div class="fre-input-field">
							<button class="fre-submit-btn btn-submit"><?php _e('Submit', ET_DOMAIN);?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>