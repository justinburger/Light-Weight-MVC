<?php
/**
 *  Justin's Framework
 * 	This framework was made for use with project's that require a small, light wieght
 * 	MVC based framework.
 * 
 * @package framework
 * @author Justin Burger <j@justinburger.com>
 * @copyright GNU Lesser General Public License (C) 2008
 * 
 * 
 * @todo True Form Var Cleaning of data; multipass.
 * @todo Global 404 Page Definition Setting.
 * @todo Add MySQL Database Abstraction Support.
 * @todo Add Postgres Database Abstraction Support.
 * @todo Add SQLLite Database Abstraction Support.
 * @todo Add Common Database functionality (insert,update,delete, count, select, getLastInsertedId)
 * @todo Add Method to inject template headers with CSS & Javascript includes.
 * @todo Add Logging functionality for error tracing.
 * @todo Add Method to expose settings & debug information.
 * @todo Complete Exception support with detailed wiki documentation.
 * @todo look into a way to create threading functionality.
 * 
 * @throws 5001 Invalid Naming Type. Should Be Numeric.
 * @throws 5002 Salt should not be empty.
 * @throws 5003 Request Parms were not an array.
 * @throws 5004 Controller Element missing from override request parms.
 * @throws 5005 Failure URL cannot be empty
 * @throws 5006 Failure URL must be a string
 * @throws 5007 Global Smarty Variable Name should not be null.
 * @throws 5008 Global Smarty Variable Name should not be an array.
 * @throws 5009 AuthenticationValidation Function Does Not Exist
 * @throws 5010 Log File is not write-able.
 * @throws 5011 Log File is not touchable.
 * @throws 5012 Invalid Controller Naming Type
 * @throws 5013 Invalid Controller Directory
 * @throws 5014 Invalid Template Directory
 * @throws 5015 Default Controller Cannot Be NULL
 * @throws 5016 Framework directory does not contain lwmvc.
 * @throws 5017 Invalid Framework Directory.
 * @throws 5018 Invalid Storage Engine Passed.
 * 
 */
class lwmvc{
	/** Framework Directory */
	private $frameworkDir;
	
	/** Controller Directory */
	private $controllerDir;
	
	/** Template Directory */
	private $templateDir;
	
	/** Default Controller Name */
	private $defaultController;
	
	/** Capture External Form Post Setting (bool) */
	private $captureExternalFormPosts;
	
	/** Post Var Storage */
	private $postVars;
	
	/** File Not Found Forward URL Setting.*/
	private $fileNotFounturl;
	
	/** Overridden Request Controller/Action. */
	private $requestParms;
	
	/** Autentication Validate Function (See Setter for more information)*/
	private $authenticationValidationFunction;
	
	/** Autentication Validate Failure URL (See Setter for more information)*/
	private $authenticationFailureURL;
	
	/** Controller Naming Type (See Setter for more information) */
	private $controllerNamingType = 1;
	
	/** Smarty Storage Vars (See assignGlobalSmarty for more information) */
	private $smartyVars;
	
	/** Full path for log file (See setter for more information)*/
	private $logFile;
	
	/**/
	private $attachedMethods;
	
	/** An Array which store a white list of engine that are supported. */
	private $supportedStorageEngines;
	
	/** The Name of the Storage Engine the Information Abstraction Class will use. */
	private $storageEngine;
	
	
	
	public function __construct(){
		$this->supportedStorageEngines = array('pg');
	}
	
	public function setCaptureExternalFormPosts($switch = true){
		if(is_bool($switch)){
			$this->captureExternalFormPosts = $switch;
			return true;	
		}else{
			$this->captureExternalFormPosts = false;
			return false;
		}
	}
	
	/**
	 * Set Controller Naming Type.
	 * Becuase this framework was built for use in differnt enviorments, with differnt 
	 * naming conventions, we've setup naming types.
	 * 
	 * 1 = {controllername.controller.php} / action_{action}
	 * 2 = controller_{controllername} / {action}
	 * 
	 * @param Integer $type Naming Type. Currently 1/2 are supported.
	 * @throws 5001 Invalid Naming Type. Should Be Numeric.
	 */
	public function setControllerNamingType($type = 1){
		if(!is_numeric($type)){
			throw new Exception('Invalid Naming Type. Should Be Numeric.',5001);
		}
		
		$this->controllerNamingType = $type;
		return true;
	}
	
