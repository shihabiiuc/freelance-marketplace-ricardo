<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 9:41 AM
 */
class FRE_Credit_WalletTest extends WP_UnitTestCase
{
    public $credit_wallet;
    /**
      * setup for this class
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function setUp(){
        parent::setUp();
        $this->credit_wallet = FRE_Credit_Wallet::getInstance();
    }
    public function test_AssertTrue(){
        $this->assertTrue(true);
    }
    /**
      * test function setCurrency
      * @param FRE_Credit_Currency $data
      * @param FRE_Credit_Currency $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_setCurrency
      */
    public function test_setCurrency($data, $expected){
        $this->credit_wallet->setCurrency($data);
        $this->assertEquals($this->credit_wallet->currency, $expected);
    }
    /**
      * data for test setCurrency
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setCurrency(){
        $default = new FRE_Credit_Currency('usd', '$', true, 1);
        $currency = new FRE_Credit_Currency('eur', '€', false, 0.93);
        return array(
            array($currency, $currency),
            array('', $default),
            array(null, $default)
        );
    }
    /**
      * test function for getCurrency
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getCurrency(){
        $currency = new FRE_Credit_Currency('usd', '$', true, 1);
        $class = new FRE_Credit_Wallet(1, $currency);
        $result = $this->credit_wallet->getCurrency();
        $this->assertEquals($class->currency, $result);
    }
    /**
      * test for setBalance funtion
      * @param float data
      * @param float $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_setBalance
      */
    public function test_setBalance($data, $expected){
        $this->credit_wallet->setBalance($data);
        $this->assertEquals($expected, $this->credit_wallet->balance);
    }
    /**
      * dataProvider for test_setBalance
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setBalance(){
        return array(
            array(2, 2),
            array('', 0),
            array(null, 0)
        );
    }
}
