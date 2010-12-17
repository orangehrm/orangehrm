<ul id="pagerResults">
  <?php foreach ($pager as $i => $article): ?>
    <li id="pagerResult<?php echo $i ?>"><?php echo $article->getTitle() ?></li>
  <?php endforeach; ?>
</ul>

<span id="pagerCount"><?php echo count($pager) ?></span>
