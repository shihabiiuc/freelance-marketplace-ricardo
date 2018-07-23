<?php
if (!class_exists('AE_Braintree_Update') && class_exists('AE_Plugin_Updater')){
    class AE_Braintree_Update extends AE_Plugin_Updater{
        const VERSION = '1.1';

        // setting up updater
        public function __construct(){
            $this->product_slug     = plugin_basename( dirname(__FILE__) . '/braintree.php' );
            $this->slug             = 'fre_braintree';
            $this->license_key      = get_option('et_license_key', '');
            $this->current_version  = self::VERSION;
            $this->update_path      = 'http://update.enginethemes.com/?do=product-update&product=fre_braintree&type=plugin';

            parent::__construct();
        }
    }
    new AE_Braintree_Update();
}
