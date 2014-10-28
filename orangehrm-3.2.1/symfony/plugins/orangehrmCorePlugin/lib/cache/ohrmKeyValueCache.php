<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

/**
 * Class that caches key/value pairs in a file under cache directory
 */
class ohrmKeyValueCache {
    protected $properties;
    protected $name;
    protected $cacheDir;
    protected $callBack;
    
    public function __construct($name, Closure $getAllValuesCallback) {
        $this->name = $name;
        if (!is_callable($getAllValuesCallback)) {
            throw new InvalidArgumentException('second parameter to constructor should be a function');
        } else {
            $this->callBack = $getAllValuesCallback;
        }
        $this->initCache();
    }
    
    public function get($key, $default = null) {
        return isset($this->properties[$key]) ? $this->properties[$key] : $default;
    }
    
    public function reloadCache() {
        $currentUmask = umask(0000);
        $tmpFile = tempnam($this->cacheDir, basename($this->cacheFile));

        if (!$fp = @fopen($tmpFile, 'wb')) {
           throw new sfCacheException(sprintf('Unable to write cache file "%s".', $tmpFile));
        }
        $this->properties = $this->getData();
        fwrite($fp, $this->properties);
        fclose($fp);

        // Hack from Agavi (http://trac.agavi.org/changeset/3979)
        // With php < 5.2.6 on win32, renaming to an already existing file doesn't work, but copy does,
        // so we simply assume that when rename() fails that we are on win32 and try to use copy()
        if (!@rename($tmpFile, $this->cacheFile)) {
            if (copy($tmpFile, $this->cacheFile)) {
                unlink($tmpFile);
            }
        }

        chmod($this->cacheFile, 0666);
        umask($currentUmask);
    }
    
    protected function getData() {
        $data = "<?php \n";
        $callBack = $this->callBack;
        $properties = $callBack();        
        
        $data .= 'return ' . var_export($properties, true) . ";\n";
        
        return $data;
    }
    
    protected function initCache() {
        
        $this->cacheDir = sfConfig::get('sf_cache_dir') . '/ohrmKeyValueCache';
        $this->cacheFile = $this->cacheDir . '/' . $this->name;
        
        // create cache dir if needed
        if (!is_dir($this->cacheDir)) {
            $currentUmask = umask(0000);
            @mkdir($this->cacheDir, 0777, true);
            umask($currentUmask);
        }
        
        if (!file_exists($this->cacheFile)) {
            $this->reloadCache($this->cacheFile);
            
        }
        
        if (is_readable($this->cacheFile)) {            
            $this->properties = (include $this->cacheFile);
        } else {
            throw new sfCacheException(sprintf('Unable to read cache file "%s"', $this->cacheFile));
        }
    }
    

    
}