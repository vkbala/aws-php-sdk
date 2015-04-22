<?php

define('BASE_PATH', realpath(dirname(__FILE__)));
function my_autoloader($class)
{
    $filename = BASE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    include($filename);
}
//spl_autoload_register('my_autoloader');
require 'aws/aws-autoloader.php';

require(__DIR__ . '/components/AWSComponent.php');

$awsObj = new AWSComponent();

// Get Cloud Front Url Submit Action
$get_url = '';
if(isset($_POST['get_cloud']) && $_POST['get_cloud'] == 'Get Cloud Front Url') {
	$file_name = $_POST['file_name'];
	$file_path = $_POST['file_path'];

	$get_url = $awsObj->getCloudFrontUrl($file_path, $file_name, 30, true);
}

// Upload file to Amazon S3 Bucket Submit Action
$upload_url = '';
if(isset($_POST['upload_cloud']) && $_POST['upload_cloud'] == 'Upload File') {
	$upload_path = $_POST['upload_path'];
	$upload_file_name = $_FILES['upload_file']['name'];
	$upload_file_content = $_FILES['upload_file']['tmp_name'];
	
	$upload_url = $awsObj->uploadFile($upload_path, $upload_file_name, $upload_file_content);	
}

// Delete file from Amazon S3 Bucket Submit Action
$delete_file_msg = '';
if(isset($_POST['delete_file']) && $_POST['delete_file'] == 'Delete File') {
	$delete_file_path = $_POST['delete_file_path'];		
	$delete_file_msg = $awsObj->deleteFile($delete_file_path);	
}

// Delete file from Amazon S3 Bucket Submit Action
$delete_folder_msg = '';
if(isset($_POST['delete_folder']) && $_POST['delete_folder'] == 'Delete Folder') {
	$delete_folder_path = $_POST['delete_folder_path'];		
	$delete_folder_msg = $awsObj->deleteFolder($delete_folder_path);	
}
?>
<html>
	<head>
		<title>AWS S3 Demo :: Using PHP</title>
	</head>
<body>
	<div>
		<div>
			<h3>Get Cloud Front Url</h3>
			<form name="get-cloud" id="get-cloud" action="" method="post">
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Enter File Name:</div>
					<div ><input type="text" name="file_name" id="file_name" value="" style="width:200px" placeholder="05Sep14.3gp"></div>
				</div>
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Enter File Path:</div>
					<div ><input type="text" name="file_path" id="file_path" value="" style="width:200px" placeholder="test-folder/sub-folder"></div>
				</div>

				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">&nbsp;</div>
					<div ><input type="submit" name="get_cloud" id="get_cloud" value="Get Cloud Front Url"></div>
				</div>
				<div id="cloud-result">
					<?php if(isset($get_url) && $get_url != '') { ?>
					<a href="<?php echo $get_url; ?>" target="_blank" ><?php echo $get_url; ?></a>
					<?php } ?>
				</div>
			</form>
		</div>

		<div>
			<h3>Upload File in S3</h3>
			<form name="upload-cloud" id="upload-cloud" action="" method="post" enctype="multipart/form-data">
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Enter Path To Upload:</div>
					<div ><input type="text" name="upload_path" id="upload_path" value="" style="width:200px" placeholder="test-folder/sub-folder"></div>
				</div>
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Browse File:</div>
					<div ><input type="file" name="upload_file" id="upload_file" style="width:200px" ></div>
				</div>

				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">&nbsp;</div>
					<div ><input type="submit" name="upload_cloud" id="upload_cloud" value="Upload File"></div>
				</div>
				<div id="result">
					<?php if(isset($upload_url) && $upload_url != '') { ?>
					<a href="<?php echo $upload_url; ?>" target="_blank" ><?php echo $upload_url; ?></a>
					<?php } ?>					
				</div>
			</form>
		</div>

		<div>
			<h3>Delete File from S3</h3>
			<form name="delete-file" id="delete-file" action="" method="post" >
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Enter Delete File Path:</div>
					<div ><input type="text" name="delete_file_path" id="delete_file_path" value="" style="width:200px" placeholder="test-bala/ajax-loader.gif"></div>
				</div>

				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">&nbsp;</div>
					<div ><input type="submit" name="delete_file" id="delete_file" value="Delete File"></div>
				</div>
				<div id="result">
					<?php if(isset($delete_file_msg) && $delete_file_msg != '') { ?>
					<a href="#" target="_blank" ><?php echo $delete_file_msg; ?></a>
					<?php } ?>
				</div>
			</form>
		</div>

		<div>
			<h3>Delete Folder from S3</h3>
			<form name="delete-folder" id="delete-folder" action="" method="post" >
				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">Enter Delete Folder Path:</div>
					<div ><input type="text" name="delete_folder_path" id="delete_folder_path" value="" style="width:200px" placeholder="test-bala/test-folder"></div>
				</div>

				<div style="width:500px;padding:10px;clear:both">
					<div style="float:left;width:150px">&nbsp;</div>
					<div ><input type="submit" name="delete_folder" id="delete_folder" value="Delete Folder"></div>
				</div>
				<div id="result">
					<?php if(isset($delete_folder_msg) && $delete_folder_msg != '') { ?>
					<a href="#" target="_blank" ><?php echo $delete_folder_msg; ?></a>
					<?php } ?>
				</div>
			</form>
		</div>
</body>
</html>