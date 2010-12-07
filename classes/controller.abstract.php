<?php
/**
 * Controllers Abstract Class.
 * Parent class for all controllers ran under this framework.
 * 
 * @author Justin Burger <j@justinburger.com>
 * @copyright GNU Lesser General Public License (C) 2008
 *
 */

/**
 * Controller Abstract
 * @package framework
 */
abstract class controller{
	/** Smarty .tpl Templates Directory Location. */
	protected $templateDir;
	
	/** lwmvc Framework Directory Location. */
	protected $frameworkDir;
	
	/** Javascript Template Assignment Storage. */
	private $js;
	
	/** CSS Template Assignment Storage. */
	private $css;
	
	private $modifiers;
	
	/**  Template Assignment Storage. */
	private $assigned;
	
	/** Template Title Assignment Storage. */
	private $templateTitle;
	
	
	private $logFile;
	
	protected $errors;
	
	public function setLogFile($file){
		$this->logFile = $file;
	}
	
	public function log($message){
		error_log('[' . date('r') . '] Controller Log:' . $message . "\n",3,$this->logFile);
	}
	
	/**
	 * Set Template Directory Location.
	 * Set and store template directory location
	 * so we know where to point smarty.
	 *
	 * @param String $dir Full Directory Path Location
	 */
	public function setTemplateDir($dir){
		$this->templateDir = $dir;
	}
	
	/**
	 * Set Framework Directory Location
	 *
	 * @param String $dir
	 */
	public function setFrameworkDir($dir){
		
		$this->frameworkDir = $dir;
	}
	
		
	private $registeredObjects;
	
	/**
	 * Register Object
	 * Register a PHP OBJECT for use in the next rendered smarty template.
	 *
	 * @param String $name
	 * @param Object $object
	 */
	protected function register_object($name, $object){
		$this->registeredObjects[$name] = $object;
	}
	
	
	
	/**
	 * Display Template.
	 * Expost Smarty Template functionality.
	 * The display method stores all the smarty template fuctionality.
	 * There is no need to require smarty unless the end controller intends to use it.
	 *
	 * @param String $template Template ".tpl" file to render and display.
	 */
	protected function display($template, $fetch = false){
		if(empty($this->frameworkDir)){
			$this->frameworkDir = LWMVC_FRAMEWORK_DIR;
		}
		if(empty($this->templateDir)){
			$this->templateDir = LWMVC_TEMPLATE_DIR;
		}
		if(empty($this->logFile)){
			$this->logFile = LWMVC_LOGFILE;
		}
		
		if(is_array(unserialize(LWMVC_SMARTY_VARS))){
			foreach(unserialize(LWMVC_SMARTY_VARS) as $name=>$val){
				$this->assigned[$name] = $val;
			}
		}
		

			
		if(!class_exists('Smarty')){
			require_once ($this->frameworkDir . '/thirdparty/smarty/libs/Smarty.class.php');
		}
		
		$smarty = new smarty();
		$compiledir = md5($this->templateDir);
		
		//attach version
		$baseUrl = $_SERVER['PHP_SELF'];
		$baseUrl   = str_replace('//','/',$baseUrl);
		$loc       = strpos($baseUrl,'/index');
		$baseUrl   = substr($baseUrl,0,($loc+1));
		$version   = str_replace('/','_',$baseUrl);
    	
		$version = str_replace('__','',$version);
		$version = str_replace('~','',$version);
    	
		if(!is_dir('/tmp/' . $compiledir)){
			mkdir('/tmp/' . $compiledir);
		}
		
		if (strlen($version) > 0) {
    		$compiledir .= '/' . $version ;
    		if(!is_dir('/tmp/' . $compiledir)){
    			mkdir('/tmp/' . $compiledir);
    		}
		}
		
		$smarty->cache_dir = '/tmp/' . $compiledir;
		$smarty->template_dir = $this->templateDir;
		$smarty->compile_dir = '/tmp/' .  $compiledir ;
		$smarty->clear_all_cache();

		if(sizeof($this->css) > 0){
			$smarty->assign('css',$this->css);
		}
		
		if(sizeof($this->js) > 0){
			$smarty->assign('js',$this->js);
		}
		
		
		if(sizeof($this->assigned) > 0){
			foreach ($this->assigned as $key=>$val){
				$smarty->assign($key,$val);
			}
		}
		if(sizeof($this->modifiers) > 0){
			foreach ($this->modifiers as $key=>$val){
		      $smarty->register_modifier($key, $val);
			}
		}
		
		if(is_array($this->registeredObjects) && sizeof($this->registeredObjects) > 0){
			foreach($this->registeredObjects as $name=>$object){
				$this->log('$name:'. $name);
				
				$smarty->register_object($name,clone $object);	
			}
		}
		
		$smarty->assign('title',$this->templateTitle);
		
		$html = $smarty->fetch($template);
		
		
		if(substr($template,(strlen($template)-3)) != 'xml'){
			$html = $this->injectHTMLHeaders($this->js,$html,'javascript');
		}
		
		
		if(!$fetch){
			print $html; //$smarty->display($template);
		}else{
			return $html;
		}
		
	}
	
