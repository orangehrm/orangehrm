<?php

$_test_dir = realpath(dirname(__FILE__).'/..');
require_once($_test_dir.'/../lib/vendor/lime/lime.php');
require_once($_test_dir.'/../lib/util/sfToolkit.class.php');

define('DS', DIRECTORY_SEPARATOR);

class sf_test_project
{
  public $php_cli = null;
  public $tmp_dir = null;
  public $t = null;
  public $current_dir = null;

  public function initialize($t)
  {
    $this->t = $t;

    $this->tmp_dir = sfToolkit::getTmpDir().DS.'sf_test_project';

    if (is_dir($this->tmp_dir))
    {
      $this->clearTmpDir();
      rmdir($this->tmp_dir);
    }

    mkdir($this->tmp_dir, 0777);

    $this->current_dir = getcwd();
    chdir($this->tmp_dir);

    $this->php_cli = sfToolkit::getPhpCli();
  }

  public function shutdown()
  {
    $this->clearTmpDir();
    rmdir($this->tmp_dir);
    chdir($this->current_dir);
  }

  protected function clearTmpDir()
  {
    require_once(dirname(__FILE__).'/../../lib/util/sfToolkit.class.php');
    sfToolkit::clearDirectory($this->tmp_dir);
  }

  public function execute_command($cmd, $awaited_return=0)
  {
    chdir($this->tmp_dir);
    $symfony = file_exists('symfony') ? 'symfony' : dirname(__FILE__).'/../../data/bin/symfony';

    ob_start();
    passthru(sprintf('%s "%s" %s 2>&1', $this->php_cli, $symfony, $cmd), $return);
    $content = ob_get_clean();
    $this->t->cmp_ok($return, '==', $awaited_return, sprintf('"symfony %s" returns awaited value (%d)', $cmd, $awaited_return));

    return $content;
  }

  public function get_fixture_content($file)
  {
    return str_replace("\r\n", "\n", file_get_contents(dirname(__FILE__).'/fixtures/'.$file));
  }
}

$t = new lime_test(40, new lime_output_color());

if (!extension_loaded('SQLite'))
{
  $t->skip('You need SQLite to run these tests', $t->plan);

  return;
}

$c = new sf_test_project();
$c->initialize($t);

// generate:*
$content = $c->execute_command('generate:project myproject');
$t->ok(file_exists($c->tmp_dir.DS.'symfony'), '"generate:project" installs the symfony CLI in root project directory');

$content = $c->execute_command('generate:app frontend --escaping-strategy=on');
$t->ok(is_dir($c->tmp_dir.DS.'apps'.DS.'frontend'), '"generate:app" creates a "frontend" directory under "apps" directory');
$t->like(file_get_contents($c->tmp_dir.'/apps/frontend/config/settings.yml'), '/escaping_strategy: +true/', '"generate:app" switches escaping_strategy "on"');

$content = $c->execute_command('generate:app backend');
$t->like(file_get_contents($c->tmp_dir.'/apps/backend/config/settings.yml'), '/escaping_strategy: +false/', '"generate:app" switches escaping_strategy "off"');

// failing
$content = $c->execute_command('generate:module wrongapp foo', 1);

$content = $c->execute_command('generate:module frontend foo');
$t->ok(is_dir($c->tmp_dir.DS.'apps'.DS.'frontend'.DS.'modules'.DS.'foo'), '"generate:module" creates a "foo" directory under "modules" directory');

copy(dirname(__FILE__).'/fixtures/propel/schema.yml', $c->tmp_dir.DS.'config'.DS.'schema.yml');
copy(dirname(__FILE__).'/fixtures/propel/databases.yml', $c->tmp_dir.DS.'config'.DS.'databases.yml');
copy(dirname(__FILE__).'/fixtures/propel/propel.ini', $c->tmp_dir.DS.'config'.DS.'propel.ini');
copy(dirname(__FILE__).'/fixtures/factories.yml', $c->tmp_dir.DS.'apps'.DS.'frontend'.DS.'config'.DS.'factories.yml');

// update propel configuration paths
file_put_contents($c->tmp_dir.DS.'config'.DS.'propel.ini', str_replace('%SF_ROOT_DIR%', $c->tmp_dir, str_replace('%SF_DATA_DIR%', $c->tmp_dir.'/data', file_get_contents($c->tmp_dir.DS.'config'.DS.'propel.ini'))));

