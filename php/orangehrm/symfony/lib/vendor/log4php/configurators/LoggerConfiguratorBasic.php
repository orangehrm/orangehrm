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
 * Use this class to quickly configure the package.
 *
 * An example how to use this configurator:
 * 
 * {@example ../../examples/php/configurator_basic.php 19}
 *
 * @version $Revision: 883108 $
 * @package log4php
 * @subpackage configurators
 */
class LoggerConfiguratorBasic implements LoggerConfigurator {

	/**
	 * Add a {@link LoggerAppenderConsole} that uses 
	 * the {@link LoggerLayoutTTCC} to the root category.
	 * 
	 * @param string $url not used here
	 */
	public function configure(LoggerHierarchy $hierarchy, $url = null) {
		$root = $hierarchy->getRootLogger();
		$appender = new LoggerAppenderConsole('A1');
		$appender->setLayout( new LoggerLayoutTTCC() );
		$appender->activateOptions();
		$root->addAppender($appender);
	}
}
