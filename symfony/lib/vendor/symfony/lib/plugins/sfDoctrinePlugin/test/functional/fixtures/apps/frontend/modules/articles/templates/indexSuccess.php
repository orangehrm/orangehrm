<h1>Articles List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Author</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($articleList as $article): ?>
    <tr>
      <td><a href="<?php echo url_for('articles/edit?id='.$article->get('id')) ?>"><?php echo $article->getid() ?></a></td>
      <td><?php echo $article->getAuthor() ?></td>
      <td><?php echo $article->getcreated_at() ?></td>
      <td><?php echo $article->getupdated_at() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('articles/create') ?>">Create</a>
