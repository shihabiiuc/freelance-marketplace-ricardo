<?php
global $post, $ae_post_factory;
$withdraw_obj = $ae_post_factory->get('fre_credit_withdraw');
$withdraw = $withdraw_obj->convert($post);

$history_id = get_post_meta($post->ID,'charge_id', true);
$history_status = get_post_meta($history_id,'history_status', true);
?>
<li class="withdraw-item withdraw-item-<?php echo $post->ID;?>" >
	<div class="method">
		<?php
			if($withdraw->post_status == 'pending') : ?>
				<a title="<?php _e("Approve", ET_DOMAIN); ?>" data-action="approve" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="<?php _e("Decline", ET_DOMAIN); ?>" data-action="archive" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
		<?php
			endif;
		?>
	</div>
	<div class="content">
		<?php
		if( $post ) {

			switch ($withdraw->post_status) {
			case 'pending':
				echo '<a title="' . __("Pending", ET_DOMAIN) . '" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>';
				break;
			case 'publish':
			case 'completed':
				echo '<a title="'. __("Confirmed", ET_DOMAIN) . '" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>';
				break;
			default:
				echo '<a title="' .__("Failed", ET_DOMAIN) .'" class="color" style="color :grey;" href="#"><span class="icon" data-icon="*"></span></a>';
				break;
			} ?>
			<span class="price font-quicksand">
				<?php //echo ae_currency_sign(false) . $order_data['total']; ?>
			</span>
		<?php if($withdraw): ?>
				<a target="_blank" rel="noopener noreferrer" href="<?php echo get_edit_post_link( $withdraw->ID ) ?>" class="ad ad-name">
					<?php echo $withdraw->post_title; ?>
				</a>
			<?php endif;
			 	_e(' by ', ET_DOMAIN); ?>
			<a target="_blank" rel="noopener noreferrer" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="company">
				<?php echo get_the_author_meta('display_name',$post->post_author) ?>
			</a>
		<?php
		} else {
			$author	=	'<a target="_blank" rel="noopener noreferrer" href="'.get_author_posts_url($post->post_author).'" class="company">' .
							get_the_author_meta('display_name',$post->post_author) .
						'</a>';
		?>
			<span>
				<?php printf (__("This post has been deleted by %s", ET_DOMAIN) , $author ); ?>
			</span>
		<?php
			}
		?>

	</div>
</li>