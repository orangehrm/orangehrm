<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ohrmListSummaryHelper
 *
 * @author madhusanka
 */
class ohrmListSummaryHelper {
    private static $collection = array();
    private static $count = array();
    
    public static function collectValue($value, $function) {
        if (!isset(self::$collection[$function])) {
            self::$collection[$function] = 0;
            self::$count[$function] = 0;
        }
        
        self::$collection[$function] += $value;
        self::$count[$function]++;
    }
    
    public static function getAggregateValue($function) {
        $aggregateValue = null;
        
        switch($function) {
            case 'SUM':
                $aggregateValue = self::$collection['SUM'];
                break;
            default:
                // TODO: Warn. Unsupported function
                break;
        }
        
        return $aggregateValue;
    }
}

