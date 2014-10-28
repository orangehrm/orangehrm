<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Appends log events to a db table using PDO.
 *
 * Configurable parameters of this appender are:
 *
 * - user            - Sets the user of this database connection
 * - password        - Sets the password of this database connection
 * - createTable     - true, if the table should be created if necessary. false otherwise
 * - table           - Sets the table name (default: log4php_log)
 * - sql             - Sets the insert statement for a logging event. Defaults
 *                     to the correct one - change only if you are sure what you are doing.
 * - dsn             - Sets the DSN string for this connection
 *
 * If $sql is set then $table and $sql are used, else $table, $insertSql and $insertPattern.
 *
 * An example:
 *
 * {@example ../../examples/php/appender_pdo.php 19}
 * 
 * {@example ../../examples/resources/appender_pdo.properties 18}
 * 
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 * @since 2.0
 */
class LoggerAppenderPDO extends LoggerAppender {

    /** Create the log table if it does not exists (optional).
     * @var string */
	private $createTable = true;
    
    /** Database user name.
     * @var string */
    private $user = '';
    
    /** Database password
     * @var string */
    private $password = '';
    
    /** DSN string for enabling a connection.
     * @var string */
    private $dsn;
    
    /** A {@link LoggerPatternLayout} string used to format a valid insert query.
     * @deprecated Use {@link $insertSql} and {@link $insertPattern} which properly handle quotes in the messages!
     * @var string */
    private $sql;
    
    /** Can be set to a complete insert statement with ? that are replaced using {@link insertPattern}.
     * @var string */
    private $insertSql = "INSERT INTO __TABLE__ (timestamp, logger, level, message, thread, file, line) VALUES (?,?,?,?,?,?,?)";

    /** A comma separated list of {@link LoggerPatternLayout} format strings that replace the "?" in {@link $sql}.
     * @var string */
    private $insertPattern = "%d,%c,%p,%m,%t,%F,%L";

    /** Table name to write events. Used only for CREATE TABLE if {@link $createTable} is true.
     * @var string */
    private $table = 'log4php_log';
    
    /** The PDO instance.
     * @var PDO */
    private $db = null;
    
    /** Prepared statement for the INSERT INTO query.
     * @var PDOStatement */
    private $preparedInsert;

    /** Set in activateOptions() and later used in append() to check if all conditions to append are true.
     * @var boolean */
    private $canAppend = true;
    
    /**
     * Constructor.
     * This apender doesn't require a layout.
     * @param string $name appender name
     */
    public function __construct($name = '') {
        parent::__construct($name);
        $this->requiresLayout = false;
    }
    
	public function __destruct() {
       $this->close();
   	}
   	
    /**
     * Setup db connection.
     * Based on defined options, this method connects to db defined in {@link $dsn}
     * and creates a {@link $table} table if {@link $createTable} is true.
     * @return boolean true if all ok.
     * @throws a PDOException if the attempt to connect to the requested database fails.
     */
    public function activateOptions() {
        try {
        	if($this->user === null) {
	           	$this->db = new PDO($this->dsn);
    	   } else if($this->password === null) {
    	       $this->db = new PDO($this->dsn, $this->user);
    	   } else {
    	       $this->db = new PDO($this->dsn,$this->user,$this->password);
    	   }
    	   $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	
            // test if log table exists
            try {
                $result = $this->db->query('SELECT * FROM ' . $this->table . ' WHERE 1 = 0');
            } catch (PDOException $e) {
                // It could be something else but a "no such table" is the most likely
                $result = false;
            }
            
            // create table if necessary
            if ($result == false and $this->createTable) {
                // The syntax should at least be compatible with MySQL, PostgreSQL, SQLite and Oracle.
                $query = "CREATE TABLE {$this->table} (".
                            "timestamp varchar(32)," .
            				"logger varchar(64)," .
            										"level varchar(32)," .
            				"message varchar(9999)," .
            										"thread varchar(32)," .
            				"file varchar(255)," .
            				"line varchar(6))";
                $result = $this->db->query($query);
            }
        } catch (PDOException $e) {
            $this->canAppend = false;
            throw new LoggerException($e);
        }
        
        $this->layout = new LoggerLayoutPattern();
        
        //
        // Keep compatibility to legacy option $sql which already included the format patterns!
        //
        if (empty($this->sql)) {
            // new style with prepared Statment and $insertSql and $insertPattern
            // Maybe the tablename has to be substituted.
            $this->insertSql = preg_replace('/__TABLE__/', $this->table, $this->insertSql);
            $this->preparedInsert = $this->db->prepare($this->insertSql);
            $this->layout->setConversionPattern($this->insertPattern);
        } else {
            // Old style with format strings in the $sql query should be used.
        $this->layout->setConversionPattern($this->sql);
        }

        $this->canAppend = true;
        return true;
    }
    
