<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Repositories\Front\HomeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class NewHomeController extends Controller
{
    public $landing_content;

    public function __construct()
    {
        $landingPage = LandingPage::first();

        if ($landingPage) {
            $this->landing_content = $landingPage->content;
        }
    }

    public function index()
    {
        return Inertia::render('Home', [
            'landing_content' => $this->landing_content
        ]);
    }

    public function about()
    {
        return Inertia::render('About', [
            'landing_content' => $this->landing_content
        ]);
    }

    public function howItWorks()
    {
        return Inertia::render('HowItWorksPage', [
            'landing_content' => $this->landing_content
        ]);
    }

    public function pricing()
    {
        return Inertia::render('Pricing', [
            'landing_content' => $this->landing_content
        ]);
    }

}
