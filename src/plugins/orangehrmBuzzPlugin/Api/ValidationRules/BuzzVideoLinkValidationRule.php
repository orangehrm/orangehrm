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

namespace OrangeHRM\Buzz\Api\ValidationRules;

use OrangeHRM\Buzz\Dto\BuzzVideoURL\BuzzEmbeddedURL;
use OrangeHRM\Buzz\Dto\BuzzVideoURL\EmbeddedURLForDailymotion;
use OrangeHRM\Buzz\Dto\BuzzVideoURL\EmbeddedURLForVimeo;
use OrangeHRM\Buzz\Dto\BuzzVideoURL\EmbeddedURLForYoutube;
use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;

class BuzzVideoLinkValidationRule extends AbstractRule
{
    private const YOUTUBE_REGEX = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/';
    private const VIMEO_REGEX = '/(http|https)?:\/\/(www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/';
    private const DAILYMOTION_REGEX = '/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/';

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $youtubeMatch = preg_match(self::YOUTUBE_REGEX, $input);
        $vimeoMatch = preg_match(self::VIMEO_REGEX, $input);
        $dailymotionMatch = preg_match(self::DAILYMOTION_REGEX, $input);

        $validationGroup = null;
        if ($youtubeMatch) {
            $validationGroup = new EmbeddedURLForYoutube($input);
        }
        if ($vimeoMatch) {
            $validationGroup = new EmbeddedURLForVimeo($input);
        }
        if ($dailymotionMatch) {
            $validationGroup = new EmbeddedURLForDailymotion($input);
        }

        $buzzEmbeddedURL = new BuzzEmbeddedURL($validationGroup);
        return $buzzEmbeddedURL->getURLValidation();
    }
}
