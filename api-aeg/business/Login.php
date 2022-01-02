<?php
  class Login{
    public static function auth($data){
      try {
        if(!$data['username'] || !$data['password']) return Utils::resp(false, ['msg' => 'Usuario invalido']);

        $sql = "SELECT `id`, `nome`, `password`, `username` FROM `users` WHERE username = :user";
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([ ':user' => $data['username'] ]); 
        $user = $stmt->fetch();

        if($user){
          if($user['password'] != $data['password']) return Utils::resp(false, ['msg' => 'Senha invalida']);

          $token = Utils::generateToken($user['username']);

          $userLogado = [ 'nome' => $user['nome'], 'id' => $user['id'], 'token' => $token ];

          return Utils::resp(true, [ 'msg' => 'Login realizado', 'user' => $userLogado ]);
        }else{
          return Utils::resp(false, ['msg' => 'Usuario invalido']);
        }
      } catch (\Throwable $th) {
        error_log('Erro ao realizar login =>'.$th);
        return Utils::resp(false, $th);
      }
    }
  }