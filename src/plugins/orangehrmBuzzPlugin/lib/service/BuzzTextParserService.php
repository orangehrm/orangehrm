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
 * Description of BuzzTextParserService
 *
 * @author aruna
 */
class BuzzTextParserService {

    protected static $smiles = array(
        '8:)' => 'cool.png',
        '8-)' => 'cool2.png',
        ':_(' => 'cry.png',
        'xD' => 'devil.png',
        '3:)' => 'devil.png',
        'x(' => 'angry.png',
        ":'(" => 'cry.png',
        ':*' => 'kiss.png',
        ':))' => 'laugh.png',
        ':D' => 'laugh.png',
        ':-D' => 'laugh.png',
        ':x' => 'love.png',
        '(:|' => 'sleepy.png',
        ':)' => 'smile.png',
        ':-)' => 'smile.png',
        ':(' => 'sad.png',
        ':-(' => 'sad.png',
        ':O' => 'surprise.png',
        ':-O' => 'surprise.png',
        'o.O' => 'surprise2.png',
        'o)' => 'blesed.png',
        ':P' => 'tongue.png',
        ':-P' => 'tongue.png',
        ';)' => 'wink.png',
        ';-)' => 'wink.png',
        '^_^' => 'star.png',
        '(y)' => 'y.png',
        '-_-' => '3.png',
        ';/' => 'sad3.png',
        ':v' => 'v.png'
    );

    /**
     * Add emoticons according to the symbols used in the text passed.
     * 
     * @param $text text to be parsed
     * @return $text emoticons inserted text
     */
    public static function parseText($text) {
        if (strpos($text, "'") !== FALSE) {
            echo 'true';
            die;
        }

        $reg_exUrl = "#(www\.|https?://)?[a-z0-9]+\.[a-z0-9]{2,4}\S*#i";

        if (preg_match_all($reg_exUrl, $text, $url,PREG_PATTERN_ORDER)) {
            $machedUrl = array_flip ( $url[0]);

            foreach( $machedUrl as $key => $aurl){
                $machedUrl[$key] = "<a href=\"{$key}\" target=\"_blank\">{$key}</a> ";
            }

            $text = strtr($text,$machedUrl);

        }
        
        foreach (self::$smiles as $key => $img) {

            $emoticonPath = '<img class = "smileys" src="' .
                    plugin_web_path('orangehrmBuzzPlugin', 'images/emoticons/') . $img .
                    '" height="40" width="40" />';
            $text = str_replace($key, $emoticonPath, $text);
        }

        return str_replace("\n", "<br />", $text);
    }

    public static function isImage($url) {
        $params = array('http' => array(
                'method' => 'HEAD'));
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return false;  // Problem with url
        }
        $meta = stream_get_meta_data($fp);
// @codeCoverageIgnoreStart
        if ($meta === false) {
            fclose($fp);
            return false;  // Problem reading data from url
        }
        // @codeCoverageIgnoreEnd
        $wrapper_data = $meta["wrapper_data"];
        if (is_array($wrapper_data)) {
            foreach (array_keys($wrapper_data) as $hh) {
                if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") { // strlen("Content-Type: image") == 19 
                    fclose($fp);
                    return true;
                }
            }
        }
        fclose($fp);
        return false;
    }

}
