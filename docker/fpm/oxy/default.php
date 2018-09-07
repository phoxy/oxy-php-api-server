<?php

ob_start();
function append_warnings_to_object($that)
{
  if (phoxy_conf()["debug_api"] && !phoxy_conf()["is_ajax_request"])
    return;

  $buffer = ob_get_contents();
  ob_end_clean();

  if (!empty($buffer))
    $that->obj["warnings"] = $buffer;

  global $phoxy_config;
  if (!$phoxy_config["debug_api"])
    unset($that->obj["warnings"]);
}


require_once('phoxy_return_worker.php');
$before_return_hooks =
[
  "append_warnings_to_object",
];

phoxy_return_worker::$add_hook_cb = function($that)
{
  global $before_return_hooks;
  $that->hooks = array_merge($before_return_hooks, $that->hooks);
};

function conf()
{
  global $config;

  if (!$config)
    $config = new \phpa2o\phpa2o(yaml_parse(file_get_contents('app/conf.yaml')));

  return $config;
}


error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);


require_once('app/oxy/init.php');
require_once('config.php');
require_once('include.php');

LoadModule('oxy', 'phoxy');
require_once('app/oxy/run.php');
