<?php
class AdminGenBrowser extends sfTestBrowser
{
  protected $_modules = array('Article'       => 'articles',
                              'Author'        => 'authors',
                              'Subscription'  => 'subscriptions',
                              'User'          => 'users');

  public function __construct()
  {
    parent::__construct();
    $this->setTester('doctrine', 'sfTesterDoctrine');

    $this->_generateAdminGenModules();
  }

  public function runTests()
  {
    $this->info('Run sfDoctrinePlugin Admin Generator Tests');

    $methods = get_class_methods($this);
    foreach ($methods as $method)
    {
      if (substr($method, 0, 5) == '_test')
      {
        $this->$method();
      }
    }
  }

  protected function _testValidSort()
  {
    $this->info('Test valid sort parameter');

    $this->get('/users?sort=username');

    $matches = 0;
    foreach ($this->_getQueryExecutionEvents() as $event)
    {
      if (false !== strpos($event->getQuery(), 'ORDER BY u.username asc'))
      {
        ++$matches;
      }
    }

    $this->test()->is($matches, 1);
  }

  protected function _testInvalidSort()
  {
    $this->info('Test invalid sort parameter');

    $this->get('/users?sort=INVALID');

    // there should be no queries that match "INVALID"
    foreach ($this->_getQueryExecutionEvents() as $event)
    {
      $this->test()->unlike($event->getQuery(), '/INVALID/');
    }
  }

  protected function _testValidSortType()
  {
    $this->info('Test valid sort_type parameter');

    foreach (array('asc', 'desc', 'ASC', 'DESC') as $sortType)
    {
      $this->get('/users?sort=username&sort_type='.$sortType);

      $matches = 0;
      foreach ($this->_getQueryExecutionEvents() as $event)
      {
        if (false !== strpos($event->getQuery(), 'ORDER BY u.username '.$sortType))
        {
          ++$matches;
        }
      }

      $this->test()->is($matches, 1);
    }
  }

  protected function _testInvalidSortType()
  {
    $this->info('Test invalid sort_type parameter');

    $this->get('/users?sort=username&sort_type=INVALID');

    // there should be no queries that match "INVALID"
    foreach ($this->_getQueryExecutionEvents() as $event)
    {
      $this->test()->unlike($event->getQuery(), '/INVALID/');
    }
  }

  protected function _testSanityCheck()
  {
    $this->info('Admin Generator Sanity Checks');

    foreach ($this->_modules as $model => $module)
    {
      $this->_runAdminGenModuleSanityCheck($model, $module);
    }
  }

  protected function _testAdminGenTableMethod()
  {
    $this->
      get('/my_articles')->
      with('response')->isStatusCode('200')
    ;
  }

  protected function _testArticleI18nEmbedded()
  {
    $this->info('Testing "articles" module embeds I18n');

    $info = array('author_id' => 1, 'is_on_homepage' => false, 'en' => array('title' => 'Test English title', 'body' => 'Test English body'), 'fr' => array('title' => 'Test French title', 'body' => 'Test French body'), 'created_at' => array('month' => '1', 'day' => '12', 'year' => '2009', 'hour' => '10', 'minute' => '03'), 'updated_at' => array('month' => '1', 'day' => '12', 'year' => '2009', 'hour' => '10', 'minute' => '03'));

    $this->
      get('/articles/new')->
        with('response')->begin()->
          matches('/En/')->
          matches('/Fr/')->
          matches('/Title/')->
          matches('/Body/')->
          matches('/Slug/')->
          matches('/Jonathan H. Wage/')->
          matches('/Fabien POTENCIER/')->
        end()->
        with('request')->begin()->
          isParameter('module', 'articles')->
          isParameter('action', 'new')->
        end()->
      click('Save', array('article' => $info))->
        with('response')->begin()->
          isRedirected()->
          followRedirect()->
        end()->
        with('doctrine')->begin()->
          check('Article', array('is_on_homepage' => $info['is_on_homepage']))->
          check('ArticleTranslation', array('lang' => 'fr', 'title' => 'Test French title'))->
          check('ArticleTranslation', array('lang' => 'en', 'title' => 'Test English title'))->
        end()
    ;
  }

  protected function _testEnumDropdown()
  {
    $this->info('Test enum column type uses a dropdown as the widget');

    $this->
      get('/subscriptions/new')->
        with('response')->begin()->
          checkElement('select', 'NewActivePendingExpired')->
        end()
    ;
  }

