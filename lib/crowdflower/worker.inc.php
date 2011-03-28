<?php
/**
 * @note don't use this API
 * @note this API will only work for CF admins at the moment
 *
 */
require_once 'CFCommonInterface.php';
require_once 'common.inc.php';
require_once 'exception.inc.php';

class CrowdFlower_Worker extends CrowdFlower_Common {
    function __construct($apiKey) {
        $this->setApiKey($apiKey);
        $this->setResource("workers");
        $this->setParentResource("jobs");
    }

    public function bonus($jobID, $workerID, $amount, $reason="") {
        $data['amount'] = $amount;
        $data['reason'] = $reason;

        return $this->sendRequest($this->_bonusURL($jobID, $workerID), "POST", $data);
    }

    public function approve($jobID, $workerID) {
        return $this->sendRequest($this->_approveURL($jobID, $workerID), "PUT");
    }

    public function reject($jobID, $workerID) {
        return $this->sendRequest($this->_rejectURL($jobID, $workerID), "PUT");
    }

    public function ban($jobID, $workerID) {
        return $this->sendRequest($this->_banURL($jobID, $workerID), "PUT");
    }

    public function deban($jobID, $workerID) {
        return $this->sendRequest($this->_debanURL($jobID, $workerID), "PUT");
    }

    public function notify($jobID, $workerID, $subject, $message) {
        $data['subject'] = $subject;
        $data['message'] = $message;
        return $this->sendRequest($this->_flagURL($jobID, $workerID), "POST", $this->prefixDataKeys($data, "worker"));
    }
    
    public function flag($jobID, $workerID, $reason="") {
        $data['reason'] = $reason;
        return $this->sendRequest($this->_flagURL($jobID, $workerID), "PUT", $this->prefixDataKeys($data, "worker"));
    }
    
    public function deflag($jobID, $workerID, $reason="") {
        $data['reason'] = $reason;
        return $this->sendRequest($this->_deflagURL($jobID, $workerID), "PUT", $this->prefixDataKeys($data, "worker"));
    }

    /** these methods actually build the url, but any data is handled by the caller **/
    // It doesn't appear that this is needed: . '.' . $this->getFormat();
    protected function _bonusURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/bonus';

        return $endpoint;
    }

    protected function _approveURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/approve';

        return $endpoint;
    }

    protected function _rejectURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/reject';

        return $endpoint;
    }

    protected function _banURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/ban';

        return $endpoint;
    }

    protected function _debanURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/deban';

        return $endpoint;
    }

    protected function _notifyURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/notify';

        return $endpoint;
    }

    protected function _flagURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/flag';

        return $endpoint;
    }

    protected function _deflagURL($jobID, $workerID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '/' . $workerID . '/deflag';

        return $endpoint;
    }
    
}