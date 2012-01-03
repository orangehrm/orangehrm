<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectRouteCollection represents a collection of routes bound to objects.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfObjectRouteCollection.class.php 32654 2011-06-15 18:58:43Z fabien $
 */
class sfObjectRouteCollection extends sfRouteCollection
{
  protected
    $routeClass = 'sfObjectRoute';

  /**
   * Constructor.
   *
   * @param array $options An array of options
   */
  public function __construct(array $options)
  {
    parent::__construct($options);

    if (!isset($this->options['model']))
    {
      throw new InvalidArgumentException(sprintf('You must pass a "model" option to %s ("%s" route)', get_class($this), $this->options['name']));
    }
    
    $this->options = array_merge(array(
      'actions'              => false,
      'module'               => $this->options['name'],
      'prefix_path'          => '/'.$this->options['name'],
      'column'               => isset($this->options['column']) ? $this->options['column'] : 'id',
      'with_show'            => true,
      'segment_names'        => array('edit' => 'edit', 'new' => 'new'),
      'model_methods'        => array(),
      'requirements'         => array(),
      'with_wildcard_routes' => false,
      'default_params'   => array(),
    ), $this->options);
    
    $this->options['requirements'] = array_merge(array($this->options['column'] => 'id' == $this->options['column'] ? '\d+' : null), $this->options['requirements']);
    $this->options['model_methods'] = array_merge(array('list' => null, 'object' => null), $this->options['model_methods']);

    if (isset($this->options['route_class']))
    {
      $this->routeClass = $this->options['route_class'];
    }

    $this->generateRoutes();
  }

  protected function generateRoutes()
  {
    // collection actions
    if (isset($this->options['collection_actions']))
    {
      foreach ($this->options['collection_actions'] as $action => $methods)
      {
        $this->routes[$this->getRoute($action)] = $this->getRouteForCollection($action, $methods);
      }
    }

    // "standard" actions
    $actions = false === $this->options['actions'] ? $this->getDefaultActions() : $this->options['actions'];
    foreach ($actions as $action)
    {
      $method = 'getRouteFor'.ucfirst($action);
      if (!method_exists($this, $method))
      {
        throw new InvalidArgumentException(sprintf('Unable to generate a route for the "%s" action.', $action));
      }

      $this->routes[$this->getRoute($action)] = $this->$method();
    }

    // object actions
    if (isset($this->options['object_actions']))
    {
      foreach ($this->options['object_actions'] as $action => $methods)
      {
        $this->routes[$this->getRoute($action)] = $this->getRouteForObject($action, $methods);
      }
    }

    if ($this->options['with_wildcard_routes'])
    {
      // wildcard object actions
      $this->routes[$this->getRoute('object')] = new $this->routeClass(
        sprintf('%s/:%s/:action.:sf_format', $this->options['prefix_path'], $this->options['column']),
        array_merge(array('module' => $this->options['module'], 'sf_format' => 'html'), $this->options['default_params']),
        array_merge($this->options['requirements'], array('sf_method' => array('get', 'head'))),
        array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
      );

      // wildcard collection actions
      $this->routes[$this->getRoute('collection')] = new $this->routeClass(
        sprintf('%s/:action/action.:sf_format', $this->options['prefix_path']),
        array_merge(array('module' => $this->options['module'], 'sf_format' => 'html'), $this->options['default_params']),
        array_merge($this->options['requirements'], array('sf_method' => 'post')),
        array('model' => $this->options['model'], 'type' => 'list', 'method' => $this->options['model_methods']['list'])
      );
    }
  }

  protected function getRouteForCollection($action, $methods)
  {
    return new $this->routeClass(
      sprintf('%s/%s.:sf_format', $this->options['prefix_path'], $action),
      array_merge(array('module' => $this->options['module'], 'action' => $action, 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => $methods)),
      array('model' => $this->options['model'], 'type' => 'list', 'method' => $this->options['model_methods']['list'])
    );
  }

  protected function getRouteForObject($action, $methods)
  {
    return new $this->routeClass(
      sprintf('%s/:%s/%s.:sf_format', $this->options['prefix_path'], $this->options['column'], $action),
      array_merge(array('module' => $this->options['module'], 'action' => $action, 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => $methods)),
      array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
    );
  }

  protected function getRouteForList()
  {
    return new $this->routeClass(
      sprintf('%s.:sf_format', $this->options['prefix_path']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('list'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => array('get', 'head'))),
      array('model' => $this->options['model'], 'type' => 'list', 'method' => $this->options['model_methods']['list'])
    );
  }

  protected function getRouteForNew()
  {
    return new $this->routeClass(
      sprintf('%s/%s.:sf_format', $this->options['prefix_path'], $this->options['segment_names']['new']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('new'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => array('get', 'head'))),
      array('model' => $this->options['model'], 'type' => 'object')
    );
  }

  protected function getRouteForCreate()
  {
    return new $this->routeClass(
      sprintf('%s.:sf_format', $this->options['prefix_path']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('create'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => 'post')),
      array('model' => $this->options['model'], 'type' => 'object')
    );
  }

  protected function getRouteForShow()
  {
    return new $this->routeClass(
      sprintf('%s/:%s.:sf_format', $this->options['prefix_path'], $this->options['column']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('show'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => array('get', 'head'))),
      array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
    );
  }

  protected function getRouteForEdit()
  {
    return new $this->routeClass(
      sprintf('%s/:%s/%s.:sf_format', $this->options['prefix_path'], $this->options['column'], $this->options['segment_names']['edit']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('edit'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => array('get', 'head'))),
      array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
    );
  }

  protected function getRouteForUpdate()
  {
    return new $this->routeClass(
      sprintf('%s/:%s.:sf_format', $this->options['prefix_path'], $this->options['column']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('update'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => 'put')),
      array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
    );
  }

  protected function getRouteForDelete()
  {
    return new $this->routeClass(
      sprintf('%s/:%s.:sf_format', $this->options['prefix_path'], $this->options['column']),
      array_merge(array('module' => $this->options['module'], 'action' => $this->getActionMethod('delete'), 'sf_format' => 'html'), $this->options['default_params']),
      array_merge($this->options['requirements'], array('sf_method' => 'delete')),
      array('model' => $this->options['model'], 'type' => 'object', 'method' => $this->options['model_methods']['object'])
    );
  }

  protected function getDefaultActions()
  {
    $actions = array('list', 'new', 'create', 'edit', 'update', 'delete');

    if ($this->options['with_show'])
    {
      $actions[] = 'show';
    }

    return $actions;
  }

  protected function getRoute($action)
  {
    return 'list' == $action ? $this->options['name'] : $this->options['name'].'_'.$action;
  }

  protected function getActionMethod($action)
  {
    return 'list' == $action ? 'index' : $action;
  }
}
