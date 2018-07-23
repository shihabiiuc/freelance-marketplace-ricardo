<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 2:46 PM
 */
class FRE_Credit_Currency_ExchangeTest extends WP_UnitTestCase
{
    public $currency_exchange;
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
        $this->currency_exchange = FRE_Credit_Currency_Exchange::getInstance();
    }
    public function test_AssertTrue(){
        $this->assertTrue(true);
    }
    /**
      * test function exchange
      * @param float $number
      * @param FRE_Credit_Currency $from_currency
      * @param FRE_Credit_Currency $to_currency
      * @param float $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_exchange
      */
    public function test_exchange($number, $from_currency, $to_currency, $expected){
        $result = $this->currency_exchange->exchange($number, $from_currency, $to_currency);
        $this->assertEquals($expected, $result);
    }
    /**
      * dataProvider for test_exchange function
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_exchange(){
        $default = new FRE_Credit_Currency('usd', '$', true, 1);
        $currency = new FRE_Credit_Currency('eur', '€', false, 0.93);
        return array(
            array(2, '', '', 2),
            array(2, $default, $currency, 2*0.93)
        );
    }
}
