<?php
/**
 * lwMVC Core Unit tests
 * @package unit
 * @subpackage unittest
 */

/**
 * Test LWMVC
 * @package unit
 * @subpackage unittest
 *
 */
class test_lwmvc extends UnitTestCase {
	public function test_create(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		if(is_object($fw)){
			$this->assertTrue(true,'Create instance of lwmvc framework object.');
		}else{
			$this->assertTrue(false,'Create instance of lwmvc framework object.');
		}
	}
	
	public function test_SETCaptureExternalFormPosts(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		if(!$fw->setCaptureExternalFormPosts('test')){
			$this->assertTrue(true,'Prevent non-bool in setCaptureExternalFormPosts');
		}else{
			$this->assertTrue(false,'Prevent non-bool in setCaptureExternalFormPosts');
		}
		
		if($fw->setCaptureExternalFormPosts(true)){
			$this->assertTrue(true,'Allow bool in setCaptureExternalFormPosts');
		}else{
			$this->assertTrue(false,'Allow bool in setCaptureExternalFormPosts');
		}
		
	}
	
	/* Check for propper handling of invalid framework directory passed.*/
	public function test_setFrameworkDir(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setFrameworkDir('.totally.invalidkrk//d');
		}catch (Exception $e){
			$this->assertTrue(true,'Prevented Invalid Framework Directory.');
			return true;
		}
			$this->assertTrue(false,'Prevented Invalid Framework Directory.');
			return true;
	}
	
	/* Check for propper handling of invalid framework directory passed.*/
	public function test_setFrameworkDir2(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setFrameworkDir('/tmp/');
		}catch (Exception $e){
			$this->assertTrue(true,'Prevented Valid Directory, Which is NOT lwmvc Framework Directory.');
			return true;
		}
			$this->assertTrue(false,'Prevented Valid Directory, Which is NOT lwmvc Framework Directory.');
			return true;
	}
	
		public function test_setFrameworkDir3(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setFrameworkDir('../');
		}catch (Exception $e){
			$this->assertTrue(false,'Confirm I can set a valid framework directory');
			return true;
		}
			$this->assertTrue(true,'Confirm I can set a valid framework directory');
			return true;
	}
	
	
	/* Check for propper handling of invalid template directory passed.*/
	public function test_setTemplateDir(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setTemplateDir('.totally.invalidkrk//d');
		}catch (Exception $e){
			$this->assertTrue(true,'Prevented Invalid Template Directory.');
			return true;
		}
			$this->assertTrue(false,'Prevented Invalid Template Directory.');
			return true;
	}
	/* Check for propper handling of invalid controller directory passed.*/
	public function test_setControllerDir(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setControllerDir('.totally.invalidkrk//d');
		}catch (Exception $e){
			$this->assertTrue(true,'Prevented Invalid Controller Directory.');
			return true;
		}
			$this->assertTrue(false,'Prevented Invalid Controller Directory.');
			return true;
	}
	
	
	public function test_setNamingType(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setControllerNamingType('bad');
		}catch (Exception $e){
			$this->assertTrue(true,'Prevented Invalid Controller Naming Type.');
			return true;
		}
			$this->assertTrue(false,'Prevented Invalid Controller Naming Type.');
			return true;
	}
	
	public function test_setNamingType_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setControllerNamingType(1);
		}catch (Exception $e){
			$this->assertTrue(false,'Allowed Valid Controller Naming Type.');
			return true;
		}
			$this->assertTrue(true,'Allowed Valid Controller Naming Type.');
			return true;
	}
	
	public function test_setsalt_bad(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setPasswordSalt(null);
		}catch (Exception $e){
			$this->assertTrue(true,'Prevent empty salt.');
			return true;
		}
			$this->assertTrue(false,'Prevent empty salt.');
			return true;
	}
	
	public function test_setsalt_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setPasswordSalt('password');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow Valid salt.');
			return true;
		}
			$this->assertTrue(true,'Allow Valid salt.');
			return true;
	}
	
	public function test_overrideRequestParms_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->overrideRequestParms(array('controller'=>'test'));
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid request parm override.');
			return true;
		}
			$this->assertTrue(true,'Allow valid request parm override.');
			return true;
	}
	
	public function test_overrideRequestParms_bad_1(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->overrideRequestParms(array('test'=>'ss'));
		}catch (Exception $e){
			$this->assertTrue(true,'Reject override parm that does not contain a controller element.');
			return true;
		}
			$this->assertTrue(false,'Reject override parm that does not contain a controller element.');
			return true;
	}
	
	public function test_overrideRequestParms_bad_2(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->overrideRequestParms('dd');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject non-array override parm.');
			return true;
		}
			$this->assertTrue(false,'Reject non-array override parm.');
			return true;
	}
	
	public function test_setAuthenticationFailureURL_bad_1(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationFailureURL(null);
		}catch (Exception $e){
			$this->assertTrue(true,'Reject Empty Failure URL.');
			return true;
		}
			$this->assertTrue(false,'Reject Empty Failure URL.');
			return true;
	}
	
	public function test_setAuthenticationFailureURL_bad_2(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationFailureURL(array());
		}catch (Exception $e){
			$this->assertTrue(true,'Reject non-string Failure URL.');
			return true;
		}
			$this->assertTrue(false,'Reject non-string Failure URL.');
			return true;
	}
	
	public function test_setAuthenticationFailureURL_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationFailureURL('www.google.com');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid Failure URL');
			return true;
		}
			$this->assertTrue(true,'Allow valid Failure URL');
			return true;
	}
	
	public function test_assignGlobalSmarty_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->assignGlobalSmarty('test','case');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid Global Smarty Var');
			return true;
		}
			$this->assertTrue(true,'Allow valid Global Smarty Var');
			return true;
	}
	
	public function test_assignGlobalSmarty_bad(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->assignGlobalSmarty(array(),'case');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject invalid Global Smarty Var');
			return true;
		}
			$this->assertTrue(false,'Reject invalid Global Smarty Var');
			return true;
	}
	
	
	
	public function test_assignGlobalSmarty_bad_2(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->assignGlobalSmarty(null,'case');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject null named Global Smarty Var');
			return true;
		}
			$this->assertTrue(false,'Reject null named Global Smarty Var');
			return true;
	}
	
	public function test_setAuthenticationValidationFunction_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationValidationFunction('test');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid function name');
			return true;
		}
			$this->assertTrue(true,'Allow valid function name');
			return true;
	}
	
	public function test_setAuthenticationValidationFunction_bad_1(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationValidationFunction('testnotreal');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject invalid function name');
			return true;
		}
			$this->assertTrue(false,'Reject invalid function name');
			return true;
	}
	
	public function test_setAuthenticationValidationFunction_bad_2(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setAuthenticationValidationFunction(null);
		}catch (Exception $e){
			$this->assertTrue(true,'Reject null function name');
			return true;
		}
			$this->assertTrue(false,'Reject null function name');
			return true;
	}
	
	public function test_setLogFile_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setLogFile('/tmp/log');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid Log File');
			return true;
		}
			$this->assertTrue(true,'Allow valid Log File');
			return true;
	}
	
	public function test_setLogFile_bad1(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setLogFile('/cantwritehere');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject invalid Log File');
			return true;
		}
			$this->assertTrue(false,'Reject invalid Log File');
			return true;
	}
	
	public function test_setDefaultController_good(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setDefaultController('default');
		}catch (Exception $e){
			$this->assertTrue(false,'Allow valid Default Controller');
			return true;
		}
			$this->assertTrue(true,'Allow valid Default Controller');
			return true;
	}
	
	public function test_setDefaultController_bad1(){
		require_once '../lwmvc.class.php';
		$fw = new lwmvc();
		
		
		try{
			$fw->setDefaultController('');
		}catch (Exception $e){
			$this->assertTrue(true,'Reject invalid Default Controller');
			return true;
		}
			$this->assertTrue(false,'Reject invalid Default Controller');
			return true;
	}
	
	

}

/** Used for a testcase */
function test(){}