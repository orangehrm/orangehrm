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

use Doctrine\DBAL\Types\BlobType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_buzz_photo")
 * @ORM\Entity
 */
class Photo
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
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private Post $post;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="photo", type="blob", nullable=true)
     */
    private $picture = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private ?string $fileName = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=50, nullable=true)
     */
    private ?string $fileType = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="size", type="string", length=20, nullable=true)
     */
    private ?string $size = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="width", type="string", length=20, nullable=true)
     */
    private ?string $width = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="height", type="string", length=20, nullable=true)
     */
    private ?string $height = null;

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
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     */
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * @param string|null $width
     */
    public function setWidth(?string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return string|null
     */
    public function getHeight(): ?string
    {
        return $this->height;
    }

    /**
     * @param string|null $height
     */
    public function setHeight(?string $height): void
    {
        $this->height = $height;
    }

    /**
     * @return resource|string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }
}
