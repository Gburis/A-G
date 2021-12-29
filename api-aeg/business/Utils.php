<?php
  class Utils {
    public static function resp($status, $dados){
      return [
        "status" => $status,
        "data" => $dados
      ];
    }
  }