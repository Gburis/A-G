<?php
  include_once($_SERVER["DOCUMENT_ROOT"].'/config.php');
  require_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');

  spl_autoload_register(
    function ($class){
      require_once $_SERVER['DOCUMENT_ROOT']."/api-aeg/business/$class.php";
    }
  );
?>