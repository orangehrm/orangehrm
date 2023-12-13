<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\OpenidAuthentication\Service;

use Error;
use Exception;
use Jumbojett\OpenIDConnectClientException;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\OpenidAuthentication\Dao\AuthProviderDao;
use OrangeHRM\OpenidAuthentication\OpenID\OpenIDConnectClient;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;

class SocialMediaAuthenticationService
{
    private AuthProviderDao $authProviderDao;
    use SocialMediaAuthenticationServiceTrait;

    public const SCOPE = 'email';
    public const REDIRECT_URL = 'https://734d-2402-d000-a500-40f9-f1e8-1109-5f81-bcf4.ngrok-free.app/openidauth/openIdCredentials/redirect';
    private $wellKnown = false;
    private array $authParams = [];
    private array $scopes = [];
    private array $responseTypes = [];
    private array $wellKnownConfigParameters = [];
    private bool $verifyHost = true;
    private bool $verifyPeer = true;
    protected int $timeOut = 60;
    private int $responseCode;
    private array $providerConfig = [];
    protected int $encType = PHP_QUERY_RFC1738;

    /**
     * @return AuthProviderDao
     */
    public function getAuthProviderDao(): AuthProviderDao
    {
        return $this->authProviderDao ??= new AuthProviderDao();
    }

    /**
     * @param AuthProviderExtraDetails $provider
     * @param string $scope
     * @param string $redirectUrl
     *
     * @return OpenIDConnectClient
     */
    public function initiateAuthentication(AuthProviderExtraDetails $provider, string $scope, string $redirectUrl): OpenIDConnectClient
    {
        $oidcClient = new OpenIDConnectClient(
            $provider->getOpenIdProvider()->getProviderUrl(),
            $provider->getClientId(),
            $provider->getClientSecret()
        );

        $oidcClient->addScope([$scope]);
        $oidcClient->setRedirectURL($redirectUrl);

        return $oidcClient;
    }

    /**
     * @param OpenIDConnectClient $oidcClient
     * @throws OpenIDConnectClientException
     */
    public function handleCallback(OpenIDConnectClient $oidcClient): string
    {
        return $this->requestAuthorization();
//        ob_start();
//
//        $oidcClient->authenticate();
//        $output = ob_get_contents();
//        dump($output);
//        dump('here1');
//        ob_end_flush();
//        try {
//            $isAuthenticated = $oidcClient->authenticate();
//            if ($isAuthenticated) {
//                $credentials = new UserCredential($oidcClient->requestUserInfo('email'));
//                $this->authenticateUser($credentials);
//            }
//        } catch (OpenIDConnectClientException $e) {
//            throw $e;
//        }
    }

    private function authenticateUser(UserCredential $userCredential): void
    {
//        $username = $userCredential->getUsername();
    }

    public function getOIDCClient(): OpenIDConnectClient
    {
        $provider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
            ->getAuthProviderDetailsByProviderId(1);
        return $this->getSocialMediaAuthenticationService()->initiateAuthentication(
            $provider,
            self::SCOPE,
            self::REDIRECT_URL
        );
    }

    private function requestAuthorization(): string
    {
        $auth_endpoint = $this->getProviderConfigValue('authorization_endpoint');
        $response_type = 'code';

        // Generate and store a nonce in the session
        // The nonce is an arbitrary value
        $nonce = $this->setNonce($this->generateRandString());

        // State essentially acts as a session key for OIDC
        $state = $this->setState($this->generateRandString());

        $auth_params = array_merge($this->authParams, [
            'response_type' => $response_type,
            'redirect_uri' => $this->getOIDCClient()->getRedirectURL(),
            'client_id' => $this->getOIDCClient()->getClientID(),
            'nonce' => $nonce,
            'state' => $state,
            'scope' => 'openid'
        ]);

        // If the client has been registered with additional scopes
        if (count($this->scopes) > 0) {
            $auth_params = array_merge($auth_params, ['scope' => implode(' ', array_merge($this->scopes, ['openid']))]);
        }

        // If the client has been registered with additional response types
        if (count($this->responseTypes) > 0) {
            $auth_params = array_merge($auth_params, ['response_type' => implode(' ', $this->responseTypes)]);
        }

        // If the client supports Proof Key for Code Exchange (PKCE)
        $codeChallengeMethod = false;
        if (!empty($codeChallengeMethod) && in_array($codeChallengeMethod, $this->getProviderConfigValue('code_challenge_methods_supported', []), true)) {
            $codeVerifier = bin2hex(random_bytes(64));
            $this->setCodeVerifier($codeVerifier);
            if (!empty($this->pkceAlgs[$codeChallengeMethod])) {
                $codeChallenge = rtrim(strtr(base64_encode(hash($this->pkceAlgs[$codeChallengeMethod], $codeVerifier, true)), '+/', '-_'), '=');
            } else {
                $codeChallenge = $codeVerifier;
            }
            $auth_params = array_merge($auth_params, [
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => $codeChallengeMethod
            ]);
        }

        $auth_endpoint .= (strpos($auth_endpoint, '?') === false ? '?' : '&') . http_build_query($auth_params, '', '&', $this->encType);

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
//        $this->redirect($auth_endpoint);
//        header('Location: ' . $auth_endpoint);

        return $auth_endpoint;
    }

    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    protected function setNonce($nonce) {
        $this->setSessionKey('openid_connect_nonce', $nonce);
        return $nonce;
    }