    /**
     * Appends a new event to the database.
     * 
     * @throws LoggerException      If the pattern conversion or the INSERT statement fails.
     */
    public function append(LoggerLoggingEvent $event) {
        // TODO: Can't activateOptions() simply throw an Exception if it encounters problems?
        if ( ! $this->canAppend) return;

            try {
            if (empty($this->sql)) {
                // new style with prepared statement
                $params = $this->layout->formatToArray($event);
                $this->preparedInsert->execute($params);
            } else {
                // old style
                $query = $this->layout->format($event);
                $this->db->exec($query);
            }
            } catch (Exception $e) {
                throw new LoggerException($e);
            }
        }
    
    /**
     * Closes the connection to the logging database
     */
    public function close() {
    	if($this->closed != true) {
        	if ($this->db !== null) {
            	$db = null;
        	}
        	$this->closed = true;
    	}
    }
    
    /**
     * Sets the username for this connection. 
     * Defaults to ''
     */
    public function setUser($user) {
        $this->user = $user;
    }
    
    /**
     * Sets the password for this connection. 
     * Defaults to ''
     */
    public function setPassword($password) {
        $this->password = $password;
    }
    
    /**
     * Indicator if the logging table should be created on startup,
     * if its not existing.
     */
    public function setCreateTable($flag) {
        $this->createTable = LoggerOptionConverter::toBoolean($flag, true);
    }
   
   	/**
     * Sets the SQL string into which the event should be transformed.
     * Defaults to:
     * 
     * INSERT INTO $this->table 
     * ( timestamp, logger, level, message, thread, file, line) 
     * VALUES 
     * ('%d','%c','%p','%m','%t','%F','%L')
     * 
     * It's not necessary to change this except you have customized logging'
     *
     * @deprecated See {@link setInsertSql} and {@link setInsertPattern}.
     */
    public function setSql($sql) {
        $this->sql = $sql;    
    }
    
    /**
     * Sets the SQL INSERT string to use with {@link $insertPattern}.
     *
     * @param $sql          A complete INSERT INTO query with "?" that gets replaced.
     */
    public function setInsertSql($sql) {
        $this->insertSql = $sql;
    }

    /**
     * Sets the {@link LoggerLayoutPattern} format strings for {@link $insertSql}.
     *
     * It's not necessary to change this except you have customized logging.
     *
     * @param $pattern          Comma separated format strings like "%p,%m,%C"
     */
    public function setInsertPattern($pattern) {
        $this->insertPattern = $pattern;
    }

    /**
     * Sets the tablename to which this appender should log.
     * Defaults to log4php_log
     */
    public function setTable($table) {
        $this->table = $table;
    }
    
    /**
     * Sets the DSN string for this connection. In case of
     * SQLite it could look like this: 'sqlite:appenders/pdotest.sqlite'
     */
    public function setDSN($dsn) {
        $this->dsn = $dsn;
    }
    
    /**
     * Sometimes databases allow only one connection to themselves in one thread.
     * SQLite has this behaviour. In that case this handle is needed if the database
     * must be checked for events.
     *
     * @return PDO
     */
    public function getDatabaseHandle() {
        return $this->db;
    }
}

