<?php

require 'AwsS3.php';
require 'Action.php';

class Magic {

    /**
     * @params folder name
     * @params bucket
     */
    static function createFolderCB($folder_name, $bucket) {

        $res = AwsS3::createFolder($folder_name, $bucket);
        if ($res['statusCode'] == 200) {
            $status = 1;
            $isPublic = 1;
            $response = Action::createFolderInDB($folder_name, $res['ObjectURL'], $isPublic, $status);
            return $response;
        }
    }

    /**
     * @params file name
     * @params folder name
     * @params bucket
     */
    static function createFileCB($file_name, $folder_name, $bucket) {

        $res = AwsS3::uploadFile($file_name, $folder_name, $bucket);
        if ($res['statusCode'] == 200) {
            $status = 5;
            $idAmi = 'dev_RawConvert';
            $response = Action::createFileInDB($file_name, $folder_name, $res['ObjectURL'], $idAmi, $status);
            return $response;
        }
    }

    /**
     * @params bucket
     */
    static function getAllFolderCB($bucket) {

        $res = AwsS3::getListOfBuckets($bucket);
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
            // $a = explode("/", $val);
            // $k = 0;       
            // foreach($a as $Akey=>$Aval) {
                // $Aval = $Aval;
                // if (!strpos($a[$k], ".")) {
                //     $Aval = $Aval.'/';
                // }
                // if (trim($Aval) !== "") {
                //     if (strpos($Aval, "/") > 0 && $k >= 1) {
                //         $resArr[$a[$k-1]][] = $a[$k];
                //     }
                // }                
                // if (trim($Aval) !== "") {
                //     if (strpos($Aval, ".") > 0) {
                //         // $resArr1[$key][$k][] = trim($Aval);
                //     } else {
                //         $resArr1[$key][$k] = trim($Aval);
                //     }
                // }
                // $k++;
            // }            
        }
        return $resArr1;
    }
}

if (!empty($_REQUEST) && !empty($_REQUEST['newfoldername'])) {
    
    $newFoldeName = $_REQUEST['newfoldername'];
    $bucket = $_REQUEST['bucket'];
    $resposne = Magic::createFolderCB($newFoldeName, $bucket);
    return $resposne;

}