<?php
/*
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

echo "<ul class=\"paging $location\">";
echo "<li class=\"desc\">".__($pager->getFirstIndice() . '-' . $pager->getLastIndice() . ' ' . __('of') . ' ' . $pager->getNumResults())."</li> ";
$path = $url . '?page=1';
echo "<li class=\"first\"><a href=\"$path\" class=\"tiptip\" title=\"".__('First')."\">".__('First')."</a></li> ";
$path = $url . '?page=' . $pager->getPreviousPage();
echo "<li class=\"previous\"><a href=\"$path\" class=\"tiptip\" title=\"".__('Previous')."\">".__('Previous')."</a></li> ";

foreach ($pager->getLinks() as $page):

    if ($page == $pager->getPage()) {
        echo "<li><a class=\"current\" href=\"#\">$page</a></li> ";
    } else {
        $path = $url . '?page=' . $page;
        echo "<li><a href=\"$path\">$page</a></li> ";
    }
    
endforeach;

$path = $url . '?page=' . $pager->getNextPage();
echo "<li class=\"next\"><a href=\"$path\" class=\"tiptip\" title=\"".__('Next')."\">".__('Next')."</a></li> ";
$path = $url . '?page=' . $pager->getLastPage();
echo "<li class=\"last\"><a href=\"$path\" class=\"tiptip\" title=\"".__('Last')."\">".__('Last')."</a></li>";
echo "</ul>";
