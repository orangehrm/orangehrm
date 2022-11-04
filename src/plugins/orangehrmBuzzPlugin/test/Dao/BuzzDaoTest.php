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

namespace OrangeHRM\Tests\Buzz\Dao;

use OrangeHRM\Buzz\Dao\BuzzDao;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Buzz
 * @group Dao
 */
class BuzzDaoTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmBuzzPlugin/test/fixtures/BuzzDao.yaml';
        TestDataService::populate($fixture);
    }

    public function testGetBuzzFeedPosts(): void
    {
        $dao = new BuzzDao();
        $buzzFeedFilterParams = new BuzzFeedFilterParams();
        $buzzFeedFilterParams->setAuthUserEmpNumber(1);
        $buzzFeedFilterParams->setLimit(2);
        $feedPosts = $dao->getBuzzFeedPosts($buzzFeedFilterParams);
        $expected = [
            [
                'employee' => [
                    'empNumber' => 6,
                    'lastName' => 'Morgan',
                    'firstName' => 'Jasmine',
                    'middleName' => '',
                    'employeeId' => '0006',
                    'terminationId' => null,
                ],
                'text' => 'https://youtu.be/qMCMgedYqac',
                'stats' => [
                    'numOfLikes' => 1,
                    'numOfComments' => 1,
                    'numOfShares' => 1,
                ],
                'type' => 'video',
                'shareId' => 5,
                'createdData' => '2022-10-27',
                'createdTime' => '00:58',
                'originalPost' => null,
                'liked' => false,
                'videoLink' => 'https://www.youtube.com/embed/qMCMgedYqac',
                'photoIds' => null,
                'hasPhotos' => false,
                'hasVideo' => true,
            ],
            [
                'employee' => [
                    'empNumber' => 6,
                    'lastName' => 'Morgan',
                    'firstName' => 'Jasmine',
                    'middleName' => '',
                    'employeeId' => '0006',
                    'terminationId' => null,
                ],
                'text' => 'Write something........',
                'stats' => [
                    'numOfLikes' => 0,
                    'numOfComments' => 0,
                    'numOfShares' => 2,
                ],
                'type' => 'text',
                'shareId' => 4,
                'createdData' => '2022-10-27',
                'createdTime' => '00:54',
                'originalPost' => [
                    'text' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                    'employee' => [
                        'empNumber' => 1,
                        'lastName' => 'Adalwin',
                        'firstName' => 'Odis',
                        'middleName' => '',
                        'employeeId' => '0001',
                        'terminationId' => null,
                    ],
                    'createdDate' => '2022-10-27',
                    'createdTime' => '00:51',
                ],
                'liked' => false,
                'videoLink' => null,
                'photoIds' => null,
                'hasPhotos' => false,
                'hasVideo' => false,
            ]
        ];
        foreach ($feedPosts as $i => $post) {
            $this->assertEquals($expected[$i]['employee'], $post->getEmployee());
            $this->assertEquals($expected[$i]['text'], $post->getText());
            $this->assertEquals($expected[$i]['stats'], $post->getStats());
            $this->assertEquals($expected[$i]['type'], $post->getType());
            $this->assertEquals($expected[$i]['shareId'], $post->getId());
            $this->assertEquals($expected[$i]['createdData'], $post->getCreatedDate());
            $this->assertEquals($expected[$i]['createdTime'], $post->getCreatedTime());
            $this->assertEquals($expected[$i]['originalPost'], $post->getOriginalPost());
            $this->assertEquals($expected[$i]['liked'], $post->isLiked());
            $this->assertEquals($expected[$i]['videoLink'], $post->getVideoLink());
            $this->assertEquals($expected[$i]['photoIds'], $post->getPhotoIds());
            $this->assertEquals($expected[$i]['hasPhotos'], $post->hasPhotos());
            $this->assertEquals($expected[$i]['hasVideo'], $post->hasVideo());
        }
    }

    public function testGetBuzzFeedPostsCount(): void
    {
        $dao = new BuzzDao();
        $buzzFeedFilterParams = new BuzzFeedFilterParams();
        $buzzFeedFilterParams->setAuthUserEmpNumber(1);
        $buzzFeedFilterParams->setLimit(2);
        $this->assertEquals(5, $dao->getBuzzFeedPostsCount($buzzFeedFilterParams));
        // Out of 10 shares, 3 shares (direct posts) from terminated employees, 2 shares (share of posts) from purged employees
    }

    public function testGetBuzzPhotoIdsByPostId(): void
    {
        $dao = new BuzzDao();
        $this->assertEquals([1, 2, 3, 4], $dao->getBuzzPhotoIdsByPostId(3));

        // Existing post, but don't have photos
        $this->assertEquals([], $dao->getBuzzPhotoIdsByPostId(1));

        // Non-existing post id
        $this->assertEquals([], $dao->getBuzzPhotoIdsByPostId(100));
    }
}
