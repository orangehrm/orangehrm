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
 * The default Renderer renders objects by type casting.
 * 
 * Example:
 * 
 * {@example ../../examples/php/renderer_default.php 19}<br>
 * {@example ../../examples/resources/renderer_default.properties 18}<br>
 * <pre>
 * DEBUG - Now comes the current MyClass object:
 * DEBUG - Person::__set_state(array(
 *  'firstName' => 'John',
 *  'lastName' => 'Doe',
 * ))
 * </pre>
 *
 * @package log4php
 * @subpackage renderers
 * @since 0.3
 */
class LoggerRendererDefault implements LoggerRendererObject {

	/**
	 * Render objects by type casting
	 *
	 * @param mixed $o the object to render
	 * @return string
	 */
	public function render($o) {
		return var_export($o, true);
	}
}
