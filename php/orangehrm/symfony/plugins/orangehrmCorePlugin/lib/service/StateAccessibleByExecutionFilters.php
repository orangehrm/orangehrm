<?php

interface StateAccessibleByExecutionFilters {
        const EMPTY_STATE = 'empty';
        const SUCCESS = 'success';
        const PARTIAL_SUCCESS = 'partial-success';
        const FAILURE = 'failure';
        
        public static function getState();
}
