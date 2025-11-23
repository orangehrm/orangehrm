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

namespace OrangeHRM\Tests\Core\Helper;

use DateTime;
use OrangeHRM\Core\Helper\LocalizedDateFormatter;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NHelper;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Core
 * @group Helper
 */
class LocalizedDateFormatterTest extends KernelTestCase
{
    /**
     * @dataProvider dataProviderForFormatDate
     */
    public function testFormatDate(
        DateTime $dateTime,
        string $dateFormat,
        array $returnMap,
        $expects,
        string $expected
    ): void {
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        $i18nHelper->expects($expects)
            ->method('transBySource')
            ->willReturnMap($returnMap);
        $this->createKernelWithMockServices([Services::I18N_HELPER => $i18nHelper]);
        $formatter = new LocalizedDateFormatter();
        $date = $formatter->formatDate($dateTime, $dateFormat);
        $this->assertEquals($expected, $date);
    }

    public function dataProviderForFormatDate(): array
    {
        $returnMapForShortDays = [
            ['Sun', [], null, '星期日'],
            ['Mon', [], null, '周一'],
            ['Tue', [], null, '周二'],
            ['Wed', [], null, '周三'],
            ['Thu', [], null, '周四'],
            ['Fri', [], null, '星期五'],
            ['Sat', [], null, '周六'],
        ];
        $returnMapForLongDays = [
            ['Sunday', [], null, 'Воскресенье'],
            ['Monday', [], null, 'Понедельник'],
            ['Tuesday', [], null, 'Вторник'],
            ['Wednesday', [], null, 'Среда'],
            ['Thursday', [], null, 'Четверг'],
            ['Friday', [], null, 'Пятница'],
            ['Saturday', [], null, 'Суббота'],
        ];
        $returnMapForShortMonths = [
            ['Jan', [], null, 'جنوری'],
            ['Feb', [], null, 'فروری'],
            ['Mar', [], null, 'مارچ'],
            ['Apr', [], null, 'اپریل'],
            ['May', [], null, 'مئی'],
            ['Jun', [], null, 'جون'],
            ['Jul', [], null, 'جولائی'],
            ['Aug', [], null, 'اگست'],
            ['Sep', [], null, 'ستمبر'],
            ['Oct', [], null, 'اکتوبر'],
            ['Nov', [], null, 'نومبر'],
            ['Dec', [], null, 'دسمبر'],
        ];
        $returnMapForLongMonths = [
            ['January', [], null, 'ஜனவரி'],
            ['February', [], null, 'பிப்ரவரி'],
            ['March', [], null, 'மார்ச்'],
            ['April', [], null, 'ஏப்ரல்'],
            ['May', [], null, 'மே'],
            ['June', [], null, 'ஜூன்'],
            ['July', [], null, 'ஜூலை'],
            ['August', [], null, 'ஆகஸ்ட்'],
            ['September', [], null, 'செப்டம்பர்'],
            ['October', [], null, 'அக்டோபர்'],
            ['November', [], null, 'நவம்பர்'],
            ['December', [], null, 'டிசம்பர்'],
        ];
        return [
            /** Testing short days */
            [
                new DateTime('2022-07-01'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '星期五, 01 07 2022'
            ],
            [
                new DateTime('2022-07-02'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '周六, 02 07 2022'
            ],
            [
                new DateTime('2022-07-03'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '星期日, 03 07 2022'
            ],
            [
                new DateTime('2022-07-04'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '周一, 04 07 2022'
            ],
            [
                new DateTime('2022-07-05'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '周二, 05 07 2022'
            ],
            [
                new DateTime('2022-07-06'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '周三, 06 07 2022'
            ],
            [
                new DateTime('2022-07-07'),
                'D, d m Y',
                $returnMapForShortDays,
                $this->exactly(7),
                '周四, 07 07 2022'
            ],
            /** Testing long days */
            [
                new DateTime('2022-07-02'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Суббота, 02-07-2022'
            ],
            [
                new DateTime('2022-07-03'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Воскресенье, 03-07-2022'
            ],
            [
                new DateTime('2022-07-04'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Понедельник, 04-07-2022'
            ],
            [
                new DateTime('2022-07-05'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Вторник, 05-07-2022'
            ],
            [
                new DateTime('2022-07-06'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Среда, 06-07-2022'
            ],
            [
                new DateTime('2022-07-07'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Четверг, 07-07-2022'
            ],
            [
                new DateTime('2022-07-08'),
                'l, d-m-Y',
                $returnMapForLongDays,
                $this->exactly(7),
                'Пятница, 08-07-2022'
            ],
            /** Testing short months */
            [
                new DateTime('2022-01-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-جنوری-2022'
            ],
            [
                new DateTime('2022-02-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-فروری-2022'
            ],
            [
                new DateTime('2022-03-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-مارچ-2022'
            ],
            [
                new DateTime('2022-04-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-اپریل-2022'
            ],
            [
                new DateTime('2022-05-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-مئی-2022'
            ],
            [
                new DateTime('2022-06-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-جون-2022'
            ],
            [
                new DateTime('2022-07-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-جولائی-2022'
            ],
            [
                new DateTime('2022-08-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-اگست-2022'
            ],
            [
                new DateTime('2022-09-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-ستمبر-2022'
            ],
            [
                new DateTime('2022-10-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-اکتوبر-2022'
            ],
            [
                new DateTime('2022-11-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-نومبر-2022'
            ],
            [
                new DateTime('2022-12-08'),
                'd-M-Y',
                $returnMapForShortMonths,
                $this->exactly(12),
                '08-دسمبر-2022'
            ],

            /** Testing long months */
            [
                new DateTime('2022-01-31'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '31/ஜனவரி/2022'
            ],
            [
                new DateTime('2022-02-28'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '28/பிப்ரவரி/2022'
            ],
            [
                new DateTime('2022-03-31'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '31/மார்ச்/2022'
            ],
            [
                new DateTime('2022-04-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/ஏப்ரல்/2022'
            ],
            [
                new DateTime('2022-05-31'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '31/மே/2022'
            ],
            [
                new DateTime('2022-06-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/ஜூன்/2022'
            ],
            [
                new DateTime('2022-07-31'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '31/ஜூலை/2022'
            ],
            [
                new DateTime('2022-08-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/ஆகஸ்ட்/2022'
            ],
            [
                new DateTime('2022-09-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/செப்டம்பர்/2022'
            ],
            [
                new DateTime('2022-10-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/அக்டோபர்/2022'
            ],
            [
                new DateTime('2022-11-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/நவம்பர்/2022'
            ],
            [
                new DateTime('2022-12-30'),
                'd/F/Y',
                $returnMapForLongMonths,
                $this->exactly(12),
                '30/டிசம்பர்/2022'
            ],
        ];
    }

    public function testFormatDateWithCachingForSameFormat(): void
    {
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        // should call only 7 times with caching
        $i18nHelper->expects($this->exactly(7))
            ->method('transBySource')
            ->willReturnMap([
                ['Sunday', [], null, 'ඉරිදා'],
                ['Monday', [], null, 'සඳුදා'],
                ['Tuesday', [], null, 'අඟහරුවාදා'],
                ['Wednesday', [], null, 'බදාදා'],
                ['Thursday', [], null, 'බ්‍රහස්පතින්දා'],
                ['Friday', [], null, 'සිකුරාදා'],
                ['Saturday', [], null, 'සෙනසුරාදා'],
            ]);
        $this->createKernelWithMockServices([Services::I18N_HELPER => $i18nHelper]);
        $formatter = new LocalizedDateFormatter();
        $date = $formatter->formatDate(new DateTime('2022-07-30'), 'l, d-m-Y');
        $this->assertEquals('සෙනසුරාදා, 30-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('2022-07-01'), 'l, d-m-Y');
        $this->assertEquals('සිකුරාදා, 01-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('1970-01-30'), 'l, d-m-Y');
        $this->assertEquals('සිකුරාදා, 30-01-1970', $date);
        $date = $formatter->formatDate(new DateTime('2050-01-30'), 'l, d-m-Y');
        $this->assertEquals('ඉරිදා, 30-01-2050', $date);
    }

    public function testFormatDateWithCachingForDifferentFormat(): void
    {
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        // call 7x2 times, since two different formats
        $i18nHelper->expects($this->exactly(14))
            ->method('transBySource')
            ->willReturnMap([
                ['Sunday', [], null, 'Zondag'],
                ['Monday', [], null, 'Maandag'],
                ['Tuesday', [], null, 'Dinsdag'],
                ['Wednesday', [], null, 'Woensdag'],
                ['Thursday', [], null, 'Donderdag'],
                ['Friday', [], null, 'Vrijdag'],
                ['Saturday', [], null, 'zaterdag'],
            ]);
        $this->createKernelWithMockServices([Services::I18N_HELPER => $i18nHelper]);
        $formatter = new LocalizedDateFormatter();
        $date = $formatter->formatDate(new DateTime('2022-07-30'), 'l, d-m-Y');
        $this->assertEquals('zaterdag, 30-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('2022-07-01'), 'l, d-m-Y');
        $this->assertEquals('Vrijdag, 01-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('1970-02-03'), 'l, d-m-Y');
        $this->assertEquals('Dinsdag, 03-02-1970', $date);
        $date = $formatter->formatDate(new DateTime('2050-01-30'), 'l, d-m-Y');
        $this->assertEquals('Zondag, 30-01-2050', $date);
        $date = $formatter->formatDate(new DateTime('2050-01-30'), 'l, d/m/Y');
        $this->assertEquals('Zondag, 30/01/2050', $date);
    }

    public function testFormatDateWithNonLocalizableFormat(): void
    {
        $i18nHelper = $this->getMockBuilder(I18NHelper::class)
            ->onlyMethods(['transBySource'])
            ->getMock();
        // should not call this method if format not contain at lest one of D, l, M, F
        $i18nHelper->expects($this->never())
            ->method('transBySource');

        $this->createKernelWithMockServices([Services::I18N_HELPER => $i18nHelper]);
        $formatter = new LocalizedDateFormatter();
        $date = $formatter->formatDate(new DateTime('2022-07-30'), 'd-m-Y');
        $this->assertEquals('30-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('2022-07-01'), 'd-m-Y');
        $this->assertEquals('01-07-2022', $date);
        $date = $formatter->formatDate(new DateTime('1970-02-03'), 'd-m-Y');
        $this->assertEquals('03-02-1970', $date);
        $date = $formatter->formatDate(new DateTime('2050-01-30'), 'd-m-Y');
        $this->assertEquals('30-01-2050', $date);
        $date = $formatter->formatDate(new DateTime('2050-01-30'), 'd/m/Y');
        $this->assertEquals('30/01/2050', $date);
    }
}
