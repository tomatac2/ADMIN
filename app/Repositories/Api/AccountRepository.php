<?php

namespace App\Repositories\Api;

use App\Helpers\ResponseStatus;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use App\Helpers\Response;
use App\Http\Resources\Api\User\ProfileResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Eloquent\BaseRepository;

class AccountRepository extends BaseRepository
{
    protected $store;

    protected $fields = [
        'name',
        'email',
        'phone',
        'country_code',
        'gender',
        'date_of_birth',
        'profile_image_id',
        'profile_image'
    ];

    function model()
    {
        return User::class;
    }

    public function self()
    {
        try {
            $user_id = getCurrentUserId();
            $user = $this->model->with(['profile_image', 'addresses'])->findOrFail($user_id);
            return Response::respondSuccess('auth.user_profile', ProfileResource::make($user));
        } catch (Exception $e) {
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function updateProfile($request)
    {
        DB::beginTransaction();
        try {
            $user = $this->model->findOrFail(getCurrentUserId());

            $data = $request->only($this->fields);

            if ($request->filled('phone')) {
                $data['phone'] = (string) $request->phone;
            }

            if (
                ($request->filled('name') && !$user->name) ||
                ($request->filled('email') && !$user->email)
            ) {
                $data['is_verified'] = 1;
            }

            $user->update($data);


            if (isset($request['profile_image_id'])) {
                $user->profile_image()->associate($request['profile_image_id']);
            }

            if ($request->hasFile('profile_image')) {
                $attachments = createAttachment();
                $media = storeImage([$request->profile_image], $attachments, 'attachment');
                $user->profile_image_id = head($media)?->id;
                $user->profile_image()->associate(head($media)?->id ?? []);
            }

            $user->save();

            if ($request->filled('address')) {
                foreach ($request->address as $addressData) {
                    if (empty($addressData['id'])) {
                        $user->address()->create($addressData);
                    } else {
                        $address = $user->address()->findOrFail($addressData['id']);
                        $address->update($addressData);
                    }
                }
            }


            DB::commit();
            return Response::respondSuccess('auth.user_profile', ProfileResource::make($user));

        } catch (Exception $e) {
            DB::rollBack();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function updatePassword($request)
    {
        DB::beginTransaction();
        try {

            $user_id = getCurrentUserId();
            $user = $this->model->findOrFail($user_id);
            DB::commit();

            $user->update(['password' => Hash::make($request->password)]);
            return Response::respondSuccess('auth.password_updated');
        } catch (Exception $e) {

            DB::rollback();
            return Response::respondError('auth.something_went_wrong');
        }
    }

    public function deleteAccount()
    {
        DB::beginTransaction();
        try {

            $user = $this->model->findOrFail(auth('sanctum')->user()->id);
            $user->forceDelete(auth('sanctum')->user()->id);
            DB::commit();

            return [
                'message' => __('static.users.user_delete'),
            ];
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function activateNotification($request)
    {
        $validator = Validator::make($request->all(), [
            'activate_notification' => 'required|bool',
        ]);
        if ($validator->fails()) {
            return Response::respondError($validator->errors()->first(), ResponseStatus::VALIDATION_ERROR);
        }
        $user = $this->model->findOrFail(getCurrentUserId());

        // Update Activate Notification
        $user->notifiable = $request->input('activate_notification');
        $user->save();

        return Response::respondSuccess('auth.notification_updated');
    }

    public function updateZone($request)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:zones,id',
        ]);

        if ($validator->fails()) {
            return Response::respondError($validator->errors()->first(), ResponseStatus::VALIDATION_ERROR);
        }
        $user = $this->model->findOrFail(getCurrentUserId());

        // Update Activate Notification
        $user->zone_id = $request->zone_id;
        $user->save();

        return Response::respondSuccess('auth.zone_updated');
    }

}
