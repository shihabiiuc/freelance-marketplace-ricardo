<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 9:52 AM
 */
class FRE_Credit_Currency extends AE_Base{
    public static $instance;
    public $code;
    public $signal;
    public $isDefault;
    public $rateExchange;
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
     * @param string $code
     * @param string $signal,
     * @param boolean $isDefault true if this currency is default and false if it isn't
     * @param float $rateExchange
     *
     * @return void
     *
     */
    public  function __construct($code = 'usd', $signal = '$', $isDefault = '', $rateExchange = 1){
        $this->setCode($code);
        $this->setSignal($signal);
        $this->setIsDefault($isDefault);
        $this->setRateExchange($rateExchange);
    }
    /**
      * get code
      * @param void
      * @return string $code
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getCode(){
        return $this->code;
    }
    /**
      * set Code
      * @param string $code
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setCode($code){
        if( null == $code || '' == $code ){
            $code = 'usd';
        }
        $this->code = $code;
    }
    /**
      * get signal
      * @param void
      * @return string $signal
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getSignal(){
        return $this->signal;
    }
    /**
      * function set signal
      *
      * @param string $signal
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setSignal($signal){
        if( null == $signal || '' == $signal ){
            $signal = '$';
        }
        $this->signal = $signal;
    }
    /**
      * get isDefault
      *
      * @param void
      * @return boolean true if this currency is default and false if it isn't
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getIsDefault(){
        return $this->isDefault;
    }
    /**
      * set currency to default
      *
      * @param boolean $isDefault
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setIsDefault($isDefault){
        if( null == $isDefault || '' == $isDefault ){
            $isDefault = false;
        }
        $this->isDefault = $isDefault;
    }
    /**
      * get rate exchange for this currency
      *
      * @param void
      * @return float rate exchange
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function getRateExchange(){
        return $this->rateExchange;
    }
    /**
      * set Rate exchange for this currency
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function setRateExchange($rateExchange){
        if( null == $rateExchange || ''== $rateExchange ){
            $rateExchange = 1;
        }
        $this->rateExchange = $rateExchange;
    }

}