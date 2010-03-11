<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CrudBrowser extends sfTestBrowser
{
  protected
    $urlPrefix = 'article',
    $projectDir = '';

  public function setup($options)
  {
    $this->projectDir = dirname(__FILE__).'/../fixtures';
    $this->cleanup();

    chdir($this->projectDir);
    $task = new sfPropelGenerateModuleTask(new sfEventDispatcher(), new sfFormatter());
    $options[] = 'env=test';
    $options[] = '--non-verbose-templates';
    $task->run(array('crud', 'article', 'Article'), $options);

    require_once($this->projectDir.'/config/ProjectConfiguration.class.php');
    sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration('crud', 'test', true, $this->projectDir));

    return $options;
  }

  public function teardown()
  {
    $this->cleanup();

    return $this;
  }

  public function browse($options)
  {
    $options = $this->setup($options);

    // list page
    $this->
      info('list page')->
      get('/'.$this->urlPrefix)->
      with('request')->begin()->
        isParameter('module', $this->urlPrefix)->
        isParameter('action', 'index')->
      end()->
      with('response')->begin()->
        isStatusCode(200)->

        checkElement('h1', ucfirst($this->urlPrefix).' List')->

        checkElement('table thead tr th:nth(0)', 'Id')->
        checkElement('table thead tr th:nth(1)', 'Title')->
        checkElement('table thead tr th:nth(2)', 'Body')->
        checkElement('table thead tr th:nth(3)', 'Online')->
        checkElement('table thead tr th:nth(4)', 'Excerpt')->
        checkElement('table thead tr th:nth(5)', 'Category')->
        checkElement('table thead tr th:nth(6)', 'Created at')->
        checkElement('table thead tr th:nth(7)', 'End date')->
        checkElement('table thead tr th:nth(8)', 'Book')->

        checkElement('table tbody tr td:nth(0)', '1')->
        checkElement('table tbody tr td:nth(1)', 'foo title')->
        checkElement('table tbody tr td:nth(2)', 'bar body')->
        checkElement('table tbody tr td:nth(3)', '1')->
        checkElement('table tbody tr td:nth(4)', 'foo excerpt')->
        checkElement('table tbody tr td:nth(5)', '1')->
        checkElement('table tbody tr td:nth(6)', '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}$/')->
        checkElement('table tbody tr td:nth(7)', '')->
        checkElement('table tbody tr td:nth(8)', '')->
        checkElement(sprintf('a[href*="/%s/new"]', $this->urlPrefix))->
        checkElement(sprintf('tbody a[href*="/%s/1%s"]', $this->urlPrefix, in_array('with-show', $options) ? '' : '/edit'))->
        checkElement(sprintf('tbody a[href*="/%s/2%s"]', $this->urlPrefix, in_array('with-show', $options) ? '' : '/edit'))->
      end()
    ;

    // create page
    $this->
      info('create page')->
      click('New')->
      with('request')->begin()->
        isParameter('module', $this->urlPrefix)->
        isParameter('action', 'new')->
        isParameter('id', null)->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('h1', 'New '.ucfirst($this->urlPrefix))->
        checkElement(sprintf('a[href*="/%s"]', $this->urlPrefix), 'Cancel')->
        checkElement(sprintf('a[href*="/%s/"]', $this->urlPrefix), false)->
      end()->
      checkFormValues(array(
        'title'               => '',
        'body'                => '',
        'Online'              => false,
        'category_id'         => 0,
        'end_date'            => array('year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => ''),
        'book_id'             => 0,
        'author_article_list' => array(),
      ))
    ;

    // save
    $this->
      info('save')->
      saveValues($options, array(
        'title'               => 'my real title',
        'body'                => 'my real body',
        'Online'              => true,
        'category_id'         => 2,
        'end_date'            => array('year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => ''),
        'book_id'             => null,
        'author_article_list' => array(1, 2),
      ), 3, true)
    ;

    // go back to the list
    $this->
      info('go back to the list')->
      click('Cancel')->
      isStatusCode(200)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'index')
    ;

    // edit page
    $this->info('edit page');
    if (!in_array('with-show', $options) && ($options['with-show'] === true))
    {
      $this->click('3');
    }
    else
    {
      $this->get(sprintf('/%s/3/edit', $this->urlPrefix));
    }

    $this->
      with('request')->begin()->
        isParameter('module', $this->urlPrefix)->
        isParameter('action', 'edit')->
        isParameter('id', 3)->
      end()->
      with('response')->begin()->
        isStatusCode(200)->
        checkElement('h1', 'Edit '.ucfirst($this->urlPrefix))->
        checkElement(sprintf('a[href*="/%s"]', $this->urlPrefix), 'Cancel')->
        checkElement(sprintf('a[href*="/%s/3"]', $this->urlPrefix), 'Delete')->
        checkElement(sprintf('a[href*="/%s/3"][onclick*="confirm"]', $this->urlPrefix))->
        checkElement('table tbody th:nth(0)', 'Title')->
        checkElement('table tbody th:nth(1)', 'Body')->
        checkElement('table tbody th:nth(2)', 'Online')->
        checkElement('table tbody th:nth(3)', 'Excerpt')->
        checkElement('table tbody th:nth(4)', 'Category id')->
        checkElement('table tbody th:nth(5)', 'Created at')->
        checkElement('table tbody th:nth(6)', 'End date')->
        checkElement('table tbody th:nth(7)', 'Book id')->
        checkElement('table tbody th:nth(8)', 'Author article list')->
        checkElement('table tbody td select[id="article_category_id"][name="article[category_id]"] option', 2)->
        checkElement('table tbody td select[id="article_book_id"][name="article[book_id]"] option', 2)->
      end()
    ;

    // save / validation
    $values = array(
      'id'                  => 1009299,
      'title'               => '',
      'body'                => 'my body',
      'Online'              => true,
      'excerpt'             => 'my excerpt',
      'category_id'         => null,
      'end_date'            => array('year' => 0, 'month' => 0, 'day' => 15, 'hour' => '10', 'minute' => '20'),
      'book_id'             => 149999,
      'author_article_list' => array(0, 5),
    );

    $this->
      info('save / validation')->
      click('Save', array('article' => $values))->
      isStatusCode(200)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'update')->
      checkFormValues(array_merge($values, array(
        'end_date' => array('year' => null, 'month' => null, 'day' => 15, 'hour' => '10', 'minute' => '20')))
      )->
      checkResponseElement('ul[class="error_list"] li:contains("Required.")', 2)->
      checkResponseElement('ul[class="error_list"] li:contains("Invalid.")', 4)
    ;

    // save
    $this->
      info('save')->
      saveValues($options, array(
        'id'                  => 3,
        'title'               => 'my title',
        'body'                => 'my body',
        'Online'              => false,
        'category_id'         => 1,
        'end_date'            => array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => '10', 'minute' => '20'),
        'book_id'             => 1,
        'author_article_list' => array(1, 3),
      ), 3, false)
    ;

    // go back to the list
    $this->
      info('go back to the list')->
      click('Cancel')->
      isStatusCode(200)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'index')
    ;

    // delete
    $this->
      info('delete')->
      get(sprintf('/%s/3/edit', $this->urlPrefix))->
      click('Delete', array(), array('method' => 'delete', '_with_csrf' => true))->
      isStatusCode(302)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'delete')->
      isRedirected()->
      followRedirect()->
      isStatusCode(200)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'index')->

      get(sprintf('/%s/3/edit', $this->urlPrefix))->
      isStatusCode(404)
    ;

    if (in_array('with-show', $options))
    {
      // show page
      $this->
        info('show page')->
        get(sprintf('/%s/2', $this->urlPrefix))->
        with('request')->begin()->
          isParameter('module', $this->urlPrefix)->
          isParameter('action', 'show')->
          isParameter('id', 2)->
        end()->
        with('response')->begin()->
          isStatusCode(200)->
          checkElement(sprintf('a[href*="/%s/2%s"]', $this->urlPrefix, in_array('with-show', $options) ? '' : '/edit'), 'Edit')->
          checkElement(sprintf('a[href*="/%s"]', $this->urlPrefix), 'List', array('position' => 1))->
          checkElement('body table tbody tr:nth(0)', '/Id\:\s+2/')->
          checkElement('body table tbody tr:nth(1)', '/Title\:\s+foo foo title/')->
          checkElement('body table tbody tr:nth(2)', '/Body\:\s+bar bar body/')->
          checkElement('body table tbody tr:nth(3)', '/Online\:\s+/')->
          checkElement('body table tbody tr:nth(4)', '/Excerpt\:\s+foo excerpt/')->
          checkElement('body table tbody tr:nth(5)', '/Category\:\s+2/')->
          checkElement('body table tbody tr:nth(6)', '/Created at\:\s+[0-9\-\:\s]+/')->
          checkElement('body table tbody tr:nth(7)', '/End date\:\s+[0-9\-\:\s]+/')->
          checkElement('body table tbody tr:nth(8)', '/Book\:\s+/')->
        end()
      ;
    }
    else
    {
      $this->get(sprintf('/%s/show/id/2', $this->urlPrefix))->isStatusCode(404);
    }

    $this->teardown();

    return $this;
  }

  public function saveValues($options, $values, $id, $creation)
  {
    $this->
      click('Save', array('article' => $values))->
      isRedirected()->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', $creation ? 'create' : 'update')
    ;

    $this->
      followRedirect()->
      isStatusCode(200)->
      isRequestParameter('module', $this->urlPrefix)->
      isRequestParameter('action', 'edit')->
      isRequestParameter('id', $id)->
      checkFormValues($values)
    ;

    return $this;
  }

  public function checkFormValues(array $values)
  {
    return $this->with('response')->begin()->
      checkElement(sprintf('table tbody td input[id="article_title"][name="article[title]"][value="%s"]', $values['title']))->

      checkElement('table tbody td textarea[id="article_body"][name="article[body]"]', $values['body'])->

      checkElement(sprintf('table tbody td input[id="article_Online"][name="article[Online]"][type="checkbox"]%s', $values['Online'] ? '[checked="checked"]' : ''))->

      checkElement(sprintf('table tbody td select[id="article_category_id"][name="article[category_id]"] option[value="1"]%s', $values['category_id'] == 1 ? '[selected="selected"]' : ''), 'Category 1')->
      checkElement(sprintf('table tbody td select[id="article_category_id"][name="article[category_id]"] option[value="2"]%s', $values['category_id'] == 2 ? '[selected="selected"]' : ''), 'Category 2')->

      checkElement(sprintf('table tbody td select[id="article_book_id"][name="article[book_id]"] option[value=""]%s', $values['book_id'] == '' ? '[selected="selected"]' : ''), '')->
      checkElement(sprintf('table tbody td select[id="article_book_id"][name="article[book_id]"] option[value="1"]%s', $values['book_id'] == 1 ? '[selected="selected"]' : ''), 'The definitive guide to symfony')->

      checkElement(sprintf('table tbody td select[id="article_author_article_list"][name="article[author_article_list][]"] option[value="1"]%s', in_array(1, $values['author_article_list']) ? '[selected="selected"]' : ''), 'Fabien')->
      checkElement(sprintf('table tbody td select[id="article_author_article_list"][name="article[author_article_list][]"] option[value="2"]%s', in_array(2, $values['author_article_list']) ? '[selected="selected"]' : ''), 'Thomas')->
      checkElement(sprintf('table tbody td select[id="article_author_article_list"][name="article[author_article_list][]"] option[value="3"]%s', in_array(3, $values['author_article_list']) ? '[selected="selected"]' : ''), 'Hélène')->

      checkElement('table tbody td select[id="article_end_date_year"][name="article[end_date][year]"] option[selected="selected"]', (string) $values['end_date']['year'])->
      checkElement('table tbody td select[id="article_end_date_month"][name="article[end_date][month]"] option[selected="selected"]', (string) $values['end_date']['month'])->
      checkElement('table tbody td select[id="article_end_date_day"][name="article[end_date][day]"] option[selected="selected"]', (string) $values['end_date']['day'])->
      checkElement('table tbody td select[id="article_end_date_hour"][name="article[end_date][hour]"] option[selected="selected"]', (string) $values['end_date']['hour'])->
      checkElement('table tbody td select[id="article_end_date_minute"][name="article[end_date][minute]"] option[selected="selected"]', (string) $values['end_date']['minute'])->
    end();
  }

  protected function clearDirectory($dir)
  {
    sfToolkit::clearDirectory($dir);
    if (is_dir($dir))
    {
      rmdir($dir);
    }
  }

  protected function cleanup()
  {
    $this->clearDirectory(sprintf($this->projectDir.'/apps/crud/modules/%s', $this->urlPrefix));
    $this->clearDirectory(sprintf($this->projectDir.'/cache/crud/test/modules/auto%s', ucfirst($this->urlPrefix)));
    $this->clearDirectory($this->projectDir.'/test/functional/crud');
  }
}
