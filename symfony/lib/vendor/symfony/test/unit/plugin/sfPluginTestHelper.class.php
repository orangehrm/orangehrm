<?php 

class sfPluginTestHelper
{
  static public function convertUrlToFixture($url)
  {
    $file = preg_replace(array('/_+/', '#/+#', '#_/#'), array('_', '/', '/'), preg_replace('#[^a-zA-Z0-9\-/\.]#', '_', $url));

    $dir  = dirname($file);
    $file = basename($file);

    $dest = SF_PLUGIN_TEST_DIR.'/'.$dir.'/'.$file;

    if (!is_dir(dirname($dest)))
    {
      mkdir(dirname($dest), 0777, true);
    }

    if (!file_exists(dirname(__FILE__).'/fixtures/'.$dir.'/'.$file))
    {
      throw new sfException(sprintf('Unable to find fixture for %s (%s)', $url, $file));
    }

    copy(dirname(__FILE__).'/fixtures/'.$dir.'/'.$file, $dest);

    return $dest;
  }
}
