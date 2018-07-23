<?php

/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 11/16/2015
 * Time: 1:45 PM
 */
class FRE_Credit_UsersTest extends WP_UnitTestCase
{
    public $credit_users;
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
        $this->credit_users = FRE_Credit_Users::getInstance();
    }
    public function test_AssertTrue(){
        $this->assertTrue(true);
    }
    /**
      * test for function setUserWallet
      * @param integer $user_id
      * @param FRE_Credit_Wallet $wallet
     * @param FRE_Credit_Wallet $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider dataProvider_test_setUserWallet
      */
    public function test_setUserWallet($user_id, $wallet, $type, $expected){
        $this->credit_users->setUserWallet($user_id, $wallet, $type);
        if( $type == 'freezable' ){
            $result = get_user_meta($user_id, 'fre_user_wallet_freezable', true);
        }
        else{
            $result = get_user_meta($user_id, 'fre_user_wallet', true);
        }
        $this->assertEquals($expected, $result);
    }
    /**
      * dataProvider for test_setUserWallet function
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setUserWallet(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $wallet = new FRE_Credit_Wallet();
        $type = 'available';
        $type1 = 'freezable';
        return array(
            array($employer, $wallet, $type, $wallet ),
            array(null, $wallet, $type, false ),
            array($employer, null, $type, $wallet),
            array($employer, $wallet, $type1, $wallet ),
            array(null, $wallet, $type1, false ),
            array($employer, null, $type1, $wallet)

        );
    }
    /**
      * test function getUserWallet
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function test_getUserWallet(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $wallet = new FRE_Credit_Wallet();
        $wallet1 = new FRE_Credit_Wallet(1);
        update_user_meta($employer, 'fre_user_wallet', $wallet);
        update_user_meta($employer, 'fre_user_wallet_freezable', $wallet1);
        $type = 'freezable';
        $type1 = 'available';
        $result = $this->credit_users->getUserWallet($employer);
        $result1 = $this->credit_users->getUserWallet($employer, $type);
        $this->assertEquals($wallet, $result);
        $this->assertEquals($wallet1, $result1);
    }
    /**
      * test updateUserBalance
      * @param integer $user_id
     * @param float $balance
     * @param FRE_Credit_Wallet $user_wallet
     * @param FRE_Credit_Wallet $expected
     * @param string $type
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
     * @dataProvider dataProvider_test_updateUserBalance
      */
    public function test_updateUserBalance( $user_id, $balance, $user_wallet, $type, $expected){
        if( $type = 'freezable' ){
            update_user_meta($user_id, 'fre_user_wallet_freezable', $user_wallet);
        }
        else {
            update_user_meta($user_id, 'fre_user_wallet', $user_wallet);
        }
        $this->credit_users->updateUserBalance($user_id, $balance, $type);
        $result =  $this->credit_users->getUserWallet($user_id, $type);
        $this->assertEquals($expected, $result);
    }
    /**
      * data provider for test_updateUserBalance
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_updateUserBalance(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $balance = 2;
        $user_wallet = new FRE_Credit_Wallet(3);
        $expected = new FRE_Credit_Wallet(2);
        $type = 'available';
        $type1 = 'freezable';
        return array(
            array($employer, $balance, $user_wallet, $type, $expected),
            array($employer, $balance, $user_wallet, $type1, $expected)
        );
    }
    /**
      * test for function check balance
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      *
      * @dataProvider  dataProvider_test_checkBalance
      */
    public function test_checkBalance($user_id, $wallet, $user_wallet, $expected){
        $this->credit_users->setUserWallet($user_id, $user_wallet);
        $result = $this->credit_users->checkBalance($user_id, $wallet);
        $this->assertEquals($expected, $result);
    }
    /**
      * dataProvider for test_checkBalance function
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_checkBalance(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $wallet = new FRE_Credit_Wallet(3);
        $wallet1 = new FRE_Credit_Wallet(4);
        $user_wallet = new FRE_Credit_Wallet(3);
        return array(
            array($employer, $wallet, $user_wallet, 0),
            array($employer, $wallet1, $user_wallet, -1),
        );
    }
    /**
      * test function setSecureCode
      * @param integer $user_id
     * @param string $code
     * @param string $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
     * @dataProvider dataProvider_test_setSecureCode
      */
    public function test_setSecureCode($user_id, $code, $expected){
        $this->credit_users->setSecureCode($user_id, $code);
        $result = get_user_meta($user_id, 'fre_credit_secure_code', true);
        $this->assertEquals($expected, $result);

    }
    /**
      * data provider for test
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_setSecureCode(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $code = '123456';
        return array(
            array($employer, $code, md5($code) ),
            array( $employer, '', md5(''))
        );
    }
    /**
      * test function getSecureCode
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_getSecureCode(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $code = '123456';
        $this->credit_users->setSecureCode($employer, $code);
        $result = $this->credit_users->getSecureCode($employer);
        $this->assertEquals(md5($code), $result);
    }
    /**
      * test function checkSecureCode
      * @param integer $user_id
     * @param string $code
     * @param string $code_check
     * @param boolean $expected
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
     *
     * @dataProvider dataProvider_test_checkSecureCode
      */
    public function test_checkSecureCode($user_id, $code, $code_check, $expected ){
        $this->credit_users->setSecureCode($user_id, $code);
        $result = $this->credit_users->checkSecureCode($user_id, $code_check);
        $this->assertEquals($expected, $result);
    }
    /**
      * data provider
      * @param void
      * @return array data
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function dataProvider_test_checkSecureCode(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $code = '123456';
        $code_check1 = '123456';
        $code_check2 = '12';
        return array(
            array($employer, $code, $code_check1, true),
            array($employer, $code, $code_check2, false),
        );
    }
    /**
      * test for function charge
      * @param void
      * @return void
      * @since 1.0
      * @package TEST FREELANCEENGINE
      * @category TEST FRE CREDIT
      * @author Jack Bui
      */
    public function test_charge(){

    }
    /**
     * data provider
     * @param void
     * @return array data
     * @since 1.0
     * @package TEST FREELANCEENGINE
     * @category TEST FRE CREDIT
     * @author Jack Bui
     */
    public function dataProvider_test_charge(){
        $employer = $this->factory->user->create( array( 'role' => EMPLOYER ) );
        $code = '123456';
        return array(
            array($employer, $code, $code_check1, true),
            array($employer, $code, $code_check2, false),
        );
    }
}
