<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_buzz_link")
 * @ORM\Entity
 */
class BuzzLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var BuzzPost
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\BuzzPost", inversedBy="links")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private BuzzPost $post;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="text")
     */
    private string $link;

    /**
     * @var string
     *
     * @ORM\Column(name="original_link", type="text", nullable=true)
     */
    private string $originalLink;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return BuzzPost
     */
    public function getPost(): BuzzPost
    {
        return $this->post;
    }

    /**
     * @param BuzzPost $post
     */
    public function setPost(BuzzPost $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @param string $originalLink
     */
    public function setOriginalLink(string $originalLink): void
    {
        $this->originalLink = $originalLink;
    }
}
