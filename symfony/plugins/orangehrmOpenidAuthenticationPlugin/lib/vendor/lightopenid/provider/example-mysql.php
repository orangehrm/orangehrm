<?php
/**
 * This example shows several things:
 * - How a setup interface should look like.
 * - How to use a mysql table for authentication
 * - How to store associations in mysql table, instead of php sessions.
 * - How to store realm authorizations.
 * - How to send AX/SREG parameters.
 * For the example to work, you need to create the necessary tables:
CREATE TABLE Users (
    id INT NOT NULL auto_increment PRIMARY KEY,
    login VARCHAR(32) NOT NULL,
    password CHAR(40) NOT NULL,
    firstName VARCHAR(32) NOT NULL,
    lastName VARCHAR(32) NOT NULL
);

CREATE TABLE AllowedSites (
    user INT NOT NULL,
    realm TEXT NOT NULL,
    attributes TEXT NOT NULL,
    INDEX(user)
);

CREATE TABLE Associations (
    id INT NOT NULL PRIMARY KEY,
    data TEXT NOT NULL
);
 *
 * This is only an example. Don't use it in your code as-is.
 * It has several security flaws, which you shouldn't copy (like storing plaintext login and password in forms).
 *
 * This setup could be very easily flooded with many associations, 
 * since non-private ones aren't automatically deleted.
 * You could prevent this by storing a date of association and removing old ones,
 * or by setting $this->dh = false;
 * However, the latter one would disable stateful mode, unless connecting via HTTPS.
 */
require 'provider.php';

mysql_connect();
mysql_select_db('test');

function getUserData($handle=null)
{
    if(isset($_POST['login'],$_POST['password'])) {
        $login = mysql_real_escape_string($_POST['login']);
        $password = sha1($_POST['password']);
        $q = mysql_query("SELECT * FROM Users WHERE login = '$login' AND password = '$password'");
        if($data = mysql_fetch_assoc($q)) {
            return $data;
        }
        if($handle) {
            echo 'Wrong login/password.';
        }
    }
    if($handle) {
    ?>
    <form action="" method="post">
    <input type="hidden" name="openid.assoc_handle" value="<?php echo $handle?>">
    Login: <input type="text" name="login"><br>
    Password: <input type="password" name="password"><br>
    <button>Submit</button>
    </form>
    <?php
    die();
    }
}

class MysqlProvider extends LightOpenIDProvider
{
    private $attrMap = array(
        'namePerson/first'    => 'First name',
        'namePerson/last'     => 'Last name',
        'namePerson/friendly' => 'Nickname (login)'
        );
    
    private $attrFieldMap = array(
        'namePerson/first'    => 'firstName',
        'namePerson/last'     => 'lastName',
        'namePerson/friendly' => 'login'
        );
    
    function setup($identity, $realm, $assoc_handle, $attributes)
    {
        $data = getUserData($assoc_handle);
        echo '<form action="" method="post">'
           . '<input type="hidden" name="openid.assoc_handle" value="' . $assoc_handle . '">'
           . '<input type="hidden" name="login" value="' . $_POST['login'] .'">'
           . '<input type="hidden" name="password" value="' . $_POST['password'] .'">'
           . "<b>$realm</b> wishes to authenticate you.";
        if($attributes['required'] || $attributes['optional']) {
            echo " It also requests following information (required fields marked with *):"
               . '<ul>';
            
            foreach($attributes['required'] as $attr) {
                if(isset($this->attrMap[$attr])) {
                    echo '<li>'
                       . '<input type="checkbox" name="attributes[' . $attr . ']"> '
                       . $this->attrMap[$attr] . '(*)</li>';
                }
            }
            
            foreach($attributes['optional'] as $attr) {
                if(isset($this->attrMap[$attr])) {
                    echo '<li>'
                       . '<input type="checkbox" name="attributes[' . $attr . ']"> '
                       . $this->attrMap[$attr] . '</li>';
                }
            }
            echo '</ul>';
        }
        echo '<br>'
           . '<button name="once">Allow once</button> '
           . '<button name="always">Always allow</button> '
           . '<button name="cancel">cancel</button> '
           . '</form>';
    }
    
    function checkid($realm, &$attributes)
    {
        if(isset($_POST['cancel'])) {
            $this->cancel();
        }
        
        $data = getUserData();
        if(!$data) {
            return false;
        }
        $realm = mysql_real_escape_string($realm);
        $q = mysql_query("SELECT attributes FROM AllowedSites WHERE user = '{$data['id']}' AND realm = '$realm'");
        
        $attrs = array();
        if($attrs = mysql_fetch_row($q)) {
            $attrs = explode(',', $attributes[0]);
        } elseif(isset($_POST['attributes'])) {
            $attrs = array_keys($_POST['attributes']);
        } elseif(!isset($_POST['once']) && !isset($_POST['always'])) {
            return false;
        }
        
        $attributes = array();
        foreach($attrs as $attr) {
            if(isset($this->attrFieldMap[$attr])) {
                $attributes[$attr] = $data[$this->attrFieldMap[$attr]];
            }
        }
        
        if(isset($_POST['always'])) {
            $attrs = mysql_real_escape_string(implode(',', array_keys($attributes)));
            mysql_query("REPLACE INTO AllowedSites VALUES('{$data['id']}', '$realm', '$attrs')");
        }
        
        return $this->serverLocation . '?' . $data['login'];
    }
    
    function assoc_handle()
    {
        # We generate an integer assoc handle, because it's just faster to look up an integer later.
        $q = mysql_query("SELECT MAX(id) FROM Associations");
        $result = mysql_fetch_row($q);
        return $q[0]+1;
    }
    
    function setAssoc($handle, $data)
    {
        $data = mysql_real_escape_string(serialize($data));
        mysql_query("REPLACE INTO Associations VALUES('$handle', '$data')");
    }
    
    function getAssoc($handle)
    {
        if(!is_numeric($handle)) {
            return false;
        }
        $q = mysql_query("SELECT data FROM Associations WHERE id = '$handle'");
        $data = mysql_fetch_row($q);
        if(!$data) {
            return false;
        }
        return unserialize($data[0]);
    }
    
    function delAssoc($handle)
    {
        if(!is_numeric($handle)) {
            return false;
        }
        mysql_query("DELETE FROM Associations WHERE id = '$handle'");
    }
    
}
$op = new MysqlProvider;
$op->server();
