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

class createSupportedLanguageFileTask extends sfBaseTask
{
    /**
     * Configure Process Notification
     */
    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'orangehrm'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('testLanguageFile', null, sfCommandOption::PARAMETER_REQUIRED, 'The absolute path to the Test Language File', null),
            new sfCommandOption('actualLanguageFile', null, sfCommandOption::PARAMETER_REQUIRED, 'The absolute path to the Actual Language File', null),
            new sfCommandOption('outputLanguageFile', null, sfCommandOption::PARAMETER_REQUIRED, 'The absolute path to the Output Language File', null),
            new sfCommandOption('sourceLanguage', null, sfCommandOption::PARAMETER_REQUIRED, 'Language translating from', 'en_US'),
            new sfCommandOption('targetLanguage', null, sfCommandOption::PARAMETER_REQUIRED, 'Language translating to', null),
        ));

        $this->namespace = 'orangehrm';
        $this->name = 'createSupportedLanguageFile';
        $this->briefDescription = 'create Supported Language File';
        $this->detailedDescription = "";
    }

    /**
     * Executes the current task.
     *
     * @param array $arguments An array of arguments
     * @param array $options An array of options
     *
     * @return integer 0 if everything went fine, or an error code
     */
    protected function execute($arguments = array(), $options = array())
    {
        $testFile = $options['testLanguageFile'];
        $actualFile = $options['actualLanguageFile'];
        $outputFile = $options['outputLanguageFile'];
        $sourceLanguage = $options['sourceLanguage'];
        $targetLanguage = $options['targetLanguage'];

        if (is_null($testFile) || !file_exists($testFile)) {
            echo "Test language file is missing\n";
            exit(0);
        }
        if (is_null($actualFile) || !file_exists($actualFile)) {
            echo "Actual language file is missing\n";
            exit(0);
        }
        if (is_null($outputFile)) {
            echo "Output file is missing\n";
            exit(0);
        }
        if (!is_dir(dirname($outputFile))) {
            echo "Path to Output files is invalid\n";
            exit(0);
        }

        $output = $this->getTranslatedXml($testFile, $actualFile, $sourceLanguage, $targetLanguage);

        $doc = new DOMDocument();
        $doc->formatOutput = TRUE;
        $doc->loadXML($output->asXML());
        $outputXml = $doc->saveXML();

        $file = fopen($outputFile,"w");
        fwrite($file, $outputXml);
        fclose($file);
    }

    /**
     * Combine two XMLs
     * @param $test
     * @param $actual
     * @return SimpleXMLElement
     */
    private function getTranslatedXml($test, $actual, $sourceLanguage, $targetLanguage) {
        $testXml=simplexml_load_file($test);
        $actualXml=simplexml_load_file($actual);

        $a1 = json_decode(json_encode($testXml),true);
        $a2 = json_decode(json_encode($actualXml),true);

        $testArray = array_map('unserialize',
            array_unique(
                array_map('serialize',
                    $a1['file']['body']['trans-unit']
                )
            )
        );

        $actualArray = array_map('unserialize',
            array_unique(
                array_map('serialize',
                    $a2['file']['body']['trans-unit']
                )
            )
        );

        $translatedArray = $this->getTranslatedArray($testArray, $actualArray);
        $date = new DateTime();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE xliff PUBLIC "-//XLIFF//DTD XLIFF//EN" "http://www.oasis-open.org/committees/xliff/documents/xliff.dtd"><xliff version="1.0"></xliff>');
        $xml->addChild('header');
        $file = $xml->addChild('file');
        $file->addAttribute('source-language', $sourceLanguage);
        $file->addAttribute('target-language', $targetLanguage);
        $file->addAttribute('datatype', "plaintext");
        $file->addAttribute('original', "messages");
        $file->addAttribute('date', $date->format('Y-m-d-H-i-s'));
        $file->addAttribute('product-name', "messages");

        $body = $file->addChild('body');

        $count = 0;
        foreach ($translatedArray as $item){
            $node = $body->addChild('trans-unit');
            $node->addAttribute('id', ++$count);
            $node->addChild('source', htmlspecialchars($item['source']));
            $node->addChild('target', htmlspecialchars($item['target']));

            if (array_key_exists('note', $item)) {
                $node->addChild('note', htmlspecialchars($item['note']));
            }
        }

        return $xml;
    }

    /**
     * Compares tow arrays $testArray and $actualArray
     * @param $testArray
     * @param $actualArray
     * @return array
     */
    private function getTranslatedArray($testArray, $actualArray) {
        $translatedArray = array();
        $nonTranslatedArray = array();
        foreach ($testArray as $testArrayKey => $testArrayNode) {
            $matched = false;
            foreach ($actualArray as $actualArrayKey => $actualArrayNode) {
                if ($testArrayNode['source'] == $actualArrayNode['source']) {
                    $matched = true;
                    $testArrayNode['target'] = $actualArrayNode['target'];

                    if (array_key_exists('note', $actualArrayNode)) {
                        $testArrayNode['note'] = $actualArrayNode['note'];
                    }
                    array_push($translatedArray, $testArrayNode);
                    break;
                }
            }
            if (!$matched) {
                $testArrayNode['target'] = "";
                    array_push($nonTranslatedArray, $testArrayNode);
            }
        }
        return array_merge($translatedArray, $nonTranslatedArray);
    }
}