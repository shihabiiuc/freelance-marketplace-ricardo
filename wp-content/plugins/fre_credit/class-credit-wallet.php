<?php
/**
 * This class will include all attribute and method for user wallet
 * User: Jack Bui
 * Date: 11/13/2015
 * Time: 4:34 PM
 */
class FRE_Credit_Wallet extends AE_Base{
    public $currency;
    public $balance;
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
      * The construct for this class
      * @param float $balance
      * @param FRE_Credit_Currency $currency
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function __construct($balance = 0 , $currency = '' ){
        if( null == $currency || empty($currency) ){
            $currency = new FRE_Credit_Currency('usd', '$', true, 1);
        }
        $this->setBalance($balance);
        $this->setCurrency($currency);

    }
    /**
      * set value for currency
      * @param FRE_Credit_Currency $currency
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setCurrency($currency= ''){
        if( null == $currency || empty($currency) ){
            $currency = new FRE_Credit_Currency('usd', '$', true, 1);
        }
        $this->currency = $currency;
    }
    /**
      * get currency
      * @param void
      * @return FRE_Credit_Currency currency
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getCurrency(){
        return $this->currency;
    }
    /**
      * set balance
      * @param float $balance
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setBalance($balance = 0){
        if( null == $balance || empty($balance) ){
            $balance = 0;
        }
        $this->balance = (float)$balance;
    }
    /**
      * get balance
      * @param void
      * @return float balance
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getBalance(){
        return (float)$this->balance;
    }

}