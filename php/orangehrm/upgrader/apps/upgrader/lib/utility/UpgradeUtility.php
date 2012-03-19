<?php

class UpgradeUtility {
    
    private $host = 'localhost';
    private $username = 'root';
    private $password = 'root';
    private $dbname = 'test';
    private $connection = null;
    
    public function connectDatabase() {
        $this->connection = mysqli_connect($this->host, $this->username, $this->password);
        if (!$this->connection)
        {
            die('Could not connect: ' . mysqli_connect_error());
        }
        mysqli_select_db($this->connection, $this->dbname);
        mysqli_autocommit($this->connection, FALSE);
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function finalizeTransaction($transactionComplete) {
        if(!$transactionComplete) {
            mysqli_rollback($this->connection);
        } else {
            mysqli_commit($this->connection);
        }
        mysqli_close($this->connection);
    }
    
    public function executeSql($statements) {
        return mysqli_query($this->connection, $statements );
    }
    
    public function fetchArray($tableData) {
        return mysqli_fetch_array($tableData);
    }
    
    public function createFile($content, $location) {
        
    }
}