<?php

class Conf
{
    private string $dbHost;
    private string $dbPort;
    private string $dbName;
    private string $dbUser;
    private string $dbPass;

    public function __construct()
    {
        $this->dbHost = $_ENV['DB_HOST'];
        $this->dbPort = $_ENV['DB_PORT'];
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'test') {
            $prefix = defined('TEST_DB_PREFIX') ? TEST_DB_PREFIX : '';
            $this->dbName = $prefix . 'test_'. $_ENV['DB_DATABASE'];
        } else {
            $this->dbName = $_ENV['DB_DATABASE'];
        }
        $this->dbUser = $_ENV['DB_USERNAME'];
        $this->dbPass = $_ENV['DB_PASSWORD'];
    }

    /**
     * @return string
     */
    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    /**
     * @return string
     */
    public function getDbPort(): string
    {
        return $this->dbPort;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    /**
     * @return string
     */
    public function getDbPass(): string
    {
        return $this->dbPass;
    }
}