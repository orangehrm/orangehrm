<?php

class InstallCleaner {

    private $rootDir;
    private $isCli;
    private $lineEnd;

    public function __construct($rootDir) {
        $this->rootDir = realpath($rootDir);
        $this->isCli = php_sapi_name() == 'cli';
        $this->lineEnd =  $this->isCli ? "\n" : "<br /><br />\n";
    }

    public function removeFile($filePath) {
        if (file_exists($filePath)) {

            if (@unlink($filePath)) {
                $this->displayMessage("File '$filePath' was deleted.");
            } else {
                $this->displayMessage("Couldn't delete file '$filePath'");
            }
        } else {
            $this->displayMessage("File '{$filePath} not found.");
        }       
    }

    public function displayMessage($message) {
        echo $message . $this->lineEnd;
    }

    public function dropDatabase($dbHost, $dbPort, $dbName, $dbUser, $dbPassword) {
        
        mysql_connect("$dbHost:$dbPort", $dbUser, $dbPassword);        
        if (mysql_query("DROP DATABASE `{$dbName}`")) {
            $this->displayMessage("Database '{$dbName}' was deleted.");
        } else {
            $this->displayMessage("Couldn't delete database '{$dbName}'");
        }        
    }

    public function resetInstall() {
        $confPhpFile = $this->rootDir . "/lib/confs/Conf.php";
        
        if (file_exists($confPhpFile)) {
            require_once $confPhpFile;

            $c = new Conf();
            $this->dropDatabase($c->dbhost, $c->dbport, $c->dbname, $c->dbuser, $c->dbpass);
            
            $this->displayMessage("Removing files created at installation time:");
            $this->removeFile($confPhpFile);


            $file = $this->rootDir . "/lib/confs/cryptokeys/key.ohrm";
            $this->removeFile($file);

            $file = $this->rootDir . "/lib/logs/notification_mails.log";
            $this->removeFile($file);

            $file = $this->rootDir  . "/symfony/apps/orangehrm/config/emailConfiguration.yml";
            $this->removeFile($file);

            $file = $this->rootDir . "/symfony/apps/orangehrm/config/parameters.yml";
            $this->removeFile($file);

            $file = $this->rootDir . "/symfony/config/databases.yml";
            $this->removeFile($file);

        } else {
            $this->displayMessage("File {$confPhpFile} not found. Skipping install reset");
        }
    }
}

$installCleaner = new InstallCleaner(dirname(__FILE__) . "/../../");
$installCleaner->resetInstall();