    protected function setState($state) {
        $this->setSessionKey('openid_connect_state', $state);
        return $state;
    }

    protected function getProviderConfigValue($param, $default = null) {

        // If the configuration value is not available, attempt to fetch it from a well known config endpoint
        // This is also known as auto "discovery"
        if (!isset($this->providerConfig[$param])) {
            $this->providerConfig[$param] = $this->getWellKnownConfigValue($param, $default);
        }

        return $this->providerConfig[$param];
    }

    private function getWellKnownConfigValue($param, $default = null) {

        // If the configuration value is not available, attempt to fetch it from a well known config endpoint
        // This is also known as auto "discovery"
        if(!$this->wellKnown) {
            $well_known_config_url = rtrim($this->getOIDCClient()->getProviderURL(), '/') . '/.well-known/openid-configuration';
            if (count($this->wellKnownConfigParameters) > 0){
                $well_known_config_url .= '?' .  http_build_query($this->wellKnownConfigParameters) ;
            }
            $this->wellKnown = json_decode($this->fetchURL($well_known_config_url));
        }

        $value = false;
        if(isset($this->wellKnown->{$param})){
            $value = $this->wellKnown->{$param};
        }

        if ($value) {
            return $value;
        }

        if (isset($default)) {
            // Uses default value if provided
            return $default;
        }

        throw new OpenIDConnectClientException("The provider {$param} could not be fetched. Make sure your provider has a well known configuration available.");
    }

    protected function fetchURL($url, $post_body = null, $headers = []) {

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Determine whether this is a GET or POST
        if ($post_body !== null) {
            // curl_setopt($ch, CURLOPT_POST, 1);
            // Alows to keep the POST method even after redirect
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);

            // Default content type is form encoded
            $content_type = 'application/x-www-form-urlencoded';

            // Determine if this is a JSON payload and add the appropriate content type
            if (is_object(json_decode($post_body))) {
                $content_type = 'application/json';
            }

            // Add POST-specific headers
            $headers[] = "Content-Type: {$content_type}";

        }

        // If we set some headers include them
        if(count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($this->httpProxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->httpProxy);
        }

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Allows to follow redirect
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        /**
         * Set cert
         * Otherwise ignore SSL peer verification
         */
        if (isset($this->certPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->certPath);
        }

        if($this->verifyHost) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if($this->verifyPeer) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // HTTP Response code from server may be required from subclass
        $info = curl_getinfo($ch);
        $this->responseCode = $info['http_code'];

        if ($output === false) {
            throw new OpenIDConnectClientException('Curl error: (' . curl_errno($ch) . ') ' . curl_error($ch));
        }

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

    /**
     * @throws OpenIDConnectClientException
     */
    protected function generateRandString(): string
    {
        // Error and Exception need to be catched in this order, see https://github.com/paragonie/random_compat/blob/master/README.md
        // random_compat polyfill library should be removed if support for PHP versions < 7 is dropped
        try {
            return \bin2hex(\random_bytes(16));
        } catch (Error|Exception $e) {
            throw new OpenIDConnectClientException('Random token generation failed.');
        }
    }
    protected function setSessionKey($key, $value) {
        $this->startSession();

        $_SESSION[$key] = $value;
    }

    protected function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }

    protected function setCodeVerifier($codeVerifier) {
        $this->setSessionKey('openid_connect_code_verifier', $codeVerifier);
        return $codeVerifier;
    }
}
