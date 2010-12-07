<?php
    // $Id: socket_test.php,v 1.1 2007/12/12 15:06:30 jburger Exp $
    
    require_once(dirname(__FILE__) . '/../socket.php');
    
    Mock::generate('SimpleSocket');

    class TestOfSimpleStickyError extends UnitTestCase {
        
        function testSettingError() {
            $error = new SimpleStickyError();
            $this->assertFalse($error->isError());
            $error->_setError('Ouch');
            $this->assertTrue($error->isError());
            $this->assertEqual($error->getError(), 'Ouch');
        }
        
        function testClearingError() {
            $error = new SimpleStickyError();
            $error->_setError('Ouch');
            $this->assertTrue($error->isError());
            $error->_clearError();
            $this->assertFalse($error->isError());
        }
    }
?>