	/**
	 * Set Password Salt
	 * set the password to be used as a salt when creating MD5s
	 *
	 * @param String $salt
	 * @throws 5002 Salt should not be empty.
	 */
	public function setPasswordSalt($salt){
		if(empty($salt)){
			throw new Exception('Salt should not be empty.',5002);
		}
		$this->salt = $salt;
		return true;
	}
	
	
	
	/**
	 * overrideRequestParms
	 * lwMVC has a built in method which attempts to parse out the controller name and action from the URL.
	 * This method allows this parsing to be overidden with your own controller and action parser information.
	 * 
	 *
	 * @param Array $parms ARRAY('controller'=>?,'action'=>?)
	 * @throws 5003 Request Parms were not an array.
	 * @throws 5004 Controller Element missing from override request parms
	 */
	public function overrideRequestParms($parms){
		if(!is_array($parms)){
			throw new Exception('Request Parms were not an array.',5003);	
		}
		
		if(!array_key_exists('controller',$parms)){
			throw new Exception('Controller Element missing from override request parms',5004);
		}
		
		
		$this->requestParms = $parms;
		return true;
	}
	
	/**
	 * Set Authentication Failure URL
	 * Triggered when authenication is failed.
	 *
	 * @param String $url
	 * @throws 5005 Failure URL cannot be empty
	 * @throws 5006 Failure URL must be a string.
	 * 
	 */
	public function setAuthenticationFailureURL($url){
		if(empty($url)){
			throw new Exception('Failure URL cannot be empty',5005);
		}
		
		if(!is_string($url)){
			throw new Exception('Failure URL must be a string.',5006);
		}
		
		$this->authenticationFailureURL = $url;
	}
	
	/**
	 * Assign Global Smarty Variable.
	 * Set a smarty assignment variable which will be set for every single template.
	 *
	 * @param String $var
	 * @param String $value
	 * @throws 5007 Global Smarty Variable Name should not be null.
	 * @throws 5008 Global Smarty Variable Name should not be an array.
	 */
	public function assignGlobalSmarty($var,$value){
		if(empty($var)){
			throw new Exception('Global Smarty Variable Name should not be null.',5007);	
		}
		
		if(is_array($var)){
			throw new Exception('Global Smarty Variable Name should not be an array.',5008);
		}
		$this->smartyVars[$var] = $value;
		return true;
	}
	
	/**
	 * Set Authenication Validation Function
	 * Set a function which will check to see if they user should be able to access the controller they are
	 * attempting to access, and return a bool result.
	 *
	 * @param String $functionName
	 * @return Boolean
	 * @throws 5009 AuthenticationValidation Function Does Not Exist
	 */
	public function setAuthenticationValidationFunction($functionName){
		if(!function_exists($functionName)){
			throw new Exception('AuthenticationValidation Function Does Not Exist:' . $functionName,5009);
		}
		
		$this->authenticationValidationFunction = $functionName;
		return true;
	}
	
	
	/**
	 * Set Log File
	 * Set the file which all errors/notices/warnings/debug will be written.
	 *
	 * @param String $file
	 * @throws 5010 Log File is not write-able.
	 * @throws 5011 Log File is not touchable.
	 */
	public function setLogFile($file){
			$filePath = explode('/',$file);
			array_pop($filePath);
			$dir = implode('/',$filePath);

		if(!is_writeable($dir)){
			throw new Exception('Log File is not write-able.',5010);
		}
		if(!touch($file)){
			throw new Exception('Log File is not touchable.',5011);
		}
		
		$this->logFile = $file;
		return true;
	}
	
	private function runAttachedMethods($data){
		if(is_array($this->attachedMethods) && sizeof($this->attachedMethods) >0){
			foreach($this->attachedMethods as $method){
				if(is_object($method['object']) && !empty($method)){
					$obj = $method['object'];
					$methodName = $method['methodName'];
					$obj->$methodName($data);
				}
			}
		}
	}
	
