<?php

/*
 * This file is part of the sfFeed2 package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2007 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFeedPeer.
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfFeedPeer
{
  /**
   * Retrieve a new sfFeed implementation instance.
   *
   * @param string A sfFeed implementation name
   *
   * @return sfFeed A sfFeed implementation instance
   *
   * @throws sfFactoryException If a new syndication feed implementation instance cannot be created
   */
  public static function newInstance($format = '')
  {
    try
    {
      $class = 'sf'.ucfirst($format).'Feed';

      // the class exists
      $object = new $class();

      if (!($object instanceof sfFeed))
      {
          // the class name is of the wrong type
          $error = 'Class "%s" is not of the type sfFeed';
          $error = sprintf($error, $class);

          throw new sfFactoryException($error);
      }

      return $object;
    }
    catch (sfException $e)
    {
      $e->printStackTrace();
    }
  }

  /**
   * Retrieve a new sfFeed implementation instance, populated from a web feed.
   * The class of the returned instance depends on the nature of the web feed.
   * This method uses the sfWebBrowser plugin.
   *
   * @param string A web feed URI
   *
   * @return sfFeed A sfFeed implementation instance
   */
  public static function createFromWeb($uri, $options = array())
  {
    if(isset($options['adapter']))
    {
      $browser = new sfWebBrowser(array(), $options['adapter'], isset($options['adapter_options']) ? $options['adapter_options'] : array());
    }
    else
    {
      $browser = new sfWebBrowser();
    }
    $browser->setUserAgent(isset($options['userAgent']) ? $options['userAgent'] : 'sfFeedReader/0.9');
    if($browser->get($uri)->responseIsError())
    {
      $error = 'The given URL (%s) returns an error (%s: %s)';
      $error = sprintf($error, $uri, $browser->getResponseCode(), $browser->getResponseMessage());
      throw new Exception($error);
    }
    $feedString = $browser->getResponseText();

    return self::createFromXml($feedString, $uri);
  }

  /**
   * Retrieve a new sfFeed implementation instance, populated from a xml feed.
   * The class of the returned instance depends on the nature of the xml feed.
   *
   * @param string $feedString a feed as xml string
   * @param string A web feed URI
   *
   * @return sfFeed A sfFeed implementation instance
   */
  public static function createFromXml($feedString, $uri)
  {
    $feedClass = '';
    if(preg_match('/xmlns=[\"\'](http:\/\/www\.w3\.org\/2005\/Atom|http:\/\/purl\.org\/atom)/', $feedString))
    {
      $feedClass = 'sfAtom1Feed';
    }
    if(strpos($feedString, '<rss') !== false)
    {
      $feedClass = 'sfRssFeed';
    }
    if(strpos($feedString, '<rdf:RDF') !== false)
    {
      $feedClass = 'sfRss10Feed';
    }

    if($feedClass)
    {
      $object = new $feedClass();
      $object->setFeedUrl($uri);
      $object->fromXml($feedString);

      return $object;
    }
    else
    {
      throw new Exception('Impossible to decode feed format');
    }
  }

  /**
   * Merge the items from several feeds and retrieve a sfFeed instance.
   * Populated with all the items, and sorted.
   *
   * @param array an array of sfFeed objects
   * @param array an associative array of feed parameters
   *
   * @return sfFeed A sfFeed implementation instance
   */
  public static function aggregate($feeds, $parameters = array())
  {
    // merge all items
    $feed_items = array();
    foreach($feeds as $feed)
    {
      foreach($feed->getItems() as $item)
      {
        $index = is_integer($item->getPubDate()) || ctype_digit($item->getPubDate()) ?  $item->getPubDate() : 0;
        while(isset($feed_items[$index]))
        {
          $index++;
        }
        $feed_items[$index] = $item;
      }
    }

    // when specified, sort items chronologically instead of reverse
    if (isset($parameters['sort']) && 'chronological' == $parameters['sort'])
    {
      ksort($feed_items);
    }
    else 
    {
      // default behaviour: sort in reverse chronological order 
      krsort($feed_items);
    }
    
    // limit the number of feed items to be added
    if(isset($parameters['limit']))
    {
      $feed_items = array_slice($feed_items, 0, $parameters['limit']);
    }

    // create a feed with these items
    $feed = self::newInstance(isset($parameters['format']) ? $parameters['format'] : '');
    $feed->initialize($parameters);
    foreach($feed_items as $item)
    {
      $origin_feed = clone $item->getFeed();
      $origin_feed->setItems();
      $feed->addItem($item);
      $item->setFeed($origin_feed);
    }

    return $feed;
  }

  /**
   * Populates a feed with items based on objects.
   * Inspects the available methods of the objects to populate items properties.
   *
   * @param array an array of objects
   * @param string A route name for building the URIs to the items
   * @param array An associative array of options
   *
   * @return sfFeed the current sfFeed object
   */
  public static function convertObjectsToItems($objects, $options = array())
  {
    $items = array();
    foreach($objects as $object)
    {
      $item = new sfFeedItem();

      // For each item property, check if an object method is provided,
      // and if not, guess it. Here is what it does for the link property
      if(isset($options['methods']['link']))
      {
        if($options['methods']['link'])
        {
          $item->setLink(call_user_func(array($object, $options['methods']['link'])));
        }
        else
        {
          $item->setLink('');
        }
      }
      else
      {
        $routeName = (isset($options['routeName'])) ? $options['routeName'] : '';
        $fallbackUrl = (isset($options['fallbackUrl'])) ? $options['fallbackUrl'] : '';
        $item->setLink(self::getItemFeedLink($object, $routeName, $fallbackUrl));
      }

      // For the other properties, it can be automated
      // Not as readable but definitely more concise
      $details = array('title', 'description', 'content', 'authorEmail', 'authorName', 'authorLink', 'pubdate', 'comments', 'uniqueId', 'enclosure', 'categories');
      foreach ($details as $detail)
      {
        $itemMethod = 'set'.ucfirst($detail);
        if (isset($options['methods'][$detail]))
        {
          if ($options['methods'][$detail])
          {
            if (is_array($options['methods'][$detail]))
            {
              call_user_func(array($item, $itemMethod), call_user_func_array(array($object, $options['methods'][$detail][0]), $options['methods'][$detail][1]));
            }
            else
            {
              call_user_func(array($item, $itemMethod), call_user_func(array($object, $options['methods'][$detail])));
            }
          }
          else
          {
            call_user_func(array($item, $itemMethod), '');
          }
        }
        else
        {
          call_user_func(array($item, $itemMethod), call_user_func(array('sfFeedPeer', 'getItemFeed'.ucfirst($detail)), $object));
        }
      }

      $items[] = $item;
    }

    return $items;
  }

  /**
   * Creates and populates a feed with items based on objects
   * This is a proxy method that combines calls to newInstance() and convertObjectsToItems()
   *
   * @param array an array of objects
   * @param array an associative array of feed parameters
   *
   * @return sfFeed A sfFeed implementation instance, containing the parameters and populated with the objects
   */
  public static function createFromObjects($objects, $options = array())
  {
    $feed = self::newInstance(isset($options['format']) ? $options['format'] : '');
    $feed->initialize($options);
    $options['fallbackUrl'] = $feed->getLink();
    $feed->addItems(self::convertObjectsToItems($objects, $options));

    return $feed;
  }

  private static function getItemFeedTitle($item)
  {
    foreach (array('getFeedTitle', 'getTitle', 'getName', '__toString') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    return '';
  }

  private static function getItemFeedLink($item, $routeName = '', $fallback_url = '')
  {
    if ($routeName)
    {
      if (method_exists('sfRouting', 'getInstance'))
      {
        $route = sfRouting::getInstance()->getRouteByName($routeName);
        $url = $route[0];
        $paramNames = $route[2];
        $defaults = $route[4];
      }
      else
      {
        $routes = sfContext::getInstance()->getRouting()->getRoutes();
        $route = $routes[substr($routeName, 1)];
        if($route instanceof sfRoute)
        {
          $url = $route->getPattern();
          $paramNames = array_keys($route->getVariables());
          $defaults = $route->getDefaults();
        }
        else
        {
          $url = $route[0];
          $paramNames = array_keys($route[2]);
          $defaults = $route[3];
        }
      }

      // we get all parameters
      $params = array();
      foreach ($paramNames as $paramName)
      {
        $value = null;
        $name = ucfirst(sfInflector::camelize($paramName));

        $found = false;
        foreach (array('getFeed'.$name, 'get'.$name) as $methodName)
        {
          if (method_exists($item, $methodName))
          {
            $value = $item->$methodName();
            $found = true;
            break;
          }
        }

        if (!$found)
        {
          if (array_key_exists($paramName, $defaults))
          {
            $value = $defaults[$paramName];
          }
          else
          {
            $error = 'Cannot find a "getFeed%s()" or "get%s()" method for object "%s" to generate URL with the "%s" route';
            $error = sprintf($error, $name, $name, get_class($item), $routeName);
            throw new sfException($error);
          }
        }

        $params[] = $paramName.'='.$value;
      }

      return sfContext::getInstance()->getController()->genUrl($routeName.($params ? '?'.implode('&', $params) : ''), true);
    }

    foreach (array('getFeedLink', 'getLink', 'getUrl') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return sfContext::getInstance()->getController()->genUrl($item->$methodName(), true);
      }
    }

    if ($fallback_url)
    {
      return sfContext::getInstance()->getController()->genUrl($fallback_url, true);
    }
    else
    {
      return sfContext::getInstance()->getController()->genUrl('/', true);
    }
  }

  private static function getItemFeedDescription($item)
  {
    foreach (array('getFeedDescription', 'getDescription', 'getBody') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    return '';
  }

  private static function getItemFeedContent($item)
  {
    foreach (array('getFeedContent', 'getContent', 'getHtmlBody', 'getBody') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    return '';
  }

  private static function getItemFeedUniqueId($item)
  {
    foreach (array('getFeedUniqueId', 'getUniqueId', 'getId') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    return '';
  }

  private static function getItemFeedAuthorEmail($item)
  {
    foreach (array('getFeedAuthorEmail', 'getAuthorEmail') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    // author as an object link
    if ($author = self::getItemFeedAuthor($item))
    {
      foreach (array('getEmail', 'getMail') as $methodName)
      {
        if (method_exists($author, $methodName))
        {
          return $author->$methodName();
        }
      }
    }

    return '';
  }

  private static function getItemFeedAuthorName($item)
  {
    foreach (array('getFeedAuthorName', 'getAuthorName') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    // author as an object link
    if ($author = self::getItemFeedAuthor($item))
    {
      foreach (array('getName', '__toString') as $methodName)
      {
        if (method_exists($author, $methodName))
        {
          return $author->$methodName();
        }
      }
    }

    return '';
  }

  private static function getItemFeedAuthorLink($item)
  {
    foreach (array('getFeedAuthorLink', 'getAuthorLink') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    // author as an object link
    if ($author = self::getItemFeedAuthor($item))
    {
      foreach (array('getLink') as $methodName)
      {
        if (method_exists($author, $methodName))
        {
          return $author->$methodName();
        }
      }
    }

    return '';
  }

  private static function getItemFeedAuthor($item)
  {
    foreach (array('getAuthor', 'getUser', 'getPerson') as $methodName)
    {
      if (method_exists($item, $methodName) && is_object($item->$methodName()))
      {
        return $item->$methodName();
      }
    }

    return null;
  }

  private static function getItemFeedPubdate($item)
  {
    foreach (array('getFeedPubdate', 'getPubdate', 'getCreatedAt', 'getDate', 'getPublishedAt') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName('U');
      }
    }

    return '';
  }

  private static function getItemFeedComments($item)
  {
    foreach (array('getFeedComments', 'getComments') as $methodName)
    {
      if (method_exists($item, $methodName))
      {
        return $item->$methodName();
      }
    }

    return '';
  }

  private static function getItemFeedCategories($item)
  {
    foreach (array('getFeedCategories', 'getCategories') as $methodName)
    {
      if (method_exists($item, $methodName) && is_array($item->$methodName()))
      {
        $cats = array();
        foreach ($item->$methodName() as $category)
        {
          $cats[] = (string) $category;
        }

        return $cats;
      }
    }

    return array();
  }

  private static function getItemFeedEnclosure($item)
  {
    if (method_exists($item, 'getFeedEnclosure'))
    {
      return $item->getFeedEnclosure();
    }

    return '';
  }

}
