<?php

namespace App\Tables;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqTable
{
    protected $faq;
    protected $request;

    public function __construct(Request $request)
    {
        $this->faq = new Faq();
        $this->request = $request;
    }
    public function getData()
    {
        $faqs   = $this->faq->newQuery()->with(['faqCategory']); // eager load to avoid N+1
        $locale = app()->getLocale();
        $perPage = (int)($this->request->paginate ?? 15);

        // status filter
        if ($this->request->filled('filter') && $this->request->filter === 'trash') {
            $faqs->onlyTrashed();
        }

        // category filter
        if ($this->request->filled('faq_category_id')) {
            $faqs->where('faq_category_id', $this->request->faq_category_id);
        }

        // search in translatable JSON fields for current locale
        if ($this->request->filled('s')) {
            $s = $this->request->s;
            $faqs->where(function ($q) use ($s, $locale) {
                $q->where("title->{$locale}", 'LIKE', "%{$s}%")
                    ->orWhere("description->{$locale}", 'LIKE', "%{$s}%");
            });
        }

        // sorting (whitelist)
        $sortable = ['created_at', 'title', 'faq_category_id'];
        if ($this->request->filled('orderby') && in_array($this->request->orderby, $sortable, true)) {
            $dir = strtolower($this->request->order ?? 'asc');
            $dir = in_array($dir, ['asc','desc'], true) ? $dir : 'asc';

            // sorting by translatable title if needed
            if ($this->request->orderby === 'title') {
                $faqs->orderBy("title->{$locale}", $dir);
            } else {
                $faqs->orderBy($this->request->orderby, $dir);
            }
        } else {
            $faqs->latest();
        }

        return $faqs->paginate($perPage);
    }
    protected function categoryFilterOptions(): array
    {
        $locale = app()->getLocale();

        $query = FaqCategory::query()->withCount(['faqs' => function ($q) {
            if ($this->request->filter === 'trash') {
                $q->onlyTrashed();
            }
        }]);

        return $query->orderBy('name')->get()->map(function ($cat) use ($locale) {
            $label = method_exists($cat, 'getTranslation')
                ? $cat->getTranslation('name', $locale)
                : $cat->name;

            return [
                'id'    => $cat->id,
                'title' => $label,
                'count' => $cat->faqs_count,
            ];
        })->toArray();
    }

    public function generate()
    {
        $faqs = $this->getData();

        // Transform paginator items (safer than $faqs->each)
        $faqs->getCollection()->transform(function ($faq) {
            $faq->title          = $faq->getTranslation('title', app()->getLocale());
            $faq->description    = $faq->getTranslation('description', app()->getLocale());
            $faq->date           = $faq->created_at->format('Y-m-d h:i:s A');
            $faq->category_name  = optional($faq->faqCategory, function ($c) {
                return method_exists($c, 'getTranslation')
                    ? $c->getTranslation('name', app()->getLocale())
                    : $c->name;
            });
            return $faq;
        });

        $tableConfig = [
            'columns' => [
                ['title' => __('static.faqs.title'),       'field' => 'title',          'imageField' => null, 'action' => true, 'sortable' => true],
                ['title' => __('static.faqs.category'),    'field' => 'category_name',  'sortable' => true, 'sortField' => 'faq_category_id'],
                ['title' => __('static.created_at'),  'field' => 'date',           'sortable' => true, 'sortField' => 'created_at'],
            ],
            'data' => $faqs,

            // existing actions unchanged…
            'actions' => [
                ['title' => __('static.faqs.edit'), 'route' => 'admin.faq.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'isTranslate' => true, 'permission' => 'faq.edit'],
                ['title' => __('static.move_to_trash'), 'route' => 'admin.faq.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'faq.destroy'],
                ['title' => __('static.restore'), 'route' => 'admin.faq.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'faq.restore'],
                ['title' => __('static.delete_permanently'), 'route' => 'admin.faq.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'faq.forceDelete'],
            ],

            // status filters (kept as is)
            'filters' => [
                ['title' => __('static.faqs.all'),   'slug' => 'all',   'count' => $this->faq->count()],
                ['title' => __('static.trash'), 'slug' => 'trash', 'count' => $this->faq->withTrashed()->whereNotNull('deleted_at')->count()],
            ],

            // NEW: category select filter config (frontend renders a dropdown)
            'categoryFilter' => [
                'name'     => 'faq_category_id',
                'title'    => __('static.faqs.category'),
                'selected' => $this->request->get('faq_category_id'),
                'options'  => $this->categoryFilterOptions(),
            ],

            'bulkactions' => [
                ['title' => __('static.move_to_trash'),        'permission' => 'faq.destroy',     'action' => 'trashed', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => __('static.restore'),              'action' => 'restore',             'permission' => 'faq.restore',     'whenFilter' => ['trash']],
                ['title' => __('static.delete_permanently'),   'action' => 'delete',              'permission' => 'faq.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $this->faq->count(),
        ];

        return $tableConfig;
    }

    public function bulkActionHandler()
    {
        switch ($this->request->action) {
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

    public function trashedHandler(): void
    {
        $this->faq->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->faq->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->faq->whereIn('id', $this->request->ids)->forceDelete();
    }
}
