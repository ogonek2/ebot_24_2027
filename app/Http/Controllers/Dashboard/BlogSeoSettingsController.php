<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Support\SeoResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogSeoSettingsController extends Controller
{
    public function edit(): View
    {
        $seoPage = SeoPage::forKey(SeoPage::KEY_BLOG_INDEX);

        return view('dashboard.blog.seo-settings', [
            'seoPage' => $seoPage,
            'action' => route('copywriter.blog-seo.update'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $seoPage = SeoPage::forKey(SeoPage::KEY_BLOG_INDEX);

        $data = $request->validate(SeoResolver::validationRules());
        $data['robots'] = $data['robots'] ?: null;

        $seoPage->fill($data);
        SeoResolver::applyOgImageUpload($seoPage, $request, 'src/seo/blog');
        $seoPage->save();

        return redirect()
            ->route('copywriter.blog-seo.edit')
            ->with('status', 'SEO налаштування списку блогу збережено.');
    }
}
