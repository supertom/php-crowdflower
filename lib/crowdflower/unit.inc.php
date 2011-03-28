<?php

require_once 'CFCommonInterface.php';
require_once 'common.inc.php';
require_once 'exception.inc.php';

class CrowdFlower_Unit extends CrowdFlower_Common {
    function __construct($apiKey) {
        $this->setApiKey($apiKey);
        $this->setResource("units");
        $this->setParentResource("jobs");
    }

    public function create($jobID, $data=array()) {
        return $this->sendRequest($this->_createURL($jobID), "POST",$this->prefixDataKeys($data, "unit"));
    }

    public function get($jobID, $unitID="") {
        return $this->sendRequest($this->_getURL($jobID, $unitID), "GET");
    }

    public function update($jobID, $unitID, $data) {
        if (!count($data)) {
            throw new CrowdFlower_Exception("data array cannot be empty", __METHOD__, __LINE__);
        }
        return $this->sendRequest($this->_getURL($jobID, $unitID), "PUT", $this->prefixDataKeys($data, "unit"));
    }

    public function delete($jobID, $unitID) {
        return $this->sendRequest($this->_deleteURL($jobID, $unitID), "DELETE");
    }

    public function ping($jobID) {
        return $this->sendRequest($this->_pingURL($jobID), "GET");
    }

    public function cancel($jobID, $unitID) {
        return $this->sendRequest($this->_cancelURL($jobID, $unitID), "PUT");
    }

    public function split($jobID, $data) {
        return $this->sendRequest($this->_splitURL($jobID), "PUT", $data);
    }

    /** these methods actually build the url, but any data is handled by the caller **/
    protected  function _createURL($jobID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _getURL($jobID, $unitID="") {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource();
        if (!empty($unitID) && is_numeric($unitID)) {
            $endpoint .= '/' . $unitID;
        }

        $endpoint .= '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _updateURL($jobID, $unitID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $unitID . '.' . $this->getFormat();

        return $endpoint;
    }

    protected  function _deleteURL($jobID, $unitID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $unitID . '.' . $this->getFormat();

        return $endpoint;
    }

    protected  function _pingURL($jobID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . 'ping' . '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _cancelURL($jobID, $unitID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $unitID
                . '/' . 'cancel' . '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _splitURL($jobID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . 'split' . '.' . $this->getFormat();

        return $endpoint;

    }
}