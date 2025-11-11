<?php

namespace App\Repositories\Admin;

use App\Models\FaqCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class FaqCategoryRepository extends BaseRepository
{
    function model()
    {
        return FaqCategory::class;
    }

    public function index($faqTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }
        return view('admin.faq_category.index', ['tableConfig' => $faqTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->faqCategories as $faqCategoryData) {
                $faq = $this->model->create([
                    'name' => $faqCategoryData['name'],
                ]);

                $locale = $request['locale'] ?? app()->getLocale();

                $faq->setTranslation('name', $locale, $faqCategoryData['name']);
            }

            DB::commit();

            return to_route('admin.faq-category.index')->with('success', __('static.faq_categories.create_successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


   public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            
            $faq = $this->model->findOrFail($id);

            $locale = $request['locale'] ?? app()->getLocale();

            if (isset($request['name'])) {
                $faq->setTranslation('name', $locale, $request['name']);
            }


            $data = array_diff_key($request, array_flip(['name', 'locale']));
            $faq->update($data);

            DB::commit();

            return to_route('admin.faq-category.index')->with('success', __('static.faq_categories.update_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

            $faqCategory = $this->model->findOrFail($id);
            $faqCategory->destroy($id);

            return redirect()->route('admin.faq-category.index')->with('success', __('static.faq_categories.delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function restore($id)
    {
        try {

            $faq = $this->model->onlyTrashed()->findOrFail($id);
            $faq->restore();

            return redirect()->back()->with('success', __('static.faq_categories.restore_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $faq = $this->model->onlyTrashed()->findOrFail($id);
            $faq->forceDelete();

            return redirect()->back()->with('success', __('static.faq_categories.permanent_delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

}
