<?php

use App\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use App\Models\Attachment;
use App\Models\Blog;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\LandingPage;
use App\Models\Language;
use App\Models\MenuItems;
use App\Models\Menus;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\WidgetManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

if (!function_exists('getSettings')) {
    function getSettings()
    {
        return Setting::pluck('values')?->first();
    }
}

if (!function_exists('getDefaultLangLocale')) {
    function getDefaultLangLocale()
    {
        $locale = 'en';
        $settings = getSettings();
        if ($settings['general']['default_language'] ?? false) {
            $locale = $settings['general']['default_language']['locale'];
        }
        return $locale;
    }
}

if (!function_exists('getCountryCodes')) {
    function getCountryCodes()
    {
        return Country::get(["calling_code", "id", "iso_3166_2", 'flag', 'name']);
    }
}

if (!function_exists('getCountries')) {
    function getCountries()
    {
        return DB::table('countries')->pluck('name', 'id');
    }
}

if (!function_exists('getLanguage')) {
    function getLanguage()
    {
        return DB::table('languages')?->where('status', true)->whereNull('deleted_at')?->pluck('name', 'id');
    }
}

if (!function_exists('getCountryFlags')) {
    function getCountryFlags()
    {
        return Country::get(['name', 'id', 'flag']);
    }
}

if (!function_exists('getStatesByCountryId')) {
    function getStatesByCountryId($country_id)
    {
        return DB::table('states')?->where('country_id', $country_id)?->select('id', 'name')?->get();
    }
}

if (!function_exists('isUserLogin')) {
    function isUserLogin()
    {
        return auth()?->check();
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId()
    {
        static $userId = null;
        if ($userId !== null) {
            return $userId;
        }

        if (isUserLogin()) {
            $userId = auth()->id();
            return $userId;
        }

        return null;
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser()
    {
        static $user = null;
        if ($user !== null) {
            return $user;
        }
        if (isUserLogin()) {
            $user = auth()->user();
            return $user;
        }

        return null;
    }
}

if (!function_exists('getAdmin')) {
    function getAdmin()
    {
        return Cache::remember('admin_user', 60, function () {
            return User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })?->first();
        });
    }
}

if (!function_exists('getAdminId')) {
    function getAdminId()
    {
        static $adminId = null;
        if ($adminId !== null) {
            return $adminId;
        }

        $admin = getAdmin();
        $adminId = $admin?->id;

        return $adminId;
    }
}

if (!function_exists('getCurrentRoleName')) {
    function getCurrentRoleName()
    {
        if (isUserLogin()) {
            return auth()?->user()?->role?->name;
        }
    }
}

if (!function_exists('getAllModules')) {
    function getAllModules()
    {
        return Module::all();
    }
}

if (!function_exists('getRoleCredentials')) {
    function getRoleCredentials()
    {
        $roleCredentials = [];
        $modules = getAllModules();
        $generalRoleConfig = config('role');
        foreach ($generalRoleConfig as $roleKey => $roleData) {
            if (isset($roleData['name'], $roleData['email'], $roleData['password'])) {
                $roleCredentials[] = $roleData;
            }
        }

        foreach ($modules as $module) {
            $roleFile = module_path($module->getName(), 'config/role.php');
            if (file_exists($roleFile)) {
                $roleConfig = include $roleFile;
                foreach ($roleConfig as $roleKey => $roleData) {
                    if (isset($roleData['name'], $roleData['email'], $roleData['password'])) {
                        $roleCredentials[] = $roleData;
                    }
                }
            }
        }

        return $roleCredentials;
    }
}

if (!function_exists('getNonAdminRoles')) {
    function getNonAdminRoles()
    {
        $nonAdminRoles = getNonAdminRolesList();
        return getActiveRoles($nonAdminRoles);
    }
}

