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

class LabelGenerator {

    protected $currentDir = __DIR__ ;
    protected $originalAngularLabels;
    protected $originalNonAngularLabels;
    protected $angularLabels;
    protected $nonAngularLabels;
    protected $climate;
    protected $diffForAngularLabels;
    protected $diffForNonAngularLabels;
    protected $fileContent = "";

    const ORIGINAL_ANGULAR_LABELS = "original_angular_labels.json";
    const ORIGINAL_NON_ANGULAR_LABELS = "original_symfony_labels.json";
    const ANGULAR_LABELS = "angular_labels.json";
    const NON_ANGULAR_LABELS = "symfony_labels.json";

    public function __construct() {
        $this->climate = new \League\CLImate\CLImate();
        $this->originalNonAngularLabels = $this->getDataAsArrayFromFile(self::ORIGINAL_NON_ANGULAR_LABELS);
        $this->nonAngularLabels = $this->getDataAsArrayFromFile(self::NON_ANGULAR_LABELS);
    }

    public function getDifferences() {
        echo "Deleting Temporary Files\n";
        $this->diffForNonAngularLabels = $this->getDifferenceForNonAngularLabels();

        $labelChanges = array();

        $labelChanges['NewSymfonyLabels'] = $this->generateFileContent(
            $this->nonAngularLabels,
            $this->diffForNonAngularLabels['newLabels']
        );

        $labelChanges['MissingSymfonyLabels'] = $this->generateFileContent(
            $this->originalNonAngularLabels,
            $this->diffForNonAngularLabels['missingLabels']
        );

        $this->fileContent = json_encode($labelChanges, JSON_PRETTY_PRINT);
        $result = file_put_contents($this->currentDir."/diff.json", $this->fileContent);
    }

    private function generateFileContent(array $dataArray, array $diffArray) {
        $changes = $this->filterFromOriginalArray(
            $dataArray,
            $diffArray
        );
        return $changes;
    }

    private function filterFromOriginalArray(array $originalDataSet, array $filterArray) {
        $filteredArray = array();
        foreach ($filterArray as $key => $value) {
            $callback = function($ar) use ($key) {
                return ($ar['State_Name'] == $key);
            };
            $filteredItem = array_filter($originalDataSet, $callback);
            $filteredArray = array_merge($filteredArray, $filteredItem);
        }
        return $filteredArray;
    }

    private function getDataAsArrayFromFile($fileName) {
        $file = file_get_contents($this->currentDir."/".$fileName);
        if(!$file) {
            $this->climate->to('error')->red("Cannot read $fileName !!!");
            exit();
        }
        return json_decode($file, true);
    }

    private function getLabelsAsKeyValuePairs(array $dataArray) {
        $resultArray = array();
        foreach ($dataArray as $item) {
            $resultArray[$item['State_Name']] = $item['Label'];
        }
        return $resultArray;
    }

    private function getDifferenceForAngularLabels() {
        return $this->getNewAndMissingLabelData($this->originalAngularLabels, $this->angularLabels);
    }

    private function getDifferenceForNonAngularLabels() {
        return $this->getNewAndMissingLabelData($this->originalNonAngularLabels, $this->nonAngularLabels);
    }

    private function getNewAndMissingLabelData(array $originalDataArray, array $currentDataArray) {
        $originalLabels = $this->getLabelsAsKeyValuePairs($originalDataArray);
        $currentLabels = $this->getLabelsAsKeyValuePairs($currentDataArray);
        $missingLabels = array_diff_assoc($originalLabels, $currentLabels);
        $newLabels = array_diff_assoc($currentLabels, $originalLabels);
        return array(
            'missingLabels' => $missingLabels,
            'newLabels' => $newLabels
        );
    }
}
