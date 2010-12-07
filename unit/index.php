<?php
/**
 * @package unit
 * @subpackage unitcore
 */

ini_set('session.save_handler','files');
session_start();


if (! defined('SIMPLE_TEST')) {
	/** Simple Test Directory */
        define('SIMPLE_TEST', '../thirdparty/simpletest/');
    }
    
    require_once(SIMPLE_TEST . 'unit_tester.php');

    require_once('show_passes.php');

    
	$unitTestPackage = 'lwmvc';
    $test = &new TestSuite($unitTestPackage . ' Unit Test Cases.');

    $test->addTestFile('unit.'.$unitTestPackage.'.php');

    $test->run(new ShowPasses());

?>
