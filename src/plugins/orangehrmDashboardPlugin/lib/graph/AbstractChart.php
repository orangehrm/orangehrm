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
 * Description of AbstractChart
 */
abstract class AbstractChart {

    protected $width = '97%';
    protected $height = '300';
    protected $styles = array(
        'margin-top' => '30px',
        'margin-left' => '10px',
    );
    protected $data = array();
    protected $dataFormatter;
    protected $metaDataObject;
    protected $type;
    protected $properties = array();
    protected $javascriptPath;
    protected $showEmptyGraph = false;
    protected $chartNumber;
    
    // Maintains number of chart objects created.
    // Used to give each chart a unique $chartNumber
    protected static $chartCount = 0;

    function __construct() {
        self::$chartCount++;
        $this->chartNumber = self::$chartCount;
    }

    /**
     *
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     *
     * @param int $width 
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     *
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     *
     * @param int $height 
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     *
     * @param array $data 
     */
    public function setData(array $data) {
        $this->data = ($this->dataFormatter instanceof GraphDataFormatter) ? $this->dataFormatter->format($data) : $data;
    }

    /**
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     *
     * @param string $type 
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     *
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     *
     * @param string $key
     * @return string 
     */
    public function getProperty($key) {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        } else {
            throw new Exception('Tried to access an undefined property: ' . $key);
        }
    }

    /**
     *
     * @param array $properties 
     */
    public function setPropertes(array $properties) {
        $this->properties = $properties;
    }

    /**
     *
     * @param string $key
     * @param string $value 
     */
    public function setProperty($key, $value) {
        if (array_key_exists($key, $this->properties)) {
            $this->properties[$key] = $value;
        } else {
            throw new Exception('Tried to access an undefined property: ' . $key);
        }
    }

    /**
     *
     * @return array
     */
    public function getStyles() {
        return $this->styles;
    }

    /**
     *
     * @param array $styles 
     */
    public function setStyles(array $styles) {
        $this->styles = $styles;
    }

    /**
     *
     * @param string $stylePropery
     * @return string 
     */
    public function getStyle($stylePropery) {
        if (array_key_exists($stylePropery, $this->styles)) {
            return $this->styles[$stylePropery];
        }
        return null;
    }

    /**
     *
     * @param string $styleProperty
     * @param string $styleValue 
     */
    public function setStyle($styleProperty, $styleValue) {
        $this->styles[$styleProperty] = $styleValue;
    }

    /**
     * @return string
     */
    public function getStyleString() {
        $this->styles['width'] = preg_match('/\%$/', $this->width) ? $this->width : $this->width . 'px';
        $this->styles['height'] = preg_match('/\%$/', $this->height) ? $this->height : $this->height . 'px';

        $stylesString = '';
        foreach ($this->styles as $styleProperty => $styleValue) {
            $stylesString .= "{$styleProperty}: {$styleValue}; ";
        }
        return $stylesString;
    }

    /**
     *
     * @return ReportVisualizerMetaData
     */
    public function getMetaDataObject() {
        return $this->metaDataObject;
    }

    public function setMetaDataObject(GraphMetaData $metaDataObject) {
        $this->metaDataObject = $metaDataObject;
    }

    /**
     * @return ohrmGraphDataFormatter
     */
    public function getDataFormatter() {
        return $this->dataFormatter;
    }

    /**
     *
     * @param ohrmGraphDataFormatter $dataFormatter 
     */
    public function setDataFormatter(GraphDataFormatter $dataFormatter) {
        $this->dataFormatter = $dataFormatter;
    }

    /**
     *
     * @return bool
     */
    public function hasData() {
        return !empty($this->data);
    }

    /**
     *
     * @param bool $showEmptyGraph
     * @return bool
     */
    public function showEmptyGraph($showEmptyGraph = null) {
        if (is_null($showEmptyGraph)) {
            return $this->showEmptyGraph;
        } else {
            $this->showEmptyGraph = (bool) $showEmptyGraph;
        }
    }

    public function getChartFunction() {

        $function = "";

        $type = $this->getType();

        $words = explode('-', $type);

        foreach ($words as $word) {
            $function .= ucfirst($word);
        }

        return $function;
    }

    public function getChartNumber() {
        return $this->chartNumber;
    }

    public function setChartNumber($chartNumber) {
        $this->chartNumber = $chartNumber;
    }

}
