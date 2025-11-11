<?php

namespace App\Repositories\Api;

use App\Helpers\Response;
use App\Models\Setting;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{
    function model()
    {
        return Setting::class;
    }

    public function index()
    {
        return Response::respondSuccess('static.settings.fetch_all_settings', $this->model->latest('created_at')->first());
    }
}
