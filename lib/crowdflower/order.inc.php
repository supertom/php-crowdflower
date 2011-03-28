<?php

require_once 'CFCommonInterface.php';
require_once 'common.inc.php';
require_once 'exception.inc.php';

class CrowdFlower_Order extends CrowdFlower_Common {
    function __construct($apiKey) {
        $this->setApiKey($apiKey);
        $this->setResource("orders");
        $this->setParentResource("jobs");
    }

    /**
     *
     * @param <int> $jobID
     * @param <int> $unitsToOrder must be >=1
     * @param <array> $channels one or more of: array('amt', 'iphone', 'mob', 'sama')
     */
    public function create($jobID, $unitsToOrder, $channels) {
        if (!is_numeric($unitsToOrder) || $unitsToOrder < 1) {
            throw new CrowdFlower_Exception("Units to Order must be an Integer and greater than 1", __METHOD__, __LINE__);
        }
        if (!count($channels)) {
            throw new CrowdFlower_Exception("You must choose at least one channel to submit your order to", __METHOD__, __LINE__);
        }

        //can't use join as we need this to happen even if there is only one.
        $channelData = "";
        foreach($channels as $c) {
            $channelData .= '&channels[]=' . $c;
        }

        $data['raw'] = 'debit[units_count]=' . $unitsToOrder . $channelData . '&key=' . $this->getApiKey();
        
        return $this->sendRequest($this->_createURL($jobID), "POST", $data);
    }

//    public function get($jobID, $orderID) {
//
//    }


    /** these methods actually build the url, but any data is handled by the caller **/
    protected  function _createURL($jobID) {
        $endpoint = $this->_baseURL() . '/' . $this->getParentResource() . '/'. $jobID
                . '/' . $this->getResource() . '.' . $this->getFormat();

        return $endpoint;

    }
//    protected  function _getURL($jobID, $orderID) {
//
//    }

}