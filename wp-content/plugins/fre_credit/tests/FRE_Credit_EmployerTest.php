<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 3:52 PM
 */
class FRE_Credit_EmployerTest extends WP_UnitTestCase
{
    public $employer;
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
        $this->employer = FRE_Credit_Employer::getInstance();
    }
    public function test_assertTrue(){
        $this->assertTrue(true);
    }
    /**
      * test init function
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_init(){
        $this->employer->init();
        $this->assertTrue(has_filter('ae_support_gateway'));
        $this->assertTrue(has_action('after_payment_list'));
    }
    /**
      * test function fre_credit_support
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_fre_credit_support(){
        $gateway = array();
        $newGateway = $this->employer->fre_credit_support($gateway);
        $this->assertEquals(array('frecredit'=>'frecredit'), $newGateway);

    }
    /**
      * test function fre_submit_project_payment
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_fre_submit_project_payment(){

    }
}
