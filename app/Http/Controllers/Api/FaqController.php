<?php

namespace App\Http\Controllers\Api;

use App\Models\FaqCategory;
use App\Repositories\Api\FaqRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{

    protected $repository;

    public function __construct(FaqRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $faqs = $this->repository;

        return $this->repository->index($faqs, $request);
    }

    public function categories(Request $request)
    {
        $categories = FaqCategory::all();

        return $this->repository->categories($categories, $request);
    }

}
