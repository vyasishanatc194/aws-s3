<?php
require 'config.php';

class Connection {

    static function connectDB()
    {
        $conn = new mysqli(HOST, USERNAME, PASSWORD, DATABASE) or die("Connect failed: %s\n". $conn->error);
            
        return $conn;
    }
}