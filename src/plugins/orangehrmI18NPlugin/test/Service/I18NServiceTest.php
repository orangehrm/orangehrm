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

namespace OrangeHRM\Tests\I18N\Service;

use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Dao\I18NDao;
use OrangeHRM\I18N\Dto\TranslationCollection;
use OrangeHRM\I18N\Service\I18NService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use Symfony\Component\Translation\Translator;

/**
 * @group I18N
 * @group Service
 */
class I18NServiceTest extends KernelTestCase
{
    public const DUMMY_FR_TRANSLATIONS = [
        ['unitId' => 'employee', 'groupName' => 'general', 'source' => 'Employee', 'target' => 'Employé'],
        ['unitId' => 'first_name', 'groupName' => 'general', 'source' => 'First Name', 'target' => 'Prénom'],
        ['unitId' => 'contact_details', 'groupName' => 'pim', 'source' => 'Contact Details', 'target' => 'Coordonnées'],
        ['unitId' => 'leave', 'groupName' => 'leave', 'source' => 'Leave', 'target' => 'Congé'],
        [
            'unitId' => 'leave_balance_days',
            'groupName' => 'leave',
            'source' => 'Leave Balance (Days)',
            'target' => 'Solde des congés (Jours)'
        ],
    ];
    public const DUMMY_ZH_HANS_CN_TRANSLATIONS = [
        ['unitId' => 'employee', 'groupName' => 'general', 'source' => 'Employee', 'target' => '员工'],
        ['unitId' => 'first_name', 'groupName' => 'general', 'source' => 'First Name', 'target' => '名字'],
        ['unitId' => 'contact_details', 'groupName' => 'pim', 'source' => 'Contact Details', 'target' => '联系详情'],
        ['unitId' => 'leave', 'groupName' => 'leave', 'source' => 'Leave', 'target' => '休假'],
        [
            'unitId' => 'leave_balance_days',
            'groupName' => 'leave',
            'source' => 'Leave Balance (Days)',
            'target' => '休假余额（天）'
        ],
    ];

    public function testGetI18NDao(): void
    {
        $i18nService = new I18NService();
        $this->assertTrue($i18nService->getI18NDao() instanceof I18NDao);
    }

