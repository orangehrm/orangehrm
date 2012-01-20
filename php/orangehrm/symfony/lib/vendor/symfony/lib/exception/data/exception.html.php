<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title><?php echo $name ?>: <?php echo htmlspecialchars($message, ENT_QUOTES, sfConfig::get('sf_charset', 'UTF-8')) ?></title>
  <style type="text/css">
  body { margin: 0; padding: 20px; margin-top: 20px; background-color: #eee }
  body, td, th { font: 11px Verdana, Arial, sans-serif; color: #333 }
  a { color: #333 }
  h1 { margin: 0 0 0 10px; padding: 10px 0 10px 0; font-weight: bold; font-size: 120% }
  h2 { margin: 0; padding: 5px 0; font-size: 110% }
  ul { padding-left: 20px; list-style: decimal }
  ul li { padding-bottom: 5px; margin: 0 }
  ol { font-family: monospace; white-space: pre; list-style-position: inside; margin: 0; padding: 10px 0 }
  ol li { margin: -5px; padding: 0 }
  ol .selected { font-weight: bold; background-color: #ddd; padding: 2px 0 }
  table.vars { padding: 0; margin: 0; border: 1px solid #999; background-color: #fff; }
  table.vars th { padding: 2px; background-color: #ddd; font-weight: bold }
  table.vars td  { padding: 2px; font-family: monospace; white-space: pre }
  p.error { padding: 10px; background-color: #f00; font-weight: bold; text-align: center; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; }
  p.error a { color: #fff }
  #main { padding: 30px 40px; border: 1px solid #ddd; background-color: #fff; text-align:left; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; min-width: 770px; max-width: 770px }
  #message { padding: 10px; margin-bottom: 10px; background-color: #eee; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; }
  a.file_link { text-decoration: none; }
  a.file_link:hover { text-decoration: underline; }
  .code, #sf_settings, #sf_request, #sf_response, #sf_user, #sf_globals { overflow: auto; }
  </style>
  <script type="text/javascript">
  function toggle(id)
  {
    el = document.getElementById(id); el.style.display = el.style.display == 'none' ? 'block' : 'none';
  }
  </script>
</head>
<body>
  <center><div id="main">
  <div style="float: right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAZCAYAAAAiwE4nAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEfklEQVRIx7VUa0wUVxT+Znd2FxZk0YKACAtaGwEDUhUTBTEIItmKYk3UNqalD7StMSQ1JKatP5omTYyx0VRrjPERX7XWAG2t9GVi3drU2h+gi4BCWV67lOe6O/uYmXtPf0BRrMBK6UlObmbON9935p6HQEQI1o7uXeSy1dsjHn2Xlpr0oKzililoEiIKymvOr9q+pzyZZN894moHcbWDZN892lOeTN9fKHgrWB5NsInZ7joOrtv4JgR2F4r0AxTpRwisEes2bsNtW+eBYHmCEqw8kVsp6oy6jMUFYIoTxFUQqWBqNzIWr4aoC9NVnlxZNSWC1mqLsa6ubd36zbug+m3gXBlypoCYAuavx4Ytu1Fbay+2VluME/GJEwHsnT3WpLlzhbi4Z6D46gBosP/gVQDA669kIzJSRWxcApLnPie0dw3cALBw0k1z5dyKrIqyWHL1/Eye7n3kcX5MH75fRAAIAJUUZ5Cnez9JPYfI1XuDKsriqOZcbtakm6alte/yqsIi6LVt4KobxAIAqSPxwUEJxAPgqgcG0YH8NS+gxT5wZVI1/PrU0q1O54OoFfmvQZZsIBYA5zIy0maOYFZmJ4GYAuIyZG8jcvLfgMPhmnHlbG7pUws2NfUeWVvyMpj3d3DVB84C4MyPxNkP+8I0TQRn/qGY6gP316J4w6uob3AceirBzw9nnBD1RmN65nLIUhOIBUBcBjEZ5viQEZx5thFcdQ+50o+A5w7SM5dBFHWhFz5bdOpJ3MLjq63mdHrIr7f6PaXbPtBGht4DUwYAQXikyVTkb/gKtbYBNFpzYYoY3egarR6D7jCcPmtly5ZEh6/ZWucfdyycPep3ycmJ2phoAzx9ziERLoMzN4hJAICI8KEkp4VxcCaP+p4zGdHTw2FOiNB2OTzfAMgf80qrjmem1zf256zf9B6kvmvgqgeqrw2qvx1cGQRxBcQV5GRFIGepaeT5cfdJXbAUPY+79z15l47MWzDmH7a3P/g2Ly9X4O6LkKUWEPeOMbwMpnANiClPDkOBXteL3OXxQnNL72UA5n/V8NLR9Bdrb/ddLN+5VvD23wTA8d9MgNH0LD759DrS5oeUbN7RWjXqSu//OXi8sCBFkN11IFJAxMZ0e4cP12+6xsUQqZC9nShclYTWtsDJUTU8cyDlsE7URqTMC4Eiu8fN+/JVF7I3NuGlna2wlDaPi1VkN1LnR0GvF00n95kPAICm+tgcQ9N9V5ll9Tz4JSem2vySE5bCFDS3+t+uPjbHIA64dF/MioU2aoYGXndgQgJLngnWL0PR1iUje0n4hHimBhA1XYA5IVz8q1eu0oSGqCc6HV4ihAIQgso6MV4flNhDUR/iYqbBI1GqZtM7zVUzZ4p3rl5rQIgxesqvVCsa0O8y4Lc/nGp8rLhcBIA7Df7C7hlKe2ZGojYmZsGUCsqygvOnf6FZsbrtm3bY+wUigiAIC/funlXR0RXYgv/BzAmGn979qGvXyOALghAJQAtAB0A/fIrDY6MNurj/LBqADW8OFYACQB4+2d80or7Ra0ZtxAAAAABJRU5ErkJggg==" /></div>
  <h1><?php echo $code ?> | <?php echo $text ?> | <?php echo $name ?></h1>
  <h2 id="message"><?php echo str_replace("\n", '<br />', htmlspecialchars($message, ENT_QUOTES, sfConfig::get('sf_charset', 'UTF-8'))) ?></h2>
  <h2>stack trace</h2>
  <ul><li><?php echo implode('</li><li>', $traces) ?></li></ul>

  <h2>symfony settings <a href="#" onclick="toggle('sf_settings'); return false;">...</a></h2>
  <div id="sf_settings" style="display: none"><?php echo $settingsTable ?></div>

  <h2>request <a href="#" onclick="toggle('sf_request'); return false;">...</a></h2>
  <div id="sf_request" style="display: none"><?php echo $requestTable ?></div>

  <h2>response <a href="#" onclick="toggle('sf_response'); return false;">...</a></h2>
  <div id="sf_response" style="display: none"><?php echo $responseTable ?></div>

  <h2>user <a href="#" onclick="toggle('sf_user'); return false;">...</a></h2>
  <div id="sf_user" style="display: none"><?php echo $userTable ?></div>

  <h2>global vars <a href="#" onclick="toggle('sf_globals'); return false;">...</a></h2>
  <div id="sf_globals" style="display: none"><?php echo $globalsTable ?></div>

  <p id="footer">
    symfony v.<?php echo SYMFONY_VERSION ?> - php <?php echo PHP_VERSION ?><br />
    for help resolving this issue, please visit <a href="http://www.symfony-project.org/">http://www.symfony-project.org/</a>.
  </p>
  </div></center>
</body>
</html>