  protected function _testUserEmbedsProfileForm()
  {
    $this->info('Test user form embeds the profile form');

    $this->
      get('/users/new')->
        with('response')->begin()->
          matches('/Profile/')->
          matches('/First name/')->
          matches('/Last name/')->
        end()
    ;

    $this->info('Test the Profile form saves and attached to user properly');

    $userInfo = array(
      'user' => array(
        'username'         => 'test',
        'password'         => 'test',
        'groups_list'      => array(1, 2),
        'permissions_list' => array(3, 4),
        'Profile'  => array(
          'first_name' => 'Test',
          'last_name'  => 'Test'
        )
      )
    );

    $this->click('Save', $userInfo);

    $user = Doctrine_Core::getTable('User')->findOneByUsername($userInfo['user']['username']);
    $userInfo['user']['Profile']['user_id'] = $user->id;

    $this->
        with('response')->begin()->
          isRedirected()->
          followRedirect()->
        end()->
        with('doctrine')->begin()->
          check('User', array('username' => $userInfo['user']['username']))->
          check('Profile', $userInfo['user']['Profile'])->
          check('UserGroup', array('user_id' => $user->id, 'group_id' => $user->Groups[0]->id))->
          check('UserGroup', array('user_id' => $user->id, 'group_id' => $user->Groups[1]->id))->
          check('UserPermission', array('user_id' => $user->id, 'permission_id' => $user->Permissions[0]->id))->
          check('UserPermission', array('user_id' => $user->id, 'permission_id' => $user->Permissions[1]->id))->
        end()
    ;

    unset($userInfo['user']['Profile']['user_id']);
    $tester = $this->get('/users/new')->
      click('Save', $userInfo)->
      with('form')->begin();
    $tester->hasErrors();
    $form = $tester->getForm();
    $this->test()->is((string) $form->getErrorSchema(), 'username [An object with the same "username" already exist.]', 'Check username gives unique error');
    $tester->end();
  }

  protected function _runAdminGenModuleSanityCheck($model, $module)
  {
    $this->info('Running admin gen sanity check for module "' . $module . '"');
    $record = Doctrine_Core::getTable($model)
      ->createQuery('a')
      ->fetchOne();

    $this->
      info('Sanity check on "' . $module . '" module')->
      getAndCheck($module, 'index', '/' . $module)->
      get('/' . $module . '/' . $record->getId() . '/edit');

    $this
      ->click('Save')->
        with('response')->begin()->
          isRedirected()->
          followRedirect()->
        end()
    ;
  }

  protected function _generateAdminGenModule($model, $module)
  {    
    $this->info('Generating admin gen module "' . $module . '"');
    $task = new sfDoctrineGenerateAdminTask($this->getContext()->getEventDispatcher(), new sfFormatter());
    $task->run(array('application' => 'backend', 'route_or_model' => $model));
  }

  protected function _generateAdminGenModules()
  {
    // $task = new sfDoctrineBuildAllReloadTask($this->getContext()->getEventDispatcher(), new sfFormatter());
    // $task->run(array(), array('--no-confirmation'));

    // Generate the admin generator modules
    foreach ($this->_modules as $model => $module)
    {
      $this->_generateAdminGenModule($model, $module);
    }
  }

  protected function _cleanupAdminGenModules()
  {
    $fs = new sfFilesystem($this->getContext()->getEventDispatcher(), new sfFormatter());
    foreach ($this->_modules as $module)
    {
      $this->info('Removing admin gen module "' . $module . '"');
      $fs->execute('rm -rf ' . sfConfig::get('sf_app_module_dir') . '/' . $module);
    }
    $fs->execute('rm -rf ' . sfConfig::get('sf_test_dir') . '/functional/backend');
    $fs->execute('rm -rf ' . sfConfig::get('sf_data_dir') . '/*.sqlite');
  }

  protected function _getQueryExecutionEvents()
  {
    $events = array();

    $databaseManager = $this->browser->getContext()->getDatabaseManager();
    foreach ($databaseManager->getNames() as $name)
    {
      $database = $databaseManager->getDatabase($name);
      if ($database instanceof sfDoctrineDatabase && $profiler = $database->getProfiler())
      {
        foreach ($profiler->getQueryExecutionEvents() as $event)
        {
          $events[$event->getSequence()] = $event;
        }
      }
    }

    ksort($events);

    return array_values($events);
  }

  public function __destruct()
  {
    $this->_cleanupAdminGenModules();
  }
}
