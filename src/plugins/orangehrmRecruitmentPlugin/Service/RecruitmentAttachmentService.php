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

namespace OrangeHRM\Recruitment\Service;

use OrangeHRM\Recruitment\Dao\RecruitmentAttachmentDao;

class RecruitmentAttachmentService
{
    public const ALLOWED_CANDIDATE_ATTACHMENT_FILE_TYPES = [
        "text/plain",
        "text/rtf",
        "application/rtf",
        "application/pdf",
        "application/msword",
        "application/vnd.oasis.opendocument.text",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    ];

    private RecruitmentAttachmentDao $recruitmentAttachmentDao;

    /**
     * Get recruitmentAttachmentDao Dao
     * @return recruitmentAttachmentDao
     */
    public function getRecruitmentAttachmentDao(): RecruitmentAttachmentDao
    {
        return $this->recruitmentAttachmentDao;
    }

    /**
     * Construct
     */
    public function __construct()
    {
        $this->recruitmentAttachmentDao = new RecruitmentAttachmentDao();
    }
}
