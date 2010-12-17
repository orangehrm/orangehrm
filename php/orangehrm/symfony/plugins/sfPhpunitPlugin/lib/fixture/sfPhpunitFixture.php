<?php

/**
 *
 * Base fixture functionallity here.
 *
 * @method sfPhpunitFixture loadOwn(string $file)
 * @method sfPhpunitFixture loadPackage(string $file)
 * @method sfPhpunitFixture loadCommon(string $file)
 * @method sfPhpunitFixture loadSymfony(string $file)
 * @method string getDirOwn()
 * @method string getDirPackage()
 * @method string getDirCommon()
 * @method string getDirSymfony()
 * @method array getFilesOwn(string $file)
 * @method array getFilesPackage(string $file)
 * @method array getFilesCommon(string $file)
 * @method array getFilesSymfony(string $file)
 * @method array getFileOwn(string $file)
 * @method array getFilePackage(string $file)
 * @method array getFileCommon(string $file)
 * @method array getFileSymfony(string $file)
 *
 * @package    sfPhpunitPlugin
 * @subpackage fixture
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class sfPhpunitFixture
{
  const
	  OWN = 'Own',
	  PACKAGE = 'Package',
	  COMMON = 'Common',
	  SYMFONY = 'Symfony';

  /**
   *
   * @var sfBasePhpunitTestCase
   */
  protected $_aggregator;

  /**
   * Example array('pdo_adapter', 'fixture_extension');
   *
   * @var array
   */
  protected $_requiredOptions = array('fixture_ext', 'snapshot-table-prefix');

  protected $_options = array();
  
  /**
   * 
   * @var PDO
   */
  protected $_pdo;
  
  /**
   * array(list of snapshots that have been made)
   */
  protected static $_snapshots = array();

  public function __construct(sfPhpunitFixtureAggregator $aggregator, array $options = array())
  {
    $this->_aggregator = $aggregator;
    $this->_options = array_merge($this->_options, $options);

    foreach ($this->_requiredOptions as $opt) {
      if (!array_key_exists($opt, $this->_options)) {
        throw new Exception('The option `'.$opt.'` is requered');
      }
    }
  }

  /**
   * If you set a concret file do it without extension.
   * For changing default extension please use `fixture_ext` option.
   *
   * @param string $file A fixture file name. File shoud exist in the fixture_type directory.
   * @param string $fixture_type Where to search fixtures.
   * @param bool $clear_before Whether clear database before loading or not.
   *
   * @return sfPhpunitFixture
   */
  abstract public function load($file = null, $fixture_type = self::OWN);

  /**
   * Clean the database
   *
   * @return sfPhpunitFixture
   */
  public function clean()
  {
    $this->pdo()->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    $snapshotPrefix = $this->_getOption('snapshot-table-prefix');
    
    $query = $this->pdo()->prepare('SHOW TABLES');
    $query->execute();
    while($table = $query->fetchColumn()) {
      if (strpos($table, $snapshotPrefix) !== false) continue;
      
      $this->pdo()->exec("TRUNCATE TABLE `{$table}`");
    }

    $this->pdo()->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    $this->_getDataLoader()->cleanObjects();
    
    return $this;
  }

  /**
   * This method should used for returning spesific fixture loader data
   * Like object of dataset in dbunit extension
   *
   * @param string $file A fixture file name. File shoud exist in the fixture_type directory.
   * @param string $fixture_type Where to search fixtures.
   *
   * @return mixed
   */
  abstract public function get($id);
  
  public function doSnapshot($name)
  {   
    $this->_notify('before_do_snapshot', array('name' => $name));
    
    $query = $this->pdo()->query('SHOW TABLES');
    $snapshotPrefix = $this->_getOption('snapshot-table-prefix');
    
    while($table = $query->fetchColumn()) {
      if (strpos($table, $snapshotPrefix) !== false) continue;
      
      $snapshop_table = "{$snapshotPrefix}_{$name}_{$table}";
      $this->pdo()->exec("DROP TABLE IF EXISTS `{$snapshop_table}`");
      $this->pdo()->exec("CREATE TABLE `{$snapshop_table}` SELECT * FROM `{$table}`");
    }

    self::$_snapshots[$name] = $name;
    
    $this->_notify('after_do_snapshot', array('name' => $name));
    
    return $this;
  }
  
  public function cleanSnapshots()
  {
    $query = $this->pdo()->query('SHOW TABLES');
    $snapshotPrefix = $this->_getOption('snapshot-table-prefix');
    
    while($table = $query->fetchColumn()) {
      if (strpos($table, $snapshotPrefix) === false) continue;
      
      $this->pdo()->exec("DROP TABLE IF EXISTS `{$table}`");
    }
    
    $this->_notify('after_clean_snapshots');

    return $this;
  }
  
  /**
   * 
   * @param string $name
   */
  public function loadSnapshot($name)
  {
//     @TODO uncomment it. work a round 
//    if (!in_array($name, self::$_snapshots)) {
//      throw new Exception('The snapshot with name `'.$name.'` was not loaded before loading');
//    }
    
    $this->_notify('before_load_snapshot', array('name' => $name));
    
    $this->pdo()->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    $query = $this->pdo()->query('SHOW TABLES');
    while($table = $query->fetchColumn()) {
      if (strpos($table, '_snapshot_') !== false) continue;
      
      $snapshop_table = "_snapshot_{$name}_{$table}";
      $this->pdo()->exec("TRUNCATE TABLE `{$table}`");
      $this->pdo()->exec("INSERT INTO {$table} SELECT * FROM `{$snapshop_table}`");
    }

    $this->pdo()->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    $this->_notify('after_load_snapshot', array('name' => $name));
    
    
    return $this;
  }
  
  /**
   * @return PDO
   */
  public function pdo()
  {
    if (!$this->_pdo) {
      if (!$this->_pdo = $this->_pdo()) {
        throw new LogicException('The context\database connection was not initialized');
      }
    }
    
    return $this->_pdo;
  }
  
  abstract protected function _pdo();

  /**
   * @see sfPhpunitFixture::getFixtureDir
   *
   * @return string
   */
  public function getDir($fixture_type = self::OWN)
  {
    return $this->_fixPath( self::getFixtureDir($this->_aggregator, $fixture_type) );
  }

  /**
   * return files that matched criteria.
   *
   * Pay attention to that this method add fixture extension so you can search only for the fixture files.
   *
   * @param string patter of the files to search
   * @param string fixture type it's one of this class constant
   *
   * @return array
   */
  public function getFiles($file = null, $fixture_type = self::OWN)
  {
    $pattern = is_null($file) ? '*'.$this->_getExt() : $file.$this->_getExt();

    return sfFinder::type('file')->name($pattern)->maxdepth(0)->in($this->getDir($fixture_type));
  }

  /**
   * return files that matched criteria.
   *
   * @param string the file name. You need to be sure that this file exists.
   * @param string fixture type it's one of this class constant
   *
   * @throws Exception if the file was not found
   *
   * @return string return absolute path to the needed file
   */
  public function getFile($file = null, $fixture_type = self::OWN)
  {
    $items = sfFinder::type('file')
    ->name($file)->maxdepth(0)->in($this->getDir($fixture_type));
    if (empty($items)) {
      throw new Exception('The needed file `'.$file.'` is not exist in the dir `'.$this->getDir($fixture_type).'` or maybe name was misspeld');
    }

    return $this->_fixPath( array_shift($items) );
  }
  
  protected function _notify($method, array $parameters = array())
  {
    if (!sfContext::hasInstance()) {
      return;
    }
    
    $dispatcher = sfContext::getInstance()->getConfiguration()->getEventDispatcher();
    $dispatcher->notify(new sfEvent($this, "sfPhpunit.fixture.{$method}", $parameters));
    
  }

  protected function _fixPath($path)
  {
    return str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $path);
  }

  /**
   * It's a magic method for shirter method calls
   *
   * @param string $name
   * @param array $args
   *
   * @throws Exception if Invalid method name
   * @throws Exception if trying access not exist fixture level
   *
   * @return mixed
   */
  public function __call($name, $args)
  {
    if (false !== strstr($name, 'getDir')) {
      $method = 'getDir';
      $args[0] = $source = str_replace('getDir', '', $name);
    } else if (false !== strstr($name, 'load')) {
      $method = 'load';
      $args[1] = $source = str_replace('load', '', $name);
    } else if (false !== strstr($name, 'getFiles')) {
      $method = 'getFiles';
      $args[1] = $source = str_replace('getFiles', '', $name);
    } else if (false !== strstr($name, 'getFile')) {
      $method = 'getFile';
      $args[1] = $source = str_replace('getFile', '', $name);
    } else {
      throw new Exception('Invalid method call '.__CLASS__.'::'.$name);
    }
     
    $sources = array(self::OWN, self::PACKAGE, self::COMMON, self::SYMFONY);
    if (!in_array($source, $sources)) {
      throw new Exception('Invalid fixture source level. Allowed values `'.implode('`, `', $sources).'` but given `'.$source.'`');
    }
     
    return call_user_func_array(array($this, $method), $args);
  }

  protected function _getOption($name)
  {
    if (!array_key_exists($name, $this->_options)) {
      throw new Exception('The option `'.$name.'` does not exist');
    }
     
    return $this->_options[$name];
  }

  /**
   *
   * @return string
   */
  protected function _getExt()
  {
    return $this->_getOption('fixture_ext');
  }

  /**
   *
   * @param sfPhpunitFixtureAggregator $aggregator It's very important that it should one of the `sfPhpunitFixtureAggregator` childs interface
   * @param array $options
   *
   * @throws Exception if the given aggregator does not implemented the base `sfPhpunitFixtureAggregator` interface
   * @throws Exception if the concret `sfPhpunitFixtureAggregator` child's interface was not defined
   *
   * @return sfPhpunitFixture
   */
  public static function build($aggregator, array $options = array())
  {
    $map = array(
  	  'sfPhpunitFixturePropel' => 'sfPhpunitFixturePropelAggregator',
  	  'sfPhpunitFixtureDoctrine' => 'sfPhpunitFixtureDoctrineAggregator',
  	  'sfPhpunitFixtureDbUnit' => 'sfPhpunitFixtureDbUnitAggregator',
      'sfPhpunitFixtureFile' => 'sfPhpunitFixtureFileAggregator');
     
    if (!$aggregator instanceof sfPhpunitFixtureAggregator) {
      throw new Exception('For using fixtures from the testcase or suite `'.get_class($aggregator).'` you have to implement one of the following interfaces `'.implode('`, `', $map).'`');
    }
     
    foreach ($map as $class => $agg_type) {
      if ($aggregator instanceof $agg_type) {
        return new $class($aggregator, $options);
      } 
    }

    throw new Exception('It does not have any sence to implement `sfPhpunitFixtureAggregator` interface. Please check this once againg and implement its child\'s interfaces  `'.implode('`, `', $map).'`');
  }

  /**
   *
   * @param sfBasePhpunitTestCase $case
   * @param strign $type
   *
   * @throws Exception if the protected method for getting spesific fixture directory does not exist.
   *
   * @return strign
   */
  public static function getFixtureDir(sfPhpunitFixtureAggregator $aggregator, $type = self::OWN)
  {
    $getFixtureDir = 'get'.$type.'FixtureDir';
    if (!method_exists($aggregator, $getFixtureDir)) {
      throw new Exception('The static method `'.get_class($aggregator).'::'.$getFixtureDir.'` does not exist and so cannot be called');
    }

    $path = call_user_func(array($aggregator, $getFixtureDir));
    if (!file_exists($path)) {
      throw new Exception('The '.$type.' level fixture dir `'.$path.'` does not exist.');
    }

    return $path;
  }
}