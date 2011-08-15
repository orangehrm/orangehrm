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
 * sfAtom1Feed.
 *
 * Specification: http://www.ietf.org/rfc/rfc4287.txt
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Stefan Koopmanschap <stefan.koopmanschap@symfony-project.com>
 */
class sfAtom1Feed extends sfFeed
{
  protected
    $context;

  protected function initContext()
  {
    if(!$this->context)
    {
      $this->context = sfContext::getInstance();
    }
  }

  /**
   * Populate the feed object from a XML feed string.
   *
   * @param string A XML feed (Atom 1.0 format)
   *
   * @return sfAtom1Feed The current object
   *
   * @throws Exception If the argument is not a well-formatted Atom feed
   */
  public function fromXml($feedXml)
  {
    preg_match('/^<\?xml\s*version="1\.0"\s*encoding="(.*?)".*?\?>$/mi', $feedXml, $matches);
    if(isset($matches[1]))
    {
      $this->setEncoding($matches[1]);
    }
    $feedXml = simplexml_load_string($feedXml);
    if(!$feedXml)
    {
      throw new Exception('Error creating feed from XML: string is not well-formatted XML');
    }

    $attributes = $feedXml->attributes('http://www.w3.org/XML/1998/namespace');

    $this->setLanguage((string) $attributes['lang']);
    $this->setTitle((string) $feedXml->title);
    $feedXml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
    if($titles = $feedXml->xpath('atom:link[@rel="alternate"]'))
    {
       $this->setLink((string) $titles[0]['href']);
    }
    if($titles = $feedXml->xpath('atom:link[@rel="self"]'))
    {
       $this->setFeedUrl((string) $titles[0]['href']);
    }
    $this->setSubtitle((string) $feedXml->subtitle);
    $this->setAuthorName((string) $feedXml->author->name);
    $this->setAuthorEmail((string) $feedXml->author->email);
    $this->setAuthorLink((string) $feedXml->author->uri);

    $image = new sfFeedImage(array(
      "favicon"  => (string)$feedXml->icon,
      "image"    => (string)$feedXml->logo,
    ));
    $this->setImage($image);

    $categories = array();
    foreach($feedXml->category as $category)
    {
      $categories[] = (string) $category['term'];
    }
    $this->setCategories($categories);

    foreach($feedXml->entry as $itemXml)
    {
      $categories = array();
      foreach($itemXml->category as $category)
      {
        $categories[] = (string) $category['term'];
      }
      $url = (string) $itemXml->link['href'];
      $pubdate = strtotime((string) $itemXml->published);
      if(!$pubdate)
      {
        if((string) $itemXml->updated)
        {
          $pubdate = strtotime((string) $itemXml->updated);
        }
        else if((string) $itemXml->modified)
        {
          $pubdate = strtotime((string) $itemXml->modified);
        }
        else if(preg_match('/\d{4}\/\d{2}\/\d{2}/', $url, $matches))
        {
          $pubdate = strtotime($matches[0]);
        }
        else
        {
          $pubdate = 0;
        }
      }
      $itemXml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
      if($enclosures = $itemXml->xpath('atom:link[@rel="enclosure"]'))
      {
        $enclosure = new sfFeedEnclosure();
        $enclosure->setUrl((string) $enclosures[0]['href']);
        $enclosure->setLength((string) $enclosures[0]['length']);
        $enclosure->setMimeType((string) $enclosures[0]['type']);
      }
      else
      {
        $enclosure = null;
      }

      $content = (string) $itemXml->content;

      if (!$content)
      {
        $content = preg_replace('/<\/?content[^>]*>/iU', '', $itemXml->content->asXml());
      }

      $this->addItemFromArray(array(
        'title'       => (string) $itemXml->title,
        'link'        => $url,
        'authorName'  => (string) $itemXml->author->name,
        'authorEmail' => (string) $itemXml->author->email,
        'authorLink'  => (string) $itemXml->author->uri,
        'pubDate'     => $pubdate,
        'description' => (string) $itemXml->summary,
        'content'     => $content,
        'uniqueId'    => (string) $itemXml->id,
        'enclosure'   => $enclosure,
        'categories'  => $categories,
        'feed'        => $this
      ));
    }

    return $this;
  }

  /**
   * Returns the the current object as a valid Atom 1.0 XML feed and sets the response content type accordingly.
   *
   * @return string An Atom 1.0 XML string
   */
  public function asXml()
  {
    $this->initContext();
    $this->context->getResponse()->setContentType('application/atom+xml; charset='.$this->getEncoding());

    return $this->toXml();
  }

