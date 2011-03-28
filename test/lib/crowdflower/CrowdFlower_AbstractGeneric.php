<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CrowdFlower_AbstractTest
 *
 * @author supertom
 */
abstract class CrowdFlower_AbstractGeneric extends PHPUnit_Framework_TestCase {

    protected $object;
    protected $data = array();
    protected $key = "_API_KEY_HERE_";
    protected $upload_data = array();
    protected $temp_file = "/tmp/crowdflower_unit_test_file.csv";
    protected $created_jobs = array();
    protected $debug = 0;
    //backwards compat, but it should be unorderedjob in the future
    protected $jobID = 99999;
    protected $unorderedJobID = 99999;
    protected $canceledJobID = 99999;
    protected $finishedJobID = 99999;
    protected $pausedJobID = 99999;
    protected $runningJobID = 99999;
    protected $workerID = 99999999;
    

    /**helper**/
    /**
     *
     * @param <type> $method class method
     * @param <type> $data array/object to dump
     * 
     * @note: call like this: $this->_reportInfo(__METHOD__, $resp);
     */
    public function _reportInfo($method,$data) {
        if ($this->debug) {
            print "\n#########\n";
            print "# $method\n";
            print "#########\n";
            print_r($data);
        }
    }

    public function _cleanUpJobs() {
        if (count($this->created_jobs)) {
            $job = CrowdFlower::factory('job', $this->key);
            foreach ($this->created_jobs as $jobID) {
                $job->delete($jobID);
            }

        }
    }
}
?>
