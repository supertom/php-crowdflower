<?php

require_once("crowdflower/exception.inc.php");
require_once("crowdflower/common.inc.php");
require_once("crowdflower/job.inc.php");
require_once("crowdflower/unit.inc.php");
require_once("crowdflower/judgment.inc.php");
require_once("crowdflower/order.inc.php");
require_once("crowdflower/worker.inc.php");

abstract class CrowdFlower {
  static function factory($classname, $key) {
    $file = "crowdflower/$classname.inc.php";
    if (!include_once($file)) {
      throw new CrowdFlower_Exception('File not found: ' . $file);
    }
    
    $class = 'CrowdFlower_' . ucwords($classname);
    if (!class_exists($class)) {
      throw new CrowdFlower_Exception('Class not found ($class): ' . $file);
    }
    
    $instance = new $class($key);
    return $instance;
  }
  
}