<?php

class UpgradeUtility {
    
    private $connection = null;
    
    public function getDbConnection($host, $username, $password, $dbname, $port) {
        if (!$this->connection) {
            if (!$port) {
                $this->connection = mysqli_connect($host, $username, $password, $dbname);
            } else {
                $this->connection = mysqli_connect($host, $username, $password, $dbname, $port);
            }
        }
        
        if (!$this->connection)
        {
            die('Could not connect: ' . mysqli_connect_error());
        }
        mysqli_autocommit($this->connection, FALSE);
        return $this->connection;
    }
    
    public function setDbConnection($connection) {
        $this->connection = $connection;
    }
    
    public function finalizeTransaction($transactionComplete) {
        if(!$transactionComplete) {
            mysqli_rollback($this->connection);
        } else {
            mysqli_commit($this->connection);
        }
    }
    
    public function closeDbConnection() {
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