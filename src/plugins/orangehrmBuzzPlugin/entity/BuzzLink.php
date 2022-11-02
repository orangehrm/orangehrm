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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var BuzzPost
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\BuzzPost")
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
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     * @deprecated
     */
    private int $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=600, nullable=true)
     * @deprecated
     */
    private ?string $title = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     * @deprecated
     */
    private ?string $description = null;

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
     * @return int|null
     * @deprecated
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     * @deprecated
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @deprecated
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @deprecated
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
