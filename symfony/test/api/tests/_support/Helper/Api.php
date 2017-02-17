<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function getDefaultToken(){

        $this->getModule('REST')->sendPOST(
            'oauth/issueToken',
            ['client_id' => 'wstest', 'client_secret' => 'wstest', 'grant_type' => 'client_credentials']);
        $response = $this->getModule('REST')->response;
        return json_decode($response)->access_token;
    }
}
