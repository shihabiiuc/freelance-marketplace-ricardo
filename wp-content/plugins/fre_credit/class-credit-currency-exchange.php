<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 2:35 PM
 */
class FRE_Credit_Currency_Exchange extends AE_Base{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct(){

    }
    /**
      * exchange currency
      *
      * @param float $number
      * @param FRE_Credit_Currency $from_currency
      * @param FRE_Credit_Currency $to_currency
      * @return float number after exchange
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function exchange($number, $from_currency = '', $to_currency = ''){
        $default = new FRE_Credit_Currency();
        if( null == $from_currency || empty($from_currency) ){
            $from_currency = $default;
        }
        if( null == $to_currency || empty($to_currency) ){
            $to_currency = $default;
        }
        if( trim($from_currency->code) == trim($to_currency->code)){
            return $number;
        }
        else{
            $rate = $to_currency->rateExchange / $from_currency->rateExchange;
            return (float)($number*$rate);
        }
    }
    /**
      * convert any wallet to user
      *
      * @param integer $user_id
     * @param FRE_Credit_Wallet
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convertToUserCurrency($user_id, $wallet ){
        $user_wallet = FRE_Credit_Users()->getUserWallet($user_id);
        return $this->exchange($wallet->balance, $wallet->currency, $user_wallet->currency);
    }

}