<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class CheckSitemap
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!File::exists(public_path('sitemap.xml'))) {
            SitemapGenerator::create('https://bruler.ru/')
                ->hasCrawled(function (Url $url) {
                    if (
                        $url->segment(2) === 'password-reset' ||
                        $url->segment(1) === 'login' ||
                        $url->segment(1) === 'api' ||
                        $url->segment(1) === 'register'
                    ) {
                        return null;
                    }

                    $url->setLastModificationDate(Carbon::yesterday())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY);
                    return $url;
                })
                ->writeToFile(public_path('sitemap.xml'));
        }

        return $next($request);
    }
}
