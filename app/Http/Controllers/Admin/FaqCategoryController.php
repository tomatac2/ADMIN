<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Tables\FaqCategoryTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\FaqCategoryRepository;

class FaqCategoryController extends Controller
{
    public $repository;
    
    public function __construct(FaqCategoryRepository $repository)
    {
//        $this->authorizeResource(Faq::class,'faq');
        $this->repository = $repository;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(FaqCategoryTable $faqTable)
    {
        return $this->repository->index($faqTable->generate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.faq_category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FaqCategory $faqCategory)
    {
        return view('admin.faq_category.edit',['faqCategory' => $faqCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FaqCategory $faqCategory)
    {
        return $this->repository->update($request->all(),$faqCategory->id);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FaqCategory $faqCategory)
    {
        return $this->repository->destroy($faqCategory->id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    /**
     * Permanent delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->repository->forceDelete($id);
    }
}
