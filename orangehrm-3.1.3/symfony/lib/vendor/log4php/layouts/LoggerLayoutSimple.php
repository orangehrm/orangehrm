<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * A simple layout.
 *
 * Returns the log statement in a format consisting of the
 * <b>level</b>, followed by " - " and then the <b>message</b>. 
 *
 * For example the following php and properties files
 * 
 * {@example ../../examples/php/layout_simple.php 19}<br>
 * 
 * {@example ../../examples/resources/layout_simple.properties 18}<br>
 *
 * would result in:
 * 
 * <samp>INFO - Hello World!</samp>
 *
 * @version $Revision: 883108 $
 * @package log4php
 * @subpackage layouts
 */  
class LoggerLayoutSimple extends LoggerLayout {
    /**
     * Constructor
     */
    public function __construct() {
    }

    /**
     * Returns the log statement in a format consisting of the
     * <b>level</b>, followed by " - " and then the
     * <b>message</b>. For example, 
     * <samp> INFO - "A message" </samp>
     *
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event) {
        $level = $event->getLevel();
        return $level->toString() . ' - ' . $event->getRenderedMessage(). PHP_EOL;
    }
}
