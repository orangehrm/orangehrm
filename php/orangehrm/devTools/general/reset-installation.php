<?php

@require_once "../../lib/confs/Conf.php";

$c = new Conf();
mysql_connect("$c->dbhost:$c->dbport", $c->dbuser, $c->dbpass);

if (mysql_query("DROP DATABASE {$c->dbname}")) {
    echo "Database '{$c->dbname}' was deleted.<br /><br />\n";
} else {
    echo "Couldn't delete datanse '{$c->dbname}'.<br /><br />\n";
}  

$file = "../../lib/confs/Conf.php";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

$file = "../../lib/confs/cryptokeys/key.ohrm";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

$file = "../../lib/logs/notification_mails.log";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

$file = "../../symfony/apps/orangehrm/config/emailConfiguration.yml";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

$file = "../../symfony/apps/orangehrm/config/parameters.yml";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

$file = "../../symfony/config/databases.yml";

if (file_exists($file)) {

    if (@unlink($file)) {
        echo "File '$file' was deleted.<br /><br />\n";
    } else {
        echo "Couldn't delete file '$file'.<br /><br />\n";
    }

}

?>