  /**
   * Returns the the current object as a valid Atom 1.0 XML feed.
   *
   * @return string An Atom 1.0 XML string
   */
  public function toXml()
  {
    $this->initContext();
    $controller = $this->context->getController();

    $xml = array();
    $xml[] = '<?xml version="1.0" encoding="'.$this->getEncoding().'" ?>';

    if ($this->getLanguage())
    {
      $xml[] = sprintf('<feed xmlns="%s" xml:lang="%s">', 'http://www.w3.org/2005/Atom', $this->getLanguage());
    }
    else
    {
      $xml[] = sprintf('<feed xmlns="%s">', 'http://www.w3.org/2005/Atom');
    }

    $xml[] = '  <title>'.$this->getTitle().'</title>';
    $xml[] = '  <link rel="alternate" href="'.$controller->genUrl($this->getLink(), true).'"></link>';
    if ($this->getFeedUrl())
    {
      $xml[] = '  <link rel="self" href="'.$controller->genUrl($this->getFeedUrl(), true).'"></link>';
    }
    $xml[] = '  <id>'.$controller->genUrl($this->getLink(), true).'</id>';
    $xml[] = '  <updated>'.gmstrftime('%Y-%m-%dT%H:%M:%SZ', $this->getLatestPostDate()).'</updated>';

    if ($this->getAuthorName())
    {
      $xml[] = '  <author>';
      $xml[] = '    <name>'.$this->getAuthorName().'</name>';
      if ($this->getAuthorEmail())
      {
        $xml[] = '    <email>'.$this->getAuthorEmail().'</email>';
      }
      if ($this->getAuthorLink())
      {
        $xml[] = '    <uri>'.$controller->genUrl($this->getAuthorLink(), true).'</uri>';
      }
      $xml[] = '  </author>';
    }

    if ($this->getSubtitle())
    {
      $xml[] = '  <subtitle>'.$this->getSubtitle().'</subtitle>';
    }

    if ($this->getImage())
    {
      $xml[] = '  <icon>'.$this->getImage()->getFavicon().'</icon>';
      $xml[] = '  <logo>'.$this->getImage()->getImage().'</logo>';
    }

    if(is_array($this->getCategories()))
    {
      foreach ($this->getCategories() as $category)
      {
        $xml[] = '  <category term="'.$category.'"></category>';
      }
    }

    $xml[] = $this->getFeedElements();

    $xml[] = '</feed>';

    return implode("\n", $xml);
  }

  /**
   * Returns an array of <entry> tags corresponding to the feed's items.
   *
   * @return string A list of <entry> elements
   */
  private function getFeedElements()
  {
    $controller = $this->context->getController();
    $xml = array();

    foreach ($this->getItems() as $item)
    {
      $xml[] = '<entry>';
      $xml[] = '  <title>'.htmlspecialchars($item->getTitle()).'</title>';
      $xml[] = '  <link rel="alternate" href="'.$controller->genUrl($item->getLink(), true).'"></link>';
      if ($item->getPubdate())
      {
        $xml[] = '  <updated>'.gmstrftime('%Y-%m-%dT%H:%M:%SZ', $item->getPubdate()).'</updated>';
      }

      // author information
      if ($item->getAuthorName())
      {
        $xml[] = '  <author>';
        $xml[] = '    <name>'.$item->getAuthorName().'</name>';
        if ($item->getAuthorEmail())
        {
          $xml[] = '    <email>'.$item->getAuthorEmail().'</email>';
        }
        if ($item->getAuthorLink())
        {
          $xml[] = '    <uri>'.$controller->genUrl($item->getAuthorLink(), true).'</uri>';
        }
        $xml[] = '  </author>';
      }

      // unique id
      if ($item->getUniqueId())
      {
        $uniqueId = $item->getUniqueId();
      }
      else
      {
        $uniqueId = $this->getTagUri($item->getLink(), $item->getPubdate());
      }
      $xml[] = '  <id>'.$uniqueId.'</id>';

      // summary
      if ($item->getDescription())
      {
        $xml[] = sprintf('  <summary type="text">%s</summary>', htmlspecialchars($item->getDescription()));
      }

      // content
      if ($item->getContent())
      {
        $xml[] = sprintf('  <content type="html"><![CDATA[%s]]></content>', $item->getContent());
      }

      // enclosure
      if ($enclosure = $item->getEnclosure())
      {
        $xml[] = sprintf('  <link rel="enclosure" href="%s" length="%s" type="%s"></link>', $enclosure->getUrl(), $enclosure->getLength(), $enclosure->getMimeType());
      }

      // categories
      if(is_array($item->getCategories()))
      {
        foreach ($item->getCategories() as $category)
        {
          $xml[] = '  <category term="'.$category.'"></category>';
        }
      }

      $xml[] = '</entry>';
    }

    return implode("\n", $xml);
  }

  /**
   * Creates a TagURI.
   * See http://diveintomark.org/archives/2004/05/28/howto-atom-id
   *
   * @param string An URI to the Feed Element
   * @param datetime A publication date
   *
   * @return string A valid TagURI
   */
  private function getTagUri($url, $date)
  {
    $tag = preg_replace('#^http\://#', '', $url);
    if ($date)
    {
      $tag = preg_replace('#/#', ','.strftime('%Y-%m-%d', $date).':/', $tag, 1);
    }
    $tag = preg_replace('/#/', '/', $tag);

    return 'tag:'.$tag;
  }

}
