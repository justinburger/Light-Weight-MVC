<?php
/**
 * @package unit
 * @subpackage unitcore
 */

    if (! defined('SIMPLE_TEST')) {
    	/** Simple Test Directory */
        define('SIMPLE_TEST', '../thirdparty/simpletest/');
    }
    require_once(SIMPLE_TEST . 'reporter.php');
/**
 * @package unit
 * @subpackage unitcore
 */    
class ShowPasses extends HtmlReporter {
        
	function ShowPasses() {
    	$this->HtmlReporter();
    }
    
    function paintPass($message) {
        parent::paintPass($message);
        
        $message = str_replace('at [', '<br/><span style="font-size:11px;">at [', $message);
        $message = str_replace(']', '] </span>', $message);
        
        //echo $message;
        //exit;
        echo "<table border='0'  width='100%'><tr>";
        echo "<td style='background:#EBEBEB; width:40px;'><span class=\"pass\">Pass</span></td>";
        $breadcrumb = $this->getTestList();
        //print_r($breadcrumb);
        //exit;
        array_shift($breadcrumb);
        foreach ($breadcrumb as $col){
        	echo "<td style='font-size:13px; width:200px; background:#EBEBEB;'>{$col}</td>\n";
        }
        
        echo "<td style='background:#EBEBEB;'>{$message}</td>\n";
        echo "</tr></table>";
    }
    }
?>