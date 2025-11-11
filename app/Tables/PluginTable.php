<?php

namespace App\Tables;

use App\Models\Plugin;
use Illuminate\Http\Request;

class PluginTable
{
  protected $plugin;
  protected $request;

  public function __construct(Request $request)
  {
    $this->plugin = new Plugin();
    $this->request = $request;
  }

  public function getData()
  {
    $plugins = $this->plugin;
    if ($this->request->has('filter')) {
      switch ($this->request->filter) {
        case 'active':
          return $plugins->where('status', true)->paginate($this->request?->paginate);
        case 'deactive':
          return $plugins->where('status', false)->paginate($this->request?->paginate);
        case 'trash':
          return $plugins->withTrashed()?->whereNotNull('deleted_at')?->paginate($this->request?->paginate);
      }
    }

    if ($this->request->has('s')) {
      return $plugins->withTrashed()->where('name', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      return $plugins->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
    }

    return $plugins->whereNull('deleted_at')->paginate($this->request?->paginate);
  }

  public function generate()
  {
    $plugins = $this->getData();
    if ($this->request->has('action') && $this->request->has('ids')) {
      $this->bulkActionHandler();
    }

    $plugins->each(function ($plugin) {
      $plugin->date = $plugin->created_at->format('Y-m-d h:i:s A');
    });

    $tableConfig = [
      'columns' => [
        ['title' => __('static.name'), 'field' => 'name', 'action' => true, 'sortable' => true],
        ['title' => __('static.description'), 'field' => 'description', 'sortable' => false],
        ['title' => __('static.status'), 'field' => 'status', 'route' => 'admin.plugin.status', 'type' => 'status', 'sortable' => false],
        ['title' => __('static.created_at'), 'field' => 'date', 'sortable' => false]
      ],
      'data' => $plugins,
      'actions' => [
      ],
      'filters' => [
        ['title' => __('static.all'), 'slug' => 'all', 'count' => $this->plugin->count()],
        ['title' => __('static.active'), 'slug' => 'active', 'count' =>  $this->plugin->where('status', true)->count()],
        ['title' => __('static.deactive'), 'slug' => 'deactive', 'count' => $this->plugin->where('status', false)->count()],
      ],
      'bulkactions' => [
        ['title' => __('static.active'), 'permission' => 'plugin.edit', 'action' => 'active'],
        ['title' => __('static.deactive'), 'permission' => 'plugin.edit', 'action' => 'deactive'],
        ['title' => __('static.delete'), 'permission' => 'plugin.destroy', 'action' => 'delete']
      ],
      'total' => $this->plugin->count()
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
    }
  }

  public function activeHandler(): void
  {
    $this->plugin->whereIn('id', $this->request->ids)->update(['status' => true]);
  }

  public function deactiveHandler(): void
  {
    $this->plugin->whereIn('id', $this->request->ids)->update(['status' => false]);
  }

  public function trashedHandler(): void
  {
    $this->plugin->whereIn('id', $this->request->ids)->delete();
  }
}
