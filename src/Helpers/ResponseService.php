<?php 

namespace Anturi\Larastarted\Helpers;


use Exception;

class ResponseService 
{

  public function responseGet($data)
  {
    return \Illuminate\Support\Facades\Response::json($data,200);
  }
  public function responseDelete($modelo)
  {
    $message = [ "message" => "El registro de $modelo se ha eliminado exitosamente", ];
    return \Illuminate\Support\Facades\Response::json($message,202);
  }

  public function responseNotFound($modelo)
  {
    $message = [ "message" => "Error! registro de $modelo no Existe", ];
    return \Illuminate\Support\Facades\Response::json($message,404);
  }
  public function responseCreate(string $modelo, mixed $data)
  {
    $message = [ 'message' => "Registro $modelo creado exitosamente", 'data' => $data ];
    return \Illuminate\Support\Facades\Response::json($message, 201);
  }


  public function responseImport($modelo, $data)
  {
    $message = [
      'message' => "Registros de $modelo importados exitosamente",
      'data' => $data,
    ];
    return \Illuminate\Support\Facades\Response::json($message, 201);
  }
  public function responseUpdate($modelo, $data = null)
  {
    $message = [
      'message' => "Registro $modelo actualizado exitosamente",
      'data' => $data
    ];
    return \Illuminate\Support\Facades\Response::json($message,);
  }
  public function responseError(Exception $error)
  {
    if(env('LOG_LEVEL') =='debug'){
      return \Illuminate\Support\Facades\Response::json([
        $error
      ], 500);
    }
    return \Illuminate\Support\Facades\Response::json([
      'message' => "Ups, algo salió mal",
    ], 500);
  }
  public function responseErrorUser($error){
    return \Illuminate\Support\Facades\Response::json([
      'message' => $error
    ], 400);
  }
}
