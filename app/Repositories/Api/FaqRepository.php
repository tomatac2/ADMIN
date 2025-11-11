<?php

namespace App\Repositories\Api;

use App\Helpers\Response;
use App\Http\Resources\Api\User\Faq\FaqCategoryResource;
use App\Http\Resources\Api\User\Faq\FaqCollection;
use App\Models\Faq;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class FaqRepository extends BaseRepository
{

    protected $notification;

    function model()
    {
        return Faq::class;
    }

    public function index($faqs, $request)
    {
        try {
            $faqs = $this->filter($faqs, $request)->latest()->paginate($request->limit ?: 10);
            return Response::respondSuccessPaginate('static.faqs.fetch_faqs_successfully', (new FaqCollection($faqs))->toArray($request));
        } catch (Exception $e) {
            return Response::respondError('static.faqs.something_went_wrong');
        }
    }

    public function categories($categories, $request)
    {
        try {
            return Response::respondSuccess('static.faq_categories.fetch_faq_categories_successfully', FaqCategoryResource::collection($categories));
        } catch (Exception $e) {
            return Response::respondError('static.faqs.something_went_wrong');
        }
    }

    public function filter($faqs, $request)
    {
        if (isset($request->category_id) && $request->category_id != 'all') {
            $faqs = $faqs->where('faq_category_id', $request->category_id);
        }
        return $faqs;
    }

}
