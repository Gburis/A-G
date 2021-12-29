<?php
  include_once('./controllers/auto-load.php');

  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: *');
  header("Access-Control-Allow-Headers: *");
  header('Content-Type: application/json; charset=utf-8');

  try {
    $GLOBALS["rurl"] = $_SERVER["REQUEST_URI"];
    
    //regex para checar se a url é valida e se é o metodo permitido para a operação
    function route($method, $url){ return preg_match("/^\/api\/$url$/", $GLOBALS["rurl"]) > 0 && $_SERVER['REQUEST_METHOD'] == $method;}

    $data = json_decode(file_get_contents('php://input'), true);

    if(!$rurl) throw new Exception("URL não informada", 1);

    // redireconar url para suas devidas funções 
    switch ($rurl) {
      //Tabs
      case (route('POST','cadastrar-tabs')): echo json_encode(Tabs::save($data)); break;
      case (route('GET', 'listar-tabs')): echo json_encode(Tabs::get()); break;
      //Produtos
      case (route('POST','cadastrar-produtos')): echo json_encode(Produtos::save($data)); break;
      case (route('POST', 'listar-produtos')): echo json_encode(Produtos::get($data)); break;
      case (route('POST', 'deletar-produto')): echo json_encode(Produtos::delete($data)); break;
      default : echo "not foud"; break;
    }

  } catch (\Throwable $th) {
    error_log("ROUTES: ".$th);
    echo "not foud";
  }
?>