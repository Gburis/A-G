<?php
  class Produtos {
    
    public static function get($data){
      try {
        // Caso seja a tela de comprados carregar todos os itens para o calculo
        $sql = $data['comprado'] 
        ? "SELECT * FROM `produtos` WHERE excluido = :exc"
        : "SELECT * FROM `produtos` WHERE tabs_id = :id AND excluido = :exc";

        $stmt= Db::gInst()->prepare($sql);

        $exe = $data['comprado']  ?  [':exc' => 0 ] : [ ':id' => $data['id'], ':exc' => 0 ];

        $stmt->execute($exe); 
        $produtos = $stmt->fetchAll();

        $valores = false;

        // filtrar todos os itens
        if($data['comprado']){
          $inComprados =  array_values(array_filter($produtos, function($prd){return($prd['comprado'] == "0");}));

          $isComprados =  array_values(array_filter($produtos, function($prd){return($prd['comprado'] == "1");}));

          $vlr_isComprados = array_map(function($prd){return $prd['valor'];}, $isComprados);

          $vlr_inComprados = array_map(function($prd){return $prd['valor'];}, $inComprados);

          // somar valores
          $vlr_isComprados = sizeof($vlr_isComprados) > 0 
            ? array_reduce($vlr_isComprados, function($total, $somar){return $total + $somar;})
            : 0;

          $vlr_inComprados = sizeof($vlr_inComprados) > 0 
            ? array_reduce($vlr_inComprados, function($total, $somar){return $total + $somar;})
            : 0;

          $valores = ['comprar' => $vlr_inComprados, 'comprados' =>  $vlr_isComprados];
          $produtos = $isComprados;
        }

        return Utils::resp(true, $produtos, $valores);
      } catch (\Throwable $th) {
        error_log('Erro ao carregar prodtuos => '.$th->getMessage());
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

    public static function comprado($data){
      try {
        $sql = 'UPDATE `produtos` SET `comprado` = :sts WHERE `_id` = :id';
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([':sts' => $data['status'], ':id' => $data['prd_id']]);

        return Utils::resp(true, ["msg" => 'Produto marcado com exito!']);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      }  
    }

    public static function update($data){
      try {
        $sql = 'UPDATE `produtos` SET `tabs_id`= :tab, `nome`= :nome, `descricao`= :desc, `link`= :link, `valor`= :vlr WHERE `_id` = :id';
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([
          ':tab'  => $data['tab'], 
          ':nome' => $data['nome'],
          ':desc' => $data['descricao'],
          ':link' => $data['link'],
          ':vlr'  => $data['valor'],
          ':id'   => $data['_id']
        ]);

        return Utils::resp(true, ["msg" => 'Produto editado com exito!']);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      }  
    }
  }

  