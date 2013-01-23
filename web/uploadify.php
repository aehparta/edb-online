<?php

$targetFolder = '/uploads';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$targetFile = rtrim($targetPath,'/').'/'.$_FILES['Filedata']['name'];

	move_uploaded_file($tempFile, $targetFile);

	echo $targetFolder.'/'.$_FILES['Filedata']['name'];
}