// propel:*
$content = $c->execute_command('propel:build-sql');
$t->ok(file_exists($c->tmp_dir.DS.'data'.DS.'sql'.DS.'lib.model.schema.sql'), '"propel:build-sql" creates a "schema.sql" file under "data/sql" directory');

$content = $c->execute_command('propel:build-model');
$t->ok(file_exists($c->tmp_dir.DS.'lib'.DS.'model'.DS.'Article.php'), '"propel:build-model" creates model classes under "lib/model" directory');

$content = $c->execute_command('propel:build-form');
$t->ok(file_exists($c->tmp_dir.DS.'lib'.DS.'form'.DS.DS.'BaseFormPropel.class.php'), '"propel:build-form" creates form classes under "lib/form" directory');

$c->execute_command('propel:insert-sql --no-confirmation');
$t->ok(file_exists($c->tmp_dir.DS.'data'.DS.'database.sqlite'), '"propel:insert-sql" creates tables in the database');

$content = $c->execute_command('propel:generate-module --generate-in-cache frontend articleInitCrud Article');
$t->ok(file_exists($c->tmp_dir.DS.'apps'.DS.'frontend'.DS.'modules'.DS.'articleInitCrud'.DS.'config'.DS.'generator.yml'), '"propel:generate-module" initializes a CRUD module');

$content = $c->execute_command('propel:init-admin frontend articleInitAdmin Article');
$t->ok(file_exists($c->tmp_dir.DS.'apps'.DS.'frontend'.DS.'modules'.DS.'articleInitAdmin'.DS.'config'.DS.'generator.yml'), '"propel:init-admin" initializes an admin generator module');

// test:*
$content = $c->execute_command('test:functional frontend articleInitCrudActions');
$t->is($content, $c->get_fixture_content('test/functional/result.txt'), '"test:functional" can launch a particular functional test');

$content = $c->execute_command('test:functional frontend', 1);
$t->is($content, $c->get_fixture_content('test/functional/result-harness.txt'), '"test:functional" can launch all functional tests');

copy(dirname(__FILE__).'/fixtures/test/unit/testTest.php', $c->tmp_dir.DS.'test'.DS.'unit'.DS.'testTest.php');

$content = $c->execute_command('test:unit test');
$t->is($content, $c->get_fixture_content('/test/unit/result.txt'), '"test:unit" can launch a particular unit test');

$content = $c->execute_command('test:unit');
$t->is($content, $c->get_fixture_content('test/unit/result-harness.txt'), '"test:unit" can launch all unit tests');

$content = $c->execute_command('test:all', 1);
$t->is($content, $c->get_fixture_content('test/result-harness.txt'), '"test:all" launches all unit and functional tests');

$content = $c->execute_command(sprintf('project:freeze %s', realpath(dirname(__FILE__).'/../../data')));
$t->like(file_get_contents($c->tmp_dir.DS.'config'.DS.'ProjectConfiguration.class.php'), '/dirname\(__FILE__\)/', '"project:freeze" freezes symfony lib and data dir into the project directory');

$content = $c->execute_command('project:unfreeze');
$t->unlike(file_get_contents($c->tmp_dir.DS.'config'.DS.'ProjectConfiguration.class.php'), '/dirname\(__FILE__\)/', '"project:unfreeze" unfreezes symfony lib and data dir');

$content = $c->execute_command('cache:clear');

// Test task autoloading
mkdir($c->tmp_dir.DS.'lib'.DS.'task');
copy(dirname(__FILE__).'/fixtures/task/aTask.class.php', $c->tmp_dir.DS.'lib'.DS.'task'.DS.'aTask.class.php');
copy(dirname(__FILE__).'/fixtures/task/zTask.class.php', $c->tmp_dir.DS.'lib'.DS.'task'.DS.'zTask.class.php');
mkdir($pluginDir = $c->tmp_dir.DS.'plugins'.DS.'myFooPlugin'.DS.'lib'.DS.'task', 0777, true);
copy(dirname(__FILE__).'/fixtures/task/myPluginTask.class.php', $pluginDir.DS.'myPluginTask.class.php');

$c->execute_command('a:run');
$c->execute_command('z:run');
$c->execute_command('p:run');

$c->shutdown();
