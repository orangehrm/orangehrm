<?php isset($path) || $path = null; ?>

<ul>
  <?php foreach($tree as $key => $leaf) : ?>
    <li>
    <?php if (is_array($leaf)) : ?>
      <?php 
        $wild = $path ? '-*' : '*';
        $path_cur = $path ? $path.'-'.$key : $key;
        echo '['.link_to(str_replace('-', '/', $path_cur), 'sfPhpunit/run?test='.$path_cur).']';
        echo '['.link_to('*', 'sfPhpunit/run?test='.$path_cur.$wild).']';
      ?> 
      <br />
      <?php include_partial('tree', array('tree' => $leaf, 'path' => $path_cur)) ?>
    <?php else : ?>
      <?php echo link_to(basename($leaf, '.php'), 'sfPhpunit/run?test='.str_replace('/', '-', $leaf)) ?>
    <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>