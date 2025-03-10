<?php 

namespace anturi\larastarted\Helpers;



use Illuminate\Support\Facades\DB;

class CrudService extends ResponseService
{

    public function index($model, $request)
    {
        $data = $model::paginate($request->input('limit',20));
        return ResponseService::responseGet($data);
    }

    public function store($model,$request,$name){
        $data = $model::create($request);
        return ResponseService::responseCreate($name, $data);
    }

    public function update($id,$table,$data, $responseName)
    {

        $query = DB::table($table)->where('id',$id );
        $query->update($data);
        $result = $query->first();
        return ResponseService::responseUpdate($responseName, $result);
    }

    public function destroy($id,$model, $responseName)
    {
            $model->destroy($id);
            return ResponseService::responseDelete($responseName);
    }
}

