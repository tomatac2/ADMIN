<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Api\AccountRepository;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $repository;

    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function self()
    {
        return $this->repository->self();
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        return $this->repository->updatePassword($request);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->repository->updateProfile($request);
    }

    public function deleteAccount() 
    {
       return $this->repository->deleteAccount(); 
    }

    public function activateNotification(Request $request)
    {
        return $this->repository->activateNotification($request);
    }
}
