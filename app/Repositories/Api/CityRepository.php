<?php

namespace App\Repositories\Api;

use App\Helpers\Response;
use App\Http\Resources\Api\User\City\CityResource;
use App\Models\City;
use Exception;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class CityRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));
        } catch (ExceptionHandler $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function model()
    {
        return City::class;
    }

    public function index($request)
    {
        try {
            $cities = $this->filter($this->model, $request);
            $cities = $cities->get();
            return Response::respondSuccess('static.cities.fetch_cities_successfully', CityResource::collection($cities));
        } catch (\Exception $e) {
            return Response::respondError('static.cities.something_went_wrong');
        }
    }

    public function show($id)
    {
        try {

            return $this->model->findOrFail($id);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getCities($state_id)
    {
        try {

            $states = $this->model->where('state_id', $state_id)->pluck('name', 'id');
            return Response::respondSuccess('static.cities.fetch_cities_successfully', $states);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function filter($cities, $request)
    {
        if ($request->country_id) {
            $cities = $cities->whereHas('state', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        if ($request->state_id) {
            $cities = $cities->where('state_id', $request->state_id);
        }

        return $cities;
    }
}
