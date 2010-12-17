<?php
sfPhpunitPluginConfiguration::initSeleniumExtension();
    
    /**
     * Delegate method calls to the driver.
     *
     * @param  string $command
     * @param  array  $arguments
     * @return mixed
     * @method unknown  addLocationStrategy()
     * @method unknown  addLocationStrategyAndWait()
     * @method unknown  addScript()
     * @method unknown  addScriptAndWait()
     * @method unknown  addSelection()
     * @method unknown  addSelectionAndWait()
     * @method unknown  allowNativeXpath()
     * @method unknown  allowNativeXpathAndWait()
     * @method unknown  altKeyDown()
     * @method unknown  altKeyDownAndWait()
     * @method unknown  altKeyUp()
     * @method unknown  altKeyUpAndWait()
     * @method unknown  answerOnNextPrompt()
     * @method unknown  assignId()
     * @method unknown  assignIdAndWait()
     * @method unknown  attachFile()
     * @method unknown  break()
     * @method unknown  captureEntirePageScreenshot()
     * @method unknown  captureEntirePageScreenshotAndWait()
     * @method unknown  captureEntirePageScreenshotToStringAndWait()
     * @method unknown  captureScreenshotAndWait()
     * @method unknown  captureScreenshotToStringAndWait()
     * @method unknown  check()
     * @method unknown  checkAndWait()
     * @method unknown  chooseCancelOnNextConfirmation()
     * @method unknown  chooseCancelOnNextConfirmationAndWait()
     * @method unknown  chooseOkOnNextConfirmation()
     * @method unknown  chooseOkOnNextConfirmationAndWait()
     * @method unknown  click()
     * @method unknown  clickAndWait()
     * @method unknown  clickAt()
     * @method unknown  clickAtAndWait()
     * @method unknown  close()
     * @method unknown  contextMenu()
     * @method unknown  contextMenuAndWait()
     * @method unknown  contextMenuAt()
     * @method unknown  contextMenuAtAndWait()
     * @method unknown  controlKeyDown()
     * @method unknown  controlKeyDownAndWait()
     * @method unknown  controlKeyUp()
     * @method unknown  controlKeyUpAndWait()
     * @method unknown  createCookie()
     * @method unknown  createCookieAndWait()
     * @method unknown  deleteAllVisibleCookies()
     * @method unknown  deleteAllVisibleCookiesAndWait()
     * @method unknown  deleteCookie()
     * @method unknown  deleteCookieAndWait()
     * @method unknown  deselectPopUp()
     * @method unknown  deselectPopUpAndWait()
     * @method unknown  doubleClick()
     * @method unknown  doubleClickAndWait()
     * @method unknown  doubleClickAt()
     * @method unknown  doubleClickAtAndWait()
     * @method unknown  dragAndDrop()
     * @method unknown  dragAndDropAndWait()
     * @method unknown  dragAndDropToObject()
     * @method unknown  dragAndDropToObjectAndWait()
     * @method unknown  dragDrop()
     * @method unknown  dragDropAndWait()
     * @method unknown  echo()
     * @method unknown  fireEvent()
     * @method unknown  fireEventAndWait()
     * @method unknown  focus()
     * @method unknown  focusAndWait()
     * @method string   getAlert()
     * @method array    getAllButtons()
     * @method array    getAllFields()
     * @method array    getAllLinks()
     * @method array    getAllWindowIds()
     * @method array    getAllWindowNames()
     * @method array    getAllWindowTitles()
     * @method string   getAttribute()
     * @method array    getAttributeFromAllWindows()
     * @method string   getBodyText()
     * @method string   getConfirmation()
     * @method string   getCookie()
     * @method string   getCookieByName()
     * @method integer  getCursorPosition()
     * @method integer  getElementHeight()
     * @method integer  getElementIndex()
     * @method integer  getElementPositionLeft()
     * @method integer  getElementPositionTop()
     * @method integer  getElementWidth()
     * @method string   getEval()
     * @method string   getExpression()
     * @method string   getHtmlSource()
     * @method string   getLocation()
     * @method string   getLogMessages()
     * @method integer  getMouseSpeed()
     * @method string   getPrompt()
     * @method array    getSelectOptions()
     * @method string   getSelectedId()
     * @method array    getSelectedIds()
     * @method string   getSelectedIndex()
     * @method array    getSelectedIndexes()
     * @method string   getSelectedLabel()
     * @method array    getSelectedLabels()
     * @method string   getSelectedValue()
     * @method array    getSelectedValues()
     * @method unknown  getSpeed()
     * @method unknown  getSpeedAndWait()
     * @method string   getTable()
     * @method string   getText()
     * @method string   getTitle()
     * @method string   getValue()
     * @method boolean  getWhetherThisFrameMatchFrameExpression()
     * @method boolean  getWhetherThisWindowMatchWindowExpression()
     * @method integer  getXpathCount()
     * @method unknown  goBack()
     * @method unknown  goBackAndWait()
     * @method unknown  highlight()
     * @method unknown  highlightAndWait()
     * @method unknown  ignoreAttributesWithoutValue()
     * @method unknown  ignoreAttributesWithoutValueAndWait()
     * @method boolean  isAlertPresent()
     * @method boolean  isChecked()
     * @method boolean  isConfirmationPresent()
     * @method boolean  isCookiePresent()
     * @method boolean  isEditable()
     * @method boolean  isElementPresent()
     * @method boolean  isOrdered()
     * @method boolean  isPromptPresent()
     * @method boolean  isSomethingSelected()
     * @method boolean  isTextPresent()
     * @method boolean  isVisible()
     * @method unknown  keyDown()
     * @method unknown  keyDownAndWait()
     * @method unknown  keyDownNative()
     * @method unknown  keyDownNativeAndWait()
     * @method unknown  keyPress()
     * @method unknown  keyPressAndWait()
     * @method unknown  keyPressNative()
     * @method unknown  keyPressNativeAndWait()
     * @method unknown  keyUp()
     * @method unknown  keyUpAndWait()
     * @method unknown  keyUpNative()
     * @method unknown  keyUpNativeAndWait()
     * @method unknown  metaKeyDown()
     * @method unknown  metaKeyDownAndWait()
     * @method unknown  metaKeyUp()
     * @method unknown  metaKeyUpAndWait()
     * @method unknown  mouseDown()
     * @method unknown  mouseDownAndWait()
     * @method unknown  mouseDownAt()
     * @method unknown  mouseDownAtAndWait()
     * @method unknown  mouseMove()
     * @method unknown  mouseMoveAndWait()
     * @method unknown  mouseMoveAt()
     * @method unknown  mouseMoveAtAndWait()
     * @method unknown  mouseOut()
     * @method unknown  mouseOutAndWait()
     * @method unknown  mouseOver()
     * @method unknown  mouseOverAndWait()
     * @method unknown  mouseUp()
     * @method unknown  mouseUpAndWait()
     * @method unknown  mouseUpAt()
     * @method unknown  mouseUpAtAndWait()
     * @method unknown  mouseUpRight()
     * @method unknown  mouseUpRightAndWait()
     * @method unknown  mouseUpRightAt()
     * @method unknown  mouseUpRightAtAndWait()
     * @method unknown  open()
     * @method unknown  openWindow()
     * @method unknown  openWindowAndWait()
     * @method unknown  pause()
     * @method unknown  refresh()
     * @method unknown  refreshAndWait()
     * @method unknown  removeAllSelections()
     * @method unknown  removeAllSelectionsAndWait()
     * @method unknown  removeScript()
     * @method unknown  removeScriptAndWait()
     * @method unknown  removeSelection()
     * @method unknown  removeSelectionAndWait()
     * @method unknown  retrieveLastRemoteControlLogs()
     * @method unknown  rollup()
     * @method unknown  rollupAndWait()
     * @method unknown  runScript()
     * @method unknown  runScriptAndWait()
     * @method unknown  select()
     * @method unknown  selectAndWait()
     * @method unknown  selectFrame()
     * @method unknown  selectPopUp()
     * @method unknown  selectPopUpAndWait()
     * @method unknown  selectWindow()
     * @method unknown  setBrowserLogLevel()
     * @method unknown  setBrowserLogLevelAndWait()
     * @method unknown  setContext()
     * @method unknown  setCursorPosition()
     * @method unknown  setCursorPositionAndWait()
     * @method unknown  setMouseSpeed()
     * @method unknown  setMouseSpeedAndWait()
     * @method unknown  setSpeed()
     * @method unknown  setSpeedAndWait()
     * @method unknown  shiftKeyDown()
     * @method unknown  shiftKeyDownAndWait()
     * @method unknown  shiftKeyUp()
     * @method unknown  shiftKeyUpAndWait()
     * @method unknown  shutDownSeleniumServer()
     * @method unknown  store()
     * @method unknown  submit()
     * @method unknown  submitAndWait()
     * @method unknown  type()
     * @method unknown  typeAndWait()
     * @method unknown  typeKeys()
     * @method unknown  typeKeysAndWait()
     * @method unknown  uncheck()
     * @method unknown  uncheckAndWait()
     * @method unknown  useXpathLibrary()
     * @method unknown  useXpathLibraryAndWait()
     * @method unknown  waitForCondition()
     * @method unknown  waitForPageToLoad()
     * @method unknown  waitForPopUp()
     * @method unknown  windowFocus()
     * @method unknown  windowMaximize()
     */
