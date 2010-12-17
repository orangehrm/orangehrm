<?php

/**
 * For using ability auto detect what fixtures type should be used 
 * you have to implement <b>one</b> of the following interfaces to the suite or testcase
 * 
 * @package    sfPhpunitPlugin
 * @subpackage fixture
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
interface sfPhpunitFixtureAggregator 
{
  /**
   * @return string the path where look for the package level fixtures
   */
  function getPackageFixtureDir();

  /**
   * @return string the path where look for the own level fixtures
   */
  function getOwnFixtureDir();
  
  /**
   * @return string the path where look for the common level fixtures
   */
  function getCommonFixtureDir();

  /**
   * @return string the path where look for the symfony level fixtures. As usual it is a data/fixtures directory
   */
  function getSymfonyFixtureDir();
}

interface sfPhpunitFixtureFileAggregator extends sfPhpunitFixtureAggregator {}

interface sfPhpunitFixturePropelAggregator extends sfPhpunitFixtureAggregator {}

interface sfPhpunitFixtureDoctrineAggregator extends sfPhpunitFixtureAggregator {}

interface sfPhpunitFixtureDbUnitAggregator extends sfPhpunitFixtureAggregator {}