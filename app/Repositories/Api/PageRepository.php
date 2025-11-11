<?php

namespace App\Repositories\Api;

use Exception;
use App\Models\Page;
use App\Exceptions\ExceptionHandler;
use App\Helpers\Response;
use App\Http\Resources\Api\User\Page\PageCollection;
use App\Http\Resources\Api\User\Page\PageDetailResource;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class PageRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'title' => 'like',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    function model()
    {
        return Page::class;
    }

    public function index($pages, $request)
    {
        try {
            $pages = $pages->latest()->paginate($request->limit ?: 10);
            return Response::respondSuccessPaginate('static.pages.fetch_pages_successfully', (new PageCollection($pages))->toArray($request));
        } catch (Exception $e) {
            return Response::respondError('static.pages.something_went_wrong');
        }
    }

    public function show($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (Exception $e) {
            return Response::respondError('static.pages.something_went_wrong');
        }
    }

    public function filter($pages, $request)
    {
        if ($request->field && $request->sort) {
            $pages = $pages->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $pages = $pages->where('status', $request->status);
        }
    }

    public function getPagesBySlug($slug)
    {
        try {
            $page = $this->model
                ->where('slug', $slug)
                ->firstOrFail()
                ->makeVisible(['content', 'meta_description']);

            isset($page->created_by) ? $page->created_by->makeHidden(['permission']) : $page;
            if (!$page) {
                return Response::respondError('static.pages.fetch_page_successfully');
            }
            return Response::respondSuccess('static.pages.fetch_page_successfully', PageDetailResource::make($page));
        } catch (Exception $e) {
            return Response::respondError('static.pages.something_went_wrong');
        }
    }
}
