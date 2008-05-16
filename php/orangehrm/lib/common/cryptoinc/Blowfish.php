<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Crypt_Blowfish allows for encryption and decryption on the fly using
 * the Blowfish algorithm. Crypt_Blowfish does not require the MCrypt
 * PHP extension, but uses it if available, otherwise it uses only PHP.
 * Crypt_Blowfish supports encryption/decryption with or without a secret key.
 *
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Encryption
 * @package    Crypt_Blowfish
 * @author     Matthew Fonda <mfonda@php.net>
 * @copyright  2005 Matthew Fonda
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Blowfish.php,v 1.85 2006/05/29 17:16:43 jausions Exp $
 * @link       http://pear.php.net/package/Crypt_Blowfish
 */

/**
 * Required PEAR package(s)
 */
require_once 'PEAR.php';

/**
 * Engine choice constants
 */
/**
 * To let the Crypt_Blowfish package decide which engine to use
 * @since 1.1.0
 */
define('CRYPT_BLOWFISH_AUTO',   1);
/**
 * To use the MCrypt PHP extension.
 * @since 1.1.0
 */
define('CRYPT_BLOWFISH_MCRYPT', 2);
/**
 * To use the PHP-only engine.
 * @since 1.1.0
 */
define('CRYPT_BLOWFISH_PHP',    3);


/**
 * Example using the factory method in CBC mode
 * <code>
 * $bf =& Crypt_Blowfish::factory('cbc');
 * if (PEAR::isError($bf)) {
 *     echo $bf->getMessage();
 *     exit;
 * }
 * $iv = 'abc123+=';
 * $key = 'My secret key';
 * $bf->setKey($key, $iv);
 * $encrypted = $bf->encrypt('this is some example plain text');
 * $bf->setKey($key, $iv);
 * $plaintext = $bf->decrypt($encrypted);
 * if (PEAR::isError($plaintext)) {
 *     echo $plaintext->getMessage();
 *     exit;
 * }
 * // Encrypted text is padded prior to encryption
 * // so you may need to trim the decrypted result.
 * echo 'plain text: ' . trim($plaintext);
 * </code>
 *
 * To disable using the mcrypt library, define the CRYPT_BLOWFISH_NOMCRYPT
 * constant. This is useful for instance on Windows platform with a buggy
 * mdecrypt_generic() function.
 * <code>
 * define('CRYPT_BLOWFISH_NOMCRYPT', true);
 * </code>
 *
 * @category   Encryption
 * @package    Crypt_Blowfish
 * @author     Matthew Fonda <mfonda@php.net>
 * @author     Philippe Jausions <jausions@php.net>
 * @copyright  2005-2006 Matthew Fonda
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @link       http://pear.php.net/package/Crypt_Blowfish
 * @version    @package_version@
 * @access     public
 */
class Crypt_Blowfish
{
    /**
     * Implementation-specific Crypt_Blowfish object
     *
     * @var object
     * @access private
     */
    var $_crypt = null;

    /**
     * Initialization vector
     *
     * @var string
     * @access protected
     */
    var $_iv = null;

    /**
     * Holds block size
     *
     * @var integer
     * @access protected
     */
    var $_block_size = 8;

    /**
     * Holds IV size
     *
     * @var integer
     * @access protected
     */
    var $_iv_size = 8;

    /**
     * Holds max key size
     *
     * @var integer
     * @access protected
     */
    var $_key_size = 56;

    /**
     * Crypt_Blowfish Constructor
     * Initializes the Crypt_Blowfish object (in EBC mode), and sets
     * the secret key
     *
     * @param string $key
     * @access public
     * @deprecated Since 1.1.0
     * @see Crypt_Blowfish::factory()
     */
    function Crypt_Blowfish($key)
    {
        $this->_crypt =& Crypt_Blowfish::factory('ecb', $key);
        if (!PEAR::isError($this->_crypt)) {
            $this->_crypt->setKey($key);
        }
    }

