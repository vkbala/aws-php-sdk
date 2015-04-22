<?php

//namespace components\wrapper;

use Aws\S3\S3Client;
use Aws\S3\StreamWrapper;
use Aws\CloudFront\CloudFrontClient;

/**
 * This will contact directly to AWS API.
 *
 */
class AwsWrapper {

    const AWS_S3_PROTOCOL = 's3://';

    private $s3Config;
    private $cloudfrontConfig; 
    
    //Cloud fron object.
    private $s3ClientObj;
    private $cloudFrontObj;
    private $isStreamWrapperRegistered = false;
    
    
    //s3 variables
    private $_s3Key;
    private $_s3Secret;
    
    //Cloud Front variables
    private $_cloudfrontKeyPairId;
    private $_cloudfrontPrivateKey;
   
    /**
     * Constructor to Initialize
     * @param type $voicecast
     */
    public function __construct($s3Config, $cloudfrontConfig) {
        $this->s3Config = $s3Config;
      	$this->cloudfrontConfig = $cloudfrontConfig;        
        
        //Loading s3 configuration
        $this->_s3Key = $this->s3Config['key'];
        $this->_s3Secret = $this->s3Config['secret'];
        
        //Loading cloud front configuration
        $this->_cloudfrontKeyPairId = $this->cloudfrontConfig['key_pair_id'];
        $this->_cloudfrontPrivateKey = $this->cloudfrontConfig['private_key'];
    }

    
    /**
     * Innitialized for S3 Functionalities
     * @return type
     */
    public function s3Client() {
    	// Create an Amazon S3 client object
    	if (!isset($this->s3ClientObj)) {
    		try {
    			$this->s3ClientObj = S3Client::factory(array(
    					'key' => $this->_s3Key,
    					'secret' => $this->_s3Secret
    			));
    		} catch (Exception $exception) {
    			throw $exception;
    		}
    	}
    	return $this->s3ClientObj;
    }
    
    /**
     * Register the stream wrapper from a client object
     * It will allow user to open directory and read files from AWS object.
     */
    public function s3StreamWrapper() {
    	if($this->isStreamWrapperRegistered == false) {
    		if (!isset($this->s3ClientObj)) {
    			$this->s3Client();
    		}
    		
    		StreamWrapper::register($this->s3ClientObj);
    		$this->isStreamWrapperRegistered = true;
    	}
    	
    	return $this->s3ClientObj;
    }
    
  
    /**
     * Innitialized for Transcode Jobs
     */
    public function elasticTranscoder() {
    	if (!isset($this->elasticTranscoderObj)) {
    		try {
    			$this->elasticTranscoderObj = ElasticTranscoderClient::factory(array(
    					'key' =>  $this->_transcodeKey,
    					'secret' => $this->_transcodeSecret,
    					'region' => $this->_transcodeRegion,
    			));
    		} catch (Exception $exception) {
    			throw $exception;
    		}
    	}
    	return $this->elasticTranscoderObj;
    }
    
    
    /**
     * Innitialized for Cloud Front Functionalities
     * @return type
     */
    public function cloudFrontClient() {
    	if (!isset($this->cloudFrontObj)) {
    		try {
    			$this->cloudFrontObj = CloudFrontClient::factory ( array (
    					'key_pair_id' => $this->_cloudfrontKeyPairId,
    					'private_key' => $this->_cloudfrontPrivateKey
    			) );
    		} catch (Exception $exception) {
    			throw $exception;
    		}
    	}
    	return $this->cloudFrontObj;
    }
}