if (!function_exists('getNonAdminRolesList')) {
    function getNonAdminRolesList()
    {
        return Role::where('name', '!=', RoleEnum::ADMIN)
            ->whereNull('module')
            ->where(function ($query) {
                $query->where('system_reserve', false)
                    ->orWhere('name', RoleEnum::USER);
            })->get();
    }
}

if (!function_exists('getActiveRoles')) {
    function getActiveRoles($roles)
    {
        $activeRoles = [];
        foreach ($roles as $role) {
            if (isRoleActive($role)) {
                $activeRoles[] = $role;
            }

            if (!$role->module) {
                $activeRoles[] = $role;
            }
        }

        return collect($activeRoles);
    }
}

if (!function_exists('isRoleActive')) {
    function isRoleActive($role)
    {
        return Plugin::where('id', $role->module)->where('status', true)?->exists();
    }
}

if (!function_exists('getMedia')) {
    function getMedia($id)
    {
        return Attachment::find($id);
    }
}

if (!function_exists('getMediaURLbyId')) {
    function getMediaURLbyId($id)
    {
        return getMedia($id)?->original_url;
    }
}

if (!function_exists('getRouteList')) {
    function getRouteList($prefix = null, $method = null)
    {
        $routes = collect(Route::getRoutes());
        if ($method) {
            $routes = $routes->filter(function ($item) use ($method) {
                return (head($item->methods()) == $method);
            });
        }

        $routes = $routes->filter(function ($route) {
            return str_ends_with($route->getName(), '.index');
        });

        $routes = $routes->values()->map(function ($route) {
            return [
                $route->getName(),
            ];
        })->flatten();

        return $routes;
    }
}

if (!function_exists('isDemoModeEnabled')) {

    function isDemoModeEnabled()
    {
        try {

            return env('DEMO_FIELDS');

        } catch (Exception $e) {

            return false;
        }
    }
}

if (!function_exists('isDemoMode')) {

    function isDemoMode()
    {
        try {

            return env('DEMO_MODE');

        } catch (Exception $e) {

            return false;
        }
    }
}

if (!function_exists('getLanguageByLocale')) {
    function getLanguageByLocale($locale)
    {
        return Language::where('locale', $locale)?->whereNull('deleted_at')?->first();
    }
}

if (!function_exists('getAttachmentId')) {
    function getAttachmentId($file_name)
    {
        return Attachment::where('file_name', $file_name)->pluck('id')->first();
        return DB::table('attachments')->where('file_name', $file_name)
            ->select('id')?->limit(1)->value('id');
    }
}

if (!function_exists('getLanguages')) {
    function getLanguages()
    {
        return Language::where('status', true)?->get();
    }
}

if (!function_exists('getLanguageDir')) {
    function getLanguageDir($locale)
    {
        $is_rtl = DB::selectOne("SELECT is_rtl FROM languages WHERE locale = ?", [$locale])->is_rtl ?? false;
        return $is_rtl ? 'rtl' : 'ltr';
    }
}

if (!function_exists('getCurrencies')) {
    function getCurrencies()
    {
        return DB::table('currencies')->pluck('code', 'id');
    }
}

if (!function_exists('getDefaultCurrency')) {
    function getDefaultCurrency()
    {
        $settings = getSettings();
        return $settings['general']['default_currency'];
    }
}

if (!function_exists('getDefaultCurrencyCode')) {
    function getDefaultCurrencyCode()
    {
        return getDefaultCurrency()?->code;
    }
}

if (!function_exists('getDefaultCurrencySymbol')) {
    function getDefaultCurrencySymbol()
    {
        $currency = getDefaultCurrency();
        if (!$currency) {
            return new \Illuminate\Support\HtmlString(''); // fallback
        }

        $symbol = $currency->symbol ?? '';
        $code = strtoupper($currency->code ?? '');

        $isRiyal = in_array($code, ['SAR', 'OMR', 'QAR', 'YER']) || stripos($symbol, 'ر') !== false;

        if ($isRiyal) {
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1124.14 1256.39" width="13" height="13">
  <path fill="#231f20" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/>
  <path fill="#231f20" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/>
</svg>
SVG;

            return new \Illuminate\Support\HtmlString($svg);
        }

        return new \Illuminate\Support\HtmlString($symbol);
    }
}

