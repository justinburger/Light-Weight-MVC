<?php
    // $Id: spl_examples.php,v 1.1 2007/12/12 15:06:32 jburger Exp $

    class IteratorImplementation implements Iterator {
        function current() { }
        function next() { }
        function key() { }
        function valid() { }
        function rewind() { }
    }

    class IteratorAggregateImplementation implements IteratorAggregate {
        function getIterator() { }
    }
?>