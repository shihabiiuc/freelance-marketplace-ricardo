<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/13/2015
 * Time: 10:45 AM
 */
class testFunctions extends WP_UnitTestCase
{
    public  function  testAssert(){
        $this->assertTrue(true);
    }
    /**
      * test function FRE_Credit_Users
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_FRE_Credit_Users(){
        $instance = FRE_Credit_Users::getInstance();
        $this->assertInstanceOf('FRE_Credit_Users', $instance);
    }
    /**
      * test for function FRE_Credit_Wallet
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_FRE_Credit_Wallet(){
        $instance = FRE_Credit_Wallet::getInstance();
        $this->assertInstanceOf('FRE_Credit_Wallet', $instance);
    }
    /**
      * test for function FRE_Credit_Currency
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_FRE_Credit_Currency(){
        $instance = FRE_Credit_Currency::getInstance();
        $this->assertInstanceOf('FRE_Credit_Currency', $instance);
    }
    /**
      * test for function fre_credit_get_payment_currency
      * @param string $code
     * @param string $signal
     * @param FRE_Credit_Currency $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
     * @dataProvider dataProvider_test_fre_credit_get_payment_currency
      */
    public function test_fre_credit_get_payment_currency($code, $signal, $expected){
        $currency = array (
            'code'=> $code,
            'icon'=> $signal,
            'align'=> 0
        );
        update_option('currency', $currency);
        $result = fre_credit_get_payment_currency();
        $this->assertEquals($expected, $result);
    }
    /**
      * dataProvider for test_fre_credit_get_payment_currency
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_fre_credit_get_payment_currency(){
        $expected = new FRE_Credit_Currency('usd', '$', true, 1);
        $expected1 = new FRE_Credit_Currency('usd','$', true, 1);
        return array(
            array('', '', $expected),
            array('usd', '$', $expected1)
        );
    }
    /**
      * test function fre_credit_convert_wallet
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_fre_credit_convert_wallet(){
        $currency = array (
            'code'=> 'usd',
            'icon'=> '$',
            'align'=> 0
        );
        update_option('currency', $currency);
        $currency = fre_credit_get_payment_currency();
        $expected = new FRE_Credit_Wallet(3, $currency);
        $result = fre_credit_convert_wallet(3);
        $this->assertEquals($expected, $result);
    }
    /**
      * test for function is_use_credit_escrow
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Tambh
      */
    public function test_is_use_credit_escrow(){
        update_option('escrow_credit_settings', array('use_credit_escrow'=> true));
        $result = is_use_credit_escrow();
        $this->assertTrue($result);
    }
    /**
      * test for function fre_parse_form_data
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_fre_parse_form_data(){
        $string = 'fre_credit_secure_code=sfsfsf';
        $expected = array('fre_credit_secure_code'=>'sfsfsf');
        $result = fre_parse_form_data($string);
        $this->assertEquals($expected, $result);
    }
}
