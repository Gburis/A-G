<?php
  class Utils {
    public static function resp($status, $dados, $values = false){
      return [
        "status" => $status,
        "data" => $dados,
        "values" => $values
      ];
    }
  }