abstract class sfBasePhpunitSeleniumTestCase 
  extends PHPUnit_Extensions_SeleniumTestCase
{
  /**
   *
   * @var sfPhpunitFixture
   */
  protected $_fixture;
  
  /**
   * 
   * @var array
   */
  protected $_backupSfConfig = array();

  /**
   * Dev hook for custom "setUp" stuff
   * Overwrite it in your test class, if you have to execute stuff before a test is called.
   */
  protected function _start()
  {
  }

  /**
   * Dev hook for custom "tearDown" stuff
   * Overwrite it in your test class, if you have to execute stuff after a test is called.
   */
  protected function _end()
  {
  }

  /**
   * Please do not touch this method and use _start directly!
   */
  public function setUp()
  {
    $this->_backupSfConfig();
    $this->_startCollectCoverage();
    $this->_startFillDriverWithDefaultOptions();
    $this->_start();
  }

  /**
   * Please do not touch this method and use _end directly!
   */
  public function tearDown()
  {
    $this->_restoreSfConfig();
    $this->_end();
  }
  
  protected function _backupSfConfig()
  {
    $this->_backupSfConfig = sfConfig::getAll();
  }
  
  protected function _restoreSfConfig()
  {
    sfConfig::clear();
    sfConfig::add($this->_backupSfConfig);
  }

  public function getPackageFixtureDir()
  {
    $reflection = new ReflectionClass($this);
    $path = dirname($reflection->getFileName());
    
    $replace = 'fixtures'.DIRECTORY_SEPARATOR;
    $search = 'phpunit' . DIRECTORY_SEPARATOR;
    
    return substr_replace($path, $replace, strpos($path, $search) + 8, 0);
  }
  
  public function getOwnFixtureDir()
  {
    $reflection = new ReflectionClass($this);
    $path = str_replace('.php', '', $reflection->getFileName());
    
    $replace = 'fixtures'.DIRECTORY_SEPARATOR;
    $search = 'phpunit' . DIRECTORY_SEPARATOR;
    
    return substr_replace($path, $replace, strpos($path, $search) + 8, 0);
  }
  
  public function getCommonFixtureDir()
  {
    $path = array(sfConfig::get('sf_test_dir'), 'phpunit', 'fixtures', 'common');
    
    return implode(DIRECTORY_SEPARATOR,$path);
  }
  
  public function getSymfonyFixtureDir()
  {
    $path = array(sfConfig::get('sf_data_dir'), 'fixtures');
    
    return implode(DIRECTORY_SEPARATOR, $path);
  }

  /**
   * 
   * 
   * @return sfPhpunitFixture|mixed
   */
  public function fixture($id = null)
  {
    if (!$this->_fixture) $this->_initFixture();

    return is_null($id) ? $this->_fixture : $this->_fixture->get($id);
  }
  
  protected function _initFixture(array $options = array())
  {
    $this->_fixture = new sfPhpunitSeleniumRemouteFixtureDecorator(
      sfPhpunitFixture::build($this, $options));
  }
  
  protected function _startCollectCoverage()
  {
    if (!$selenium_options = sfConfig::get('sf_phpunit_selenium', false)) {
      throw new Exception('For some reasone the config was not found. This would never happen');
    }
   
    if (!$selenium_options['collect_coverage']) return;
      
    $host = $selenium_options['driver']['browser_url'];
      
    $this->coverageScriptUrl = "$host/sfPhpunitPlugin/phpunit_coverage.php";
    $this->collectCodeCoverageInformation = true;
  }

  protected function _startFillDriverWithDefaultOptions()
  {
    $selenium_options = sfConfig::get('sf_phpunit_selenium', array());
    
    foreach ($selenium_options['driver'] as $option => $value) {
      if (false === $value) continue;

      $setOption = explode('_', $option);
      $setOption = 'set'.implode(array_map('ucfirst', $setOption));

      // @TODO validate options
      if (!method_exists($this->drivers[0], $setOption)) {
        // remove this Exception for option setSpeed(300);
        //        throw new Exception('Invalid selenium option `'.$option.'` provided. Please check the app.yml config file');
      }

      $this->drivers[0]->$setOption($value);
    }
  }
}