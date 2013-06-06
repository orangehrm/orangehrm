<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

$offset = $pager->getOffset();
$offset = empty($offset) ? 0 : $offset;

$limit = $pager->getMaxPerPage();
$numOfRecords = $pager->getNumResults();
?>
<ul class="paging top">
    <li class="desc"> <?php echo $offset . "-" . ($offset + $limit) . " of " . $numOfRecords;?> </li>
    <li class="first"><a title="Go to First Page" class="tiptip" href="javascript:submitPage(1)"><?php echo __("First");?></a></li>
    <li class="previous"><a title="Go to Previous Page" class="tiptip" href="javascript:submitPage(<?php echo $pager->getPreviousPage();?>)"><?php echo __("Previous");?></a></li>

<?php
//$numOfRecords = $pager
foreach ($pager->getLinks() as $page):
      if ($page == $pager->getPage()):?>            
    <li><a class="current" href="#"><?php echo $page;?></a></li>
<?php else: ?>
        <li><a href="javascript:submitPage(<?php echo $page;?>)"><?php echo $page;?></a></li>   
<?php endif;?>
<?php endforeach;?>
        <li class="next"><a title="Go to Next Page" class="tiptip" href="javascript:submitPage(<?php echo $pager->getNextPage();?>)"><?php echo __("Next");?></a></li>
        <li class="last"><a title="Go to Last Page" class="tiptip" href="javascript:submitPage(<?php echo $pager->getLastPage();?>)"><?php echo __("Last");?></a></li>
</ul>