    public function testGetTranslator(): void
    {
        $i18nService = new I18NService();
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getAdminLocalizationDefaultLanguage'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultLanguage')
            ->willReturn('fr');
        $this->createKernelWithMockServices([Services::CONFIG_SERVICE => $configService]);
        $this->assertTrue($i18nService->getTranslator() instanceof Translator);
        $this->assertEquals('fr', $i18nService->getTranslator()->getLocale());
        $this->assertEmpty($i18nService->getTranslator()->getCatalogues());
        $this->assertEquals('general.test', $i18nService->getTranslator()->trans('general.test'));
    }

    public function testGetTranslationCollection(): void
    {
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));

        /** @var TranslationCollection $translationCollection */
        $translationCollection = $this->invokeProtectedMethodOnMock(
            I18NService::class,
            $i18nService,
            'getTranslationCollection',
            ['fr']
        );
        $this->assertEquals([
            'general.employee' => 'Employé',
            'general.first_name' => 'Prénom',
            'pim.contact_details' => 'Coordonnées',
            'leave.leave' => 'Congé',
            'leave.leave_balance_days' => 'Solde des congés (Jours)'
        ], $translationCollection->getKeyAndTarget());
        $this->assertEquals([
            'Employee' => 'Employé',
            'First Name' => 'Prénom',
            'Contact Details' => 'Coordonnées',
            'Leave' => 'Congé',
            'Leave Balance (Days)' => 'Solde des congés (Jours)'
        ], $translationCollection->getSourceAndTarget());
        $this->assertEquals([
            'general.employee' => ['source' => 'Employee', 'target' => 'Employé'],
            'general.first_name' => ['source' => 'First Name', 'target' => 'Prénom'],
            'pim.contact_details' => ['source' => 'Contact Details', 'target' => 'Coordonnées'],
            'leave.leave' => ['source' => 'Leave', 'target' => 'Congé'],
            'leave.leave_balance_days' => ['source' => 'Leave Balance (Days)', 'target' => 'Solde des congés (Jours)']
        ], $translationCollection->getKeyAndSourceTarget());
    }

    public function testGenerateCacheKeyPrefixForLang(): void
    {
        $prefix = $this->invokeProtectedMethod(I18NService::class, 'generateCacheKeyPrefixForLang', ['zh_Hans_CN']);
        $this->assertEquals('core.i18n.zh_Hans_CN', $prefix);
    }

    public function testGenerateTranslationCacheKey(): void
    {
        $prefix = $this->invokeProtectedMethod(I18NService::class, 'generateTranslationCacheKey', ['zh_Hant_TW']);
        $this->assertEquals('core.i18n.zh_Hant_TW.translation', $prefix);
    }

    public function testGenerateTranslationKeyTargetArrayCacheKey(): void
    {
        $prefix = $this->invokeProtectedMethod(I18NService::class, 'generateTranslationKeyTargetArrayCacheKey', ['fr']);
        $this->assertEquals('core.i18n.fr.translation_key_target_array', $prefix);
    }

    public function testGenerateTranslationSourceTargetArrayCacheKey(): void
    {
        $prefix = $this->invokeProtectedMethod(
            I18NService::class,
            'generateTranslationSourceTargetArrayCacheKey',
            ['en_US']
        );
        $this->assertEquals('core.i18n.en_US.translation_source_target_array', $prefix);
    }

    public function testGenerateETagCacheKey(): void
    {
        $prefix = $this->invokeProtectedMethod(I18NService::class, 'generateETagCacheKey', ['zz_ZZ']);
        $this->assertEquals('core.i18n.zz_ZZ.etag', $prefix);
    }

    public function testGetETagByLangCode(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));
        $this->assertEquals('lQ5b95Z0muoJdZUAo+1rjh/A1N7ifsQBieBXuK3DVYg=', $i18nService->getETagByLangCode('fr'));

        // Testing cache adapter with ArrayAdapter, to make sure to not call `getI18NDao` more than once
        $this->assertEquals('lQ5b95Z0muoJdZUAo+1rjh/A1N7ifsQBieBXuK3DVYg=', $i18nService->getETagByLangCode('fr'));
    }

    public function testGetETagByLangCodeWithMultipleLanguages(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->exactly(2))
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS, 2));
        $this->assertEquals('lQ5b95Z0muoJdZUAo+1rjh/A1N7ifsQBieBXuK3DVYg=', $i18nService->getETagByLangCode('fr'));
        $this->assertEquals('lQ5b95Z0muoJdZUAo+1rjh/A1N7ifsQBieBXuK3DVYg=', $i18nService->getETagByLangCode('fr'));

        // When language changing, need to fetch from DB, `getI18NDao` calls twice in this test case
        $this->assertEquals('lQ5b95Z0muoJdZUAo+1rjh/A1N7ifsQBieBXuK3DVYg=', $i18nService->getETagByLangCode('en_US'));
    }

    public function testGetTranslationMessagesAsJsonString(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));
        $expected = '{"general.employee":{"source":"Employee","target":"Employ\u00e9"},"general.first_name":{"source":"First Name","target":"Pr\u00e9nom"},"pim.contact_details":{"source":"Contact Details","target":"Coordonn\u00e9es"},"leave.leave":{"source":"Leave","target":"Cong\u00e9"},"leave.leave_balance_days":{"source":"Leave Balance (Days)","target":"Solde des cong\u00e9s (Jours)"}}';
        $this->assertEquals($expected, $i18nService->getTranslationMessagesAsJsonString('fr'));
        $this->assertEquals($expected, $i18nService->getTranslationMessagesAsJsonString('fr'));
    }

    public function testGetTranslationMessagesKeyTargetArray(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));
        $expected = [
            'general.employee' => 'Employé',
            'general.first_name' => 'Prénom',
            'pim.contact_details' => 'Coordonnées',
            'leave.leave' => 'Congé',
            'leave.leave_balance_days' => 'Solde des congés (Jours)'
        ];
        $this->assertEquals($expected, $i18nService->getTranslationMessagesKeyTargetArray('fr'));
        $this->assertEquals($expected, $i18nService->getTranslationMessagesKeyTargetArray('fr'));
    }

    public function testGetTranslationMessagesKeyTargetArrayWithInCaseNotCreatedCache(): void
    {
        $cache = MockCacheService::getCache();
        $this->createKernelWithMockServices([Services::CACHE => $cache]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));
        $expected = [
            'general.employee' => 'Employé',
            'general.first_name' => 'Prénom',
            'pim.contact_details' => 'Coordonnées',
            'leave.leave' => 'Congé',
            'leave.leave_balance_days' => 'Solde des congés (Jours)'
        ];
        $this->assertEquals($expected, $i18nService->getTranslationMessagesKeyTargetArray('fr'));
        $this->assertEquals($expected, $i18nService->getTranslationMessagesKeyTargetArray('fr'));

        $prefix = $this->invokeProtectedMethod(
            I18NService::class,
            'generateTranslationSourceTargetArrayCacheKey',
            ['fr']
        );
        // Clearing cache to simulate
        $cache->clear($prefix);

        // Have to call `getI18NDao` twice, since cache cleaned
        $this->assertEquals($expected, $i18nService->getTranslationMessagesKeyTargetArray('fr'));
    }

    public function testGetTranslationMessagesSourceTargetArray(): void
    {
        $this->createKernelWithMockServices([Services::CACHE => MockCacheService::getCache()]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS));
        $expected = [
            'Employee' => 'Employé',
            'First Name' => 'Prénom',
            'Contact Details' => 'Coordonnées',
            'Leave' => 'Congé',
            'Leave Balance (Days)' => 'Solde des congés (Jours)'
        ];
        $this->assertEquals($expected, $i18nService->getTranslationMessagesSourceTargetArray('fr'));
        $this->assertEquals($expected, $i18nService->getTranslationMessagesSourceTargetArray('fr'));
    }

    public function testGetTranslationMessagesSourceTargetArrayWithInCaseNotCreatedCache(): void
    {
        $cache = MockCacheService::getCache();
        $this->createKernelWithMockServices([Services::CACHE => $cache]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->exactly(2))
            ->method('getI18NDao')
            ->willReturn($this->getMockI18NDao(self::DUMMY_FR_TRANSLATIONS, 2));
        $expected = [
            'Employee' => 'Employé',
            'First Name' => 'Prénom',
            'Contact Details' => 'Coordonnées',
            'Leave' => 'Congé',
            'Leave Balance (Days)' => 'Solde des congés (Jours)'
        ];
        $this->assertEquals($expected, $i18nService->getTranslationMessagesSourceTargetArray('fr'));
        $this->assertEquals($expected, $i18nService->getTranslationMessagesSourceTargetArray('fr'));

        $prefix = $this->invokeProtectedMethod(
            I18NService::class,
            'generateTranslationSourceTargetArrayCacheKey',
            ['fr']
        );
        // Clearing cache to simulate
        $cache->clear($prefix);

        // Have to call `getI18NDao` twice, since cache cleaned
        $this->assertEquals($expected, $i18nService->getTranslationMessagesSourceTargetArray('fr'));
    }

    public function testTrans(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getAdminLocalizationDefaultLanguage'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultLanguage')
            ->willReturn('fr');
        $this->createKernelWithMockServices(
            [
                Services::CACHE => MockCacheService::getCache(),
                Services::CONFIG_SERVICE => $configService
            ]
        );
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NDao')
            ->willReturn(
                $this->getMockI18NDao([
                    ...self::DUMMY_FR_TRANSLATIONS,
                    [
                        'unitId' => 'name_validation',
                        'groupName' => 'admin',
                        'source' => 'Should be less than {amount}',
                        'target' => 'Le montant devrait être inférieur à {amount}'
                    ]
                ])
            );

        $this->assertEquals('Employé', $i18nService->trans('general.employee'));
        $this->assertEquals('Coordonnées', $i18nService->trans('pim.contact_details'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->trans('leave.leave_balance_days'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->trans('leave.leave_balance_days', [], 'fr'));
        $this->assertEquals(
            'Le montant devrait être inférieur à 100',
            $i18nService->trans('admin.name_validation', ['amount' => 100])
        );
    }

    public function testTransWithMultipleLanguages(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getAdminLocalizationDefaultLanguage'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultLanguage')
            ->willReturn('fr');
        $this->createKernelWithMockServices(
            [
                Services::CACHE => MockCacheService::getCache(),
                Services::CONFIG_SERVICE => $configService
            ]
        );

        $i18nDao = $this->getMockBuilder(I18NDao::class)
            ->onlyMethods(['getAllTranslationMessagesByLangCode'])
            ->getMock();
        $i18nDao->expects($this->exactly(2))
            ->method('getAllTranslationMessagesByLangCode')
            ->willReturnMap([
                ['fr', self::DUMMY_FR_TRANSLATIONS],
                ['zh_Hans_CN', self::DUMMY_ZH_HANS_CN_TRANSLATIONS],
            ]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->exactly(2))
            ->method('getI18NDao')
            ->willReturn($i18nDao);

        $this->assertEquals('Employé', $i18nService->trans('general.employee'));
        $this->assertEquals('Coordonnées', $i18nService->trans('pim.contact_details'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->trans('leave.leave_balance_days'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->trans('leave.leave_balance_days', [], 'fr'));
        // Don't have translation, fallback to key
        $this->assertEquals('admin.name_validation', $i18nService->trans('admin.name_validation'));

        $this->assertEquals('休假余额（天）', $i18nService->trans('leave.leave_balance_days', [], 'zh_Hans_CN'));
        $i18nService->setTranslatorLanguage('zh_Hans_CN');
        $this->assertEquals('员工', $i18nService->trans('general.employee'));
    }

    public function testTransBySource(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getAdminLocalizationDefaultLanguage'])
            ->getMock();
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultLanguage')
            ->willReturn('fr');
        $this->createKernelWithMockServices(
            [
                Services::CACHE => MockCacheService::getCache(),
                Services::CONFIG_SERVICE => $configService
            ]
        );

        $i18nDao = $this->getMockBuilder(I18NDao::class)
            ->onlyMethods(['getAllTranslationMessagesByLangCode'])
            ->getMock();
        $i18nDao->expects($this->exactly(2))
            ->method('getAllTranslationMessagesByLangCode')
            ->willReturnMap([
                ['fr', self::DUMMY_FR_TRANSLATIONS],
                ['zh_Hans_CN', self::DUMMY_ZH_HANS_CN_TRANSLATIONS],
            ]);
        $i18nService = $this->getMockBuilder(I18NService::class)
            ->onlyMethods(['getI18NDao'])
            ->getMock();
        $i18nService->expects($this->exactly(2))
            ->method('getI18NDao')
            ->willReturn($i18nDao);

        $this->assertEquals('Employé', $i18nService->transBySource('Employee'));
        $this->assertEquals('Coordonnées', $i18nService->transBySource('Contact Details'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->transBySource('Leave Balance (Days)'));
        $this->assertEquals('Solde des congés (Jours)', $i18nService->transBySource('Leave Balance (Days)', [], 'fr'));
        // Don't have translation, fallback to source
        $this->assertEquals('Name Validation', $i18nService->transBySource('Name Validation'));

        $this->assertEquals('休假余额（天）', $i18nService->transBySource('Leave Balance (Days)', [], 'zh_Hans_CN'));
        $i18nService->setTranslatorLanguage('zh_Hans_CN');
        $this->assertEquals('员工', $i18nService->transBySource('Employee'));
    }

    /**
     * @param array $resultsArray
     * @param int $expects
     * @return I18NDao
     */
    public function getMockI18NDao(array $resultsArray, int $expects = 1): I18NDao
    {
        $i18nDao = $this->getMockBuilder(I18NDao::class)
            ->onlyMethods(['getAllTranslationMessagesByLangCode'])
            ->getMock();
        $i18nDao->expects($this->exactly($expects))
            ->method('getAllTranslationMessagesByLangCode')
            ->willReturn($resultsArray);
        return $i18nDao;
    }
}
