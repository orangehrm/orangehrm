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
 * sfRssFeed.
 *
 * Specification: 2.01 http://www.rssboard.org/rss-2-0-1-rv-6
 *                0.91 http://www.rssboard.org/rss-0-9-1
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfRssFeed extends sfFeed
{
  protected
    $context,
    $version = '2.0';

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
   * @param string A XML feed (RSS 2.0 format)
   *
   * @return sfRss10Feed The current object
   *
   * @throws Exception If the argument is not a well-formatted RSS feed
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

    $authorString = (string) $feedXml->channel[0]->managingEditor;
    $pos = strpos($authorString, '(');
    if($pos !== false)
    {
      $this->setAuthorEmail(trim(substr($authorString, 0, $pos)));
      $this->setAuthorName(trim(substr($authorString, $pos+1, strlen($authorString)-$pos-2)));
    }
    else
    {
      $this->setAuthorEmail(trim($authorString));
    }
    $this->setTitle((string) $feedXml->channel[0]->title);
    $this->setLink((string) $feedXml->channel[0]->link);
    $this->setDescription((string) $feedXml->channel[0]->description);
    $this->setLanguage((string) $feedXml->channel[0]->language);

    if ($feedXml->channel[0]->image)
    {
      $image = new sfFeedImage(array(
        "image"  => (string)$feedXml->channel[0]->image->url,
        "imageX" => (int)$feedXml->channel[0]->image->width,
        "imageY" => (int)$feedXml->channel[0]->image->height,
        "link"   => (string)$feedXml->channel[0]->image->link,
        "title"  => (string)$feedXml->channel[0]->image->title
      ));
      $this->setImage($image);
    }

    $categories = array();
    foreach($feedXml->channel[0]->category as $category)
    {
      $categories[] = (string) $category;
    }
    $this->setCategories($categories);

    foreach($feedXml->channel[0]->item as $itemXml)
    {
      $url = (string) $itemXml->link;
      $authorString = (string) $itemXml->author;
      $pos = strpos($authorString, '(');
      if($pos !== false)
      {
        $authorEmail = trim(substr($authorString, 0, $pos));
        $authorName = trim(substr($authorString, $pos+1, strlen($authorString)-$pos-2));
      }
      else
      {
        $authorEmail = trim($authorString);
        $authorName = '';
      }
      $dc = $itemXml->children("http://purl.org/dc/elements/1.1/");
      if(!$authorName)
      {
        $authorName = (string) $dc->creator;
      }
      $pubdate = strtotime(str_replace(array('UT', 'Z'), '', (string) $itemXml->pubDate));
      if(!$pubdate)
      {
        if((string) $dc->date)
        {
          $pubdate = strtotime(str_replace(array('UT', 'Z'), '', (string) $dc->date));
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
      $content = $itemXml->children("http://purl.org/rss/1.0/modules/content/");
      $categories = array();
      foreach($itemXml->category as $category)
      {
        $categories[] = (string) $category;
      }
      if($enclosureElement = $itemXml->enclosure)
      {
        $enclosure = new sfFeedEnclosure();
        $enclosure->setUrl((string) $enclosureElement['url']);
        $enclosure->setLength((string) $enclosureElement['length']);
        $enclosure->setMimeType((string) $enclosureElement['type']);
      }
      else
      {
        $enclosure = null;
      }
      $this->addItemFromArray(array(
        'title'       => (string) $itemXml->title,
        'link'        => $url,
        'description' => (string) $itemXml->description,
        'content'     => (string) $content->encoded,
        'authorName'  => $authorName,
        'authorEmail' => $authorEmail,
        'pubDate'     => $pubdate,
        'comments'    => (string) $itemXml->comments,
        'uniqueId'    => (string) $itemXml->guid,
        'enclosure'   => $enclosure,
        'categories'  => $categories,
        'feed'        => $this
      ));
    }
  }

  /**
   * Returns the the current object as a valid RSS 1.0 XML feed.
   * And sets the response content type accordingly.
   *
   * @return string A RSS 2.0 XML string
   */
  public function asXml()
  {
    $this->initContext();
    $this->context->getResponse()->setContentType('application/rss+xml; charset='.$this->getEncoding());

    return $this->toXml();
  }

  /**
   * Returns the the current object as a valid RSS 1.0 XML feed.
   *
   * @return string A RSS 2.0 XML string
   */
  public function toXml()
  {
    $this->initContext();
    $xml = array();
    $xml[] = '<?xml version="1.0" encoding="'.$this->getEncoding().'" ?>';
    $xml[] = '<rss version="'.$this->getVersion().'" xmlns:content="http://purl.org/rss/1.0/modules/content/">';
    $xml[] = '  <channel>';
    $xml[] = '    <title>'.$this->getTitle().'</title>';
    $xml[] = '    <link>'.$this->context->getController()->genUrl($this->getLink(), true).'</link>';
    $xml[] = '    <description>'.$this->getDescription().'</description>';
    $xml[] = '    <pubDate>'.date(DATE_RSS, $this->getLatestPostDate()).'</pubDate>';
    if ($this->getAuthorEmail())
    {
      $xml[] = '    <managingEditor>'.$this->getAuthorEmail().($this->getAuthorName() ? ' ('.$this->getAuthorName().')' : '').'</managingEditor>';
    }
    if (!$this->getAuthorEmail() && $this->getAuthorName())
    {
      $xml[] = '    <managingEditor>'.$this->getAuthorName().'</managingEditor>';
    }    if ($this->getLanguage())
    {
      $xml[] = '    <language>'.$this->getLanguage().'</language>';
    }
    if ($this->getImage())
    {
      $xml[] = '    <image>';
      $xml[] = '      <url>'.$this->getImage()->getImage().'</url>';
      $xml[] = '      <title>'.$this->getImage()->getTitle().'</title>';
      $xml[] = '      <link>'.$this->context->getController()->genUrl($this->getImage()->getLink(), true).'</link>';
      $xml[] = '      <width>'.$this->getImage()->getImageX().'</width>';
      $xml[] = '      <height>'.$this->getImage()->getImageY().'</height>';
      $xml[] = '    </image>';
    }
    if(strpos($this->version, '2.') !== false)
    {
      if(is_array($this->getCategories()))
      {
        foreach ($this->getCategories() as $category)
        {
          $xml[] = '    <category>'.$category.'</category>';
        }
      }
    }
    $xml[] = implode("\n", $this->getFeedElements());
    $xml[] = '  </channel>';
    $xml[] = '</rss>';

    return implode("\n", $xml);
  }

  /**
   * Returns an array of <item> tags corresponding to the feed's items.
   *
   * @return string An list of <item> elements
   */
  protected function getFeedElements()
  {
    $xml = array();
    foreach ($this->getItems() as $item)
    {
      $xml[] = '    <item>';
      $xml[] = '      <title><![CDATA['.$item->getTitle().']]></title>';
      $xml[] = '      <link>'.$this->context->getController()->genUrl($item->getLink(), true).'</link>';
      if ($item->getDescription())
      {
        $xml[] = '      <description><![CDATA['.$item->getDescription().']]></description>';
      }
      if ($item->getContent())
      {
        $xml[] = '      <content:encoded><![CDATA['.$item->getContent().']]></content:encoded>';
      }
      if(strpos($this->version, '2.') !== false)
      {
        if ($item->getUniqueId())
        {
          $xml[] = '      <guid isPermaLink="false">'.$item->getUniqueId().'</guid>';
        }

        // author information
        if ($item->getAuthorEmail())
        {
          $xml[] = sprintf('      <author>%s%s</author>', $item->getAuthorEmail(), ($item->getAuthorName()) ? ' ('.$item->getAuthorName().')' : '');
        }
        if ($item->getPubdate())
        {
          $xml[] = '      <pubDate>'.date(DATE_RSS, $item->getPubdate()).'</pubDate>';
        }
        if (is_string($item->getComments()))
        {
          $xml[] = '      <comments>'.htmlspecialchars($item->getComments()).'</comments>';
        }

        // enclosure
        if ($enclosure = $item->getEnclosure())
        {
          $enclosure_attributes = sprintf('url="%s" length="%s" type="%s"', $enclosure->getUrl(), $enclosure->getLength(), $enclosure->getMimeType());
          $xml[] = '      <enclosure '.$enclosure_attributes.'></enclosure>';
        }

        // categories
        if(is_array($item->getCategories()))
        {
          foreach ($item->getCategories() as $category)
          {
            $xml[] = '      <category><![CDATA['.$category.']]></category>';
          }
        }
      }
      $xml[] = '    </item>';
    }

    return $xml;
  }

  public function initialize($feed_array)
  {
    $this->setVersion(isset($feed_array['version']) ? $feed_array['version'] : '');
    parent::initialize($feed_array);

    return $this;
  }

  public function setVersion($version)
  {
    if($version)
    {
      $this->version = $version;
    }
  }

  public function getVersion()
  {
    return $this->version;
  }
}
