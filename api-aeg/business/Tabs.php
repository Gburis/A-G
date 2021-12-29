<?php
  class Tabs{
    public static function get(){
      try {
        $tabs = Db::gInst()->query("SELECT * FROM tabs")->fetchAll();
        return Utils::resp(true, $tabs);
      } catch (\Throwable $th) {
        error_log('ERRO AO CARREGAR TABS => '.$th);
        return Utils::resp(false, $th);
      }
    }
    public static function save($data){
      try {
        $sql = "INSERT INTO `tabs`(`name`) VALUES (?)";
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([ $data['nome'] ]);

        return Utils::resp(true, ["msg" => $data['nome'].' Adicionada(o) com exito!']);
      } catch (\Throwable $th) {
        error_log('ERRO AO CADASTRAR TABS => '.$th);
        return Utils::resp(false, $th);
      } 
    }

  }