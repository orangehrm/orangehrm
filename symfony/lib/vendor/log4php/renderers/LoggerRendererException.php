<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
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
 * Exception renderer
 *
 * @package log4php
 * @subpackage renderers
 * @since 2.1
 */
class LoggerRendererException implements LoggerRendererObject {

	public function render($o) {
		$strRep  = 'Throwable('.get_class($o).'): '.$o->getMessage().' in '.$o->getFile().' on line '.$o->getLine();
		$strRep .= PHP_EOL.$o->getTraceAsString();
		
		if (method_exists($o, 'getPrevious') && $o->getPrevious() !== null) {
			$strRep .= PHP_EOL.'Caused by: '.$this->render($o->getPrevious());
		}
		
		return $strRep;		
	}
}
?>