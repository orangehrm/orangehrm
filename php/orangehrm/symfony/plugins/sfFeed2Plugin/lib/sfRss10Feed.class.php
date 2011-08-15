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
 * sfRss10Feed.
 *
 * Specification: http://web.resource.org/rss/1.0/spec
 *                http://web.resource.org/rss/1.0/modules/dc/
 *                http://web.resource.org/rss/1.0/modules/syndication/
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Stefan Koopmanschap <stefan.koopmanschap@symfony-project.com>
 */
class sfRss10Feed extends sfRssFeed
{

  /**
   * Populate the feed object from a XML feed string.
   *
   * @param string A XML feed (RSS 1.0 format)
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

    // we get rid of namespaces to avoid simpleXML headaches
    $feedXml = str_replace(
      array('<dc:', '</dc:', '<rdf:', '</rdf:', '<content:', '</content:'),
      array('<', '</', '<', '</', '<', '</'),
      $feedXml
    );
    $feedXml = simplexml_load_string($feedXml);
    if(!$feedXml)
    {
      throw new Exception('Error creating feed from XML: string is not well-formatted XML');
    }

    $this->setTitle((string) $feedXml->channel->title);
    $this->setLink((string) $feedXml->channel->link);
    $this->setDescription((string) $feedXml->channel->description);

    foreach($feedXml->item as $itemXml)
    {
      $this->addItemFromArray(array(
        'title'       => (string) $itemXml->title,
        'link'        => (string) $itemXml->link,
        'description' => (string) $itemXml->description,
        'content'     => (string) $itemXml->encoded,
        'authorName'  => (string) $itemXml->creator,
        'pubDate'     => strtotime((string) $itemXml->date),
        'feed'        => $this
      ));
    }

    return $this;
  }

  /**
   * Returns the the current object as a valid RSS 1.0 XML feed.
   *
   * @return string A RSS 1.0 XML string
   */
  public function toXml()
  {
    $this->initContext();
    $xml = array();
    $xml[] = '<?xml version="1.0" encoding="'.$this->getEncoding().'" ?>';
    $xml[] = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns="http://purl.org/rss/1.0/">';
    $xml[] = '  <channel rdf:about="'.htmlspecialchars($this->context->getController()->genUrl($this->getLink(), true)).'">';
    $xml[] = '    <title>'.htmlspecialchars($this->getTitle()).'</title>';
    $xml[] = '    <link>'.htmlspecialchars($this->context->getController()->genUrl($this->getLink(), true)).'</link>';
    $xml[] = '    <description>'.htmlspecialchars($this->getDescription()).'</description>';
    $xml[] = '    <items>';
    $xml[] = '      <rdf:Seq>';
    $xml[] = implode("\n", $this->getFeedItemSequence());
    $xml[] = '      </rdf:Seq>';
    $xml[] = '    </items>';
    $xml[] = '  </channel>';
    $xml[] = implode("\n", $this->getFeedElements());
    $xml[] = '</rdf:RDF>';

    return implode("\n", $xml);
  }

  /**
   * Returns an array of <rdf:li> tags corresponding to the feed's items sequence.
   *
   * @return string A list of <rdf:li> elements
   */
  protected function getFeedItemSequence()
  {
    $xml = array();
    foreach ($this->getItems() as $item)
    {
      $xml[] = '        <rdf:li rdf:resource="'.htmlspecialchars($this->context->getController()->genUrl($item->getLink(), true)).'" />';
    }

    return $xml;
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
      $xml[] = '  <item rdf:about="'.htmlspecialchars($this->context->getController()->genUrl($item->getLink(), true)).'">';
      $xml[] = '    <title>'.htmlspecialchars($item->getTitle()).'</title>';
      $xml[] = '    <link>'.htmlspecialchars($this->context->getController()->genUrl($item->getLink(), true)).'</link>';
      if ($item->getDescription())
      {
        $xml[] = '    <description>'.htmlspecialchars($item->getDescription()).'</description>';
      }
      if ($item->getContent())
      {
        $xml[] = '    <content:encoded><![CDATA['.$item->getContent().']]></content:encoded>';
      }
      if ($item->getAuthorName())
      {
        $xml[] = '    <dc:creator>'.htmlspecialchars($item->getAuthorName()).'</dc:creator>';
      }
      if ($item->getPubdate())
      {
        $xml[] = '    <dc:date>'.gmstrftime('%Y-%m-%dT%H:%M:%SZ', $item->getPubdate()).'</dc:date>';
      }
      $xml[] = '  </item>';
    }

    return $xml;
  }

}
