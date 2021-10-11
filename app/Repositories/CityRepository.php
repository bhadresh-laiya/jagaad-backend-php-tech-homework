<?php
/**
 * Created by PhpStorm.
 * User: hectnandez
 * Date: 06/02/2019
 * Time: 13:11
 */

namespace App\Repositories;

use App\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityRepository implements RepositoryInterface
{
    /**
     * @var City
     */
    protected $model;

    /**
     * CityRepository constructor.
     * @param City $model
     */
    public function __construct(City $model)
    {
        $this->model = $model;
    }

    /**
     * @return City[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, int $id)
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * @param int $id
     * @return int
     */
    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param array $select
     * @param Request $request
     * @return array
     */
    public function searchDataTable(array $select, Request $request){
        $search = $request->get('search');
        $hasFilter = false;
        $cities = DB::table('cities')->select($select);
        if(!empty($search['value'])){
            $cities->where('name','like', '%'.$search['value'].'%');
            $hasFilter = true;
        }
        $cities->offset(intval($request->get('start')))->limit(intval($request->get('length')));
        $recordsTotal = DB::table('cities')->count();
        $recordsFilter = $recordsTotal;
        if($hasFilter){
            $recordsFilter = $cities->count();
        }
        $response = array(
            'draw' => intval($request->get('draw')),
            'recordsTotal' => DB::table('cities')->count(),
            'recordsFiltered' => $recordsFilter,
            'data' => $cities->get()
        );
        if(env('APP_DEBUG') === true){
            $response['query'] = $cities->toSql();
        }
        return $response;
    }
}