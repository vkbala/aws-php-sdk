<?php

// Amazon S3 Bucket - Configuration 
// Amazon Cloud Front URL - Configuration

class AWSConfig {

	public static function getConfig() {
		return [ 
		    's3' => [    
		        'key' => '<Your S3 Bucket Access Key>',
		        'secret' => '<Your S3 Bucket Secret Key>',
		        'bucket' => '<S3 Upload Bucket Name>', 
		        'url' => 'https://s3.amazonaws.com/'
		    ],		    
		    'cloudfront' => [
		        'key_pair_id' => '<Cloudfront Key Pair Id>',
		        'private_key' => '<Enter the pem private key file Path', // Eg: __DIR__ . DIRECTORY_SEPARATOR . 'pk-dd34e3fer3.pem'
		        'domainName' => '<Your Cloudfront domain url>'
		    ]
		];
	}
}