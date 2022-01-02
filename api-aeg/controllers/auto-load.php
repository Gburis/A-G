<?php
  declare(strict_types=1);
  require_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
  
  include_once($_SERVER["DOCUMENT_ROOT"].'/config.php');

  spl_autoload_register(
    function ($class){
      $file = $_SERVER['DOCUMENT_ROOT']."/api-aeg/business/$class.php";
      if(file_exists($file)) require_once $file;
    }
  );
?>