<?php


namespace Anturi\Larastarted\Helpers;

use Anturi\Larastarted\Models\Log; 
use Anturi\Larastarted\Helpers\ResponseService;

class LogService extends ResponseService
{

  public static function catchError($e, $app, $fileName = null, $line = null)
  {
      $code = $e->getCode();
      $message = $e->getMessage();

      $log = Log::create([
          'message'=> $message,
          'statusCode'=> $code,
          'filename' => $fileName,
          'app' => $app,
          'line' => $line
      ]);
      return self::responseCreate('log', $log);
  }
}
