<?php
/**
 * CSRFTokenGenerator to prevent forms from CSRF Attacks
 *
 * @author Sujith T
 */
class CSRFTokenGenerator {
   private $seed;
   private $token;
   private $cryptoKey;

   //we do this to make it a singleton class
   static private $tokenGenerator = null;
   
   /**
    * Class Constructor
    */
   private function __construct() {
      $filePath = ROOT_PATH . "/lib/confs/cryptokeys/key.csrf";
      if(file_exists($filePath)) {
         $file = fopen($filePath, "r");
         $this->cryptoKey = fread($file, filesize($filePath));
      }
   }

   /**
    * Returns own instance
    * @returns CSRFTokenGenerator
    */
   public static function getInstance() {
      if(is_null(self::$tokenGenerator)) {
         self::$tokenGenerator = new CSRFTokenGenerator();
      }
      return self::$tokenGenerator;
   }

   /**
    * Adding the Form for protection. Setting query strings array for maintaining uniqueness
    * @param array() $queryStrings
    * @return boolean
    */
   public function setKeyGenerationInput($queryStrings) {
      if(is_array($queryStrings)) {
         $seed = "";
         foreach($queryStrings as $k => $v) {
            $seed .= $v;
         }
         $keys = array_keys($queryStrings);
         sort($keys);
         $this->seed[implode("|", $keys)] = md5($seed);
         return true;
      }
      return false;
   }

   /**
    * Return Tokens based on generated keys
    * @param array() $queryStringKeys
    * @returns String
    */
   public function getCSRFToken($queryStringKeys) {
      if(is_array($queryStringKeys)) {
         sort($queryStringKeys);
         $key = implode("|", $queryStringKeys);
         if(isset($this->token[$key])) {
            return $this->token[$key];
         }
         $token = md5($this->seed[$key] . session_id() . __FILE__ . php_uname() . $this->cryptoKey);
         $this->token[$key] = $token;
      }
      return $this->token[$key];
   }

   /**
    * Clear Generated Tokens on generated keys
    * @param array() $queryStringKeys
    */
   public function clearToken($queryStringKeys) {
      $key = implode("|", $queryStringKeys);
      unset($this->token[$key]);
      unset($this->seed[$key]);
   }
}
?>
