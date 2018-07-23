<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 10:05 AM
 */
class FRE_Credit_CurrencyTest extends WP_UnitTestCase
{
    public $currency;
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
        $this->currency = FRE_Credit_Currency::getInstance();
    }
    public function test_AssertTrue(){
        $this->assertTrue(true);
    }
    /**
      * test function getCode
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getCode(){
        $class = new FRE_Credit_Currency('usd', '$', true, 1);
        $code = $class->getCode();
        $this->assertEquals('usd', $code);
    }
    /**
      * test setCode function
      * @param string $data
      * @param string $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_setCode
      */
    public function test_setCode($data, $expected){
        $this->currency->setCode($data);
        $this->assertEquals($expected, $this->currency->code);
    }
    /**
      * data provider for test_setCode
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setCode(){
        return array(
            array('eur', 'eur'),
            array('', 'usd' ),
            array(null, 'usd')
        );
    }
    /**
      * test function get signal
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getSingal(){
        $class = new FRE_Credit_Currency('usd', '$', true, 1);
        $signal = $class->getSignal();
        $this->assertEquals('$', $signal);
    }
    /**
      * test function setSingal
      * @param string $data
      * @param string $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
      * @dataProvider dataProvider_test_setSignal
      */
    public function test_setSignal($data, $expected){
        $this->currency->setSignal($data);
        $this->assertEquals($expected, $this->currency->signal);
    }
    /**
      * dataProvider for test_setSingal
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setSignal(){
        return array(
            array('€', '€'),
            array('', '$'),
            array(null, '$')
        );
    }
    /**
      * test getIsDefault function
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getIsDefault(){
        $class = new FRE_Credit_Currency('usd', '$', true, 1);
        $this->assertTrue($class->isDefault);
    }
    /**
      * test for function setIsDefault
      * @param boolean $data
      * @param boolean $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_setIsDefault
      */
    public function test_setIsDefault($data, $expected){
        $this->currency->setIsDefault($data);
        $this->assertEquals($expected, $this->currency->isDefault);
    }
    /**
      * dataProvider for test_setIsDefault function
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setIsDefault(){
        return array(
            array(true, true),
            array('', false),
            array(null, false)
        );
    }
    /**
      * test for function getRateExchange
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getRateExchange(){
        $class = new FRE_Credit_Currency('usd', '$', true, 1);
        $this->assertEquals( 1,$class->rateExchange );
    }
    /**
      * test for function setRateExchange
      * @param float $data
     * @param float $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
     * @dataProvider dataProvider_test_setRateExchange
      */
    public function test_setRateExchange($data, $expected){

    }
    /**
      * data provider for test_setRateExchange function
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setRateExchange(){
        return array(
            array(2.1, 2.1),
            array('', 1),
            array(null, 1)
        );
    }
}
