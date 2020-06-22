<?php
require 'config.php';

class Connection {

    static function connectDB()
    {

        shell_exec("ssh -fNg -L HOST:3306 bitnami@inspectra.org");
        $conn = new mysqli(HOST, USERNAME, PASSWORD, DATABASE);

        // $conn = new mysqli(HOST, USERNAME, PASSWORD, DATABASE) or die("Connect failed: %s\n". $conn->error);
            
        return $conn;
            
        // return new PDO("mysql:host=".HOST.";dbname=".DATABASE."", USERNAME, PASSWORD);
    }

}

// mysql_connect(HOST, USERNAME, PASSWORD) or die (mysql_error());
// mysql_select_database(DATABASE) or die (mysql_error());