<?php

class ohrmWebServiceClient {
    
    protected $authorizeUrl;
    protected $tokenUrl;
    protected $baseUrl;
    protected $clientId;
    protected $secret;
    protected $token;
    protected $port;


    /**
     * Construct OHRM Web Service Client
     * @param string $baseUrl Base URL of server with web service:
     *        eg: http://localhost, http://192.168.1.23/enterprise/symfony/web etc.
     * @param  int $port Port to connect to. Leave as null to connect to default port based
     *        on whether request is http or https.
     */
    public function __construct($baseUrl, $port = null) {
        $baseUrl = rtrim($baseUrl, '/');
        $this->baseUrl = $baseUrl;
        $this->authorizeUrl = "$baseUrl/oauth/authorize";
        $this->tokenUrl= "$baseUrl/oauth/issueToken";
        $this->port = $port;
    }

    public function setCredentials($clientId, $secret) {
        $this->clientId = $clientId;
        $this->secret = $secret;        
    }

    /**
     * Authenticate and get token
     * @return string Authentication token
     */
    public function authenticate() {

        if (empty($this->clientId) || empty($this->secret)) {
            throw new ohrmWebServiceClientException("clientId and secret should be specified by using setCredentials method");
        }

        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->secret,
            'grant_type' => "client_credentials"
        );

        $curlOptions = array();

        if (!empty($this->port)) {
            $curlOptions[CURLOPT_PORT] = $this->port;
        }

        $curl = new Curl();           
        $response = $curl->request($this->tokenUrl, $params, "POST", $curlOptions);


        $json = json_decode($response['response'], true);

        if (empty($json['access_token'])) {
            throw new ohrmWebServiceClientException("access_token not returned:" . print_r($response, true));
        }

        $this->token = $json['access_token'];

        return $this->token;
    }

    /**
     * Call web service method
     * 
     * @param  string $methodUrl Web service method URL eg: /getEmployee/empNumber/5
     * @param  string $httpVerb  HTTP Verb. Eg: GET, POST, PUT, DELETE
     * @return Array  Webservice return values as an array
     */
    public function callMethod($methodUrl, $httpVerb, array $parameters = array()) {
        $token = $this->getToken();
        $curlOptions = array(
            'headers' => array("Authorization: Bearer {$token}"));

        if (!empty($this->port)) {
            $curlOptions[CURLOPT_PORT] = $this->port;
        }

        $endpoint = $this->baseUrl . '/api/' . ltrim($methodUrl, '/');

        $curl = new Curl();    
        $response = $curl->request($endpoint, json_encode($parameters), $httpVerb, $curlOptions);

        return $response;
    }

    
    /**
     * Get authentication token
     *
     * @return string Authentication token
     */
    public function getToken() {
        if (empty($this->token)) {
            $this->authenticate();
        }

        return $this->token;
    }
    
    /**
     * Set Authentication token
     *
     * @param string $newtoken Authentication token
     */
    public function setToken($token) {
        $this->token = $token;
    
        return $this;
    }
}

class ohrmWebServiceClientException extends Exception {

}

class Curl
{
    private $options;

    public function __construct($options = array())
    {
        $this->options = array_merge(array(
            'debug'      => true,
            'user_agent' => 'Sample Web Service client',
            'timeout'    => 20,
            'curlopts'   => null,
            'verifyssl'  => false,
        ), $options);
    }

    /**
    * Send a request to the server, receive a response
    *
    * @param  string   $apiPath       Request API path
    * @param  array    $parameters    Parameters
    * @param  string   $httpMethod    HTTP method to use
    * @param  array    $options       curl options
    * @return string   HTTP response
    */
    public function request($url, $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        $options = array_merge($this->options, $options);

        $curlOptions = array();
        $headers = array();

        if ('POST' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_POST  => true,
            );
        }
        elseif ('PUT' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_POST  => true, // This is so cURL doesn't strip CURLOPT_POSTFIELDS
                CURLOPT_CUSTOMREQUEST => 'PUT',
            );
        }
        elseif ('DELETE' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_CUSTOMREQUEST => 'DELETE',
            );
        }

        if (!empty($parameters))
        {
            if('GET' === $httpMethod)
            {
                if (is_array($parameters)) {
                    $queryString = utf8_encode($this->buildQuery($parameters));
                    $url .= '?' . $queryString;
                }
            } elseif ('POST' === $httpMethod) {
                if (is_array($parameters)) {
                    $curlOptions += array(
                        CURLOPT_POSTFIELDS  => http_build_query($parameters),
                    );                    
                } else {
                    $curlOptions += array(
                        CURLOPT_POSTFIELDS  => $parameters,
                    );
                    $headers[] = 'Content-Type: application/json';
                    
                }
            } else {
                $curlOptions += array(
                    CURLOPT_POSTFIELDS  => $parameters
                );
                $headers[] = 'Content-Type: application/json';
            }
        } else {
            $headers[] = 'Content-Length: 0';
        }

        $this->debug('send '.$httpMethod.' request: '.$url);
    if(isset($options['headers']) && is_array($options['headers'])){
        $headers = array_merge($headers, $options['headers']);
    }
        $curlOptions += array(
            CURLOPT_URL             => $url,
            CURLOPT_PORT            => $options['http_port'],
            CURLOPT_USERAGENT       => $options['user_agent'],
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $options['timeout'],
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_SSL_VERIFYPEER  => $options['verifyssl'],
        );

        if (ini_get('open_basedir') == '' && ini_get('safe_mode') != 'On') {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = true;
        }

        if (is_array($options['curlopts'])) {
            $curlOptions += $options['curlopts'];
        }

        if (isset($options['proxy'])) {
            $curlOptions[CURLOPT_PROXY] = $options['proxy'];
        }

        if (isset($options['debug'])) {
            $curlOptions[CURLOPT_VERBOSE] = $options['debug'];
        }

        $response = $this->doCurlCall($curlOptions);

        return $response;
    }

    /**
     * Get a JSON response and transform it to a PHP array
     *
     * @return  array   the response
     */
    protected function decodeResponse($response)
    {
        // "false" means a failed curl request
        if (false === $response['response']) {
            $this->debug(print_r($response, true));
            return false;
        }
        return parent::decodeResponse($response);
    }
   
    protected function doCurlCall(array $curlOptions)
    {
        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return compact('response', 'headers', 'errorNumber', 'errorMessage');
    }

    protected function buildQuery($parameters)
    {
        return http_build_query($parameters, '', '&');
    }

    protected function debug($message)
    {
        if($this->options['debug'])
        {
            print $message."\n";
        }
    }
}