<?php

require_once 'CFCommonInterface.php';
require_once 'common.inc.php';
require_once 'exception.inc.php';

//namespace CrowdFlower
class CrowdFlower_Job extends CrowdFlower_Common implements CFCommonInterface {

    function __construct($apiKey) {
        $this->setApiKey($apiKey);
        $this->setResource("jobs");
    }

    public function create($data) {
        return $this->sendRequest($this->_createURL($data), "POST",$this->prefixDataKeys($data, "job"));
    }

    public function delete($id) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_deleteURL($id), "DELETE",array("id" => $id));
    }

    public function get($id="") {
        return $this->sendRequest($this->_getURL($id), "GET", $id);
    }

    public function update($id,$data) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }
        if (!count($data)) { throw new CrowdFlower_Exception("data array must not be empty", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_updateURL($id), "PUT",$this->prefixDataKeys($data, "job"));
    }

    /**
     * uploading files is a little different
     *
     * We need a path to the file and the content type
     * $data['file'] = "/path/to/file";
     * $data['content-type'] = "text/csv";
     *
     * @param <array> $data expects to have 'file' and 'content-type' elements,
     * and optionally a job ID to append to
     *
     * @see http://crowdflower.com/docs/api/uploading/
     *
     */
    public function upload($data) {
        if (empty($data['file']) || !file_exists($data['file']) || !is_readable($data['file'])) {
            throw new CrowdFlower_Exception("file element not specified in data array or file does not exist.", __METHOD__, __LINE__);
        }

        if (empty($data['content-type'])) {
            throw new CrowdFlower_Exception("content-type element not set in data array.", __METHOD__, __LINE__);
        }

        return $this->sendRequest($this->_uploadURL($data), 'UPLOAD',$data);
    }


    public function pause($id) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_pauseURL($id), 'GET',array("id" => $id));
    }

    public function ping($id) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_pingURL($id), 'GET',array("id" => $id));
        
    }

    public function resume($id) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_resumeURL($id), 'GET',array("id" => $id));

    }

    public function cancel($id) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        return $this->sendRequest($this->_cancelURL($id), 'GET',array("id" => $id));

    }

    public function copy($id, $data=array()) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        $data['id'] = $id;

        return $this->sendRequest($this->_copyURL($id), 'POST', $data);

    }

    public function legend($id) {
        if (!is_numeric($id)) { return false; }

        return $this->sendRequest($this->_legendURL($id), 'GET', array("id" => $id));

    }

    public function gold($id, $data=array()) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        $data['id'] = $id;

        return $this->sendRequest($this->_goldURL($id), 'PUT', $data);

    }

    /**
     * If you send $data, the channels in $data will be enabled, otherwise
     * you'll get a list of channels for that job
     *
     * @param <type> $id
     * @param <type> $data - optional, if you send it, you'll enable channels
     * @return <type>
     */
    public function channels($id, $data=array()) {
        if (!is_numeric($id)) { throw new CrowdFlower_Exception("id must be numeric", __METHOD__, __LINE__); }

        $c_data = array();
        $method="GET";
        if (count($data)) {
            $method="PUT";
            $c_data['raw']="";
            foreach ($data as $c) {
                $c_data['raw'].="channels[]=$c&";
            }
            //$c_data['raw']=urlencode($c_data['raw']);
            $c_data['raw'].='key='.$this->getApiKey();
        }

        $c_data['id'] = $id;

        return $this->sendRequest($this->_channelsURL($id), $method, $c_data);

    }

    /** convenience methods **/
    /**
     * returns a unit object
     *
     * @return CrowdFlower_Unit
     */
    public function units() {
        return new CrowdFlower_Unit($this->getApiKey());
    }

    /**
     * wrapper around judgment->download
     *
     * @param <type> $jobID
     * @param <type> $filename
     * @param <type> $full
     */
    public function download($jobID, $filename, $full=false) {
        $j = new CrowdFlower_Judgment($this->getApiKey());
        return $j->download($jobID, $filename, $full);
    }

    /**
     * this is a convenience around $order->create()
     * The orders class only has one public method anyway, so we jump right to it
     *
     * @param <int> $jobID
     * @param <int> $unitsToOrder must be >=1
     * @param <array> $channels one or more of: array('amt', 'iphone', 'mob', 'sama')
     */

    public function order($jobID, $unitsToOrder, $channels) {
        $o = new CrowdFlower_Order($this->getApiKey());
        return $o->create($jobID, $unitsToOrder, $channels);
    }

    /** these methods actually build the url, but any data is handled by the caller **/
    protected  function _getURL($id="") {
        $endpoint = $this->_baseURL() . '/' . $this->getResource();
        if (!empty($id) && is_numeric($id)) {
            $endpoint .= '/' . $id;
        }
        $endpoint .= '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _createURL($data) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _updateURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _deleteURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _uploadURL($data) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource();

        if (!empty($data['id']) && is_numeric($data['id'])) {
            $endpoint .= '/' . $data['id'];
        }
        
        $endpoint .= '/' . 'upload' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _pingURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'ping' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _pauseURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'pause' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _resumeURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'resume' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _cancelURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'cancel' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _copyURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'copy' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _legendURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'legend' . '.' . $this->getFormat();

        return $endpoint;
    }

    protected function _goldURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'gold' . '.' . $this->getFormat();

        return $endpoint;
    }

    /**
     *
     * @param <type> $id
     * @return string
     *
     * @note, channel API doesn't require the format at the end, like the others
     */
    protected function _channelsURL($id) {
        $endpoint = $this->_baseURL() . '/' . $this->getResource() . '/' . $id
                . '/' . 'channels';

        return $endpoint;
    }

}