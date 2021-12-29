<?php
  class Db extends \PDO{
    private static $instancia;

    static function gInst() {

      if(!isset( self::$instancia )){
        try {
          self::$instancia = new Db(
            $GLOBALS['dbconfig'], 
            $GLOBALS['dbauthenticate']['user'], 
            $GLOBALS['dbauthenticate']['pass'], 
            array(
              \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"
            )
          );
        } catch ( Exception $e ) {
          echo 'Erro ao conectar: '.$e; exit ();
        }
      }
      return self::$instancia;
    }
      
  }