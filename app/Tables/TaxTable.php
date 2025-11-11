<?php

namespace App\Tables;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxTable
{
    protected $tax;
    protected $request;

    public function __construct(Request $request)
    {
        $this->tax = new Tax();
        $this->request = $request;
    }

    public function getData()
    {
        $taxes = $this->tax;
        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case 'active':
                    return $taxes->where('status', true)->paginate($this->request?->paginate);
                case 'deactive':
                    return $taxes->where('status', false)->paginate($this->request?->paginate);
                case 'trash':
                    return $taxes->withTrashed()?->whereNotNull('deleted_at')?->paginate($this->request?->paginate);
            }
        }

        if ($this->request->has('s')) {
            return $taxes->withTrashed()->where(function ($query) {
                $query->where('name', 'LIKE', "%" . $this->request->s . "%")
                    ->orWhere('rate', 'LIKE', "%" . $this->request->s . "%");
            })->paginate($this->request->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            return $taxes->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
        }

        return $taxes->whereNull('deleted_at')->paginate($this->request?->paginate);
    }
    public function generate()
    {
        $taxes = $this->getData();
        if ($this->request->has('action') && $this->request->has('ids')) {
            $this->bulkActionHandler();
        }

        $taxes->each(function ($tax) {
            $tax->name = $tax->getTranslation('name', app()->getLocale());
            $tax->rate = $tax->rate . '%';
            $tax->date = $tax->created_at->format('Y-m-d h:i:s A');
        });


        $tableConfig = [
            'columns' => [
                ['title' => __('static.taxes.name'), 'field' => 'name', 'imageField' => null, 'action' => true, 'sortable' => true],
                ['title' => __('static.taxes.rate'), 'field' => 'rate', 'sortable' => true],
                ['title' => __('static.taxes.status'), 'field' => 'status', 'route' => 'admin.tax.status', 'type' => 'status', 'sortable' => true],
                ['title' => __('static.created_at'), 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at']
            ],
            'data' => $taxes,
            'actions' => [
                ['title' => __('static.taxes.edit'),  'route' => 'admin.tax.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'isTranslate' => true, 'permission' => 'tax.edit'],
                ['title' => __('static.move_to_trash'), 'route' => 'admin.tax.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'tax.destroy'],
                ['title' => __('static.restore'), 'route' => 'admin.tax.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'tax.restore'],
                ['title' => __('static.delete_permanently'), 'route' => 'admin.tax.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'tax.forceDelete']
            ],
            'filters' => [
                ['title' => __('static.taxes.all'), 'slug' => 'all', 'count' => $this->tax->count()],
                ['title' => __('static.active'), 'slug' => 'active', 'count' => $this->tax->where('status', true)->count()],
                ['title' => __('static.deactive'), 'slug' => 'deactive', 'count' => $this->tax->where('status', false)->count()],
                ['title' => __('static.trash'), 'slug' => 'trash', 'count' => $this->tax->withTrashed()?->whereNotNull('deleted_at')?->count()]
            ],
            'bulkactions' => [
                ['title' => __('static.active'), 'permission' => 'tax.edit', 'action' => 'active', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.deactive'), 'permission' => 'tax.edit', 'action' => 'deactive', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.move_to_trash'), 'permission' => 'tax.destroy', 'action' => 'trashed', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.restore'), 'action' => 'restore', 'permission' => 'tax.restore', 'whenFilter' => ['trash']],
                ['title' => __('static.delete_permanently'), 'action' => 'delete', 'permission' => 'tax.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $this->tax->count()
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
            case 'trashed':
                $this->trashedHandler();
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
        $this->tax->whereIn('id', $this->request->ids)->update(['status' => true]);
    }

    public function deactiveHandler(): void
    {
        $this->tax->whereIn('id', $this->request->ids)->update(['status' => false]);
    }

    public function trashedHandler(): void
    {
        $this->tax->whereIn('id', $this->request->ids)->delete();
    }
    public function restoreHandler(): void
    {
        $this->tax->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->tax->whereIn('id', $this->request->ids)->forceDelete();
    }
}
