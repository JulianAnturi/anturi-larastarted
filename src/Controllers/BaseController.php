<?php


namespace Anturi\Larastarted\Controllers;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Anturi\Larastarted\Helpers\ResponseService;
use Anturi\Larastarted\Helpers\LogService;
use Anturi\Larastarted\Helpers\CrudService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Routing\Controller ;

class BaseController extends Controller
{

  use AuthorizesRequests, ValidatesRequests;

  protected $model;
  protected $class; //This is variable
  protected $responseName; //This is variable
  protected $table; //This is variable

  public function __construct( $model, $class, $responseName, $table) {
    $this->model = $model;
    $this->class = $class;
    $this->responseName = $responseName;
    $this->table = $table;
  }

  /*
   * index show all created model
   *
   */
  public function antIndex(Request $request)
  {
    try
    {
      return CrudService::index($this->model, $request);
    }catch(Exception $e)
    {
      // LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return ResponseService::responseError($e);

    }
  }

  /*
   * Store a model on DataBase
   */
  public function antStore(array $request)
  {
    try
    {
     return CrudService::store($this->model, $request, $this->responseName);
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return ResponseService::responseError($e);
    }
  }

  /*
   * Update a record from database
   */
  public function antUpdate( $request, $id){
    try
    {
      return CrudService::update($id, $this->table,$request,$this->responseName );
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return ResponseService::responseError($e);
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
     return  CrudService::destroy($id,$this->model, $this->responseName);
    }catch(Exception $e)
    {
      LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return ResponseService::responseError($e);
    }
  }

  public function antShow($id,$model){
    try{

    return CrudService::show($id,$model);

    }catch(Exception $e)
    {
      // LogService::catchError($e,env('APP_NAME'),$this->class,__LINE__);
      return ResponseService::responseError($e);
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


