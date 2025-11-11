<?php

namespace App\Repositories\Api;

use App\Helpers\Response;
use App\Http\Resources\Api\User\Notification\NotificationCollection;
use App\Http\Resources\Api\User\Page\PageCollection;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class NotificationRepository extends BaseRepository
{

    protected $notification;

    function model()
    {
        return User::class;
    }

    public function index($notifications, $request)
    {
        try {
            $notifications = $notifications->latest()->paginate($request->paginate ?? 10);
            return Response::respondSuccessPaginate('static.notifications.fetch_notifications_successfully', (new NotificationCollection($notifications))->toArray($request));
        } catch (Exception $e) {
            return Response::respondError('static.notifications.something_went_wrong');
        }
    }

    public function markAsRead($request)
    {
        DB::beginTransaction();

        try {

            $user_id = getCurrentUserId();
            $user = $this->model->findOrFail($user_id);
            $user->unreadNotifications->markAsRead();
            DB::commit();

            return Response::respondSuccess('static.notifications.mark_as_read_successfully');
        
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {   
        try{

            $user_id = getCurrentUserId();
            $user = $this->model->findOrFail($user_id)->first();
            return $user->notifications()->where('id',$id)->first()->destroy($id);

        }catch(Exception $e)
        {
            throw new ExceptionHandler($e->getMessage(),$e->getCode());
        }
    }
}
