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
 * Most of the work of the {@link LoggerPatternLayout} class 
 * is delegated to the {@link LoggerPatternParser} class.
 * 
 * <p>It is this class that parses conversion patterns and creates
 * a chained list of {@link LoggerPatternConverter} converters.</p>
 * 
 * @version $Revision: 822445 $ 
 * @package log4php
 * @subpackage helpers
 *
 * @since 0.3
 */
class LoggerPatternParser {

	const ESCAPE_CHAR = '%';
	
	const LITERAL_STATE = 0;
	const CONVERTER_STATE = 1;
	const MINUS_STATE = 2;
	const DOT_STATE = 3;
	const MIN_STATE = 4;
	const MAX_STATE = 5;
	
	const FULL_LOCATION_CONVERTER = 1000;
	const METHOD_LOCATION_CONVERTER = 1001;
	const CLASS_LOCATION_CONVERTER = 1002;
	const FILE_LOCATION_CONVERTER = 1003;
	const LINE_LOCATION_CONVERTER = 1004;
	
	const RELATIVE_TIME_CONVERTER = 2000;
	const THREAD_CONVERTER = 2001;
	const LEVEL_CONVERTER = 2002;
	const NDC_CONVERTER = 2003;
	const MESSAGE_CONVERTER = 2004;
	
	const DATE_FORMAT_ISO8601 = 'Y-m-d H:i:s,u'; 
	const DATE_FORMAT_ABSOLUTE = 'H:i:s';
	const DATE_FORMAT_DATE = 'd M Y H:i:s,u';

	private $state;
	private $currentLiteral;
	private $patternLength;
	private $i;
	
	/**
	 * @var LoggerPatternConverter
	 */
	private $head = null;
	 
	/**
	 * @var LoggerPatternConverter
	 */
	private $tail = null;
	
	/**
	 * @var LoggerFormattingInfo
	 */
	private $formattingInfo;
	
	/**
	 * @var string pattern to parse
	 */
	private $pattern;

	/**
	 * Constructor 
	 *
	 * @param string $pattern
	 */
	public function __construct($pattern) {
		$this->pattern = $pattern;
		$this->patternLength =	strlen($pattern);
		$this->formattingInfo = new LoggerFormattingInfo();
		$this->state = self::LITERAL_STATE;
	}

	/**
	 * @param LoggerPatternConverter $pc
	 */
	public function addToList($pc) {
		if($this->head == null) {
			$this->head = $pc;
			$this->tail = $this->head;
		} else {
			$this->tail->next = $pc;
			$this->tail = $this->tail->next;
		}
	}

	/**
	 * @return string
	 */
	public function extractOption() {
		if(($this->i < $this->patternLength) and ($this->pattern{$this->i} == '{')) {
			$end = strpos($this->pattern, '}' , $this->i);
			if($end !== false) {
				$r = substr($this->pattern, ($this->i + 1), ($end - $this->i - 1));
				$this->i= $end + 1;
				return $r;
			}
		}
		return null;
	}

	/**
	 * The option is expected to be in decimal and positive. In case of
	 * error, zero is returned.	 
	 */
	public function extractPrecisionOption() {
		$opt = $this->extractOption();
		$r = 0;
		if($opt !== null) {
			if(is_numeric($opt)) {
				$r = (int)$opt;
				if($r <= 0) {
					$r = 0;
				}
			}
		}
		return $r;
	}

	
	/** Parser.
	 * 
	 * @return LoggerPatternConverter Returns $this->head.
	 */
	public function parse() {
		$c = '';
		$this->i = 0;
		$this->currentLiteral = '';
		while($this->i < $this->patternLength) {
			$c = $this->pattern{$this->i++};

			switch($this->state) {
				case self::LITERAL_STATE:
					// In literal state, the last char is always a literal.
					if($this->i == $this->patternLength) {
						$this->currentLiteral .= $c;
						continue;
					}
					if($c == self::ESCAPE_CHAR) {
						// peek at the next char.
						switch($this->pattern{$this->i}) {
							case self::ESCAPE_CHAR:
								$this->currentLiteral .= $c;
								$this->i++; // move pointer
								break;
							case 'n':
								$this->currentLiteral .= PHP_EOL;
								$this->i++; // move pointer
								break;
							default:
								if(strlen($this->currentLiteral) != 0) {
									$this->addToList(new LoggerLiteralPatternConverter($this->currentLiteral));
								}
								$this->currentLiteral = $c;
								$this->state = self::CONVERTER_STATE;
								$this->formattingInfo->reset();
						}
					} else {
						$this->currentLiteral .= $c;
					}
					break;
				case self::CONVERTER_STATE:
						$this->currentLiteral .= $c;
						switch($c) {
						case '-':
							$this->formattingInfo->leftAlign = true;
							break;
						case '.':
							$this->state = self::DOT_STATE;
								break;
						default:
							if(ord($c) >= ord('0') and ord($c) <= ord('9')) {
								$this->formattingInfo->min = ord($c) - ord('0');
								$this->state = self::MIN_STATE;
							} else {
								$this->finalizeConverter($c);
							}
						} // switch
					break;
				case self::MIN_STATE:
					$this->currentLiteral .= $c;
					if(ord($c) >= ord('0') and ord($c) <= ord('9')) {
						$this->formattingInfo->min = ($this->formattingInfo->min * 10) + (ord($c) - ord('0'));
					} else if ($c == '.') {
						$this->state = self::DOT_STATE;
					} else {
						$this->finalizeConverter($c);
					}
					break;
				case self::DOT_STATE:
					$this->currentLiteral .= $c;
					if(ord($c) >= ord('0') and ord($c) <= ord('9')) {
						$this->formattingInfo->max = ord($c) - ord('0');
						$this->state = self::MAX_STATE;
					} else {
						$this->state = self::LITERAL_STATE;
					}
					break;
				case self::MAX_STATE:
					$this->currentLiteral .= $c;
					if(ord($c) >= ord('0') and ord($c) <= ord('9')) {
						$this->formattingInfo->max = ($this->formattingInfo->max * 10) + (ord($c) - ord('0'));
					} else {
						$this->finalizeConverter($c);
						$this->state = self::LITERAL_STATE;
					}
					break;
			} // switch
		} // while
		if(strlen($this->currentLiteral) != 0) {
			$this->addToList(new LoggerLiteralPatternConverter($this->currentLiteral));
		}
		return $this->head;
	}

