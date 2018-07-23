<?php

	require_once dirname(__FILE__) . '/inc/inc.update.php';
	if (!class_exists('AE_Pin_Update')){
		class AE_Pin_Update extends AE_Plugin_Updater{
			const VERSION = '1.1';

			// setting update
			public function __construct(){
				$this->product_slug 	= plugin_basename( dirname(__FILE__). '/ae_pin.php');
				$this->slug 			= 'pin';
				$this->license_key 		= get_option('et_license_key','');
				$this->current_version	= self::VERSION;
				$this->update_path		= 'http://www.enginethemes.com/forums/?do=product-update&product=ae_pin&type=plugin';

				parent::__construct();
			}
		}
		new AE_Pin_Update();
	}

?>