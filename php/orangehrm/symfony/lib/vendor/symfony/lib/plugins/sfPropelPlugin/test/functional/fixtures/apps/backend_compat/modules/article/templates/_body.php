<?php if ('list' == $type): ?>
before <?php echo $article->getBody() ?> after
<?php elseif ('edit' == $type): ?>
<textarea name="article[body]" id="article_body">before <?php echo $article->getBody() ?> after</textarea>
<?php elseif ('filter' == $type): ?>
<input type="text" name="body_filter" value="before after" />
<?php else: ?>
SOMETHING WRONG HAPPENED
<?php endif; ?>
