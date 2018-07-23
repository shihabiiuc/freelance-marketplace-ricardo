<?php
if (!class_exists('FRE_Credit') && class_exists('AE_Plugin_Updater')){
    class FRE_Credit extends AE_Plugin_Updater{
        const VERSION = '1.2.3';
        // setting up updater
        public function __construct(){
            $this->product_slug     = plugin_basename( dirname(__FILE__) . '/fre-credit.php' );
            $this->slug             = 'fre-credit';
            $this->license_key      = get_option('et_license_key', '');
            $this->current_version  = self::VERSION;
            $this->update_path      = 'http://update.enginethemes.com/?do=product-update&product=fre-credit&type=plugin';

            parent::__construct();
        }
    }
    new FRE_Credit();
}