if (!function_exists('getCurrencySymbolByCode')) {
    function getCurrencySymbolByCode($currencyCode)
    {
        return DB::table('currencies')?->where('code', $currencyCode)?->whereNull('deleted_at')?->value('symbol');
    }
}

if (!function_exists('covertDefaultExchangeRate')) {
    function covertDefaultExchangeRate($amount)
    {
        return currencyConvert(getDefaultCurrencyCode(), $amount);
    }
}

if (!function_exists('getCurrencyExchangeRate')) {
    function getCurrencyExchangeRate($currencyCode)
    {
        return DB::table('currencies')?->where('code', $currencyCode)?->whereNull('deleted_at')?->value('exchange_rate');
    }
}

if (!function_exists('getActiveCurrencies')) {
    function getActiveCurrencies()
    {
        return Currency::where('status', true)?->whereNull('deleted_at')?->get();
    }
}

if (!function_exists('currencyConvert')) {
    function currencyConvert($currencySymbol, $amount)
    {
        $exchangeRate = getCurrencyExchangeRate($currencySymbol) ?? 1;
        $price = $amount * $exchangeRate;
        return round($price);
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $currencyCode = null)
    {
        $currencyCode = $currencyCode ?? session('currency', getDefaultCurrencyCode());
        $currency = DB::selectOne("SELECT symbol, code FROM currencies WHERE code = ?", [$currencyCode]);
        $currencySymbol = $currency->symbol ?? getDefaultCurrencySymbol();
        $convertedAmount = currencyConvert($currencyCode, $amount);
        $formatted = number_format($convertedAmount, 2);

        $isRiyal = in_array(strtoupper($currencyCode), ['SAR', 'OMR', 'QAR', 'YER']) || stripos($currencySymbol, 'ر') !== false;

        if ($isRiyal) {
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1124.14 1256.39" width="13" height="13">
  <path fill="#231f20" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/>
  <path fill="#231f20" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/>
</svg>
SVG;
            return new \Illuminate\Support\HtmlString($formatted . ' ' . $svg);
        }

        return new \Illuminate\Support\HtmlString($currencySymbol . ' ' . $formatted);
    }
}


if (!function_exists('isDefaultLang')) {
    function isDefaultLang($id)
    {
        $settings = getSettings();
        if ($settings) {
            if (isset($settings['general'])) {
                return $settings['general']['default_language_id'] == $id;
            }
        }
    }
}

if (!function_exists('getPluginBySlug')) {
    function getPluginBySlug($slug)
    {
        return DB::selectOne("SELECT * FROM plugins WHERE slug = ? AND deleted_at IS NULL LIMIT 1", [$slug]);
    }
}

if (!function_exists('getReferralCodeByName')) {
    function getReferralCodeByName($name)
    {
        $name = strtoupper(str_replace(' ', '', $name));
        $firstChar = substr($name, 0, 3);
        do {

            $referral_code = $firstChar . (mt_rand(100, 999));

        } while (User::where("referral_code", "=", $referral_code)?->exists());

        return $referral_code;
    }
}

if (!function_exists('addWidget')) {

    function addWidget(string $id, string $name, callable $callback, array $options = [])
    {

        try {

            if (shouldRegisterAdminUi()) {
                $widgetManager = app(WidgetManager::class);
                $widgetManager->registerWidget($id, $name, $callback, $options);
            }

        } catch (Exception $e) {

            throw $e;
        }
    }
}

if (!function_exists('shouldRegisterAdminUi')) {
    function shouldRegisterAdminUi()
    {
        if (app()->runningInConsole()) {
            return false;
        }

        $request = request();
        if ($request->expectsJson() || $request->isJson() || $request->wantsJson() || $request->ajax()) {
            return false;
        }

        return true;
    }
}

