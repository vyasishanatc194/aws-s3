<?php
require 'Connection.php';

class Action {
    
    /**
     * @params folder name
     * @params Object Url (folder path from s3)
     * @params is Puclic
     * @params status
     */
    static function createFolderInDB($folder_name, $ObjectURL, $isPublic, $status)
    {
        $MyConnection = Connection::connectDB();

        $MyParam0 = time(); // idFolder
        $MyParam1 = time(); // folderName
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

        if (mysqli_multi_query($MyConnection, "CALL CREATE_Folder(@p0, @p1, @p2, @p3, @p4, @p5, @p6)")) {
            return 'Folder created successfully in Database';
            mysqli_close($MyConnection);
        } else {
            var_dump(mysqli_error($MyConnection));
            exit;
        }
    }

    /**
     * @params file name
     * @params folder name
     * @params Object Url (Folder / file url from s3)
     * @params id Ami
     * @params status
     */
    static function createFileInDB($file_name, $folder_name, $ObjectURL, $idAmi, $status)
    {
        $MyConnection = Connection::connectDB();

        $MyParam0 = $folder_name; // idFolder
        $MyParam1 = $file_name; // idFile
        $MyParam2 = ''; // type
        $MyParam3 = $status; // status
        $MyParam4 = $idAmi; // idAmi
        $MyParam5 = ''; // Errortype
        $MyParam6 = $file_name; // filename
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
            return 'File created successfully in Database';
            mysqli_close($MyConnection);
        } else {
            var_dump(mysqli_error($MyConnection));
            exit;
        }
    }
}
