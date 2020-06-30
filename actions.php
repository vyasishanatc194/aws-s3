<?php

$bucket = AWS_S3_BUCKET;

if (!empty($_REQUEST) && !empty($_REQUEST['newfoldername'])) {

    $resposne = [];
    $resposne['message'] = 'Error while creating Folder';

    $newFoldeName = $_REQUEST['newfoldername'];
    require 'Magic.php';
    $resposne = Magic::createFolderCB($bucket, $newFoldeName);

    return json_decode($resposne);
}

?>