<ul id="movies">
  <?php foreach ($movies as $movie): ?>
    <li class="default"><?php echo $movie->getTitle() ?></li>
    <li class="fr"><?php echo $movie->getTitle('fr') ?></li>
    <li class="it"><?php echo $movie->getTitle('it') ?></li>
  <?php endforeach; ?>
</ul>
