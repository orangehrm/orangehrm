<?php echo sprintf('<?xml version="1.0" encoding="%s" ?>', sfConfig::get('sf_charset', 'UTF-8'))."\n" ?>
<error code="<?php echo $code ?>" message="<?php echo $text ?>">
  <debug>
    <name><?php echo $name ?></name>
    <message><?php echo htmlspecialchars($message, ENT_QUOTES, sfConfig::get('sf_charset', 'UTF-8')) ?></message>
    <traces>
<?php foreach ($traces as $trace): ?>
        <trace><?php echo $trace ?></trace>
<?php endforeach; ?>
    </traces>
  </debug>
</error>
