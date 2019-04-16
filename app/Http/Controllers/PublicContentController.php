<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function privacyPolicy() {
        return view("new_version.landing-pages.privacy-policy");
    }

    public function termsOfUse() {
        return view("new_version.landing-pages.terms-of-use");
    }

    public function supportPage() {
        return view("new_version.landing-pages.supportPage");
    }
}