if (!function_exists('add_menu')) {
    function add_menu(
        $label,
        $module_slug,
        $permission = null,
        $slug = null,
        $route = null,
        $params = null,
        $section = null,
        $parent_slug = null,
        $icon = null,
        $position = null,
        $badge = 0,
        $badgeable = null,
    )
    {

        if (shouldRegisterAdminUi()) {
            if (DB::connection()?->getPDO() && DB::connection()?->getDatabaseName()) {
                try {

                    $slug = $slug ?? Str::slug($label);
                    $menuItem = MenuItems::isSlugExists($slug);
                    $module = Plugin::isSlugExists($module_slug);
                    $parent = MenuItems::isSlugExists($parent_slug);
                    $menu = Menus::find(1);
                    if ($module && $menu) {
                        if (!$menuItem) {
                            $menuItem = MenuItems::create([
                                'label' => $label,
                                'route' => $route,
                                'params' => $params,
                                'slug' => $slug,
                                'permission' => $permission,
                                'section' => $section,
                                'icon' => $icon ?? 'ri-plug-line',
                                'parent' => $parent?->id ?? 0,
                                'sort' => $position ?? MenuItems::getNextSortRoot(1),
                                'depth' => $parent ? 1 : 0,
                                'menu' => $menu?->id,
                                'module' => $module?->id,
                                'status' => 0,
                                'created_by_id' => 1,
                                'badge' => $badge,
                                'badgeable' => $badgeable ? 1 : 0,
                            ]);
                        } else {

                            $menuItem->update([
                                'label' => $label ?? $menuItem->label,
                                'route' => $route ?? $menuItem->route,
                                'slug' => $slug ?? $menuItem->slug,
                                'params' => $params ?? $menuItem->params,
                                'permission' => $permission,
                                'section' => $section ?? $menuItem->section,
                                'icon' => $icon ?? $menuItem->icon,
                                'sort' => $position ?? $menuItem->sort,
                                'parent' => $parent?->id ?? 0,
                                'menu' => 1,
                                'status' => $module?->status,
                                'badge' => $badge ?? 0,
                                'badgeable' => $badgeable,
                            ]);
                        }

                        $menuItem->refresh();
                        return $menuItem;
                    }

                } catch (Exception $e) {

                    // throw $e;
                }
            }
        }
    }

    if (!function_exists('createAttachment')) {
        function createAttachment()
        {
            $attachment = new Attachment();
            $attachment->save();
            return $attachment;
        }
    }

    if (!function_exists('storeImage')) {
        function storeImage($request, $model, $collectionName = 'attachment')
        {
            foreach ($request as $media) {
                $attachments[] = addMedia($model, $media, $collectionName);
            }
            $model->delete($model->id);
            return $attachments;
        }
    }

    if (!function_exists('addMedia')) {
        function addMedia($model, $media, $collectionName = 'attachment')
        {

            $media = $model->addMedia($media)->toMediaCollection($collectionName);
            $model->delete($model->id);
            return $media;
        }
    }

    if (!function_exists('uploadFileMedia')) {
        function uploadFileMedia($model, $media, $collectionName = 'attachment')
        {
            $media = $model->addMedia($media)->toMediaCollection($collectionName);
            return $media;
        }
    }

    if (!function_exists('getDefaultSMSMethod')) {
        function getDefaultSMSMethod()
        {
            $settings = getSettings();
            return $settings['general']['default_sms_gateway'] ?? null;
        }
    }

    if (!function_exists('convertFileSize')) {
        function convertFileSize($size)
        {
            $units = [' bytes', ' KB', ' MB', ' GB', ' TB'];
            for ($i = 0; $size > 1024; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . ' ' . $units[$i];
        }
    }

    if (!function_exists('getAllgetPaymentMethodListModules')) {
        function getAllPaymentModules()
        {
            return Module::all();
        }
    }

    if (!function_exists('getPaymentMethodList')) {
        function getPaymentMethodList()
        {
            $paymentMethods = [];
            $settings = getSettings();
            $modules = getAllPaymentModules();
            $paymentMethods[] = [
                'name' => __('static.cash'),
                'slug' => 'cash',
                'image' => asset('images/payment/cod.svg'),
                'status' => (bool)@$settings['activation']['cash_payments'] ?? false,
            ];
            $paymentMethods[] = [
                'name' => __('static.wallet'),
                'slug' => 'wallet',
                'image' => asset('images/payment/wallet.svg'),
                'status' => (bool)@$settings['activation']['rider_wallet'] ?? false,
            ];
            foreach ($modules as $module) {
                if ($module?->isEnabled()) {
                    $paymentFile = module_path($module->getName(), 'config/payment.php');
                    if (file_exists($paymentFile)) {
                        $payment = include $paymentFile;
                        $paymentMethods[] = [
                            'name' => $payment['name'],
                            'slug' => $payment['slug'],
                            'title' => $payment['title'],
                            'processing_fee' => $payment['processing_fee'],
                            'subscription' => $payment['subscription'],
                            'image' => url($payment['image']),
                            'status' => $module?->isEnabled(),
                        ];
                    }
                }
            }

            return $paymentMethods;
        }
    }
    if (!function_exists('getRiderPaymentMethodList')) {
        function getRiderPaymentMethodList(?string $type = null): array
        {
            $paymentMethods = [];
            $settings = \Modules\Taxido\Models\TaxidoSetting::latest('created_at')->first();
            $values = $settings['taxido_values'];
            $modules = getAllPaymentModules();
            if ($type !== 'store') {
                $paymentMethods[] = [
                    'name' => __('static.cash'),
                    'slug' => 'cash',
                    'image' => asset('images/payment/cod.svg'),
                    'status' => (bool)@$values['activation']['cash_payments'] ?? false,
                ];
                $paymentMethods[] = [
                    'name' => __('static.wallet'),
                    'slug' => 'wallet',
                    'image' => asset('images/payment/wallet.svg'),
                    'status' => (bool)@$values['activation']['rider_wallet'] ?? false,
                ];
            }
            foreach ($modules as $module) {
                if ($module?->isEnabled()) {
                    $paymentFile = module_path($module->getName(), 'config/payment.php');
                    if (file_exists($paymentFile)) {
                        $payment = include $paymentFile;
                        $paymentMethods[] = [
                            'name' => $payment['name'],
                            'slug' => $payment['slug'],
                            'title' => $payment['title'],
                            'processing_fee' => $payment['processing_fee'],
                            'subscription' => $payment['subscription'],
                            'image' => url($payment['image']),
                            'status' => $module?->isEnabled(),
                        ];
                    }
                }
            }

            return $paymentMethods;
        }
    }
    if (!function_exists('getDriverPaymentMethodList')) {
        function getDriverPaymentMethodList(?string $type = null): array
        {
            $paymentMethods = [];
            $settings = \Modules\Taxido\Models\TaxidoSetting::latest('created_at')->first();
            $values = $settings['taxido_values'] ?? [];
            $modules = getAllPaymentModules();

            // only add cash/wallet if type is not "store"
            if ($type !== 'store') {
                $paymentMethods[] = [
                    'type' => 'cash',
                    'name' => __('static.cash'),
                    'slug' => 'cash',
                    'image' => asset('images/payment/cod.svg'),
                    'status' => (bool)($values['activation']['cash_payments'] ?? false),
                ];

                $paymentMethods[] = [
                    'type' => 'wallet',
                    'name' => __('static.wallet'),
                    'slug' => 'wallet',
                    'image' => asset('images/payment/wallet.svg'),
                    'status' => (bool)($values['activation']['driver_wallet'] ?? false),
                ];
            }

            foreach ($modules as $module) {
                if ($module?->isEnabled()) {
                    $paymentFile = module_path($module->getName(), 'config/payment.php');
                    if (file_exists($paymentFile)) {
                        $payment = include $paymentFile;
                        $paymentMethods[] = [
                            'type' => $payment['slug'],
                            'name' => $payment['name'],
                            'slug' => $payment['slug'],
                            'title' => $payment['title'],
                            'processing_fee' => $payment['processing_fee'],
                            'subscription' => $payment['subscription'],
                            'image' => url($payment['image']),
                            'status' => $module?->isEnabled(),
                        ];
                    }
                }
            }

            return $paymentMethods;
        }
    }

    function getStartAndEndDate($sort, $startDate = null, $endDate = null)
    {
        $startCurrentDate = Carbon::now();
        $endCurrentDate = Carbon::now();

        switch ($sort) {
            case 'today':
                return [
                    'start' => $startCurrentDate->startOfDay(),
                    'end' => $endCurrentDate->endOfDay(),
                ];

            case 'this_week':
                return [
                    'start' => $startCurrentDate->startOfWeek(),
                    'end' => $endCurrentDate->endOfWeek(),
                ];

            case 'this_month':
                return [
                    'start' => $startCurrentDate->startOfMonth(),
                    'end' => $endCurrentDate->endOfMonth(),
                ];

            case 'this_year':
                return [
                    'start' => $startCurrentDate->startOfYear(),
                    'end' => $endCurrentDate->endOfYear(),
                ];

            case 'custom':
                if ($startDate && $endDate) {
                    return [
                        'start' => Carbon::createFromFormat('m-d-Y', $startDate)->startOfDay(),
                        'end' => Carbon::createFromFormat('m-d-Y', $endDate)->endOfDay(),
                    ];
                }
                break;
            default:
                return [
                    'start' => $startCurrentDate->startOfYear(),
                    'end' => $endCurrentDate->endOfYear(),
                ];
        }
    }

    if (!function_exists('getPaymentMethodConfigs')) {
        function getPaymentMethodConfigs()
        {
            $paymentMethods = [];
            $modules = getAllPaymentModules();
            foreach ($modules as $module) {
                $paymentFile = module_path($module->getName(), 'config/payment.php');
                if (file_exists($paymentFile)) {
                    $payment = include $paymentFile;
                    $paymentMethods[] = [
                        'name' => $payment['name'],
                        'slug' => $payment['slug'],
                        'image' => url($payment['image']),
                        'title' => $payment['title'],
                        'processing_fee' => $payment['processing_fee'],
                        'status' => $module?->isEnabled(),
                        'configs' => $payment['configs'],
                        'fields' => $payment['fields'],
                        'subscription' => $payment['subscription'],
                    ];
                }
            }

            return $paymentMethods;
        }
    }

    if (!function_exists('getPaymentLogoUrl')) {
        function getPaymentLogoUrl($paymentMethodSlug)
        {
            $paymentMethods = getPaymentMethodConfigs();
            foreach ($paymentMethods as $paymentMethod) {
                if ($paymentMethod['slug'] === $paymentMethodSlug) {
                    return $paymentMethod['image'];
                }
            }
            return null;
        }
    }

    if (!function_exists('getEncrypter')) {
        function getEncrypter()
        {
            return App::make('encrypter');
        }
    }

    if (!function_exists('isEncrypted')) {
        function isEncrypted($key)
        {
            return strpos($key, 'eyJpdiI') === 0;
        }
    }

    if (!function_exists('getBlogsByIds')) {
        function getBlogsByIds($ids = [])
        {
            if (count($ids)) {
                $locale = Session::get('front-locale', getDefaultLangLocale());
                $blogs = Blog::whereIn('id', $ids)?->with(['blog_thumbnail'])->orderBy('created_at')->paginate(9);
                $blogs = $blogs ? $blogs->map(function ($blog) use ($locale) {
                    return $blog->toArray($locale);
                })->toArray() : [];

                return $blogs;
            }
            return [];
        }
    }

    if (!function_exists('getFaqsByIds')) {
        function getFaqsByIds($ids = [])
        {
            if (count($ids)) {
                $locale = Session::get('front-locale', getDefaultLangLocale());
                $faqs = Faq::whereIn('id', $ids)->orderBy('created_at')->get();

                $faqs = $faqs ? $faqs->map(function ($faq) use ($locale) {
                    return $faq->toArray($locale);
                })->toArray() : [];

                return $faqs;
            }
            return [];
        }
    }

    if (!function_exists('getTestimonialByIds')) {
        function getTestimonialByIds($ids = [])
        {
            if (count($ids)) {
                return Testimonial::whereIn('id', $ids)->orderBy('created_at')->get();
            }
            return [];
        }
    }

    if (!function_exists('roundNumber')) {
        function roundNumber($numb)
        {
            return number_format($numb, 2, '.', '');
        }
    }

    if (!function_exists('isActiveSection')) {
        function isActiveSection($items)
        {
            $itemPermissions = $items->pluck('permission')->toArray();
            foreach ($itemPermissions as $permission) {
                if (empty($permission) || auth()?->user()?->can($permission)) {
                    return true;
                }
            }
            return false;
        }
    }

    if (!function_exists('getLandingPage')) {
        function getLandingPage()
        {
            $content = LandingPage::pluck('content')?->first();
            return $content;
        }
    }

    if (!function_exists('isSameModelSlug')) {
        function isSameModelSlug($model, $slug)
        {
            return $model->slug == $slug;
        }
    }
    if (!function_exists('createSitemap')) {
        function createSitemap()
        {
            $sitemap = Sitemap::create();

            Blog::all()->each(function (Blog $blog) use ($sitemap) {
                $sitemap->add(
                    Url::create("/blog/{$blog->slug}")
                        ->setPriority(0.9)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            });

            Page::all()->each(function (Page $page) use ($sitemap) {
                $sitemap->add(
                    Url::create("/page/{$page->slug}")
                        ->setPriority(0.9)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            });

            return $sitemap;
        }
    }

    if (!function_exists('getSMSGatewayList')) {
        function getSMSGatewayList()
        {
            $smsGateways = [];
            $modules = Module::all();
            $smsGateways[] = [
                'name' => 'Firebase',
                'slug' => 'firebase',
                'image' => null,
                'status' => true,
            ];
            foreach ($modules as $module) {
                $smsFile = module_path($module->getName(), 'config/sms.php');
                if (file_exists($smsFile)) {
                    $sms = include $smsFile;
                    $smsGateways[] = [
                        'name' => $sms['name'],
                        'slug' => $sms['slug'],
                        'image' => url($sms['image']),
                        'status' => $module?->isEnabled(),
                    ];
                }
            }

            return $smsGateways;
        }
    }

    if (!function_exists('getSMSGatewayConfigs')) {
        function getSMSGatewayConfigs()
        {
            $smsGateways = [];
            $modules = getAllPaymentModules();
            foreach ($modules as $module) {
                $smsFile = module_path($module->getName(), 'config/sms.php');
                if (file_exists($smsFile)) {
                    $sms = include $smsFile;
                    $smsGateways[] = [
                        'name' => $sms['name'],
                        'slug' => $sms['slug'],
                        'image' => url($sms['image']),
                        'status' => $module?->isEnabled(),
                        'configs' => $sms['configs'],
                        'fields' => $sms['fields'],
                    ];
                }
            }

            return $smsGateways;
        }
    }

    if (!function_exists('getDefaultSMSGateway')) {
        function getDefaultSMSGateway()
        {
            $settings = getSettings();
            return $settings['general']['default_sms_gateway'] ?? null;
        }
    }

    if (!function_exists('sendSMS')) {
        function sendSMS($sendTo, $message)
        {
            try {
                $defaultSMSGateway = getDefaultSMSGateway();
                if (!$defaultSMSGateway) {
                    throw new Exception(__('static.sms_gateways.default_not_select'), 400);
                }

                if ($defaultSMSGateway !== 'firebase') {
                    $module = Module::find($defaultSMSGateway);
                    if ($module) {
                        if (!is_null($module) && $module?->isEnabled()) {
                            $moduleName = $module->getName();
                            $sms = 'Modules\\' . $moduleName . '\\SMS\\' . $moduleName;
                            if (class_exists($sms) && method_exists($sms, 'getIntent')) {
                                return $sms::getIntent($sendTo, $message);
                            }
                        }
                    }
                    throw new Exception(__('static.sms_gateways.not_found'), 400);
                }

                return true;

            } catch (Exception $e) {
                throw new ExceptionHandler($e->getMessage(), $e->getCode());
            }
        }
    }

    if (!function_exists('getActiveMenuItem')) {
        function getActiveMenuItem($menuList)
        {
            $activeMenus = [];
            foreach ($menuList as $menuItem) {
                if (isModuleActive($menuItem)) {
                    $activeMenus[] = $menuItem;
                }

                if (!$menuItem->module) {
                    $activeMenus[] = $menuItem;
                }
            }

            return collect($activeMenus);
        }
    }

    if (!function_exists('isModuleActive')) {
        function isModuleActive($model)
        {
            return Plugin::where('id', $model->module)->where('status', true)->exists();
        }
    }

    if (!function_exists('encryptKey')) {
        function encryptKey($key)
        {
            if (config('app.demo')) {
                if ($key) {
                    return getEncrypter()?->encrypt($key);
                }
            }
            return $key;
        }
    }

    if (!function_exists('decryptKey')) {
        function decryptKey($key)
        {
            if (config('app.demo')) {
                if (isEncrypted($key)) {
                    return getEncrypter()?->decrypt($key);
                }

                return $key;
            }

            return $key;
        }
    }

    if (!function_exists('add_quick_link')) {
        function add_quick_link($label, $route, $icon)
        {
            if (shouldRegisterAdminUi()) {
                $quickLinks = app('quickLinks');
//                $quickLinks->push([
//                    'label' => $label,
//                    'route' => $route,
//                    'icon'  => $icon,
//                ]);
                $quickLinks[] = [
                    'label' => $label,
                    'route' => $route,
                    'icon' => $icon,
                ];
                app()->instance('quickLinks', $quickLinks);
            }
        }
    }

    if (!function_exists('get_quick_links')) {
        function get_quick_links()
        {
            return app('quickLinks')->toArray();
        }
    }

    if (!function_exists('file_exists_public')) {
        function file_exists_public($url)
        {
            // Extract the relative path from the URL
            $baseUrl = config('app.url') . '/';
            $relativePath = str_replace($baseUrl, '', $url);
            $filePath = public_path($relativePath);
            return file_exists($filePath);
        }
    }

    if (!function_exists('render_image')) {
        function render_image($file, $mimeImageMapping = [], $defaultImage = 'images/nodata1.webp')
        {
            if (substr($file?->mime_type, 0, 5) == 'image' && file_exists_public($file->original_url)) {
                return $file->original_url;
            }

            if ($file?->mime_type !== null && isset($mimeImageMapping[$file->mime_type])) {
                return asset($mimeImageMapping[$file->mime_type]);
            }

            return asset($defaultImage);
        }
    }
}

if (!function_exists('formatNotificationTime')) {
    function formatNotificationTime($created_at, string $tz = null): string
    {
        $dt = $created_at?->copy()->timezone($tz ?? config('app.timezone'));
        if (!$dt) return '';

        $now = now($dt->timezone);

        if ($dt->diffInSeconds($now) < 60) {
            return __('Now');
        }

        if ($dt->isToday()) {
            return __('Today');
        }

        if ($dt->isYesterday()) {
            return __('Yesterday');
        }

        if ($dt->isSameYear($now)) {
            return $dt->translatedFormat('F j');
        }

        return $dt->translatedFormat('F j, Y');
    }
}

