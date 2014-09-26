<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * Encrypts, Decrypts encrypted fields when enabled.
 * Has to be added as a listener to the model class with the encrpted field.
 * 
 * @author ruchira
 */
class EncryptionListener extends Doctrine_Record_Listener {

    private $fieldName;
    
    private $key;

    private $logger;

    /**
     * Construct listener
     *
     * @param string $fieldName Field Name
     * @param string $key Encryption key
     */
    public function __construct($fieldName, $key) {
        $this->fieldName = $fieldName;

        $this->key = $key;
        $this->logger = Logger::getLogger('encryptionListener');
    }

    /*
     * Encrypts necessary fields before save
     *
     * @param Doctrine_Event $event Doctrine Event object
     * @return none
     */
    public function preSave(Doctrine_Event $event) {

        $this->logger->debug('In preSave');
        $data = $event->getInvoker();

        if (isset($data[$this->fieldName])) {
            
            $val = $data[$this->fieldName];
            $this->logger->debug($this->fieldName . ' (Before) : ' . $val);

            $enc = $this->encrypt($val, $this->key);
           
            $data[$this->fieldName] = $enc;
            $event->setInvoker($data);
            $this->logger->debug('After: ' . $data[$this->fieldName]);
        }
        $this->logger->debug('End of preSave');
    }

    /**
     *
     * Decrypt values before hydration.
     * 
     * @param Doctrine_Event $event Doctrine Event object
     */
    public function preHydrate(Doctrine_Event $event) {
        $this->logger->debug('In pre hydrate!!');
        $data = $event->data;

        if (isset($data[$this->fieldName])) {

            $enc = $data[$this->fieldName];
            $this->logger->debug($this->fieldName . ' Before: ' . $enc);
            
            $data[$this->fieldName] = $this->decrypt($enc, $this->key);

            $this->logger->debug('After: ' . $data[$this->fieldName]);
            $event->data = $data;
        }
        $this->logger->debug('End of pre hydrate!!');
    }

    /**
     * Decrypt values on dql updates
     * 
     * @param Doctrine_Event $event Doctrine Event object
     */
    public function preDqlUpdate(Doctrine_Event $event) {

        $this->logger->debug($this->fieldName .'preDqlUpdate');

        //$params = $event->getParams();
        //$alias = $params['alias'];

        $query = $event->getQuery();

        if ($query->contains($this->fieldName) ){

            // Find field in "set" query part
            $dqlSet = $query->getDqlPart("set");

            $paramNdx = null;
            $ndx = 0;

            $this->logger->debug("Contains " . $this->fieldName);

            foreach ($dqlSet as $set) {

                if ( stripos($set, "?") !== FALSE ) {

                    if (stripos($set, $this->fieldName) !== FALSE ) {
                        $this->logger->debug("Field found. paramNdx = " . $ndx);
                        $paramNdx = $ndx;
                    }
                    $ndx++;
                }

            }

            // Find corresponding query parameter and encrypt it.
            if (!is_null($ndx)) {
                $params = $query->getParams();                

                if (isset($params['set'])) {
                    $this->logger->debug("Set parameters found.");

                    $setParams = $params['set'];
                    if (isset($setParams[$paramNdx])) {
                        $setParams[$paramNdx] = $this->encrypt($setParams[$paramNdx], $this->key);
                        $params['set'] = $setParams;
                        $query->setParams($params);
                    }
                }
            }
        }
    }

    /**
     *
     * Decrypts given value with given key (hex decoding it first)
     *
     * Compatible with mysql: "aes_decrypt(unhex($val), $key)
     *
     * @param string $val - value to encrypt
     * @param string $key - key
     * @return string decrypted value
     */
    public function decrypt($crypt, $key){

        if (empty($crypt)) {
            return $crypt;
        }

        $crypt = pack("H*" , $crypt);
        $mysqlKey="\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

        for ($a=0;$a<strlen($key);$a++) {
            $mysqlKey[$a%16]=chr(ord($mysqlKey[$a%16]) ^ ord($key[$a]));
        }

        $aes = new Crypt_Rijndael(CRYPT_RIJNDAEL_MODE_ECB);

        $aes->setKeyLength(128);
        $aes->setBlockLength(128);
        $aes->setKey($mysqlKey);

        $decrypt = $aes->decrypt($crypt);
        return $decrypt;
    }

    /**
     *
     * Encrypts given value, with given key, and hex encodes it before
     * returning.
     *
     * Compatible with mysql: "hex(aes_encrypt($val, $key))
     *
     * @param string $val - value to encrypt
     * @param string $ky - key
     * @return string encrypted value
     */
    public function encrypt($val, $key){

        if (empty($val)) {
            return $val;
        }

        $mysqlKey = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

        for ($a=0;$a<strlen($key);$a++) {
            $mysqlKey[$a%16]=chr(ord($mysqlKey[$a%16]) ^ ord($key[$a]));
        }

        $aes = new Crypt_Rijndael(CRYPT_RIJNDAEL_MODE_ECB);

        $aes->setKeyLength(128);
        $aes->setBlockLength(128);
        $aes->setKey($mysqlKey);

        $encrypt = $aes->encrypt($val);

        $encrypt = strtoupper(bin2hex($encrypt));
        return $encrypt;
    }


    /**
     * Check if this is necessary? Only needed if we need to decrypts fields
     * after save();
     *
     * @param Doctrine_Event $event
     */
    public function postSave(Doctrine_Event $event) {
        //$this->logger->debug('In postSave');
    }

    /* Methods kept for debugging */
    public function preInsert(Doctrine_Event $event) {
        //$this->logger->debug('In preInsert');
    }
    public function preUpdate(Doctrine_Event $event) {
        //$this->logger->debug('In preUpdate');
    }

    public function preDqlSelect(Doctrine_Event $event) {
        //$this->logger->debug($this->fieldName .' - preDqlSelect');
    }

    public function preDqlDelete(Doctrine_Event $event){
       // $this->logger->debug($this->fieldName .'preDqlDelete!!');
    }

    public function preQuery(Doctrine_Event $event){
        //$this->logger->debug($this->fieldName .'preQuery!!');
    }


}
?>
