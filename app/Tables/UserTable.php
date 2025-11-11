<?php

namespace App\Tables;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;

class UserTable
{
    protected $user;
    protected $request;

    public function __construct(Request $request)
    {
        $this->user = new User();
        $this->request = $request;
    }

    public function getUser()
    {
        $roleIds = getNonAdminRolesList()->pluck('id')->toArray();

        return $this->user->where('system_reserve', false)
            ->whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('id', $roleIds)
                    ->where(function($q) {
                        $q->where('system_reserve', false)
                        ->orWhere('name', RoleEnum::USER);
                    });
            });
    }
    
    public function getData()
    {
        $users = $this->getUser();
        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case 'active':
                    return $users->whereNull('deleted_at')->where('status', true)->paginate($this->request?->paginate);
                case 'deactive':
                    return $users->whereNull('deleted_at')->where('status', false)->paginate($this->request?->paginate);
                case 'trash':
                    return $users->withTrashed()->whereNotNull('deleted_at')->paginate($this->request?->paginate);
            }
        }

        if (isset($this->request->s)) {
            return $users->withTrashed()->where(function ($query) {
                $query->where('name', 'LIKE', "%" . $this->request->s . "%")
                    ->orWhere('email', 'LIKE', "%" . $this->request->s . "%");
            })->paginate($this->request?->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            return $users->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
        }

        return $users->whereNull('deleted_at')->latest()->paginate($this->request?->paginate);
    }


    public function generate()
    {
        $users = $this->getData();
        if ($this->request->has('action') && $this->request->has('ids')) {
            $this->bulkActionHandler();
        }

        $users->each(function ($user) {
            if (isDemoModeEnabled()) {
                $user->email = __('static.demo_mode');
            }
            $user->date = $user->created_at->format('Y-m-d h:i:s A');
        });

        $tableConfig = [
            'columns' => [
                ['title' => __('static.users.full_name'), 'field' => 'name', 'route' => 'admin.user.edit', 'imageField' => 'profile_image_id', 'placeholderLetter' => true, 'action' => true, 'sortable' => true],
                ['title' => __('static.users.email'), 'field' => 'email', 'sortable' => true],
                ['title' => __('static.status'), 'field' => 'status', 'route' => 'admin.user.status', 'type' => 'status', 'sortable' => true],
                ['title' => __('static.created_at'), 'field' => 'date', 'sortable' => true, 'sortField' => 'users.created_at'],
            ],
            'data' => $users,
            'actions' => [
                ['title' => __('static.users.edit'), 'route' => 'admin.user.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'user.edit'],
                ['title' => __('static.move_to_trash'), 'route' => 'admin.user.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'user.destroy'],
                ['title' => __('static.restore'), 'route' => 'admin.user.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'user.restore'],
                ['title' => __('static.delete_permanently'), 'route' => 'admin.user.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'user.forceDelete'],
            ],
            'filters' => [
                ['title' => __('static.users.all'), 'slug' => 'all', 'count' => $this->getUser()?->whereNull('deleted_at')->count()],
                ['title' => __('static.active'), 'slug' => 'active', 'count' => $this->getUser()?->whereNull('deleted_at')->where('status', true)->count()],
                ['title' => __('static.deactive'), 'slug' => 'deactive', 'count' => $this->getUser()?->whereNull('deleted_at')->where('status', false)->count()],
                ['title' => __('static.trash'), 'slug' => 'trash', 'count' => $this->getUser()?->withTrashed()?->whereNotNull('deleted_at')?->count()],
            ],
            'bulkactions' => [
                ['title' => __('static.active'), 'action' => 'active', 'permission' => 'user.edit', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.deactive'), 'action' => 'deactive', 'permission' => 'user.edit', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.move_to_trash'), 'action' => 'trash', 'permission' => 'user.destroy', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.restore'), 'action' => 'restore', 'permission' => 'user.restore', 'whenFilter' => ['trash']],
                ['title' => __('static.delete_permanently'), 'action' => 'delete', 'permission' => 'user.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $users->count(),
        ];

        return $tableConfig;
    }

    public function bulkActionHandler()
    {
        switch ($this->request->action) {
            case 'active':
                $this->activeHandler();
                break;
            case 'deactive':
                $this->deactiveHandler();
                break;
            case 'trash':
                $this->trashHandler();
                break;
            case 'restore':
                $this->restoreHandler();
                break;
            case 'delete':
                $this->deleteHandler();
                break;
        }
    }

    public function activeHandler(): void
    {
        $this->user->whereIn('id', $this->request->ids)->update(['status' => true]);
    }

    public function deactiveHandler(): void
    {
        $this->user->whereIn('id', $this->request->ids)->update(['status' => false]);
    }

    public function trashHandler(): void
    {
        $this->user->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->user->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->user->whereIn('id', $this->request->ids)->forceDelete();
    }
}
