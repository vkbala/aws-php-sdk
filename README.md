# aws-php-sdk
A simple PHP application illustrating usage of the AWS S3 and CloudFront SDK for PHP.

#Configuration
You need to set up your AWS S3 and CloudFront security credentials before start the sample code. 

1) components\AWSConfig.php 

		s3' => [    
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
			

#Demo
I have included the demo page (index.php) for your quick workaround in this repository. 

The Demo page will explain the following functionality, 

1) Upload Assets/files to your S3 bucket 
2) Access the Upload S3 Assets/files via CloudFront using AWS Signature method.
3) Delete Uploaded S3 Assets (or) files (or) folder

You can run this in your local machine (apache (xampp or wamp) server). 