	protected function render($template, $fetch = false){
		if($fetch){
			return $this->display($template, true);
		}else{
			$this->display($template, false);
		}
	}
	
	protected function addModifier($name, $impl){
	    $this->modifiers[$name]=$impl;
	    return true;
	}
	
	
	/**
	 * Get Browser Type
	 * Parses HTTP_USER_AGENT and returns the browser type
	 *
	 * @return String (FF, IE6, IE7) or false if the browser is not supported.
	 */
	protected  function getBrowserType(){
		$browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($browser,'firefox') > 0){
			return 'FF';
		}elseif(strpos($browser,'msie 6.0') > 0){
			return 'IE6';
		}elseif(strpos($browser,'msie 7.0') > 0){
			return 'IE7';
		}else{
			return false;
		}
	}
		
	/**
	 * Add Style
	 * Add a css style sheet to the list of style sheets that will be rendered
	 * 
	 * @param String $file
	 */
	protected function addStyle($file){
		$this->addCss($file);
	}
	
	/**
	 * Get Styles
	 * Get a list of style sheets that will be used for the next render.
	 *
	 * @return Array
	 */
	public function getStyles(){
		return $this->css;
	}
	
	
	/**
	 * Assign
	 * Expose needed smarty functionality, but store
	 * assigned values until we really need them.
	 *
	 * @param String $var Var Name
	 * @param String $value Value
	 */
	public function assign($var,$value){
		$this->assigned[$var] = $value;
	}
	
	/** Add JS to Template Assignment Storage */
	protected function addjs($js){
		$this->js[] = $js;
	}
	
	/** Add CSS to Template Assignment Storage */
	protected function addCss($css){
		$this->css[] = $css;
	}

	/** Add Title to Template Assignment Storage */
	protected function setTitle($tite){
		$this->templateTitle = $title;
	}
	
	
	public function ControllerActionNotFound($action){
		die('Controller Action Not Found:' . $action);
	}
	
	public function action_index(){
		echo 'No Default Action.';
	}
	
	
	/**
	 * Inject HTML Headers.
	 * Injects via a simple string parse javascript & css include headers 
	 * in HTML strings before they are printing to the screen.
	 *
	 * @param Array $headers
	 * @param String $html
	 * @param String $type
	 * @return String
	 */
	private function injectHTMLHeaders($headers, $html, $type){
		$headLoc = strpos(strtolower($html),'<head>');
		
		if($headLoc < 1){
			/* Cannot Find Headers */
			$html;
		}
		
		$tmpHtml = substr($html,0,($headLoc+6));
		
		if(is_array($headers)){
			foreach ($headers as $header){
				switch ($type){
					case 'javascript':
						$tmpHtml .="\n" . ' <script type="text/javascript" src="'.$header.'"></script>'."\n";
						break;
					case 'css':
						$tmpHtml .="\n" . ' <link href="'.$header.'" rel="stylesheet" type="text/css" media="screen" />'."\n";
						break;
						default:
							die("\n" . '<!-- invalid type:' . $type . '-->');
				}
				
			}
		}else{
			//$tmpHtml .= "\n" . '<!-- No Injected Headers. -->';
		}
		$tmpHtml .= substr($html,($headLoc+6));
		return $tmpHtml;
	}
	
	protected function getCleanPost(){
		require $this->frameworkDir . '/thirdparty/sanitize.function.php';
		$clean = array();
		foreach($_POST as $key=>$val){
			$clean[$key] = sanitize_sql_string(sanitize_html_string($val));
		}
		return $clean;
	}
	
	protected function validateEmail($email){
		if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * User Error Handeler.
	 * This function is used in place of the standard PHP Engine error functions.
	 * Because this is a SOAP server which needs to send every response as a valid
	 * XML/SOAP package, allowing PHP to throw up an error message with an Echo will
	 * render the response (even an error response) invalid, which would force the 
	 * calling application to ingnore (not parse) the response.
	 * 
	 * This function stores all the errors in this classes $errors var, which in turn
	 * will be sent back to the calling application as valid XML.
	 * 
	 * This is also nessasary because PHP's XML validator does not store the
	 * validation error's, rather, it send them via echo to the screen. This is
	 * not practical for a SOAP server, thus, this function allows us to send back
	 * the validation errors to the calling application in a usable format.
	 * 
	 *
	 * @param Integer $errno Error Number
	 * @param String $errmsg Error Message
	 * @param String $filename Filename (Of the file that the error happend in)
	 * @param Integer $linenum Line Number
	 * @param String $vars Vars values at the time of this error.
	 * @return null This function is called by the PHP Engine and a return would not be proccessed.
	 */
	function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
	{
	   /* timestamp for the error entry */
	   date_default_timezone_set('America/New_York');
	   $dt = date("Y-m-d H:i:s (T)");
	
	   /*
	    define an assoc array of error string
	    in reality the only entries we should
	    consider are E_WARNING, E_NOTICE, E_USER_ERROR,
	    E_USER_WARNING and E_USER_NOTICE
	    */
	   $errortype = array (
	               E_ERROR           => 'Error',
	               E_WARNING         => 'Warning',
	               E_PARSE           => 'Parsing Error',
	               E_NOTICE          => 'Notice',
	               E_CORE_ERROR      => 'Core Error',
	               E_CORE_WARNING    => 'Core Warning',
	               E_COMPILE_ERROR   => 'Compile Error',
	               E_COMPILE_WARNING => 'Compile Warning',
	               E_USER_ERROR      => 'User Error',
	               E_USER_WARNING    => 'User Warning',
	               E_USER_NOTICE     => 'User Notice',
	               E_STRICT          => 'Runtime Notice'
	               );
	   /* set of errors for which a var trace will be saved */
	   $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
	  
	   $err = "\t\t<errorentry>\n";
	   $err .= "\t\t\t<datetime>" 		. $dt 				. "</datetime>\n";
	   $err .= "\t\t\t<errornum>" 		. $errno 			. "</errornum>\n";
	   $err .= "\t\t\t<errortype>" 		. $errortype[$errno]. "</errortype>\n";
	   $err .= "\t\t\t<errormsg>" 		. $errmsg 			. "</errormsg>\n";
	   $err .= "\t\t\t<scriptname>" 	. $filename 		. "</scriptname>\n";
	   $err .= "\t\t\t<scriptlinenum>" 	. $linenum 			. "</scriptlinenum>\n";
	
	   if (in_array($errno, $user_errors)) {
	       $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
	   }
	   $err .= "\t\t</errorentry>\n\n";
	   error_log('['.date('c').']' . $err,3,$this->logFile);
	  
	   if(isset($this->errors)){
	   $this->errors .= $err;
	  }
	}
}