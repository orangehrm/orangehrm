<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconNotificationService
 *
 * @author chathura
 * @group beacon
 */
class BeaconNotificationServiceTest extends PHPUnit_Framework_TestCase {
    protected $beaconNotificationService;
    protected $fixture;
    
    public function setUp() {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconNotificationService.yml';
        $this->beaconNotificationService = new BeaconNotificationService();
        
    }
    
    public function testSanitizeNotificationSection() {
        $notifications = TestDataService::loadObjectList('BeaconNotification', $this->fixture, 'BeaconNotification');
        $notificationXML = new SimpleXMLElement($notifications[0]->getDefinition());
        $sanitizedBody = $this->beaconNotificationService->sanitizeNotificationSection($notificationXML->content->body."");
        $this->assertTrue(substr_count($sanitizedBody, '<script>')==0);
    }
}
