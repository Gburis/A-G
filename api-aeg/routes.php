<?php
  include_once('./controllers/auto-load.php');

  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: *');
  header("Access-Control-Allow-Headers: *");
  header('Content-Type: application/json; charset=utf-8');

  try {
    $GLOBALS["rurl"] = $_SERVER["REQUEST_URI"];
    
    //regex para checar se a url é valida e se é o metodo permitido para a operação
    function route($method, $url, $auth = false){
      $isUrl = preg_match("/^\/api\/$url$/", $GLOBALS["rurl"]) > 0 && $_SERVER['REQUEST_METHOD'] == $method;
      if($isUrl && !$auth) Utils::authToken(); 
      return $isUrl;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if(!$rurl) throw new Exception("URL não informada", 1);

    // redireconar url para suas devidas funções 
    switch ($rurl) {
      //Login
      case (route('POST','login', true)): echo json_encode(Login::auth($data)); break;
      case (route('GET','check-auth', true)): echo json_encode(Utils::authToken()); break;
      //Tabs
      case (route('POST','cadastrar-tabs')): echo json_encode(Tabs::save($data)); break;
      case (route('GET', 'listar-tabs')): echo json_encode(Tabs::get()); break;
      //Produtos
      case (route('POST','cadastrar-produtos')): echo json_encode(Produtos::save($data)); break;
      case (route('POST', 'listar-produtos')): echo json_encode(Produtos::get($data)); break;
      case (route('POST', 'editar-produtos')): echo json_encode(Produtos::update($data)); break;
      case (route('POST', 'deletar-produto')): echo json_encode(Produtos::delete($data)); break;
      case (route('POST', 'marcar-compra')): echo json_encode(Produtos::comprado($data)); break;
      default : echo json_encode( Utils::resp(false, ["msg"=>"not foud"])); break;
    }

  } catch (\Throwable $th) {
    error_log("ROUTES: ".strval($th));
    header('HTTP/1.0 400 Bad Request');
    echo json_encode( Utils::resp(false, [ "err" => $th->getMessage() ]));
  }
?>