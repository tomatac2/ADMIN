<?php

namespace App\Repositories\Api;

use App\Helpers\Response;
use App\Http\Resources\Api\User\State\StateCollection;
use App\Http\Resources\Api\User\State\StateResource;
use Exception;
use App\Models\State;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class StateRepository extends BaseRepository
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
        return State::class;
    }

    public function index($request)
    {
        try {
            $states = $this->filter($this->model, $request);
            $states = $states->get();
            return Response::respondSuccess('static.states.fetch_states_successfully', StateResource::collection($states));
        } catch (\Exception $e) {
            return Response::respondError('static.states.something_went_wrong');
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

    public function getStates($country_id)
    {
        try {

            $states = $this->model->where('country_id', $country_id)->pluck('name', 'id');
            return Response::respondSuccess('static.states.fetch_states_successfully', $states);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function filter($states, $request)
    {
        if ($request->country_id) {
            $states = $states->where('country_id', $request->country_id);
        }

        return $states;
    }
}
