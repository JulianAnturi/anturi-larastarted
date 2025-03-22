<?php


namespace Anturi\Larastarted\Controllers;

// use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Anturi\Larastarted\Helpers\ResponseService;
use Anturi\Larastarted\Helpers\LogService;
use Anturi\Larastarted\Helpers\CrudService;
use Illuminate\Support\Facades\DB;
use Exception;

class BaseController extends Controller
{

  protected $model;
  protected $formRequest;
  protected $class = 'CommentsController'; //This is variable
  protected $responseName = 'Comentario'; //This is variable
  protected $table = 'comments'; //This is variable
  protected $CrudService;
  protected $responseService;

  public function __construct( $model, $formRequest = null, CrudService $crudService, ResponseService $responseService )
  {
    $this->responseService = $responseService;
    $this->model = $model;
    $this->CrudService = $crudService;
    $this->formRequest = $formRequest;
  }

  /*
   * index show all created model
   *
   */
  public function antIndex(Request $request)
  {
    try
    {
      return $this->CrudService->index($this->model, $request);
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return $this->responseService->responseError($e);

    }
  }

  /*
   * Store a model on DataBase
   */
  public function antStore(Request $request)
  {
    try
    {
      $this->validate($request);
      $this->CrudService->store($this->model, $request, $this->responseName);
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return $this->responseService->responseError($e);
    }
  }

  /*
   * Update a record from database
   */
  public function antUpdate(Request $request, $id){
    try
    {
      $this->validate($request);
      $data = $request->all();
      $this->CrudService->update($id, $this->table,$data,$this->responseName );
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return $this->responseService->responseError($e);
    }
  }

  /**
   * Destroy an element from database
   *
   **/
  public function antDestroy($id)
  {
    try
    {
      $this->CrudService->destroy($id,$this->model, $this->responseName);
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return $this->responseService->responseError($e);
    }
  }

  public function antSelect($table, $id, $field = 'name')
  {
    return DB::table($table)->get([$id,$field]);
  }

  public function antsubSelect($table,$tableId = 'id', $parentTable, $parentTableId = 'id', $parentIdValue, $field){
    return DB::table($table . ' as f')
      ->join($parentTable . ' as s','f.'.$tableId,'=', 's.'.$parentTableId)
      ->where('f.'. $field, '=', $parentIdValue,  )
      ->get();
  }



}


