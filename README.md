# php-crowdflower

A toolkit for interacting with CrowdFlower via the REST API.

This is alpha software. Have fun!

Example Usage
-------------


Upload a CSV file for a job:

        $upload_data['file'] = '/path/to/my/file'
        $upload_data['content-type'] = "text/csv";
        //if appending, you need the id, otherwise omit it
        $upload_data['id'] = "text/csv";
        $job = CrowdFlower::factory("job", $apiKey);
        $resp = $job->upload($upload_data);

Copy an existing job into a new one:
     
        $job = CrowdFlower::factory("job", $apiKey);
        $copy_resp = $job->copy($jobID, array('all_units' => 'true'));
        //get new job id
        $copied_job_id = $copy_resp['response']->id;
        print $copied_job_id;
     
Check the status of a job:

        $job = CrowdFlower::factory("job", $apiKey);
        $job->ping($jobID);

Order Units in a job

        $job = CrowdFlower::factory("job", $apiKey);
        //$channels one or more of: array('amt', 'iphone', 'mob', 'sama')
        //mob is free and is for testing, others require money in your account
        $channels = array('mob', 'amt');
        $job->order($jobID, $unitsToOrder, $channels);

Download all judgments from a job

        $job = CrowdFlower::factory("job", $apiKey);
        $filename = '/path/to/download';
        //the file will be downloaded to the path above
        //$resp will have the API resp
        $resp = $job->download($jobID, $filename, true);

General Structure of the Response Object
-----------

When making calls to the API, an array is returned with the following:
1. 'response', which contains the actual json_decoded response from Crowdflower
2. 'info', which contains the associative array returned by curl_getinfo()
3. If an error due to curl (network timeout, etc.) occurs, the array will contain two extra elements: error_msg and error_code
4. 'request', the complete request, including URL, method, and data sent

Contributing
------------

1. Fork php-crowdflower
2. Create a topic branch - `git checkout -b my_branch`
3. Make your feature addition or bug fix and add tests for it.
4. Commit, but do not mess with the rakefile, version, or history.
5. Push to your branch - `git push origin my_branch`
6. Create an Issue with a link to your branch

Copyright
---------

Copyright &copy; 2011 [Tom Melendez](http://www.supertom.com/). See LICENSE for details.