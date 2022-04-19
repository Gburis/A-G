<?php
  class Convidados {
		public static function save($data){
			try {
        $sql = "INSERT INTO `convidados`(`nome`, `por`) VALUES (?,?)";
        $stmt= Db::gInst()->prepare($sql);

				$stmt->execute([ $data['nome'], $data['por'] ]);

				return Utils::resp(true, ["msg" => $data['nome'].' Adicionada(o) com exito!']);

			} catch (\Throwable $th) {
				error_log('Erro ao salvar =>'.$th);
        return Utils::resp(false, $th);
			}
		}

		public static function list(){
			try {
				$sql = "SELECT * FROM `convidados` WHERE excluido = :exc";
        $stmt= Db::gInst()->prepare($sql);
        $exe = [':exc' => 0 ];
        $stmt->execute($exe); 
        $convidados = $stmt->fetchAll();

				return Utils::resp(true, $convidados);
			} catch (\Throwable $th) {
				error_log('Erro ao listar convidados =>'.$th);
        return Utils::resp(false, $th);
			}
		}

		public static function delete($data){
      try {
        $sql = 'UPDATE `convidados` SET `excluido` = 1 WHERE `id` = :id';
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([':id' => $data['prd_id']]);

        return Utils::resp(true, ["msg" => 'Convidado removido com exito!']);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      }  
    }

		public static function sendEnvite($data){
      try {
        $sql = 'UPDATE `convidados` SET `envite` = :envite WHERE `id` = :id';
        $stmt= Db::gInst()->prepare($sql);
        $stmt->execute([':envite' => $data['envite'], ':id' => $data['id']]);

        return Utils::resp(true, ["msg" => 'Convite enviado']);
      } catch (\Throwable $th) {
        return Utils::resp(false, $th);
      }  
    }
	}