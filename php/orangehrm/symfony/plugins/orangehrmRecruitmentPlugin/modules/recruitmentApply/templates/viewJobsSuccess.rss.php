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
 *
 */
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title><?php echo __('Active Job Vacancies');?></title>
    <link><?php echo public_path('index.php/recruitmentApply/jobs.rss'); ?></link>
    <description></description>
    <pubDate><?php echo date('D, d M Y H:i:s T');?></pubDate>
    <language>en</language>
<?php foreach ($publishedVacancies as $vacancy): ?>    
    <item>
      <title><![CDATA[<?php echo $vacancy->name;?>]]></title>
      <link><?php echo public_path('index.php/recruitmentApply/applyVacancy/id/'.$vacancy->getId(), true); ?></link>
      <description><![CDATA[<pre><?php echo wordwrap($vacancy->description, 110); ?></pre>]]>
      </description>
    </item>
<?php endforeach; ?>    
  </channel>
</rss>
