<?php
/**
 * sfPhpunitTestBrowser extends sfBrowser
 *
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class sfPhpunitTestBrowser extends sfBrowser
{
    private $_lastRequest       = '';
    private $_lastRequestParams = '';


    /**
     * Constructor
     */
    public function __construct($hostname = null, $remote = null, $options = array())
    {
        $this->cleanUp();
        parent::initialize($hostname, $remote, $options);
    }


    /**
     * Reset browser temp
     *
     * @return void
     */
    public function cleanUp()
    {
        // Drop all HTTP_* keys
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                unset($_SERVER[$key]);
            }
        }
        // Reset internal $_SERVER vars
        $this->vars = array();

        $this->_lastRequest       = '';
        $this->_lastRequestParams = '';
    }


    /**
     * Remembers last request data for debuging
     * Throws current exception
     */
    public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
    {
        $this->cleanUp();

        parent::call($uri, $method, $parameters, $changeStack);
        $this->_lastRequest       = $this->getRequest()->getUri();
        $this->_lastRequestParams = var_export($this->getRequest()->getParameterHolder()->getAll(), true);

        if ($e = $this->getCurrentException()) {
            // Throw exception if not forward404
            if (!$e instanceof sfError404Exception) {
                throw $e;
            }
        }
    }


    /**
     * Get last request URI
     *
     * @return string
     */
    public function getLastRequestUri()
    {
        return $this->_lastRequest;
    }


    /**
     * Get last request params as string (var_export)
     *
     * @return string
     */
    public function getLastRequestParams()
    {
        return $this->_lastRequestParams;
    }


    /**
     * Upload files
     *
     * When you can not use click() method and submit form with file
     * or want to modify uploaded file params.
     *
     * Example:
     *  $_FILES = array(
     *      array(
     *          'name'     => 'filename.jpg',
     *          'type'     => 'image/jpeg'
     *          'tmp_name' => '/tmp/filename.jpg'
     *          'error'    => 0,
     *          'size'     => 1000,
     *      );
     *  );
     *
     *  $this->browser
     *       ->uploadFiles($_FILES);
     *       ->post('/upload');
     *
     * @param  array $files - same as $_FILES array
     * @return $this
     */
    public function uploadFiles(array $files)
    {
        $this->files = $files;
        return $this;
    }
}
