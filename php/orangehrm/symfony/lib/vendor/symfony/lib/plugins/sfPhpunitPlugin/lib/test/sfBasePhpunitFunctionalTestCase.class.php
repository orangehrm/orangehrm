<?php

/**
 * sfBasePhpunitFunctionalTestCase is the super class for all functional
 * tests using PHPUnit.
 * The "getBrowser" method provides the current functional test/browser
 * instance of symfony and you can do anything with it you are used from
 * the normal lime based tests.
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfBasePhpunitFunctionalTestCase extends sfBasePhpunitTestCase
{
    /**
     * The sfTestFunctional instance
     *
     * @var sfTestFunctional
     */
    protected $browser;


    /**
     * Inject your own functional testers
     *
     * @see sfTestFunctionalBase::setTesters()
     *
     * @return array
     *          'request'  => 'sfTesterRequest',
     *          'response' => 'sfTesterResponse',
     *          'user'     => 'sfTesterUser',
     */
    protected function getFunctionalTesters()
    {
        return array();
    }


    /**
     * SetUp method for PHPUnit
     */
    protected function setUp()
    {
        // Initialize SCRIPT_NAME for correct work $this->generateUrl()
        // when $_SERVER is empty before first request
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        // Create context once for current app
        $this->getContext();

        // Remove current app cache
        sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

        // Init test browser
        $this->browser = new sfTestFunctional(new sfPhpunitTestBrowser, new sfPhpunitTest($this), $this->getFunctionalTesters());

        $this->_start();
    }


    /**
     * Returns the sfTestFunctional instance
     *
     * @return sfTestFunctional
     */
    public function getBrowser()
    {
        return $this->browser;
    }


    /**
     * Generate URL from route name
     *
     * Example:
     *   $this->generateUrl('homepage');
     *      -> "/"
     *   $this->generateUrl('article_edit', $articleObject);
     *      -> "/article/1/edit"
     *   $this->generateUrl('custom_route', $arrRouteParams);
     *
     * @see sfPatternRouting::generate()
     *
     * @param  string      $name   - Route name from routing.yml
     * @param  array|Model $params - Routing params
     * @return string
     */
    protected function generateUrl($name, $params = array())
    {
        return $this->browser->getContext()->getRouting()->generate($name, $params);
    }


    /**
     * Run test
     *
     * Catch exception and decorate it with last request data
     */
    protected function runTest()
    {
        try {
            parent::runTest();
        } catch (Exception $e) {
            throw $this->_decorateExeption($e);
        }
    }


    /**
     * Decorate exception with last request data
     *
     * @param  Exception $e
     * @return Exception
     */
    private function _decorateExeption(Exception $e)
    {
        if (!$this->browser->getLastRequestUri()) {
            return $e;
        }

        $className = get_class($e);

        if ($e instanceof PHPUnit_Framework_ExpectationFailedException) {
            if (!$e->getCustomMessage()) {
                return new $className(
                    $this->_makeRequestErrorMessage($e->getDescription(), $e) . PHP_EOL,
                    $e->getComparisonFailure()
                );
            } else {
                return new $className(
                    $e->getDescription(),
                    $e->getComparisonFailure(),
                    $this->_makeRequestErrorMessage($e->getCustomMessage(), $e) . PHP_EOL
                );
            }

        } else if ($e instanceof PHPUnit_Framework_Error) {
            return new $className(
                $this->_makeRequestErrorMessage($e->getMessage(), $e),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTrace()
            );
        }

        return $e;
    }


    /**
     * Make request error message with last request uri and params
     *
     * @param  string - User defined message
     * @return strung
     */
    private function _makeRequestErrorMessage($mess, Exception $e)
    {
        $result = $mess  . PHP_EOL . PHP_EOL
                . 'Request: ' . $this->browser->getLastRequestUri() . PHP_EOL
                . 'Request params: ' . PHP_EOL . $this->browser->getLastRequestParams();

        if ($_FILES) {
            $result .= PHP_EOL . PHP_EOL
                    .  'Submited FILES: '  . PHP_EOL
                    .  var_export($_FILES, true);
        }

        $result .= PHP_EOL . PHP_EOL
                .  'Trace: ' . PHP_EOL
                .  PHPUnit_Util_Filter::getFilteredStacktrace($e, false);

        return $result;
    }

}
