<?php

class CrowdFlower_Exception {
  function __construct($msg, $method="", $line="") {
    print "EXCEPTION: Line: $line Method: $method Error: $msg";
    debug_print_backtrace();
    exit(1);
  }
}