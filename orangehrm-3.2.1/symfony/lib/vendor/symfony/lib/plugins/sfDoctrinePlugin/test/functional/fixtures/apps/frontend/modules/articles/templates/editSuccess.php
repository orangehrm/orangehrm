<?php $article = $form->getObject() ?>
<h1><?php echo $form->isNew() ? 'New' : 'Edit' ?> Articles</h1>

<form action="<?php echo url_for('articles/update'.(!$form->isNew() ? '?id='.$article->get('id') : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('articles/index') ?>">Back to list</a>
          <?php if (!$form->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'articles/delete?id='.$article->get('id'), array('post' => true, 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><label for="article_author_id">Author id</label></th>
        <td>
          <?php echo $form['author_id']->renderError() ?>
          <?php echo $form['author_id'] ?>
        </td>
      </tr>
      <tr>
        <th><label for="article_created_at">Created at</label></th>
        <td>
          <?php echo $form['created_at']->renderError() ?>
          <?php echo $form['created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><label for="article_updated_at">Updated at</label></th>
        <td>
          <?php echo $form['updated_at']->renderError() ?>
          <?php echo $form['updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><label for="article_en">En</label></th>
        <td>
          <?php echo $form['en']->renderError() ?>
          <?php echo $form['en'] ?>
        </td>
      </tr>
      <tr>
        <th><label for="article_fr">Fr</label></th>
        <td>
          <?php echo $form['fr']->renderError() ?>
          <?php echo $form['fr'] ?>

        <?php echo $form['id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
