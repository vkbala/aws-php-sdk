<?php

//namespace components;

//use components\wrapper\AwsWrapper;

/**
 * It will provide methods used by showcase to interact with AWS
 * @since 28 December, 2014
 * @version v1.0
 * @author: Balaganesh K
 * @email: vkbalaganesh@gmail.com
 */
require (__DIR__ . '/wrapper/AwsWrapper.php');
require (__DIR__ . '/AWSConfig.php');

class AWSComponent extends AwsWrapper {
	
	const AWS_S3_PROTOCOL = 's3://';

	private $awsconfig;
	private $s3Config;
	private $cloudfrontConfig;

	//s3 variables
	private $_s3Key;
	private $_s3Secret;
	private $_s3Bucket;
	//private $_s3KeyPrefix;		
	private $_s3Url;	
	
	//Cloud Front variables
	private $_cloudfrontKeyPairId;
	private $_cloudfrontPrivateKey;
	private $_cloudfrontDomainName;

	function __construct() {
		$this->awsconfig = AWSConfig::getConfig();
		$this->s3Config = $this->awsconfig['s3'];
		$this->cloudfrontConfig = $this->awsconfig['cloudfront'];
		
		//Loading s3 configuration
		$this->_s3Key = $this->s3Config['key'];
		$this->_s3Secret = $this->s3Config['secret'];
		$this->_s3Url = $this->s3Config['url'];
		$this->_s3Bucket = $this->s3Config['bucket'];
		//$this->_s3KeyPrefix = $this->s3Config['keyPrefix'];
		
		//Loading cloud front configuration
		$this->_cloudfrontKeyPairId = $this->cloudfrontConfig['key_pair_id'];
		$this->_cloudfrontPrivateKey = $this->cloudfrontConfig['private_key'];
		$this->_cloudfrontDomainName = $this->cloudfrontConfig['domainName'];		
		
		parent::__construct($this->s3Config, $this->cloudfrontConfig);	
	}

	/**
	 * It will upload file to S3 bucket.
	 * @category S3 Call
	 * @param string $upload_path the target file upload path
	 * @param string $upload_file_name
	 * @param tmp_path $upload_file_content
	 * @return string Uploaded File Amazon S3 URL 
	 */
	public function uploadFile($upload_path, $upload_file_name, $upload_file_content) {
		$key = "{$upload_path}/{$upload_file_name}";

		try {
			$s3ClientObj = $this->s3Client();
			$bucket = $this->_s3Bucket;
				
			$s3ClientObj->putObject(
				array(
						'Bucket' => $bucket,
						'Key' => $key,
						'Body' => fopen($upload_file_content, "rb"),
						'ACL' => 'public-read'
				));			
			$imageUrl = "{$this->_s3Url}{$bucket}/{$key}";

		} catch(Exception $exception) {
			throw $exception;
		}		
		return $imageUrl;
	}

	/**
	 * This function will get the Cloud Front Url for S3 object.
	 * @category S3 Call
	 * @param string $path the target file upload path
	 * @param string $file_name
	 * @param int $expires In minutes default is 5 minute
	 * @return string Temporary Amazon S3 URL 
	 */

	public function getCloudFrontUrl($folder, $file, $expires = 5, $download = true) {
		$resourceUrl = $path = "";
		
		$resourcePath = ($folder != '') ? "/{$folder}/{$file}" : "/{$file}";
		try {
			$s3ClientObj = $this->s3Client();
			$bucket = $this->_s3Bucket;
				
			if ($s3ClientObj->doesObjectExist($bucket, $resourcePath)) {
				$resourceUrl = $this->getTempLink($resourcePath, $expires, $download);
			} else {
				$resourceUrl = '';
			}
		} catch(Exception $exception) {
			throw $exception;
		}		
		return $resourceUrl;
	}

	/**
	 * Create Cloud Front (temporary) URLs to your protected Amazon S3 files. 
	 * @return string Temporary Amazon S3 URL
	 */
	private function getTempLink($path, $expires, $download) {
		$tempUrl = "";		
		$resourcePath = $path; //str_replace(" ", "%20", $path); // replace whitespace
		$expires = time() + ($expires * 60);

		$hostUrl = $this->_cloudfrontDomainName;		
		$cloudFrontClient = $this->cloudFrontClient();		
		try {
			$tempUrl = $cloudFrontClient->getSignedUrl(array(
					'url' => $hostUrl . $resourcePath,
					'expires' => $expires,
			));
		} catch (Exception $exception) {
			throw $exception;
		}
		return $tempUrl;
	}

	/**
	 * This function will delet the file/folder from the S3 bucket.
	 * @category S3 Call
	 * @param string $bucket name of the bucket
	 * @param string $path the delete target file path
	 * @param boolean $isFile going to delete file or not
	 * @return boolean (true / false) 
	 */
	private function deleteObject($bucket, $path, $isFile = true) {
		$isDelete = false;
    	try {			
			$s3ClientObj = $this->s3Client();

			if($isFile) {
				if ($s3ClientObj->doesObjectExist($bucket, $path)) {
					$s3ClientObj->deleteObject(array('Bucket' => $bucket, 'Key' => $path));
					$isDelete = true;
				}
			} else {
				$iterator = $s3ClientObj->getIterator('ListObjects', array('Bucket' => $bucket, 'Prefix' => $path));    			 
    			foreach ($iterator as $object) {
    				$deleteKey = $object['Key'];
    				$s3ClientObj->deleteObject(array('Bucket' => $bucket, 'Key' => $deleteKey));
    			}
    			$isDelete = true;
			}
		} catch(Exception $e) {
			throw $e;
		}
		return $isDelete;
	}

    /**
     * It will delete the uploaded files from S3 buckets.
     * @param string $path deleting file path 
     * @example '{foldername}/{subfoldername}/{fileName}'
     * @example "{filename}"
    */
	public function deleteFile($filepath) {
    	$isDelete = false;   
    	$bucket = $this->_s3Bucket; 	
    	try {
    		$isDelete = $this->deleteObject($bucket, $filepath, true);
    	} catch (Exception $exception) {
    		throw $exception;
    	}    
    	return $isDelete;
	}

    /**
     * It will delete the all the object/files in the folder from S3 buckets.
     * @param string $path deleting folder path 
     * @example '{foldername}/{subfoldername}'
     * @example "{foldername}"
    */
	public function deleteFolder($folderpath) {
    	$isDelete = false;   
    	$bucket = $this->_s3Bucket; 	
    	try {
    		$isDelete = $this->deleteObject($bucket, $folderpath, false);
    	} catch (Exception $exception) {
    		throw $exception;
    	}    
    	return $isDelete;
	}
}