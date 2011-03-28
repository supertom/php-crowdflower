<?php

require_once 'CFCommonInterface.php';
require_once 'common.inc.php';
require_once 'exception.inc.php';

class CrowdFlower_Judgment extends CrowdFlower_Common {
    function __construct($apiKey) {
        $this->setApiKey($apiKey);
        $this->setResource("judgments");
        $this->setParentResource("jobs");
    }

    public function create($jobID, $data=array()) {
        print_r(http_build_query($this->prefixDataKeys($data, "judgment")));
        return $this->sendRequest($this->_createURL($jobID), "POST",$this->prefixDataKeys($data, "judgment"));
    }

    /**
     *
     * @param <type> $jobID
     * @param <type> $judgmentID
     * @return <type>
     *
     * @note http://crowdflower.com/docs/api/judgments/
     * @note Read accepts a :limit and :page property. See “Reading judgments” below.
     * @todo implement the above
     */
    public function get($jobID, $judgmentID) {
        return $this->sendRequest($this->_getURL($jobID, $judgmentID), "GET");
    }


    public function update($jobID, $judgmentID, $data) {
        if (!count($data)) {
            throw new CrowdFlower_Exception("data array cannot be empty", __METHOD__, __LINE__);
        }
        return $this->sendRequest($this->_getURL($jobID, $judgmentID), "PUT", $this->prefixDataKeys($data, "judgment"));
    }

    public function delete($jobID, $judgmentID) {
        return $this->sendRequest($this->_deleteURL($jobID, $judgmentID), "DELETE");
    }

    /**
     *
     * @param <type> $jobID
     * @param <type> $filename this is the /path/to/zipfile.zip that will be written
     * @param <type> $full
     * @return <type>
     */
    public function download($jobID, $filename, $full=false) {
        $data['file'] = $filename;
        return $this->sendRequest($this->_downloadURL($jobID, $full), "DOWNLOAD", $data);
    }

    /** these methods actually build the url, but any data is handled by the caller **/
    protected  function _createURL($jobID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '.' . $this->getFormat();

        return $endpoint;

    }
    protected  function _getURL($jobID, $judgmentID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $judgmentID . '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _updateURL($jobID, $judgmentID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $judgmentID . '.' . $this->getFormat();

        return $endpoint;

    }

    protected  function _deleteURL($jobID, $judgmentID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $judgmentID . '.' . $this->getFormat();

        return $endpoint;

    }

    protected function _downloadURL($jobID, $full=false) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID . '.csv?full=' . $full;

        return $endpoint;
        
    }
}