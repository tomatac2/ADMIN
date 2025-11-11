<?php

namespace App\Repositories\Api;

use Exception;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use App\Helpers\Response;
use App\Helpers\ResponseStatus;
use App\Http\Resources\Api\User\Address\AddressResource;
use Prettus\Repository\Eloquent\BaseRepository;

class AddressRepository extends BaseRepository
{
    protected $fields = [
        'title',
        'address',
        'is_primary',
        'status',
    ];

    function model()
    {
        return Address::class;
    }

    public function index()
    {
        try {
            $user_id = getCurrentUserId();
            $addresses = $this->model
                ->where('user_id', $user_id)
                ->where('status', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return Response::respondSuccess('static.addresses.list_retrieved', AddressResource::collection($addresses));
        } catch (Exception $e) {
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $user_id = getCurrentUserId();
            
            // If this is the first address, make it primary
            $isFirstAddress = $this->model->where('user_id', $user_id)->count() === 0;
            
            $addressData = $request->validated();
            $addressData['user_id'] = $user_id;
            $addressData['is_primary'] = $isFirstAddress ? true : ($request->input('is_primary') ?? false);
            $addressData['status'] = true;

            // If this address is being set as primary, unset other primary addresses
            if ($addressData['is_primary']) {
                $this->model->where('user_id', $user_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $address = $this->model->create($addressData);

            DB::commit();
            return Response::respondSuccess('static.addresses.created_successfully', AddressResource::make($address));
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }

    public function show($id)
    {
        try {
            $user_id = getCurrentUserId();
            $address = $this->model
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return Response::respondError('static.addresses.address_not_found', ResponseStatus::NOT_FOUND);
            }

            return Response::respondSuccess('static.addresses.retrieved_successfully', AddressResource::make($address));
        } catch (Exception $e) {
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $user_id = getCurrentUserId();
            $address = $this->model->where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return Response::respondError('static.addresses.address_not_found', ResponseStatus::NOT_FOUND);
            }

            $addressData = $request->validated();

            // If this address is being set as primary, unset other primary addresses
            if (isset($addressData['is_primary']) && $addressData['is_primary']) {
                $this->model->where('user_id', $user_id)
                    ->where('id', '!=', $id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $address->fill($addressData);
            $address->save();

            DB::commit();
            return Response::respondSuccess('static.addresses.updated_successfully', AddressResource::make($address));
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user_id = getCurrentUserId();
            $address = $this->model->where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return Response::respondError('static.addresses.address_not_found', ResponseStatus::NOT_FOUND);
            }

            // If this is the primary address, make another address primary
            if ($address->is_primary) {
                $nextPrimary = $this->model->where('user_id', $user_id)
                    ->where('id', '!=', $id)
                    ->where('status', true)
                    ->first();
                
                if ($nextPrimary) {
                    $nextPrimary->update(['is_primary' => true]);
                }
            }

            $address->delete();

            DB::commit();
            return Response::respondSuccess('static.addresses.deleted_successfully');
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }

    public function setPrimary($id)
    {
        DB::beginTransaction();
        try {
            $user_id = getCurrentUserId();
            $address = $this->model->where('user_id', $user_id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return Response::respondError('static.addresses.address_not_found', ResponseStatus::NOT_FOUND);
            }

            // Unset all other primary addresses
            $this->model->where('user_id', $user_id)
                ->where('id', '!=', $id)
                ->update(['is_primary' => false]);

            // Set this address as primary
            $address->update(['is_primary' => true]);

            DB::commit();
            return Response::respondSuccess('static.addresses.primary_set_successfully', AddressResource::make($address));
        } catch (Exception $e) {
            DB::rollback();
            return Response::respondError('static.addresses.something_went_wrong');
        }
    }
} 