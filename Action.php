<?php
require 'Connection.php';

class Action {
    
    /**
     * @param folder_name
     * @param objectUrl
     * @param isPuclic
     * @param status
     */
    static function createFolderInDB($folder_name, $ObjectURL, $isPublic, $status)
    {
        $MyConnection = Connection::connectDB();

        $arr = explode("/", $folder_name);
        
        // $MyParam = '@CRUD_CREATE'; // idFolder
        $MyParam0 = $arr[count($arr)-2]; // idFolder
        $MyParam1 = $arr[count($arr)-2]; // folderName
        $MyParam2 = ''; // idProject
        $MyParam3 = $isPublic; // isPublic
        $MyParam4 = ''; // idUser
        $MyParam5 = $status; // status
        $MyParam6 = $ObjectURL; // folderLink

        mysqli_query($MyConnection ,"SET @p0='".$MyParam0."'");
        mysqli_query($MyConnection ,"SET @p1='".$MyParam1."'");
        mysqli_query($MyConnection ,"SET @p2='".$MyParam2."'");
        mysqli_query($MyConnection ,"SET @p3='".$MyParam3."'");
        mysqli_query($MyConnection ,"SET @p4='".$MyParam4."'");
        mysqli_query($MyConnection ,"SET @p5='".$MyParam5."'");
        mysqli_query($MyConnection ,"SET @p6='".$MyParam6."'");

        // mysqli_query($MyConnection ,"SET par_CRUD='".$MyParam."'");
        // mysqli_query($MyConnection ,"SET par_idFolder_HEX='".$MyParam0."'");
        // mysqli_query($MyConnection ,"SET par_folderName='".$MyParam1."'");
        // mysqli_query($MyConnection ,"SET par_idProject='".$MyParam2."'");
        // mysqli_query($MyConnection ,"SET par_ispublic='".$MyParam3."'");
        // mysqli_query($MyConnection ,"SET par_idUser='".$MyParam4."'");
        // mysqli_query($MyConnection ,"SET par_status='".$MyParam5."'");
        // mysqli_query($MyConnection ,"SET par_folderLink='".$MyParam6."'");
        // mysqli_query($MyConnection ,"SET par_RETURN='@PAR_NONE'");

        // mysqli_multi_query($MyConnection, "CALL get_constants()");

        // if (mysqli_multi_query($MyConnection, "CALL CRUD_prs_folders(par_CRUD, par_idFolder_HEX, par_folderName, par_idProject, par_ispublic, par_idUser, par_status, par_folderLink, par_RETURN)")) {
        if (mysqli_multi_query($MyConnection, "CALL CREATE_Folder(@p0, @p1, @p2, @p3, @p4, @p5, @p6)")) {
            return [
                'msg' => 'Folder created successfully in Database',
                'success' => true
            ];
            mysqli_close($MyConnection);
        } else {
            return [
                'msg' => mysqli_error($MyConnection),
                'success' => false
            ];
            exit;
        }
    }

    /**
     * @param file_name
     * @param folder_name
     * @param objectUrl
     * @param idAmi
     * @param status
     */
    static function createFileInDB($file_name, $folder_name, $ObjectURL, $idAmi, $status)
    {
        $MyConnection = Connection::connectDB();

        $MyParam0 = isset($file_name['name']) ? $file_name['name'] : ''; // idFolder
        $MyParam1 = isset($file_name['name']) ? $file_name['name'] : ''; // idFile
        $MyParam2 = ''; // type
        $MyParam3 = $status; // status
        $MyParam4 = $idAmi; // idAmi
        $MyParam5 = ''; // Errortype
        $MyParam6 = isset($file_name['name']) ? $file_name['name'] : ''; // filename
        $MyParam7 = ''; // hashfile
        $MyParam8 = $ObjectURL; // filetarget
        $MyParam9 = ''; // idSibling

        mysqli_query($MyConnection ,"SET @p0='".$MyParam0."'");
        mysqli_query($MyConnection ,"SET @p1='".$MyParam1."'");
        mysqli_query($MyConnection ,"SET @p2='".$MyParam2."'");
        mysqli_query($MyConnection ,"SET @p3='".$MyParam3."'");
        mysqli_query($MyConnection ,"SET @p4='".$MyParam4."'");
        mysqli_query($MyConnection ,"SET @p5='".$MyParam5."'");
        mysqli_query($MyConnection ,"SET @p6='".$MyParam6."'");
        mysqli_query($MyConnection ,"SET @p7='".$MyParam7."'");
        mysqli_query($MyConnection ,"SET @p8='".$MyParam8."'");
        mysqli_query($MyConnection ,"SET @p9='".$MyParam9."'");

        if (mysqli_multi_query($MyConnection, "CALL CREATE_File(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9)")) {
            return [
                'msg' => 'File created successfully in Database',
                'success' => true
            ];
            mysqli_close($MyConnection);
        } else {
            return [
                'msg' => mysqli_error($MyConnection),
                'success' => false
            ];
            exit;
        }
    }
}
