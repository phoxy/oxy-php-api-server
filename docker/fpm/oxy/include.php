<?php

function InstanceClassByName($classname, $args)
{
  $reflection = new \ReflectionClass($classname);
  $obj = $reflection->newInstanceArgs($args);
  $obj->phoxy_api_init();
  return $obj;
}

$_phoxy_loaded_classes = [];
function IncludeModule( $dir, $module )
{
  $args = [];
  if (is_array($module))
  {
    $args = $module[1];
    $module = $module[0];
  }

  if (substr($dir, 0, 2) === './')
    $dir = substr($dir, 2);

  $module_file = str_replace('\\', '/', $module);
  $file = "{$dir}/{$module_file}.php";


  include_once(__DIR__ . "/api.php");
  try
  {
    phoxy_protected_assert(stripos($file, "..") === false, 'File name contains parent directory access');
    phoxy_protected_assert(file_exists($file), 'File doesnt exists');

    global $phoxy_loading_module;
    $phoxy_loading_module = $module;

    global $_phoxy_loaded_classes;

    if (isset($_phoxy_loaded_classes[$dir][$module]))
      return $_phoxy_loaded_classes[$dir][$module];

    $classname = $module;
    $cross_include = class_exists($classname);


    if ($cross_include)
      include('virtual_namespace_helper.php');
    else
      include_once($file);

    phoxy_protected_assert(class_exists($classname), 'Class include failed. File do not carrying that');

    $obj = InstanceClassByName($classname, $args);
    phoxy_protected_assert($obj, 'Failure at object create');

    if (!isset($_phoxy_loaded_classes[$dir]))
      $_phoxy_loaded_classes[$dir] = [];
    $_phoxy_loaded_classes[$dir][$module] = $obj;

    return $obj;
  }
  catch (phoxy_protected_call_error $e)
  {
    $e->result['classname'] = $module;
    $e->result['path'] = $dir;
    throw $e;
  }
  catch (Exception $e)
  {
    phoxy_protected_assert(false, 'Uncaught script exception at module load');
  }
}

function LoadModule( $dir, $module, $force_raw = false, $expect_simple_result = true )
{
  $obj = IncludeModule($dir, $module);
  return $obj->fork($force_raw, $expect_simple_result);
}
