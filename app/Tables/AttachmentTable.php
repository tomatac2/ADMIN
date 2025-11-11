<?php

namespace App\Tables;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentTable
{
  protected $attachment;
  protected $request;

  public function __construct(Request $request)
  {
    $this->attachment = new Attachment();
    $this->request = $request;
  }

  public function getAttachment()
  {
    return $this->attachment->whereNull('deleted_at')?->latest();
  }

  public function getData()
  {
    $attachments = $this->getAttachment();
    if (isset($this->request->s)) {
      return $attachments->where('name', 'LIKE', "%" . $this->request->s . "%")
        ->orWhere('file_name', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      return $attachments->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
    }

    return $attachments?->latest()?->paginate($this->request?->paginate);
  }

  public function generate()
  {
    $attachments = $this->getData();

    if ($this->request->has('action') && $this->request->has('ids')) {
      $this->bulkActionHandler();
    }

    $attachments->each(function ($attachment) {
      $attachment->author = $attachment?->created_by?->name;
    });

    $attachments->each(function ($attachment) {
      $attachment->date = $attachment->created_at->format('Y-m-d h:i:s A');
    });

    $totalAttachmentsCount = $this->attachment->count();

    $tableConfig = [
      'columns' => [
        ['title' => __('static.media.file'), 'field' => 'name', 'mediaImage' => 'id', 'action' => true, 'sortable' => true],
        ['title' => __('static.media.author'), 'field' => 'author', 'sortable' => true],
        ['title' => __('static.media.type'), 'field' => 'mime_type', 'sortable' => true],
        ['title' => __('static.media.created_at'), 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at'],

      ],
      'data' => $attachments,
      'actions' => [
        ['title' => __('static.media.copy_url'), 'action' => 'copy', 'field' => 'id', 'class' => 'copy', 'permission' => 'media.edit'],
        ['title' => __('static.media.download'), 'action' => 'download', 'field' => 'id', 'class' => 'download', 'permission' => 'media.create'],
        ['title' => __('static.media.delete_permanently'), 'route' => 'admin.media.forceDelete', 'class' => 'delete', 'permission' => 'media.destroy']
      ],
      'bulkactions' => [
        ['title' => __('static.media.delete_permanently'), 'permission' => 'attachment.destroy', 'action' => 'delete'],
      ],
      'actionButtons' => [],

      'modalActionButtons' => [],

      'total' => $totalAttachmentsCount
    ];

    return $tableConfig;
  }


  public function bulkActionHandler()
  {
    switch ($this->request->action) {
      case 'delete':
        $this->deleteHandler();
        break;
    }
  }

  public function deleteHandler(): void
  {
    $this->attachment->whereIn('id', $this->request->ids ?? [])?->forcedelete();
    // foreach ($ids as $id) {
    //   $attachment = $this->attachment->findOrFail($id);
    //   $this->deleteImage($attachment);
    // }
  }

  public function deleteImage($model)
  {
    return $model->forcedelete($model->id);
  }
}