	/**
	 * Handle Request
	 * Handle an incomming request.
	 * This function should be used from the main index.php file that 
	 * accepts requests. This function will process the request
	 *
	 * @throws 5012 Invalid Controller Naming Type
	 */
	public function handleRequest(){
		if($this->captureExternalFormPosts){
			$password = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $this->salt;
				
			if(!isset($_SESSION['creditals'])){
				$_SESSION['creditals'] = md5($password);
				$postvar = $this->getCleanPost(false);
			}else{
				if($_SESSION['creditals'] != md5($password)){
					$postvar = $this->getCleanPost(false);
				}else{
					$postvar = $this->getCleanPost(true);
				}
			}
		}else{
			$postvar = $this->getCleanPost(true);
		}
		
		$this->postVars = $postvar;
	
		
		$parms = $this->getRequestParams();	
		switch ($this->controllerNamingType){	
			case 1:
				$controllerFile = $this->controllerDir . '/'. $parms['controller'] . '.controller.php';
				$controller = $parms['controller'];
				$action = 'action_' . $parms['action'];
				break;
			case 2:
				$controllerFile = $this->controllerDir . '/controller_'. $parms['controller'] . '.php';
				$controller = 'controller_' . $parms['controller'];
				$action = $parms['action'];
				break;
			default:
				throw new Exception('Invalid Controller Naming Type:' . $this->controllerNamingType,5012);
				break;
		}
		
		if(!file_exists($controllerFile)){
			if(!empty($this->fileNotFounturl)){
				$this->runAttachedMethods(array('File Not Found: ' . $controllerFile));
				header('Location:' . $this->fileNotFounturl);
			}else{
				die('Invalid controller:' . $controllerFile . '/' . $this->controllerNamingType);
			}
		}
	
		require($controllerFile);
		
		define('LWMVC_FRAMEWORK_DIR',$this->frameworkDir);
		define('LWMVC_TEMPLATE_DIR',$this->templateDir);
		define('LWMVC_LOGFILE',$this->logFile);
		define('LWMVC_SMARTY_VARS',serialize($this->smartyVars));
		$tmpClass = new $controller();
		if(method_exists($tmpClass,'setTemplateDir')){
			$tmpClass->setTemplateDir($this->templateDir);
		}
		
		if(method_exists($tmpClass,'setLogFile')){
			$tmpClass->setLogFile($this->logFile);
		}
		
		//if(method_exists($tmpClass,'setFrameworkDir')){
		
			$tmpClass->setFrameworkDir($this->frameworkDir);
		//}
	
		if(is_array($this->smartyVars) && sizeof($this->smartyVars) > 0){
			foreach ($this->smartyVars as $name=>$val){
				$tmpClass->assign($name,$val);
			}
		}
		
		if(empty($action)){
			
			$action = $this->defaultController;
		}
		
		if(method_exists($tmpClass,$action)){
			$this->runAttachedMethods(array('Running Conntroller Action:' . $action . ' in class' . $controller));
			$tmpClass->$action();
		}else{
			$this->runAttachedMethods(array('Conntroller Action Not Found:' . $action . 'in class' . $controller));
			if(method_exists($tmpClass,'ControllerActionNotFound')){
				$tmpClass->ControllerActionNotFound($action);
			}else{
				die('Action Not Found:' . $controller . '::' . $action);
			}
		}
	}
	
	public function attachRequestClass($object, $methodName){
		$this->attachedMethods[] = array('object'=>$object,'methodName'=>$methodName);
		return true;
	}
	
	/**
	 * Get Request Parms.
	 * Retrieves and formats the request parms from the url via mod rewrite.
	 *
	 * @return Array controller/action
	 */
	public function getRequestParams(){
		if(!is_array($this->requestParms) || !array_key_exists('controller',$this->requestParms)){
			$controller = (isset($_GET['category']) && !empty($_GET['category'])) ? $_GET['category'] : null;
			$action = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page'] : null;
			$id = (isset($_GET['section']) && !empty($_GET['section'])) ? $_GET['section'] : null;
			
			if(!isset($controller) || empty($controller)){
				$controller = $this->defaultController;
			}
			
				if(!isset($action) || empty($action)){
				$action = 'index';
			}
			return array('controller'=>$controller, 'action'=> $action,'id'=>$id);
		}else{
			return $this->requestParms;
		}
		
	}
	
