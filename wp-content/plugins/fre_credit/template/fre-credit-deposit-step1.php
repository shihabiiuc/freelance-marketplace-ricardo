<!-- Step 1 -->
<?php
    global $user_ID, $ae_post_factory;
    $ae_pack = $ae_post_factory->get('fre_credit_plan');
    $packs = $ae_pack->fetch('fre_credit_plan');
?>
<div id="fre-post-project-1 step-plan" class="fre-post-project-step step-wrapper step-plan active">
    <div class="fre-post-project-box">
        <div class="step-post-package">
            <h2><?php _e('Choose your most appropriate package', ET_DOMAIN)?></h2>
            <ul class="fre-post-package">
             <?php foreach ($packs as $key => $package) {
                if( $package->et_price ) {
                    $price = fre_price_format($package->et_price);
                }else {
                    $price = __("Free", ET_DOMAIN);
                }

                if($package->et_price > 0){
                    if($package->et_price > 1){
                        $text = sprintf(__("%s for %s credits.", ET_DOMAIN) , $price, $price);
                    }else{
                        $text = sprintf(__("%s for %s credit.", ET_DOMAIN) , $price, $price);
                    }
                }else{
                    $text = sprintf(__("%s for %s credits.", ET_DOMAIN) , $price, $price);
                }
            ?>
                <li data-sku="<?php echo trim($package->sku);?>"
                    data-id="<?php echo $package->ID ?>"
                    data-package-type="<?php echo $package->post_type; ?>"
                    data-price="<?php echo $package->et_price; ?>"
                    data-title="<?php echo $package->post_title ;?>"
                    data-description="<?php echo $text;?>">
                    <label class="fre-radio" for="package-<?php echo $package->ID?>">
                        <input id="package-<?php echo $package->ID?>" name="post-package" type="radio">
                        <span><?php echo $package->post_title ; ?></span>
                    </label>
                    <span class="disc"><?php echo $text;?> <?php echo wp_strip_all_tags( $package->post_content );?></span>
                </li>
            <?php } ?>
            </ul>
            <?php
            echo '<script type="data/json" id="package_plans">'.json_encode($packs).'</script>';
            ?>
            <div class="fre-select-package-btn">
                <!-- <a class="fre-btn" href="">Select Package</a> -->
                <input class="fre-btn fre-post-project-next-btn select-plan" type="button" value="<?php _e('Next Step', ET_DOMAIN);?>">
            </div>
        </div>
    </div>
</div>
