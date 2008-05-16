<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHP implementation of the Blowfish algorithm in CBC mode
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
 * @version    CVS: $Id: CBC.php,v 1.7 2006/05/29 17:16:43 jausions Exp $
 * @link       http://pear.php.net/package/Crypt_Blowfish
 * @since      1.1.0
 */

/**
 * Required parent class
 */
require_once 'PHP.php';

/**
 * Example
 * <code>
 * $bf =& Crypt_Blowfish::factory('cbc');
 * if (PEAR::isError($bf)) {
 *     echo $bf->getMessage();
 *     exit;
 * }
 * $iv = 'abc123@%';
 * $bf->setKey('My secret key', $iv);
 * $encrypted = $bf->encrypt('this is some example plain text');
 * $bf->setKey('My secret key', $iv);
 * $plaintext = $bf->decrypt($encrypted);
 * echo "plain text: $plaintext";
 * <code>
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
class Crypt_Blowfish_CBC extends Crypt_Blowfish_PHP
{
    /**
     * Crypt_Blowfish Constructor
     * Initializes the Crypt_Blowfish object, and sets
     * the secret key
     *
     * @param string $key
     * @param string $iv initialization vector
     * @access public
     */
    function Crypt_Blowfish_CBC($key = null, $iv = null)
    {
        $this->__construct($key, $iv);
    }

    /**
     * Class constructor
     *
     * @param string $key
     * @param string $iv initialization vector
     * @access public
     */
    function __construct($key = null, $iv = null)
    {
        $this->_iv_required = true;
        parent::__construct($key, $iv);
    }

    /**
     * Encrypts a string
     *
     * Value is padded with NUL characters prior to encryption. You may
     * need to trim or cast the type when you decrypt.
     *
     * @param string $plainText string of characters/bytes to encrypt
     * @return string|PEAR_Error Returns cipher text on success, PEAR_Error on failure
     * @access public
     */
    function encrypt($plainText)
    {
        if (!is_string($plainText)) {
            return PEAR::raiseError('Input must be a string', 0);
        } elseif (empty($this->_P)) {
            return PEAR::raiseError('The key is not initialized.', 8);
        }

        $cipherText = '';
        $len = strlen($plainText);
        $plainText .= str_repeat(chr(0), (8 - ($len % 8)) % 8);

        list(, $Xl, $Xr) = unpack('N2', substr($plainText, 0, 8) ^ $this->_iv);
        $this->_encipher($Xl, $Xr);
        $cipherText .= pack('N2', $Xl, $Xr);

        for ($i = 8; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack('N2', substr($plainText, $i, 8) ^ substr($cipherText, $i - 8, 8));
            $this->_encipher($Xl, $Xr);
            $cipherText .= pack('N2', $Xl, $Xr);
        }

        return $cipherText;
    }

    /**
     * Decrypts an encrypted string
     *
     * The value was padded with NUL characters when encrypted. You may
     * need to trim the result or cast its type.
     *
     * @param string $cipherText
     * @return string|PEAR_Error Returns plain text on success, PEAR_Error on failure
     * @access public
     */
    function decrypt($cipherText)
    {
        if (!is_string($cipherText)) {
            return PEAR::raiseError('Cipher text must be a string', 1);
        }
        if (empty($this->_P)) {
            return PEAR::raiseError('The key is not initialized.', 8);
        }

        $plainText = '';
        $len = strlen($cipherText);
        $cipherText .= str_repeat(chr(0), (8 - ($len % 8)) % 8);

        list(, $Xl, $Xr) = unpack('N2', substr($cipherText, 0, 8));
        $this->_decipher($Xl, $Xr);
        $plainText .= (pack('N2', $Xl, $Xr) ^ $this->_iv);

        for ($i = 8; $i < $len; $i += 8) {
            list(, $Xl, $Xr) = unpack('N2', substr($cipherText, $i, 8));
            $this->_decipher($Xl, $Xr);
            $plainText .= (pack('N2', $Xl, $Xr) ^ substr($cipherText, $i - 8, 8));
        }

        return $plainText;
    }
}

?>