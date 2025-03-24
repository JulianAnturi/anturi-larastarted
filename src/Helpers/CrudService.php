<?php 

namespace Anturi\Larastarted\Helpers;

use Illuminate\Support\Facades\DB;

class CrudService extends ResponseService
{

    public static function index($model, $request)
    {
        $data = $model::paginate($request->input('limit',20));
        return ResponseService::responseGet($data);
    }

    public static function store($model,$request,$name){
        $data = $model::create($request);
        return ResponseService::responseCreate($name, $data);
    }

    public static function update($id,$table,$data, $responseName)
    {

        $query = DB::table($table)->where('id',$id );
        $query->update($data);
        $result = $query->first();
        return ResponseService::responseUpdate($responseName, $result);
    }

    public static function destroy($id,$model, $responseName)
    {
            $model->destroy($id);
            return ResponseService::responseDelete($responseName);
    }

    public static function show($id,$model){
       $data = $model->find($id);
       return ResponseService::responseGet($data);

    }
}

