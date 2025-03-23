<?php 

namespace Anturi\Larastarted\Helpers;


use Exception;

class ResponseService 
{

  public static function responseGet($data)
  {
    return \Illuminate\Support\Facades\Response::json($data,200);
  }
  public static function responseDelete($modelo)
  {
    $message = [ "message" => "El registro de $modelo se ha eliminado exitosamente", ];
    return \Illuminate\Support\Facades\Response::json($message,202);
  }

  public static function responseNotFound($modelo)
  {
    $message = [ "message" => "Error! registro de $modelo no Existe", ];
    return \Illuminate\Support\Facades\Response::json($message,404);
  }
  public static function responseCreate(string $modelo, mixed $data)
  {
    $message = [ 'message' => "Registro $modelo creado exitosamente", 'data' => $data ];
    return \Illuminate\Support\Facades\Response::json($message, 201);
  }


  public static function responseImport($modelo, $data)
  {
    $message = [
      'message' => "Registros de $modelo importados exitosamente",
      'data' => $data,
    ];
    return \Illuminate\Support\Facades\Response::json($message, 201);
  }
  public static function responseUpdate($modelo, $data = null)
  {
    $message = [
      'message' => "Registro $modelo actualizado exitosamente",
      'data' => $data
    ];
    return \Illuminate\Support\Facades\Response::json($message,);
  }
  public static function responseError(Exception $error)
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
  public static function responseErrorUser($error){
    return \Illuminate\Support\Facades\Response::json([
      'message' => $error
    ], 400);
  }
}
