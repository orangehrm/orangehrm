<?php
/**
 * Using this class, you can easily set up an OpenID Provider.
 * It's independent of LightOpenID class.
 * It requires either GMP or BCMath for session encryption, 
 * but will work without them (although either via SSL, or in stateless mode only).
 * Also, it requires PHP >= 5.1.2
 * 
 * This is an alpha version, using it in production code is not recommended,
 * until you are *sure* that it works and is secure.
 *
 * Please send me messages about your testing results 
 * (even if successful, so I know that it has been tested).
 * Also, if you think there's a way to make it easier to use, tell me -- it's an alpha for a reason.
 * Same thing applies to bugs in code, suggestions, 
 * and everything else you'd like to say about the library.
 *
 * There's no usage documentation here, see the examples.
 *
 * @author Mewp
 * @copyright Copyright (c) 2010, Mewp
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
ini_set('error_log','log');
abstract class LightOpenIDProvider
{
    # URL-s to XRDS and server location.
    public $xrdsLocation, $serverLocation;
    
    # Should we operate in server, or signon mode?
    public $select_id = false;
    
    # Lifetime of an association.
    protected $assoc_lifetime = 600;
    
    # Variables below are either set automatically, or are constant.
    # -----
    # Can we support DH?
    protected $dh = true;
    protected $ns = 'http://specs.openid.net/auth/2.0';
    protected $data, $assoc;
    
    # Default DH parameters as defined in the specification.
    protected $default_modulus;
    protected $default_gen = 'Ag==';
    
    # AX <-> SREG transform
    protected $ax_to_sreg = array(
        'namePerson/friendly'     => 'nickname',
        'contact/email'           => 'email',
        'namePerson'              => 'fullname',
        'birthDate'               => 'dob',
        'person/gender'           => 'gender',
        'contact/postalCode/home' => 'postcode',
        'contact/country/home'    => 'country',
        'pref/language'           => 'language',
        'pref/timezone'           => 'timezone',
        );
    
    # Math
    private $add, $mul, $pow, $mod, $div, $powmod;
    # -----
    
    # ------------------------------------------------------------------------ #
    #  Functions you probably want to implement when extending the class.
    
    /**
     * Checks whether an user is authenticated.
     * The function should determine what fields it wants to send to the RP, 
     * and put them in the $attributes array.
     * @param Array $attributes
     * @param String $realm Realm used for authentication.
     * @return String OP-local identifier of an authenticated user, or an empty value.
     */
    abstract function checkid($realm, &$attributes);
    
    /**
     * Displays an user interface for inputting user's login and password.
     * Attributes are always AX field namespaces, with stripped host part.
     * For example, the $attributes array may be:
     * array( 'required' => array('namePerson/friendly', 'contact/email'),
     *        'optional' => array('pref/timezone', 'pref/language')
     * @param String $identity Discovered identity string. May be used to extract login, unless using $this->select_id
     * @param String $realm Realm used for authentication.
     * @param String Association handle. must be sent as openid.assoc_handle in $_GET or $_POST in subsequent requests.
     * @param Array User attributes requested by the RP.
     */
    abstract function setup($identity, $realm, $assoc_handle, $attributes);
    
    /**
     * Stores an association.
     * If you want to use php sessions in your provider code, you have to replace it.
     * @param String $handle Association handle -- should be used as a key.
     * @param Array $assoc Association data.
     */
    protected function setAssoc($handle, $assoc)
    {
        $oldSession = session_id();
        session_commit();
        session_id($assoc['handle']);
        session_start();
        $_SESSION['assoc'] = $assoc;
        session_commit();
        if($oldSession) {
            session_id($oldSession);
            session_start();
        }
    }
    
    /**
     * Retreives association data.
     * If you want to use php sessions in your provider code, you have to replace it.
     * @param String $handle Association handle.
     * @return Array Association data.
     */
    protected function getAssoc($handle)
    {
        $oldSession = session_id();
        session_commit();
        session_id($handle);
        session_start();
        $assoc = null;
        if(!empty($_SESSION['assoc'])) {
            $assoc = $_SESSION['assoc'];
        }
        session_commit();
        if($oldSession) {
            session_id($oldSession);
            session_start();
        }
        return $assoc;
    }
    
    /**
     * Deletes an association.
     * If you want to use php sessions in your provider code, you have to replace it.
     * @param String $handle Association handle.
     */
    protected function delAssoc($handle)
    {
        $oldSession = session_id();
        session_commit();
        session_id($handle);
        session_start();
        session_destroy();
        if($oldSession) {
            session_id($oldSession);
            session_start();
        }
    }
    
    # ------------------------------------------------------------------------ #
    # Functions that you might want to implement.
    
    /**
     * Redirects the user to an url.
     * @param String $location The url that the user will be redirected to.
     */
    protected function redirect($location)
    {
        header('Location: ' . $location);
        die();
    }
    
    /**
     * Generates a new association handle.
     * @return string
     */
    protected function assoc_handle()
    {
        return sha1(microtime());
    }
    
    /**
     * Generates a random shared secret.
     * @return string
     */
    protected function shared_secret($hash)
    {
        $length = 20;
        if($hash == 'sha256') {
            $length = 256;
        }
        
        $secret = '';
        for($i = 0; $i < $length; $i++) {
            $secret .= mt_rand(0,255);
        }
        
        return $secret;
    }
    
    /**
     * Generates a private key.
     * @param int $length Length of the key.
     */
    protected function keygen($length)
    {
        $key = '';
        for($i = 1; $i < $length; $i++) {
            $key .= mt_rand(0,9);
        }
        $key .= mt_rand(1,9);
        
        return $key;
    }
    
    # ------------------------------------------------------------------------ #
    # Functions that you probably shouldn't touch.
    
    function __construct()
    {
        $this->default_modulus = 
            'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPy'
          . 'm2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0'
          . 'rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr';

        $location = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
                  . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $location = preg_replace('/\?.*/','',$location);
        $this->serverLocation = $location;
        $location .= (strpos($location, '?') ? '&' : '?') . 'xrds';
        $this->xrdsLocation = $location;
        
        $this->data = $_GET + $_POST;
        
        # We choose GMP if avaiable, and bcmath otherwise
        if(function_exists('gmp_add')) {
            $this->add = 'gmp_add';
            $this->mul = 'gmp_mul';
            $this->pow = 'gmp_pow';
            $this->mod = 'gmp_mod';
            $this->div = 'gmp_div';
            $this->powmod = 'gmp_powm';
        } elseif(function_exists('bcadd')) {
            $this->add = 'bcadd';
            $this->mul = 'bcmul';
            $this->pow = 'bcpow';
            $this->mod = 'bcmod';
            $this->div = 'bcdiv';
            $this->powmod = 'bcpowmod';
        } else {
            # If neither are avaiable, we can't use DH
            $this->dh = false;
        }
        
        # However, we do require the hash functions.
        # They should be built-in anyway.
        if(!function_exists('hash_algos')) {
            $this->dh = false;
        }
    }
    
    /**
     * Displays an XRDS document, or redirects to it.
     * By default, it detects whether it should display or redirect automatically.
     * @param bool|null $force When true, always display the document, when false always redirect.
     */
    function xrds($force=null)
    {
        if($force) {
            echo $this->xrdsContent();
            die();
        } elseif($force === false) {
            header('X-XRDS-Location: '. $this->xrdsLocation);
            return;
        }
        
        if (isset($_GET['xrds'])
            || (isset($_SERVER['HTTP_ACCEPT']) &&  strpos($_SERVER['HTTP_ACCEPT'], 'application/xrds+xml') !== false)
        ) {
            header('Content-Type: application/xrds+xml');
            echo $this->xrdsContent();
            die();
        }
        
        header('X-XRDS-Location: ' . $this->xrdsLocation);
    }
    
    /**
     * Returns the content of the XRDS document
     * @return String The XRDS document.
     */
    protected function xrdsContent()
    {
        $lines = array(
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<xrds:XRDS xmlns:xrds="xri://$xrds" xmlns="xri://$xrd*($v*2.0)">',
            '<XRD>',
            '    <Service>',
            '        <Type>' . $this->ns . '/' . ($this->select_id ? 'server' : 'signon') .'</Type>',
            '        <URI>' . $this->serverLocation . '</URI>',
            '    </Service>',
            '</XRD>',
            '</xrds:XRDS>'
            );
        return implode("\n", $lines);
    }
    
    /**
     * Does everything that a provider has to -- in one function.
     */
    function server()
    {
        if(isset($this->data['openid_assoc_handle'])) {
            $this->assoc = $this->getAssoc($this->data['openid_assoc_handle']);
            if(isset($this->assoc['data'])) {
                # We have additional data stored for setup.
                $this->data += $this->assoc['data'];
                unset($this->assoc['data']);
            }
        }
            
        if (isset($this->data['openid_ns'])
            && $this->data['openid_ns'] == $this->ns
        ) {
            if(!isset($this->data['openid_mode'])) $this->errorResponse();
            
            switch($this->data['openid_mode'])
            { 
            case 'checkid_immediate':
            case 'checkid_setup':
                $this->checkRealm();
                # We support AX xor SREG.
                $attributes = $this->ax();
                if(!$attributes) {
                    $attributes = $this->sreg();
                }
                
                # Even if some user is authenticated, we need to know if it's
                # the same one that want's to authenticate.
                # Of course, if we use select_id, we accept any user.
                if (($identity = $this->checkid($this->data['openid_realm'], $attrValues))
                    && ($this->select_id || $identity == $this->data['openid_identity'])
                ) {
                    $this->positiveResponse($identity, $attrValues);
                } elseif($this->data['openid_mode'] == 'checkid_immediate') {
                    $this->redirect($this->response(array('openid.mode' => 'setup_needed')));
                } else {
                    if(!$this->assoc) {
                        $this->generateAssociation();
                        $this->assoc['private'] = true;
                    }
                    $this->assoc['data'] = $this->data;
                    $this->setAssoc($this->assoc['handle'], $this->assoc);
                    $this->setup($this->data['openid_identity'],
                                 $this->data['openid_realm'],
                                 $this->assoc['handle'],
                                 $attributes);
                }
                break;
            case 'associate':
                $this->associate();
                break;
            case 'check_authentication':
                $this->checkRealm();
                if($this->verify()) {
                    echo "ns:$this->ns\nis_valid:true";
                    if(strpos($this->data['openid_signed'],'invalidate_handle') !== false) {
                        echo "\ninvalidate_handle:" . $this->data['openid_invalidate_handle'];
                    }
                } else {
                    echo "ns:$this->ns\nis_valid:false";
                }
                die();
                break;
            default:
                $this->errorResponse();
            }
        } else {
            $this->xrds();
        }
    }
    
    protected function checkRealm()
    {
        if (!isset($this->data['openid_return_to'], $this->data['openid_realm'])) {
            $this->errorResponse();
        }
        
        $realm = str_replace('\*', '[^/]', preg_quote($this->data['openid_realm']));
        if(!preg_match("#^$realm#", $this->data['openid_return_to'])) {
            $this->errorResponse();
        }
    }
    
    protected function ax()
    {
        # Namespace prefix that the fields must have.
        $ns = 'http://axschema.org/';
        
        # First, we must find out what alias is used for AX.
        # Let's check the most likely one
        $alias = null;
        if (isset($this->data['openid_ns_ax'])
            && $this->data['openid_ns_ax'] == 'http://openid.net/srv/ax/1.0'
        ) {
            $alias = 'ax';
        } else {
            foreach($this->data as $name => $value) {
                if ($value == 'http://openid.net/srv/ax/1.0'
                    && preg_match('/openid_ns_(.+)/', $name, $m)
                ) {
                    $alias = $m[1];
                    break;
                }
            }
        }
        
        if(!$alias) {
            return null;
        }
        
        $fields = array();
        # Now, we must search again, this time for field aliases
        foreach($this->data as $name => $value) {
            if (strpos($name, 'openid_' . $alias . '_type') === false
                || strpos($value, $ns) === false) {
                continue;
            }
            
            $name = substr($name, strlen('openid_' . $alias . '_type_'));
            $value = substr($value, strlen($ns));
            
            $fields[$name] = $value;
        }
        
        # Then, we find out what fields are required and optional
        $required = array();
        $if_available = array();
        foreach(array('required','if_available') as $type) {
            if(empty($this->data["openid_{$alias}_{$type}"])) {
                continue;
            }
            $attributes = explode(',', $this->data["openid_{$alias}_{$type}"]);
            foreach($attributes as $attr) {
                if(empty($fields[$attr])) {
                    # There is an undefined field here, so we ignore it.
                    continue;
                }
                
                ${$type}[] = $fields[$attr];
            }
        }
        
        $this->data['ax'] = true;
        return array('required' => $required, 'optional' => $if_available);
    }
    
    protected function sreg()
    {
        $sreg_to_ax = array_flip($this->ax_to_sreg);
        
        $attributes = array('required' => array(), 'optional' => array());
        
        if (empty($this->data['openid_sreg_required'])
            && empty($this->data['openid_sreg_optional'])
        ) {
            return $attributes;
        }
        
        foreach(array('required', 'optional') as $type) {
            foreach(explode(',',$this->data['openid_sreg_' . $type]) as $attr) {
                if(empty($sreg_to_ax[$attr])) {
                    # Undefined attribute in SREG request.
                    # Shouldn't happen, but we check anyway.
                    continue;
                }
                
                $attributes[$type][] = $sreg_to_ax[$attr];
            }
        }
        
        return $attributes;
    }
    
    /**
     * Aids an RP in assertion verification.
     * @return bool Information whether the verification suceeded.
     */
    protected function verify()
    {
        # Firstly, we need to make sure that there's an association.
        # Otherwise the verification will fail, 
        # because we've signed assoc_handle in the assertion
        if(empty($this->assoc)) {
            return false;
        }
        
        # Next, we check that it's a private association, 
        # i.e. one made without RP input.
        # Otherwise, the RP shouldn't ask us to verify.
        if(empty($this->assoc['private'])) {
            return false;
        }
        
        # Now we have to check if the nonce is correct, to prevent replay attacks.
        if($this->data['openid_response_nonce'] != $this->assoc['nonce']) {
            return false;
        }
        
        # Getting the signed fields for signature.
        $sig = array();
        $signed = explode(',', $this->data['openid_signed']);
        foreach($signed as $field) {
            $name = strtr($field, '.', '_');
            if(!isset($this->data['openid_' . $name])) {
                return false;
            }
            
            $sig[$field] = $this->data['openid_' . $name];
        }
        
        # Computing the signature and checking if it matches.
        $sig = $this->keyValueForm($sig);
        if ($this->data['openid_sig'] != 
            base64_encode(hash_hmac($this->assoc['hash'], $sig, $this->assoc['mac'], true))
        ) {
            return false;
        }
        
        # Clearing the nonce, so that it won't be used again.
        $this->assoc['nonce'] = null;
        
        if(empty($this->assoc['private'])) {
            # Commiting changes to the association.
            $this->setAssoc($this->assoc['handle'], $this->assoc);
        } else {
            # Private associations shouldn't be used again, se we can as well delete them.
            $this->delAssoc($this->assoc['handle']);
        }
        
        # Nothing has failed, so the verification was a success.
        return true;
    }
    
    /**
     * Performs association with an RP.
     */
    protected function associate()
    {
        # Rejecting no-encryption without TLS.
        if(empty($_SERVER['HTTPS']) && $this->data['openid_session_type'] == 'no-encryption') {
            $this->directErrorResponse();
        }
        
        # Checking whether we support DH at all.
        if (!$this->dh && substr($this->data['openid_session_type'], 0, 2) == 'DH') {
            $this->redirect($this->response(array(
                'openid.error' => 'DH not supported',
                'openid.error_code' => 'unsupported-type',
                'openid.session_type' => 'no-encryption'
                )));
        }
        
        # Creating the association
        $this->assoc = array();
        $this->assoc['hash'] = $this->data['openid_assoc_type'] == 'HMAC-SHA256' ? 'sha256' : 'sha1';
        $this->assoc['handle'] = $this->assoc_handle();
        
        # Getting the shared secret
        if($this->data['openid_session_type'] == 'no-encryption') {
            $this->assoc['mac'] = base64_encode($this->shared_secret($this->assoc['hash']));
        } else {
            $this->dh();
        }
        
        # Preparing the direct response...
        $response = array(
            'ns'           => $this->ns,
            'assoc_handle' => $this->assoc['handle'],
            'assoc_type'   => $this->data['openid_assoc_type'],
            'session_type' => $this->data['openid_session_type'],
            'expires_in'   => $this->assoc_lifetime
            );
        
        if(isset($this->assoc['dh_server_public'])) {
            $response['dh_server_public'] = $this->assoc['dh_server_public'];
            $response['enc_mac_key'] = $this->assoc['mac'];
        } else {
            $response['mac_key'] = $this->assoc['mac'];
        }
        
        # ...and sending it.
        echo $this->keyValueForm($response);
        die();
    }
    
    /**
     * Creates a private association.
     */
    protected function generateAssociation()
    {
        $this->assoc = array();
        # We use sha1 by default.
        $this->assoc['hash']   = 'sha1';
        $this->assoc['mac']    = $this->shared_secret('sha1');
        $this->assoc['handle'] = $this->assoc_handle();
    }
    
    /**
     * Encrypts the MAC key using DH key exchange.
     */
    protected function dh()
    {
        if(empty($this->data['openid_dh_modulus'])) {
            $this->data['openid_dh_modulus'] = $this->default_modulus;
        }
        
        if(empty($this->data['openid_dh_gen'])) {
            $this->data['openid_dh_gen'] = $this->default_gen;
        }
        
        if(empty($this->data['openid_dh_consumer_public'])) {
            $this->directErrorResponse();
        }
        
        $modulus = $this->b64dec($this->data['openid_dh_modulus']);
        $gen = $this->b64dec($this->data['openid_dh_gen']);
        $consumerKey = $this->b64dec($this->data['openid_dh_consumer_public']);
        
        $privateKey = $this->keygen(strlen($modulus));
        $publicKey = $this->powmod($gen, $privateKey, $modulus);
        $ss = $this->powmod($consumerKey, $privateKey, $modulus);
        
        $mac = $this->x_or(hash($this->assoc['hash'], $ss, true), $this->shared_secret($this->assoc['hash']));
        $this->assoc['dh_server_public'] = $this->decb64($publicKey);
        $this->assoc['mac'] = base64_encode($mac);
    }
    
    /**
     * XORs two strings.
     * @param String $a
     * @param String $b
     * @return String $a ^ $b
     */
    protected function x_or($a, $b)
    {
        $length = strlen($a);
        for($i = 0; $i < $length; $i++) {
            $a[$i] = $a[$i] ^ $b[$i];
        }
        
        return $a;
    }
    
    /**
     * Prepares an indirect response url.
     * @param array $params Parameters to be sent.
     */
    protected function response($params)
    {
        $params += array('openid.ns' => $this->ns);
        return $this->data['openid_return_to']
             . (strpos($this->data['openid_return_to'],'?') ? '&' : '?')
             . http_build_query($params, '', '&');
    }
    
    /**
     * Outputs a direct error.
     */
    protected function errorResponse()
    {
        if(!empty($this->data['openid_return_to'])) {
            $response = array(
            'openid.mode'  => 'error',
            'openid.error' => 'Invalid request'
            );
            $this->redirect($this->response($response));
        } else {
            header('HTTP/1.1 400 Bad Request');
            $response = array(
                'ns' => $this->ns,
                'error' => 'Invalid request'
                );
            echo $this->keyValueForm($response);
        }
        die();
    }
    
    /**
     * Sends an positive assertion.
     * @param String $identity the OP-Local Identifier that is being authenticated.
     * @param Array $attributes User attributes to be sent.
     */
    protected function positiveResponse($identity, $attributes)
    {
        # We generate a private association if there is none established.
        if(!$this->assoc) {
            $this->generateAssociation();
            $this->assoc['private'] = true;
        }
        
        # We set openid.identity (and openid.claimed_id if necessary) to our $identity
        if($this->data['openid_identity'] == $this->data['openid_claimed_id'] || $this->select_id) {
            $this->data['openid_claimed_id'] = $identity;
        }
        $this->data['openid_identity'] = $identity;
        
        # Preparing fields to be signed
        $params = array(
            'op_endpoint'    => $this->serverLocation,
            'claimed_id'     => $this->data['openid_claimed_id'],
            'identity'       => $this->data['openid_identity'],
            'return_to'      => $this->data['openid_return_to'],
            'realm'          => $this->data['openid_realm'],
            'response_nonce' => gmdate("Y-m-d\TH:i:s\Z"),
            'assoc_handle'   => $this->assoc['handle'],
            );
        
        $params += $this->responseAttributes($attributes);
        
        # Has the RP used an invalid association handle?
        if (isset($this->data['openid_assoc_handle'])
            && $this->data['openid_assoc_handle'] != $this->assoc['handle']
        ) {
            $params['invalidate_handle'] = $this->data['openid_assoc_handle'];
        }
        
        # Signing the $params
        $sig = hash_hmac($this->assoc['hash'], $this->keyValueForm($params), $this->assoc['mac'], true);
        $req = array(
            'openid.mode'   => 'id_res',
            'openid.signed' => implode(',', array_keys($params)),
            'openid.sig'    => base64_encode($sig),
            );
        
        # Saving the nonce and commiting the association.
        $this->assoc['nonce'] = $params['response_nonce'];
        $this->setAssoc($this->assoc['handle'], $this->assoc);
        
        # Preparing and sending the response itself
        foreach($params as $name => $value) {
            $req['openid.' . $name] = $value;
        }
        
        $this->redirect($this->response($req));
    }
    
    /**
     * Prepares an array of attributes to send
     */
    protected function responseAttributes($attributes)
    {
        if(!$attributes) return array();
        
        $ns = 'http://axschema.org/';

        $response = array();
        if(isset($this->data['ax'])) {
            $response['ns.ax'] = 'http://openid.net/srv/ax/1.0';
            foreach($attributes as $name => $value) {
                $alias = strtr($name, '/', '_');
                $response['ax.type.' . $alias] = $ns . $name;
                $response['ax.value.' . $alias] = $value;
            }
            return $response;
        }
        
        foreach($attributes as $name => $value) {
            if(!isset($this->ax_to_sreg[$name])) {
                continue;
            }
            
            $response['sreg.' . $this->ax_to_sreg[$name]] = $value;
        }
        return $response;
    }
    
    /**
     * Encodes fields in key-value form.
     * @param Array $params Fields to be encoded.
     * @return String $params in key-value form.
     */
    protected function keyValueForm($params)
    {
        $str = '';
        foreach($params as $name => $value) {
            $str .= "$name:$value\n";
        }
        
        return $str;
    }
    
    /**
     * Responds with an information that the user has canceled authentication.
     */
    protected function cancel()
    {
        $this->redirect($this->response(array('openid.mode' => 'cancel')));
    }
    
    /**
     * Converts base64 encoded number to it's decimal representation.
     * @param String $str base64 encoded number.
     * @return String Decimal representation of that number.
     */
    protected function b64dec($str)
    {
        $bytes = unpack('C*', base64_decode($str));
        $n = 0;
        foreach($bytes as $byte) {
            $n = $this->add($this->mul($n, 256), $byte);
        }
        
        return $n;
    }
    
    /**
     * Complements b64dec.
     */
    protected function decb64($num)
    {
        $bytes = array();
        while($num) {
            array_unshift($bytes, $this->mod($num, 256));
            $num = $this->div($num, 256);
        }
        
        if($bytes && $bytes[0] > 127) {
            array_unshift($bytes,0);
        }
        
        array_unshift($bytes, 'C*');
        
        return base64_encode(call_user_func_array('pack', $bytes));
    }
    
    function __call($name, $args)
    {
        switch($name) {
        case 'add':
        case 'mul':
        case 'pow':
        case 'mod':
        case 'div':
        case 'powmod':
            if(function_exists('gmp_strval')) {
                return gmp_strval(call_user_func_array($this->$name, $args));
            }
            return call_user_func_array($this->$name, $args);
        default:
            throw new BadMethodCallException();
        }
    }
}
