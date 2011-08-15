<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Job Feed  Service
 * 
 */
class JobFeedService {

    private $feedType;
    private $feedPath;
    private $vacancyDao;

    /**
     * Set Feed Type
     * 
     */
    public function setFeedType($feetType) {
        $this->feedType = $feetType;       
    }
    
    /**
     * Get Feed Type
     * @return string $feedType
     */
    public function getFeedType() {

        if (is_null($this->feedType)) {
            $this->feedType = 'rss';
        }

        return $this->feedType;
    }
    
    /**
     * Set Feed Path
     * 
     */
    public function setFeedPath($feedPath) {
        $this->feedPath = $feedPath;       
    }

    /**
     * Get RSS File Path
     * @return string $feedPath
     */
    public function getFeedPath() {

        if (is_null($this->feedPath)) {
            $this->feedPath = sfConfig::get('sf_root_dir') . '/web/job.rss';
        }

        return $this->feedPath;
    }

    /**
     * Get Feed Object
     * @return sfRssFeed Object
     */
    protected function getFeedInstance() {

        $feedType = $this->getFeedType();

        if ($feedType == 'rss') {
            return new sfRssFeed();
        }
    }

    /**
     * Set Vacancy Dao
     * 
     */
    public function setVacancyDao($vacancyDao) {
        $this->vacancyDao = $vacancyDao;       
    }
    
    /**
     * Get Vacancy Dao
     * @return VacancyDao Object
     */
    public function getVacancyDao() {

        if (is_null($this->vacancyDao)) {
            return new VacancyDao();
        }

        return $this->vacancyDao;
    }

    /**
     * Update RSS Feed
     * @return boolean
     */
    public function updateJobFeed() {

        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
        
        $feed = $this->getFeedInstance();  
        $feed->setTitle('Job Vacancies');
        $feed->setLink('http://www.orangehrm.com/');
        
        $jobVacancyObjects = $this->getVacancyDao()->getVacancyList();
        
        foreach ($jobVacancyObjects as $vacancy) {
            $item = new sfFeedItem();
            $item->setTitle($vacancy->getName());
            $item->setDescription($vacancy->getDescription());
            $item->setLink(public_path('index.php/recruitment/applyVacancy/id/'.$vacancy->getId(), true));
            $feed->addItem($item);
        }

        return $this->createFeed($feed);
    }

    /**
     * Create Feed
     * @param Feed Object $feed
     * @return boolean
     */
    protected function createFeed($feed) {

        $fileHandle = fopen($this->getFeedPath(), 'w');
        $result = fwrite($fileHandle, $feed->asXml(ESC_RAW));
        fclose($fileHandle);

        if (!$result) {
            return true;
        }

        return false;

    }    

}
