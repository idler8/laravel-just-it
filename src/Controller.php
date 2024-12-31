<?php

namespace Justit;

use Illuminate\Http\Request;
use Exception;

/** 数据资源管理系统 */
class Controller
{
    /** 
     * 资源查询-全部
     * 可以获取所有本资源列表
     * @param {name} 资源名，既resources[].key属性
     * @param * Query参数参考resources[].parameters属性
     */
    public function index(Request $request, $name)
    {
        if (empty($model = Resource::get(Resource::namespace($name)))) {
            throw new Exception('资源不存在', 418);
        }
        $builder = Resource::filter(collect($request->query()), $model);
        if (!empty($primaryKey = $model->getKeyName())) {
            $builder = $builder->orderByDesc($primaryKey);
        }
        $limit = $builder->getQuery()->limit;
        if ($limit > 0) {
            $builder = $builder->limit(min(1000, $limit));
        } else {
            $builder = $builder->limit(1000);
        }
        return $builder->get();
    }
    /** 
     * 资源查询-分页
     * 获取本资源的分页对象
     * @param {name} 资源名，既resources[].key属性
     * @param {per_page} 每页数量
     * @param {page} 分页页码
     * @param * Query参数参考resources[].parameters属性
     **/
    public function paginate(Request $request, $name, $per_page, $page = 1)
    {
        if (empty($model = Resource::get(Resource::namespace($name)))) {
            throw new Exception('资源不存在', 418);
        }
        $builder = Resource::filter(collect($request->query()), $model);
        if (!empty($primaryKey = $model->getKeyName())) {
            $builder = $builder->orderByDesc($primaryKey);
        }
        return $builder->paginate(min(1000, $per_page), ['*'], null, $page);
    }
    /** 
     * 资源查询-详情
     * 获取单个资源的详情
     * @param {name} 资源名，既resources[].key属性
     * @param {id} 资源主键
     * @param * Query参数参考resources[].parameters属性
     */
    public function show(Request $request, $name, $id)
    {
        if (empty($model = Resource::get(Resource::namespace($name)))) {
            throw new Exception('资源不存在', 418);
        }
        $query = collect($request->query());
        $builder = Resource::filter($query, $model);
        if ($id === '+') {
            $primaryKey = $model->getKeyName() ?? $model::CREATED_AT;
            $entity = $builder->orderByDesc($primaryKey)->first();
        } elseif ($id === '-') {
            $entity = $builder->first();
        } else {
            $entity = $builder->find($id);
        }
        if (empty($entity)) throw new Exception('该资源不存在', 418);
        return $entity;
    }
    /** 
     * 资源更新 
     * @param {name} 资源名，既resources[].key属性
     * @param {id} 资源主键
     * @param * Query与Body参数参考resources[].parameters属性
     **/
    public function update(Request $request, $name, $id)
    {
        $model = $this->show($request,  $name, $id);
        $values = collect($request->post())->toArray();
        return $model->fill($values)->save();
    }
    /** 
     * 资源销毁 
     * 返回被销毁的资源
     * @param {name} 资源名，既resources[].key属性
     * @param * Query参数参考resources[].parameters属性
     **/
    public function destroy(Request $request, $name, $id)
    {
        $model = $this->show($request, $name, $id);
        $model->destroy($id);
        return $model;
    }
    /** 
     * 资源新增 
     * 返回新增的资源ID
     * @param {name} 资源名，既resources[].key属性
     * @param * Body参数参考resources[].parameters属性
     */
    public function store(Request $request, $name)
    {
        if (empty($model = Resource::get(Resource::namespace($name)))) {
            throw new Exception('资源不存在', 418);
        }
        $values = collect($request->post())->toArray();
        $response = $model->fill($values)->save();
        if (!empty($primaryKey = $model->getKeyName())) {
            $model->$primaryKey;
        }
        return $model->id;
    }
}