    /**
     * Crypt_Blowfish object factory
     *
     * This is the recommended method to create a Crypt_Blowfish instance.
     *
     * When using CRYPT_BLOWFISH_AUTO, you can force the package to ignore
     * the MCrypt extension, by defining CRYPT_BLOWFISH_NOMCRYPT.
     *
     * @param string $mode operating mode 'ecb' or 'cbc' (case insensitive)
     * @param string $key
     * @param string $iv initialization vector (must be provided for CBC mode)
     * @param integer $engine one of CRYPT_BLOWFISH_AUTO, CRYPT_BLOWFISH_PHP
     *                or CRYPT_BLOWFISH_MCRYPT
     * @return object Crypt_Blowfish object or PEAR_Error object on error
     * @access public
     * @static
     * @since 1.1.0
     */
    function &factory($mode = 'ecb', $key = null, $iv = null, $engine = CRYPT_BLOWFISH_AUTO)
    {
        switch ($engine) {
            case CRYPT_BLOWFISH_AUTO:
                if (!defined('CRYPT_BLOWFISH_NOMCRYPT')
                    && extension_loaded('mcrypt')) {
                    $engine = CRYPT_BLOWFISH_MCRYPT;
                } else {
                    $engine = CRYPT_BLOWFISH_PHP;
                }
                break;
            case CRYPT_BLOWFISH_MCRYPT:
                if (!PEAR::loadExtension('mcrypt')) {
                    return PEAR::raiseError('MCrypt extension is not available.');
                }
                break;
        }

        switch ($engine) {
            case CRYPT_BLOWFISH_PHP:
                $mode = strtoupper($mode);
                $class = 'Crypt_Blowfish_' . $mode;
                include_once '' . $mode . '.php';
                $crypt = new $class(null);
                break;

            case CRYPT_BLOWFISH_MCRYPT:
                include_once 'MCrypt.php';
                $crypt = new Crypt_Blowfish_MCrypt(null, $mode);
                break;
        }

        if (!is_null($key) || !is_null($iv)) {
            $result = $crypt->setKey($key, $iv);
            if (PEAR::isError($result)) {
                return $result;
            }
        }

        return $crypt;
    }

    /**
     * Returns the algorithm's block size
     *
     * @return integer
     * @access public
     * @since 1.1.0
     */
    function getBlockSize()
    {
        return $this->_block_size;
    }

    /**
     * Returns the algorithm's IV size
     *
     * @return integer
     * @access public
     * @since 1.1.0
     */
    function getIVSize()
    {
        return $this->_iv_size;
    }

    /**
     * Returns the algorithm's maximum key size
     *
     * @return integer
     * @access public
     * @since 1.1.0
     */
    function getMaxKeySize()
    {
        return $this->_key_size;
    }

    /**
     * Deprecated isReady method
     *
     * @return bool
     * @access public
     * @deprecated
     */
    function isReady()
    {
        return true;
    }

    /**
     * Deprecated init method - init is now a private
     * method and has been replaced with _init
     *
     * @return bool
     * @access public
     * @deprecated
     */
    function init()
    {
        return $this->_crypt->init();
    }

    /**
     * Encrypts a string
     *
     * Value is padded with NUL characters prior to encryption. You may
     * need to trim or cast the type when you decrypt.
     *
     * @param string $plainText the string of characters/bytes to encrypt
     * @return string|PEAR_Error Returns cipher text on success, PEAR_Error on failure
     * @access public
     */
    function encrypt($plainText)
    {
        return $this->_crypt->encrypt($plainText);
    }


    /**
     * Decrypts an encrypted string
     *
     * The value was padded with NUL characters when encrypted. You may
     * need to trim the result or cast its type.
     *
     * @param string $cipherText the binary string to decrypt
     * @return string|PEAR_Error Returns plain text on success, PEAR_Error on failure
     * @access public
     */
    function decrypt($cipherText)
    {
        return $this->_crypt->decrypt($cipherText);
    }

    /**
     * Sets the secret key
     * The key must be non-zero, and less than or equal to
     * 56 characters (bytes) in length.
     *
     * If you are making use of the PHP MCrypt extension, you must call this
     * method before each encrypt() and decrypt() call.
     *
     * @param string $key
     * @return boolean|PEAR_Error  Returns TRUE on success, PEAR_Error on failure
     * @access public
     */
    function setKey($key)
    {
        return $this->_crypt->setKey($key);
    }
}

?>