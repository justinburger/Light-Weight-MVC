<?php
    // $Id: parse_error_test.php,v 1.1 2007/12/12 15:06:30 jburger Exp $
    
    require_once('../unit_tester.php');
    require_once('../reporter.php');

    $test = &new TestSuite('This should fail');
    $test->addTestFile('test_with_parse_error.php');
    $test->run(new HtmlReporter());
?>