	public function finalizeConverter($c) {
		$pc = null;
		switch($c) {
			case 'c':
				$pc = new LoggerCategoryPatternConverter($this->formattingInfo, $this->extractPrecisionOption());
				$this->currentLiteral = '';
				break;
			case 'C':
				$pc = new LoggerClassNamePatternConverter($this->formattingInfo, $this->extractPrecisionOption());
				$this->currentLiteral = '';
				break;
			case 'd':
				$dateFormatStr = self::DATE_FORMAT_ISO8601; // ISO8601_DATE_FORMAT;
				$dOpt = $this->extractOption();

				if($dOpt !== null)
					$dateFormatStr = $dOpt;
					
				if($dateFormatStr == 'ISO8601') {
					$df = self::DATE_FORMAT_ISO8601;
				} else if($dateFormatStr == 'ABSOLUTE') {
					$df = self::DATE_FORMAT_ABSOLUTE;
				} else if($dateFormatStr == 'DATE') {
					$df = self::DATE_FORMAT_DATE;
				} else {
					$df = $dateFormatStr;
					if($df == null) {
						$df = self::DATE_FORMAT_ISO8601;
					}
				}
				$pc = new LoggerDatePatternConverter($this->formattingInfo, $df);
				$this->currentLiteral = '';
				break;
			case 'F':
				$pc = new LoggerLocationPatternConverter($this->formattingInfo, self::FILE_LOCATION_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'l':
				$pc = new LoggerLocationPatternConverter($this->formattingInfo, self::FULL_LOCATION_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'L':
				$pc = new LoggerLocationPatternConverter($this->formattingInfo, self::LINE_LOCATION_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'm':
				$pc = new LoggerBasicPatternConverter($this->formattingInfo, self::MESSAGE_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'M':
				$pc = new LoggerLocationPatternConverter($this->formattingInfo, self::METHOD_LOCATION_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'p':
				$pc = new LoggerBasicPatternConverter($this->formattingInfo, self::LEVEL_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'r':
				$pc = new LoggerBasicPatternConverter($this->formattingInfo, self::RELATIVE_TIME_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 't':
				$pc = new LoggerBasicPatternConverter($this->formattingInfo, self::THREAD_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'u':
				if($this->i < $this->patternLength) {
					$cNext = $this->pattern{$this->i};
					if(ord($cNext) >= ord('0') and ord($cNext) <= ord('9')) {
						$pc = new LoggerUserFieldPatternConverter($this->formattingInfo, (string)(ord($cNext) - ord('0')));
						$this->currentLiteral = '';
						$this->i++;
					}
				}
				break;
			case 'x':
				$pc = new LoggerBasicPatternConverter($this->formattingInfo, self::NDC_CONVERTER);
				$this->currentLiteral = '';
				break;
			case 'X':
				$xOpt = $this->extractOption();
				$pc = new LoggerMDCPatternConverter($this->formattingInfo, $xOpt);
				$this->currentLiteral = '';
				break;
			default:
				$pc = new LoggerLiteralPatternConverter($this->currentLiteral);
				$this->currentLiteral = '';
		}
		$this->addConverter($pc);
	}

	public function addConverter($pc) {
		$this->currentLiteral = '';
		// Add the pattern converter to the list.
		$this->addToList($pc);
		// Next pattern is assumed to be a literal.
		$this->state = self::LITERAL_STATE;
		// Reset formatting info
		$this->formattingInfo->reset();
	}
}

