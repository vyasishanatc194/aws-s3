<?php
require 'AwsS3.php';
require 'Action.php';

class Magic {

    /**
     * @param folder_name
     * @param bucket
     */
    static function createFolderCB($folder_name, $bucket) {

        if (!$bucket) {
            return 'Please enter bucket name';
        }
        if (!$folder_name) {
            return 'Please enter folder name';
        }
        $res = AwsS3::createFolder($folder_name, $bucket);
        if ($res['statusCode'] == 200) {
            $status = 1;
            $isPublic = 1;
            $response = Action::createFolderInDB($folder_name, $res['ObjectURL'], $isPublic, $status);
            return $response;
        }
    }

    /**
     * @param file_name
     * @param folder_name
     * @param bucket
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
    $bucket = $_REQUEST['bucket'];
    $response = Magic::createFolderCB($newFolderName, $bucket);
    if ($response['success']) {
        $folderNameArr = explode("/", $newFolderName);
        $html = '';
        // $newResposne = Magic::getAllFolderCB($bucket, $newFolderName);
        $html .= '<form action="Magic.php" method="POST" enctype="multipart/form-data">';
        $html .= '<label for="destination">';
        $html .= '<i class="fa fa-folder-open" style="margin: 0 10px;"></i><input type="radio" value="'.$newFolderName.'" id="destination" name="destination" selected />';
        $html .= $folderNameArr[count($folderNameArr)-2].'</label> <br/>';
        $html .= '<input type="file" name="upload_file" class="upload_file" /> <br/>';
        $html .= '<button type="submit" name="upload_file_btn" id="upload_file_btn">Upload File</button>';
        $html .= '</form>';
        // if (count($resposne) == 1) {
            
        // } else {
        //     $html .= '<ul>';
        //     foreach ($resposne as $key=>$val) {
        //         $valArray = explode("/", $val);
        //         $class = "child_folder";

        //         if ($key == 0) {
        //             $class = "root_folder";
        //         }

        //         $html .= '<li>';
        //         $html .= '<label for="'.$class.'">';
        //         $html .= '<input type="radio" id="'.$class.'" name="child_folder" class="'.$class.'" value="'.$val.'" /> ';
        //         $html .= $valArray[count($valArray) - 2].'</label>';
        //         $html .= '</li>';          
        //     }
        //     $html .= '</ul>';
        // }
        echo $html; die;
    } else {
        echo $response['msg'];
    }
    die;
}

if (!empty($_REQUEST) && !empty($_REQUEST['root'])) {
    
    $rootFolder = $_REQUEST['root'];
    $bucket = $_REQUEST['bucket'];
    $resposne = Magic::getAllFolderCB($bucket, $rootFolder);
    $html = '';
    $html .= '<input type="text" placeholder="Create a new folder" name="folder_name" class="folder_name" /> ';
    $html .= '<button type="button" name="create_folder" id="create_folder">Create a New Folder</button>';
    // if (count($resposne) == 1) {
        
    // } else {
    //     $html .= '<ul>';
    //     foreach ($resposne as $key=>$val) {
    //         $valArray = explode("/", $val);
    //         $class = "child_folder";

    //         if ($key == 0) {
    //             $class = "root_folder";
    //         }

    //         $html .= '<li>';
    //         $html .= '<label for="'.$class.'">';
    //         $html .= '<input type="radio" id="'.$class.'" name="child_folder" class="'.$class.'" value="'.$val.'" /> ';
    //         $html .= $valArray[count($valArray) - 2].'</label>';
    //         $html .= '</li>';            
    //     }
    //     $html .= '</ul>';
    // }
    echo $html; die;

}

if (!empty($_REQUEST) && !empty($_REQUEST['destination'])) {
    
    $destination = $_REQUEST['destination'];
    $bucket = AWS_S3_BUCKET;
    $fileName = $_FILES['upload_file'];
    $response = Magic::createFileCB($fileName, $destination, $bucket);
    if ($response['success']) {
        header('Location:'.BASEURL);
        // $fileNameArr = explode("/", $desctination);
        $html = '';
        print_r($response);
        echo $html; die;
    } else {
        echo $response['msg'];
    }
    die;
}