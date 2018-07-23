<?php global $post; ?>
<script type="text/template" id="fre-credit-withdraw-loop">
	<div class="method">
		<#	if(post_status == 'pending') { #>
				<a title="<?php _e("Approve", ET_DOMAIN); ?>" data-action="approve" class="color-green action publish" data-id="{{= ID }}" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="<?php _e("Decline", ET_DOMAIN); ?>" data-action="archive" class="color-red action decline" data-id="{{= ID }}" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
			<# } #>
	</div>
	<div class="content">
		<?php if( $post ) { ?>
		<#	if(post_status == 'pending') { #>
				<a title="<?php _e("Pending", ET_DOMAIN)?>" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>
		<#	}else if( post_status == 'publish'){ #>
				<a title="<?php _e("Confirmed", ET_DOMAIN) ?>" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>
		<# } else{ #>
				<a title="<?php _e("Failed", ET_DOMAIN) ?>" class="color" style="color :grey;" href="#"><span class="icon" data-icon="*"></span></a>
		<#		}  #>
		<?php	} ?>
			<span class="price font-quicksand">
				<?php //echo ae_currency_sign(false) . $order_data['total']; ?>
			</span>
		<?php if( $post ): ?>
				<a target="_blank" rel="noopener noreferrer" href="{{= withdraw_edit_link }}" class="ad ad-name">
					{{= post_title }}
				</a>
			<?php endif;
			 	_e(' by ', ET_DOMAIN); ?>
			<a target="_blank" rel="noopener noreferrer" href="{{= withdraw_author_url }}" class="company">
				{{= withdraw_author_name }}
			</a>
	</div>
</script>