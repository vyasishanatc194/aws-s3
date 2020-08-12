<?php
require_once WPS3_PLUGIN_DIR . '/classes/AwsS3.php';

class magic {

    /**
     * @param folder_name
     * @param bucket
     */
    static function createFolderCB($folder_name, $bucket, $skip) {
        
        if (!$bucket) {
            return 'Please enter bucket name';
        }
        if (!$folder_name) {
            return 'Please enter folder name';
        }
        $status = '';
        if (!$skip) {
            $status = 200;
        } else { 
            $res = AwsS3::createFolder($folder_name, $bucket);
            $status = $res['statusCode'];
        }
    }

    /**
     * @param bucket
     * @param userId
     */
    static function getAllFolderCB($bucket, $prefix) {
        $res = AwsS3::getListOfBuckets($bucket, $prefix);
        return $res;
    }

    /**
     * function to make an array from array
     */
    static function makeAnArr($str) {
        $resArr = [];
        $resArr1 = [
            'private' => [],
            'public' => [],
        ];
        foreach($str as $key=>$val) {
            if (!strpos($val, ".")) {
                $explode = explode('/', $val);
                if ($explode[0] == "private") {
                    if ($key >= 2 && isset($explode[2]) && trim($explode[2]) != "") {
                        $resArr1['private'][$explode[$key-1]][] =  $val;
                    } else {
                        $resArr1['private'][] = $val;
                    }                    
                } else {
                    if ($key >= 2 && isset($explode[2]) && trim($explode[2]) != "") {
                        $resArr1['public'][$explode[$key-1]][] =  $val;
                    } else {
                        $resArr1['public'][] = $val;
                    }
                }
            }         
        }
        return $resArr1;
    }
}

if (!empty($_REQUEST) && !empty($_REQUEST['newfoldername'])) {
    
    $newFolderName = $_REQUEST['newfoldername'];
    $skip = $_REQUEST['skip'];
    $bucket = $_REQUEST['bucket'];
    $response = Magic::createFolderCB($newFolderName, $bucket, $skip);
    if ($response['success']) {
        $folderNameArr = explode("/", $newFolderName);
        $response['data'] = [
            'folderName' => $folderNameArr[count($folderNameArr)-2],
            'folderPath' => $newFolderName
        ];
        $html = '';
        echo json_encode($response['data']); die;
    } else {
        echo $response['msg'];
    }
    die;
}

if (!empty($_REQUEST['getFolderList'])) {
    $bucket = $_REQUEST['bucket'];
    $desti = $_REQUEST['desti'];
    $newResposne = Magic::getAllFolderCB($bucket, $desti);
    $htmlArr = [];
    if ($newResposne) {
        foreach($newResposne as $key=>$val) {
            $explodedVal = explode("/", $val);
            $lastVal = trim($explodedVal[count($explodedVal)-1]);
            $v = ($lastVal != '') ? $lastVal : trim($explodedVal[count($explodedVal)-2]);
            $htmlArr[] = $v;
        }
        echo json_encode($htmlArr); die;
    } else {
        echo $response['msg'];
    }
}
