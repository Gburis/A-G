<?php
  use Kreait\Firebase\Factory;
  use Kreait\Firebase\Messaging\CloudMessage;
  use Kreait\Firebase\Messaging\Message;

  class FireBase{
    public static function insertAndUpdateDevice($data){
      try {
        $sql = "SELECT `id` FROM `device_token` WHERE user_id = :id";
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([ ':id' => $data['id'] ]); 
        $device = $stmt->fetch();
  
        if(!$device){ // se não houver dispostivo cadastrado
          $sql = "INSERT INTO `device_token`(`user_id`, `token`, `dt_register`) VALUES (?, ?, ?)";
          $stmt= Db::gInst()->prepare($sql);

          $stmt->execute([ $data['id'], $data['token'], $data['date']]);

          return Utils::resp(true, ["msg" => 'Dispositivo do usuario adicionado com exito!']);

        }else if($device && $device['token'] != $data['token']){ // caso o dispositivo mude
          $sql = 'UPDATE `device_token` SET `token` = :token  WHERE `user_id` = :id';
          $stmt= Db::gInst()->prepare($sql);
          $stmt->execute([ 'token' => $data['token'], ':id'=> $data['id'] ]);

          return Utils::resp(true, ["msg" => 'Dispositivo do usuario adicionado com exito!']);

        }else{
          return Utils::resp(true, ["msg" => 'Dispositivo valido!']);
        }

      } catch (\Throwable $th) {
        error_log('ERRO AO GERAR NOTIFICAÇÃO => '.$th);
        return Utils::resp(false, json_decode($th));
      }
    }

    public static function notification($data){
        try {
          $factory = (new Factory)
            ->withServiceAccount($_SERVER["DOCUMENT_ROOT"].'firebase_credentials.json')
            ->withDatabaseUri('firebase-adminsdk-luk76@lista-casamento-db10c.iam.gserviceaccount.com');

            $messaging = $factory->createMessaging();

            $sql = "SELECT u.id, u.nome, u.username, u.nick, d.token FROM users AS u INNER JOIN device_token AS d ON u.id = d.user_id WHERE u.id != :id";
            $stmt= Db::gInst()->prepare($sql);
            $stmt->execute([ ':id' => $data['id'] ]); 
            $device = $stmt->fetch();

            $deviceToken = $device['token'];
            
            if($data['produto']){
              $notification = [
                "title" => "A&G Novo Item",
                "body" => 'Olá, '.$device['nick'].'! '.$data['nome']." adicionou ".$data['produto']
              ];
            }

            if($data['convidado']){
              $notification = [
                "title" => "Convidados",
                "body" => 'Olá, '.$device['nick'].'! '.$data['nome']." adicionou ".$data['convidado']." a lista"
              ];
            }

            if($data['envite']){
              $notification = [
                "title" => "Convidados",
                "body" => 'Olá, '.$device['nick'].'! '.$data['nome']." Já convidou ".$data['envite']
              ];
            }
            
            $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);
            
            $messaging->send($message);

          return Utils::resp(true, ["msg" => 'Notificação gerada com exito!']);
        } catch (\Throwable $th) {
          error_log('ERRO AO GERAR NOTIFICAÇÃO => '.$th);
          return Utils::resp(false, $th);
        }
    }
  }