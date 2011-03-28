<?php

class CrowdFlower_Exception {
  function __construct($msg, $method="", $line="") {
    print "EXCEPTION: Line: $line Method: $method Error: $msg";
    exit(1);
  }
}