<?php
/**
 * Only used in Leave module. Please do not use for anything new.
 * Will be removed in the future.
 * 
 * @deprecated
 */
class ParameterService {

    public static function getParameter($key, $default = null) {
        
        $key = self::_adjustKey($key);
        $value = OrangeConfig::getInstance()->getAppConfValue($key);
        
        if (empty($value)) {
            $value = $default;
        }
        
        return $value;
    }

    public static function setParameter($key, $value) {
        $key = self::_adjustKey($key);
        OrangeConfig::getInstance()->setAppConfValue($key, $value);
    }
    
    /**
     * Adjusts key for specific keys related to leave period, by adding a 
     * 'leave.' in front. This is a temporary fix for live, and will no longer
     * be needed after moving to 2.6.9.
     * 
     * @param string $key Key
     * @return string Adjusted Key 
     */
    private static function _adjustKey($key) {
        
        if ($key == 'nonLeapYearLeavePeriodStartDate' || 
                $key == 'isLeavePeriodStartOnFeb29th' || 
                $key == 'leavePeriodStartDate') {
            
            $key = 'leave.' . $key;
        }

        return $key;
    }

}
