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

namespace OrangeHRM\Core\Dto;

class Base64Attachment
{
    private string $filename;
    private string $fileType;
    private string $size;
    private string $base64Content;

    /**
     * @param string $filename
     * @param string $fileType
     * @param string $base64Content
     * @param string $size
     */
    public function __construct(string $filename, string $fileType, string $base64Content, string $size)
    {
        $this->filename = $filename;
        $this->fileType = $fileType;
        $this->base64Content = $base64Content;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getBase64Content(): string
    {
        return $this->base64Content;
    }

    /**
     * @param string $base64Content
     */
    public function setBase64Content(string $base64Content): void
    {
        $this->base64Content = $base64Content;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return base64_decode($this->base64Content) ?? null;
    }
}
