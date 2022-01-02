<?php
  use Firebase\JWT\JWT;

  class Utils {
    public static function generateToken($username){
      try {
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+1 day')->getTimestamp();

        $data = [
          'iat'  => $issuedAt->getTimestamp(),
          'iss'  => "gburis.com.br",
          'nbf'  => $issuedAt->getTimestamp(),
          'exp'  => $expire,
          'userName' => $username
        ];

        return JWT::encode(
          $data,
          $GLOBALS['secretKey'],
          'HS512'
        );

      } catch (\Throwable $th) {
        error_log('Erro ao gerar token =>'.$th);
        throw $th;
      }
    }

    public static function authToken(){
      try {
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
          throw new Exception('Unauthorized');
        }

        $jwt = $matches[1];
        if (!$jwt) {
          throw new Exception('Unauthorized');
        }

        $token = JWT::decode($jwt, $GLOBALS['secretKey'], ['HS512']);
        $now = new DateTimeImmutable();

        if ($token->iss !== "gburis.com.br" || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp()){
          throw new Exception('Unauthorized');
        }
      } catch (\Throwable $th) {
        throw $th;
      }
    }


    public static function resp($status, $dados, $values = false){
      $return = [
        "success" => $status,
        "data" => $dados
      ];

      if($values) $return['values'] = $values;

      return $return;
    }
  }