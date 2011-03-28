<?php

//namespace CrowdFlower
require_once 'exception.inc.php';

abstract class CrowdFlower_Common {
  private $headers = array("accept" => "application/json");
  private $format = "json";
  private $apiKey = "";
  private $uri = "http://api.crowdflower.com";
  private $version = 1;
  private $timeouts = array("CURLOPT_TIMEOUT" => 10,
                            "CURLOPT_CONNECTTIMEOUT" => 2);

  private $resource = "";
  private $parent_resource = "";
  private $debug = FALSE;

	 
  public function sendRequest($url, $method, $data="") {

    $result = array();
    $postdata = "";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_MAXREDIRS,2);
    if ($this->getDebug() == TRUE) {
        curl_setopt($ch, CURLOPT_VERBOSE, true);
    }
 
    $timeouts = $this->getTimeouts();
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeouts['CURLOPT_TIMEOUT']);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeouts['CURLOPT_CONNECTTIMEOUT']);

    switch($method) {
      case "GET":
	$url.="?key=" . $this->getApiKey();
        break;
      case "UPLOAD":
          $url.="?key=" . $this->getApiKey();
          if (empty($data['content-type'])) {
              throw new CrowdFlower_Exception("No content type set on file upload.  Please set 'content-type' in your data array", __METHOD__, __LINE__);
          }

          if (empty($data['file']) || !file_exists($data['file']) || !is_readable($data['file'])) {
              throw new CrowdFlower_Exception("File does not exist or is not readable", __METHOD__, __LINE__);
          }

          //id is set, we do a PUT, otherwise a post
          if (!empty($data['id']) && $this->checkID($data['id'])) {
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
          }
          else {
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          }

          curl_setopt($ch, CURLOPT_UPLOAD, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: {$data['content-type']}"));
          curl_setopt($ch, CURLOPT_INFILE, fopen($data['file'],'r'));
          curl_setopt($ch, CURLOPT_READFUNCTION, create_function('$ch, $fd, $size', 'return fread($fd,$size);'));
          curl_setopt($ch, CURLOPT_INFILESIZE, filesize($data['file']));

        break;
      case "DOWNLOAD":
          curl_setopt($ch, CURLOPT_FILE, fopen($data['file'],'w'));
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          $url.="&key=" . $this->getApiKey();
        break;
      default:
        if (empty($data['raw'])) {
            //add key to data
            $data['key'] = $this->getApiKey();
            $postdata = http_build_query($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
//            print "\nPOST DATA:" . http_build_query($data) . "\n";
        }
        else {
            //we assume the key is already in the raw value
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data['raw']);
            $postdata = $data['raw'];
        }
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    }

    if ($this->getFormat() == "json" && $method != "UPLOAD") {
        curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type','application/json'));
    }

    //set the actual URL, make the call and get the info about it
    curl_setopt($ch,CURLOPT_URL,$url);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    if ($this->getFormat() == "json") {
        $response = json_decode($response);
    }

    $result['response'] = $response;
    $result['info'] = $info;
    $result['info']['error_msg'] = curl_error($ch);
    $result['info']['error_code'] = curl_errno($ch);
    
    $result['request'] = array("url" => $url, "method" => $method, "data" => $data, "postdata" => $postdata);

    return $result;

  }

  public function verifyResponse($response) {
    if (!empty($response['response']->error)) {
      throw new CrowdFlower_Exception($response['error']);
    }
    if ($response['info']['error_code'] > 0) {
        throw new CrowdFlower_Exception("CURL ERROR: error #" . $response['info']['error_code'] . " " . $response['info']['error_msg']);
    }

    return true;
  }

  /**
   * takes an array like array("key" => "value")
   * and turns it into
   * array("prefix[key] => "value")
   * 
   * @param <array> $data
   * @param <string> $prefix 
   */

  protected function prefixDataKeys($data,$prefix) {
      $newdata = array();

      foreach ($data as $key => $value) {
          $newkey = "$prefix" . '[' . $key . ']';
          $newdata[$newkey] = $value;
      }

      return $newdata;
  }

  protected function checkID($id) {
      $status = FALSE;

      if (!empty($id)) {
          if (!is_numeric($id)) {
              throw new CrowdFlower_Exception("id in data array is not an integer");
          }
          else {
              $status = TRUE;
          }
      }
      else {
          $status = TRUE;
      }

      return $status;
  }

  /** getters/setters **/
  public function setHeaders($headers) {
    $this->headers = $headers;
  }
  
  public function getHeaders() {
    return $this->headers;
  }
  
  public function setFormat($format) {
    $this->format = $format;
  }
  
  public function getFormat() {
    return $this->format;
  }
  
  public function setApiKey($key) {
    $this->apiKey = $key;
  }
  
  public function getApiKey() {
    return $this->apiKey;
  }
  
  public function setUri($uri) {
    $this->uri = $uri;
  }
  
  public function getUri() {
    return $this->uri;
  }
  
  public function setVersion($version) {
    $this->version = $version;
  }
  
  public function getVersion() {
    return $this->version;
  }

  /**
   * format:
   * array("CURLOPT_TIMEOUT" => 10,
   *                         "CURLOPT_CONNECTTIMEOUT" => 2);
   */
  public function setTimeouts($arry) {
      $this->timeouts = $arry;
  }

  public function getTimeouts() {
      return $this->timeouts;
  }

  public function setResource($r) {
      $this->resource = $r;
  }

  public function getResource() {
      return $this->resource;
  }

  public function setParentResource($r) {
      $this->parent_resource = $r;
  }

  public function getParentResource() {
      return $this->parent_resource;
  }

  public function setDebug($debug) {
      $this->debug = $debug;
  }

  public function getDebug() {
      return $this->debug;
  }

  public function _baseURL() {
    return $this->getUri() . '/v' . $this->getVersion();
  }

}