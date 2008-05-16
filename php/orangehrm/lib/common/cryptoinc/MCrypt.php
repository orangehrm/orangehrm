<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MCrypt PHP extension wrapper for Crypt_Blowfish package
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
 * @author     Philippe Jausions <jausions@php.net>
 * @copyright  2005-2006 Matthew Fonda
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: MCrypt.php,v 1.3 2006/05/29 17:16:43 jausions Exp $
 * @link       http://pear.php.net/package/Crypt_Blowfish
 * @since      1.1.0
 */

/**
 * Include base class
 */
require_once 'Blowfish.php';

/**
 * Example using the factory method in CBC mode and forcing using
 * the MCrypt library.
 * <code>
 * $bf =& Crypt_Blowfish::factory('cbc', null, null, CRYPT_BLOWFISH_MCRYPT);
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
 * echo "plain text: $plaintext";
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
 * @since      1.1.0
 */
class Crypt_Blowfish_MCrypt extends Crypt_Blowfish
{
    /**
     * Mcrypt td resource
     *
     * @var resource
     * @access private
     */
    var $_td = null;

    /**
     * Crypt_Blowfish Constructor
     * Initializes the Crypt_Blowfish object, and sets the secret key
     *
     * @param string $key
     * @param string $mode operating mode 'ecb', 'cbc'...
     * @param string $iv initialization vector
     * @access public
     */
    function Crypt_Blowfish_MCrypt($key = null, $mode = 'ecb', $iv = null)
    {
        $this->_iv = $iv . ((strlen($iv) < 8)
                            ? str_repeat(chr(0), 8 - strlen($iv)) : '');

        $this->_td = mcrypt_module_open(MCRYPT_BLOWFISH, '', $mode, '');
        if (is_null($iv)) {
            $this->_iv = mcrypt_create_iv(8, MCRYPT_RAND);
        }

        switch (strtolower($mode)) {
            case 'ecb':
                $this->_iv_required = false;
                break;

            case 'cbc':
            default:
                $this->_iv_required = true;
                break;
        }

        $this->setKey($key, $this->_iv);
    }

    /**
     * Encrypts a string
     *
     * Value is padded with NUL characters prior to encryption. You may
     * need to trim or cast the type when you decrypt.
     *
     * @param string $plainText string of characters/bytes to encrypt
     * @return string|PEAR_Error Returns cipher text on success,
     *                           or PEAR_Error on failure
     * @access public
     */
    function encrypt($plainText)
    {
        if (!is_string($plainText)) {
            return PEAR::raiseError('Input must be a string', 0);
        }

        return mcrypt_generic($this->_td, $plainText);
    }


    /**
     * Decrypts an encrypted string
     *
     * The value was padded with NUL characters when encrypted. You may
     * need to trim the result or cast its type.
     *
     * @param string $cipherText
     * @return string|PEAR_Error Returns plain text on success,
     *                           or PEAR_Error on failure
     * @access public
     */
    function decrypt($cipherText)
    {
        if (!is_string($cipherText)) {
            return PEAR::raiseError('Cipher text must be a string', 1);
        }

        return mdecrypt_generic($this->_td, $cipherText);
    }

    /**
     * Sets the secret key
     * The key must be non-zero, and less than or equal to
     * 56 characters (bytes) in length.
     *
     * If you are making use of the PHP mcrypt extension, you must call this
     * method before each encrypt() and decrypt() call.
     *
     * @param string $key
     * @param string $iv 8-char initialization vector (required for CBC mode)
     * @return boolean|PEAR_Error  Returns TRUE on success, PEAR_Error on failure
     * @access public
     */
    function setKey($key, $iv = null)
    {
        static $keyHash = null;

        if (!is_string($key)) {
            return PEAR::raiseError('Key must be a string', 2);
        }

        $len = strlen($key);

        if ($len > 56 || $len == 0) {
            return PEAR::raiseError('Key must be less than 56 characters (bytes) and non-zero. Supplied key length: ' . $len, 3);
        }

        if ($this->_iv_required) {
            if (strlen($iv) != 8) {
                return PEAR::raiseError('IV must be 8-character (byte) long. Supplied IV length: ' . strlen($iv), 7);
            }
            $this->_iv = $iv;
        }

        $return = mcrypt_generic_init($this->_td, $key, $this->_iv);
        if ($return < 0) {
            return PEAR::raiseError('Unknown PHP MCrypt library error', 4);
        }
        return true;
    }
}

?>