<?php
global $ae_post_factory, $post;
$his_obj = $ae_post_factory->get('fre_credit_history');
$convert = $his_obj->convert($post);
$style = $convert->style;
?>


<div class="fre-table-row">
    <div class="fre-table-col table-col-type"> <?php echo $convert->post_title;?> <b><?php echo $convert->amount_text ?></b></div>
    <div class="fre-table-col table-col-amount"><?php echo $convert->amount_text ?></div>
    <div class="fre-table-col table-col-action"> <?php echo $convert->info_changelog ;?></div>
    <div class="fre-table-col table-col-payment"><?php echo $convert->payment_gateway ?></div>
    <div class="fre-table-col table-col-status"><?php echo $convert->history_status_text; ?> <span>on <?php echo $convert->history_time ?></span></div>
    <div class="fre-table-col table-col-time"><?php echo $convert->history_time ?></div>
</div>