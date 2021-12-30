<?php
  class Produtos {
    
    public static function get($data){
      try {
        $sql = "SELECT * FROM `produtos` WHERE tabs_id = :id AND excluido = :exc";
        $stmt= Db::gInst()->prepare($sql);;
        $stmt->execute([ ':id' => $data['id'], ':exc' => 0 ]); 
        $produtos = $stmt->fetchAll();

        return Utils::resp(true, $produtos);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      } 
    }

    public static function save($data){
      try {
        $ext = ""; 
        $sql = "INSERT INTO `produtos`(`tabs_id`, `nome`, `descricao`, `img`, `link`, `valor`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt= Db::gInst()->prepare($sql);
        // Convertendo base 64 e imagem
        $image = $data['img'];
        error_log('TYPE :'.$data['type_img']);
        $image = str_replace('data:'.$data['type_img'].';base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $image = base64_decode($image);

        switch ($data['type_img']) {
          case 'image/gif':
            $ext = ".gif";
          break;
          case 'image/jpeg':
            $ext = ".jpg";
          break;
          case 'image/png':
            $ext = ".png";
          break;
          default:
            throw new Exception("Formato de arquivo não permitido!", 1);
          break;
        }

        $new_name = 'image'.date("Y.m.d-H.i.s").$ext;
        error_log('NAME :'.$new_name);
        file_put_contents('./upload-imgs/'.$new_name, $image);

        $stmt->execute([ $data['tab_id'], $data['nome'], $data['desc'], $new_name, $data['link'], $data['valor']]);

        return Utils::resp(true, ["msg" => $data['nome'].' Adicionada(o) com exito!']);
      } catch (\Throwable $th) {
        error_log('ERRO AO CADASTRAR PRODUTOS => '.$th);
        return Utils::resp(false, $th);
      } 
    }

    public static function delete($data){
      try {
        $sql = 'UPDATE `produtos` SET `excluido` = 1 WHERE `_id` = :id';
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([':id' => $data['prd_id']]);

        return Utils::resp(true, ["msg" => 'Produto excluído com exito!']);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      }  
    }
  }

  