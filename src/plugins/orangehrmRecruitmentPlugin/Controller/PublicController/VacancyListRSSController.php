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

namespace OrangeHRM\Recruitment\Controller\PublicController;

use DOMDocument;
use DOMException;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VacancyListRSSController extends AbstractController implements PublicControllerInterface
{
    use VacancyServiceTrait;
    use DateTimeHelperTrait;
    use ConfigServiceTrait;
    use I18NHelperTrait;

    public const CONTENT_TYPE_KEY = 'Content-Type';
    public const CONTENT_TYPE_XML = 'text/xml';

    /**
     * @return Response
     * @throws DOMException
     */
    public function handle(): Response
    {
        $response = $this->getResponse();
        $response->headers->set(self::CONTENT_TYPE_KEY, self::CONTENT_TYPE_XML);
        $response->setContent($this->generateRSSFeed());
        return $response;
    }

    /**
     * @return string
     * @throws DOMException
     */
    private function generateRSSFeed(): string
    {
        $siteName = $this->getI18NHelper()->transBySource('Active Job Vacancies');
        $siteDescription = '';
        $language = $this->getConfigService()->getAdminLocalizationDefaultLanguage();
        $logoUrl = '';

        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $rssFeedUrl = $urlGenerator->generate(
            'recruitment_rss_feed',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $webUrl = $urlGenerator->generate(
            'recruitment_view_vacancy_list',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $currentDateTime = $this->getDateTimeHelper()->getNow();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('rss');
        $dom->appendChild($root);
        $root->setAttribute('version', '2.0');
        $root->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $root->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
        $root->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $root->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');
        $channel = $dom->createElement('channel');
        $root->appendChild($channel);
        $channel->appendChild($dom->createElement('title', $siteName));
        $channel->appendChild($dom->createElement('link', $rssFeedUrl));
        $channel->appendChild($dom->createElement('description', $siteDescription));
        $channel->appendChild($dom->createElement('pubDate', $currentDateTime->format(DATE_RSS)));
        $channel->appendChild($dom->createElement('language', $language));
        $image = $dom->createElement('image');
        $channel->appendChild($image);
        $image->appendChild($dom->createElement('url', $logoUrl));
        $image->appendChild($dom->createElement('title', $siteName));
        $image->appendChild($dom->createElement('link', $webUrl));

        foreach ($this->getPublishedVacancies() as $vacancy) {
            $item = $dom->createElement('item');
            $channel->appendChild($item);
            $title = $item->appendChild($dom->createElement('title'));
            $title->appendChild($dom->createCDATASection($vacancy->getName()));
            $recruitmentApplyUrl = $urlGenerator->generate(
                'recruitment_apply_job_vacancy',
                ['id' => $vacancy->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $item->appendChild($dom->createElement('link', $recruitmentApplyUrl));
            $description = $item->appendChild($dom->createElement('description'));
            $description->appendChild($dom->createCDATASection($vacancy->getDescription()));
            $item->appendChild(
                $dom->createElement(
                    'pubDate',
                    $vacancy->getUpdatedTime()->format(DATE_RSS)
                )
            );
        }
        return $dom->saveXML();
    }

    /**
     * @return Vacancy[]
     */
    private function getPublishedVacancies(): array
    {
        return $this->getVacancyService()
            ->getVacancyDao()
            ->getPublishedVacancyList();
    }
}