	/**
	 * Get Clean Post Varables
	 * Runs Post vars thru a cleaning proccess to be used without fear of 
	 * injections.
	 *
	 */
	public function getCleanPost($bool = true){
		if(!$bool){
			$this->cleanPostVars = null;
		}else{
			$this->cleanPostVars = $_POST;
		}
	}
	
	/** 
	 * Set Controller Directory 
	 * 
	 * @param $dir Directory which controllers reside.
	 * @return boolean
	 * @throws 5013 Invalid Controller Directory
	 * 
	 * */
	public function setControllerDir($dir){
		if(is_dir($dir)){
			$this->controllerDir = $dir;
		}else{
			throw new Exception('Invalid Controller Directory:' . $dir,5013);
		}
	}
	
	/**
	 * Set Template Directory
	 *
	 * @param String $dir
	 * @throws 5014 Invalid Template Directory
	 */
	public function setTemplateDir($dir){
		if(is_dir($dir)){
			$this->templateDir = $dir;
		}else{
			throw new Exception('Invalid Template Directory:' . $dir,5014);
		}
	}
	
	/**
	 * Set Default Controller
	 *
	 * @param String $controller
	 * @throws 5015 Default Controller Cannot Be NULL
	 */
	public function setDefaultController($controller){
		if(empty($controller)){
			throw new Exception('Default Controller cannot be null',5015);
		}
		$this->defaultController = $controller;
	}
	
	/**
	 * Set Framework Directory
	 * Directory where lwmvc framework is located.
	 *
	 * @param String $dir 
	 * @throws 5016 Framework directory does not contain lwmvc.
	 * @throws 5017 Invalid Framework Directory.
	 */
	public function setFrameworkDir($dir){
		if(is_dir($dir)){
			if(is_file($dir . '/lwmvc.class.php')){
				$this->frameworkDir = $dir;
				require $this->frameworkDir . '/classes/controller.abstract.php';
			}else{
				throw new Exception('Framework directory does not contain lwmvc:' . $dir,5016);
			}
		}else{
			throw new Exception('Invalid Framework Directory:' . $dir,5017);
		}
		
	}
	
	public function getFrameworkDir(){
		return $this->frameworkDir;
	}
	
	/**
	 * Set Storage Engine Name
	 * LWMVC Uses a custom storage abstraction system, which allows plugin based use of storage medium via 
	 * "table" classes, which implement a standard interface, which defines standard method calls for insert update delete select.
	 * This system was first created to support postgres. In the future, I plan on adding support for mysql, 
	 * along with plugable memory based caching.
	 *
	 * @param String $engineName The appended name which is used by the storage method you'd like to select.
	 * @return Boolean True = Success, Vaild. False = Failed.
	 */
	public function setStorageEngine($engineName){
		if(in_array($engineName,$this->supportedStorageEngines)){
			$this->storageEngine = $engineName;
			return true;
		}else{
			throw new Exception('Selected Engine Type is not supported.', 5018);
		}
	}
	
	
	public function getStorageEngine(){
		return $this->storageEngine;
	}
	
	/**
	 * Get Storage Class
	 *
	 * @param $name String "table" name. or storage lookup, depending on the plugin used.
	 */
	public function getStorageClass($name){
		global $settings;
		require_once($this->frameworkDir . 'classes/plugins/storage/IAStorage.interface.php');
		require_once($this->frameworkDir . 'classes/plugins/storage/IAPlugin.abstract.php');
		require_once($this->frameworkDir . 'classes/informationAbstract.class.php');
		
		$sc = new informationAbstract($name);
		if(!isset($settings['memcache']['enabled'])){
			$sc->useMemCache(false);	
		}else{
			$sc->useMemCache($settings['memcache']['enabled']);
		}
		
		return $sc;
		
	}
	
	/**
	 * Set File Not FoundURL
	 *
	 * @param String $url
	 */
	public function setFileNotFoundURL($url){
		$this->fileNotFounturl = $url;
	}
	
	
}
