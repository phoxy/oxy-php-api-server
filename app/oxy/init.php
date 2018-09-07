<?php

// Default config
function phoxy_conf()
{
  global $phoxy_config;

  if ($phoxy_config)
    return $phoxy_config;

  $phoxy_config = phoxy_default_conf();
  $phoxy_config["ip"] = "disabled for sake of caching";
  $phoxy_config["api_csrf_prevent"] = false;
  $phoxy_config["autostart"] = true;
  $phoxy_config["autoload"] = false;
  $phoxy_config["rethrow_phoxy_exception"] = true;
  $phoxy_config["ejs_dir"] = "assets/enjs";
  $phoxy_config["js_dir"] = "assets/js";
  $phoxy_config["api_dir"] = "app";
  $phoxy_config["api_prefix"] = "api";

  if (isset($_SERVER['HTTP_CF_VISITOR']))
    $phoxy_config["site"] = json_decode($_SERVER['HTTP_CF_VISITOR'])->scheme . "://" . $_SERVER['HTTP_HOST'];


  $phoxy_config["debug_api"] = false;

  if (preg_match('/.*localhost.*/i', $phoxy_config["site"]))
    $phoxy_config["debug_api"] = true;

  $ret["debug_api"] &= !$ret["is_ajax_request"];

  $phoxy_config["cache"] =
  [
    "global" => "1w",
  ];
  $phoxy_config["sync_cascade"] = false;

  return $phoxy_config;
}

// Default result data
function default_addons()
{
  $ret =
  [
    "result" => "canvas",
  ];

  return $ret;
}

$before_return_hooks[] = function /* user_sensitive */($that)
{
  global $USER_SENSITIVE;

  // If data depends on user
  if ($USER_SENSITIVE)
  {
    // Require auth script for each request
    if (!$that->obj['script'])
      $that->obj['script'] = ["auth.js"];

    // Session scope limit max local timeout
    if (isset(phoxy_return_worker::$minimal_cache['session']))
      $that->NewCache(['local' => phoxy_return_worker::$minimal_cache['session']]);
    // Global scope limit max session timeout
    if (isset(phoxy_return_worker::$minimal_cache['global']))
      $that->NewCache(['session' => phoxy_return_worker::$minimal_cache['global']]);

    // Global scope is forbidden
    $that->NewCache(['global' => 'no']);
  }
};

$before_return_hooks[] = function /* default_cache_timeouts */($that)
{
$that->NewCache('no');
return;
  // Do not cache any error
  if (!isset($that->obj["error"]))
    $that->NewCache('no');

  // Default app cache for data response 1 day
  if (isset($that->obj["data"]))
    $that->NewCache(['global' => '1d']);
  // If not data and no error default cache is 1 week
  else if (!isset($that->obj["error"]))
    $that->NewCache(['global' => '1w']);
};

$before_return_hooks[] = function /* error_log */($that)
{
  if (isset($that->obj['error']))
    error_log(json_encode($that->obj));
};
