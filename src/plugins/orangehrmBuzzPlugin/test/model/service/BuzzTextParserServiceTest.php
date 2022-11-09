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
 */

/**
 * @group buzz
 */
class BuzzTextParserServiceTest extends PHPUnit\Framework\TestCase{
    protected function setUp(): void {

        $this->buzzTextParserService = new BuzzTextParserService();
        
    }
    
    /**
     * testing image url is correct
     */
//    public function testImageUrl(){
//        $url='http://www.jpl.nasa.gov/spaceimages/images/mediumsize/PIA17011_ip.jpg';
//        $result=BuzzTextParserService::isImage($url);
//        $this->assertEquals(true,$result);
//    }
    
    /**
     * testing image url is correct
     */
    public function testTextWithUrl(){
        $url='http://www.jpl.nasa.gov/spaceimages/images/mediumsize/';
        $result=BuzzTextParserService::parseText($url);
        $trueResult= "<a href=\"{$url}\" target=\"_blank\">{$url}</a> ";
        
        $this->assertEquals($result,$trueResult);
    }
    
    /**
     * testing image url is correct
     */
    public function testTextWithEmoticals(){
        $url=':)';
        $result=BuzzTextParserService::parseText($url);
        $trueResult= '<img class = "smileys" src="' .
                    plugin_web_path('orangehrmBuzzPlugin', 'images/emoticons/') . 'smile.png' .
                    '" height="40" width="40" />';
        
        $this->assertEquals($result,$trueResult);
    }
    
     /**
     * testing image url is correct
     */
//    public function testTextWithImageUrl(){
//        $url='http://www.jpl.nasa.gov/spaceimages/images/mediumsize/PIA17011_ip.jpg';
//        $result=BuzzTextParserService::parseText($url);
//        $trueResult= "<img src=\"".$url."\" height=\"100px\" >";
//        
//        $this->assertEquals($result,$trueResult);
//    }
    
    public function testImageUrlErro(){
        $url='http://www.jpl.nasa.gov/spaceimages/images/mediumsize/PIA17011_';
        $result=BuzzTextParserService::isImage($url);
        $this->assertEquals(false,$result);
    }
}
