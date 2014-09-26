<?php

class Cryptographer {

    private static $cryptographicScheme;
    private static $key;

    /**
     *
     * Encrypts given value, and hex encodes it before returning.
     * Compatible with mysql: hex(aes_encrypt($val, $key))
     *
     * @param string $value Value to encrypt
     * @return string Encrypted value
     */
    public static function encrypt($value) {
        if (empty($value) || !KeyHandler::keyExists()) {
            return $value;
        }

        self::init();
        $encrypt = self::$cryptographicScheme->encrypt($value);
        $encrypt = strtoupper(bin2hex($encrypt));
        return $encrypt;
    }

    /**
     *
     * Decrypts given value
     * Compatible with mysql: aes_decrypt(unhex($val), $key)
     *
     * @param string $crypt Value to decrypt
     * @return string Decrypted value
     */
    public static function decrypt($crypt) {
        if (empty($crypt) || !KeyHandler::keyExists()) {
            return $crypt;
        }

        self::init();
        $crypt = pack("H*", $crypt);
        $decrypt = self::$cryptographicScheme->decrypt($crypt);
        return $decrypt;
    }

    /**
     * Initilizes cryptographic scheme
     */
    private static function init() {
        if (is_null(self::$cryptographicScheme)) {
            $key = KeyHandler::readKey();


            $mysqlKey = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

            for ($a = 0; $a < strlen($key); $a++) {
                $mysqlKey[$a % 16] = chr(ord($mysqlKey[$a % 16]) ^ ord($key[$a]));
            }

            $aes = new Crypt_Rijndael(CRYPT_RIJNDAEL_MODE_ECB);

            $aes->setKeyLength(128);
            $aes->setBlockLength(128);
            $aes->setKey($mysqlKey);

            self::$cryptographicScheme = $aes;
        }
    }

}
