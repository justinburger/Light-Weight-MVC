<?php
/**
 * Controllers Abstract Class.
 * Parent class for all controllers ran under this framework.
 * 
 * @package framework
 * @author Justin Burger <j@justinburger.com>
 * @copyright GNU Lesser General Public License (C) 2008
 *
 */
abstract class controllers{
	/** Smarty .tpl Templates Directory Location. */
	protected $templateDir;
	
	/** lwmvc Framework Directory Location. */
	protected $frameworkDir;
	
	/** Javascript Template Assignment Storage. */
	private $js;
	
	/** CSS Template Assignment Storage. */
	private $css;
	
	/**  Template Assignment Storage. */
	private $assigned;
	
	/** Template Title Assignment Storage. */
	private $templateTitle;
	
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
	
	
	/**
	 * Display Template.
	 * Expost Smarty Template functionality.
	 * The display method stores all the smarty template fuctionality.
	 * There is no need to require smarty unless the end controller intends to use it.
	 *
	 * @param String $template Template ".tpl" file to render and display.
	 * @param Boolean $fetch If set to true, It will return the XHTML, rather than printing it.
	 */
	protected function display($template, $fetch = false){
		require ($this->frameworkDir . '/thirdparty/smarty/libs/Smarty.class.php');
		
		$smarty = new smarty();
		$smarty->cache_dir = '/tmp';
		$smarty->template_dir = $this->templateDir;
		$smarty->compile_dir = '/tmp';
		
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
		
		$smarty->assign('title',$this->templateTitle);
		
		$html = $smarty->fetch($template);
		$html = $this->injectHTMLHeaders($this->js,$html,'javascript');
		
		if(!$fetch){
			print $html; //$smarty->display($template);
		}else{
			return $html;
		}
		
	}
	
	
	/**
	 * Assign
	 * Expose needed smarty functionality, but store
	 * assigned values until we really need them.
	 *
	 * @param String $var Var Name
	 * @param String $value Value
	 */
	protected function assign($var,$value){
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
			return false;
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
			$tmpHtml .= "\n" . '<!-- No Injected Headers. -->';
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
}