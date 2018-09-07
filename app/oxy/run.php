<?php


/* Module preload if  required:
phoxy::Load("db/db");
*/


$route = $_SERVER['DOCUMENT_URI'];
$data = array_merge($_GET, $_POST);

try {
  if (phoxy::Config()['autostart'])
    phoxy::Start('/app' . $route);
} catch (phoxy_protected_call_error $e)
{
  var_dump($e);
  die('ERROR');
}
