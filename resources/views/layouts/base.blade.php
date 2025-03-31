<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    @php
        $page_render_type;
        $page_type;
        $page_title;
        $route = request()->route();
        switch ($route->uri) {
            case '{category}':
                $category_id = $route->parameters['category']['id'];
                $db_category = \App\Models\Category::find($category_id);
                $page_type = \App\Models\Page::PAGE_TYPE_CATEGORY;
                break;
            case '{category}/{product}':
                $category_id = $route->parameters['category']['id'];
                $product_id = $route->parameters['product']['id'];
                $db_product = \App\Models\Product::find($product_id);
                $page_type = \App\Models\Page::PAGE_TYPE_PRODUCT;
                break;
            default:
                $page = \App\Models\Page::where('route', $route->uri)->first();
                $page_type = \App\Models\Page::PAGE_TYPE_LANDING;
                break;
        }

        $page_render_type = match ($page_type) {
            \App\Models\Page::PAGE_TYPE_CATEGORY,
            \App\Models\Page::PAGE_TYPE_PRODUCT
                => \App\Models\Page::RENDER_TYPE_DYNAMIC_PAGE,
            default => \App\Models\Page::RENDER_TYPE_STATIC_PAGE,
        };

        $page_title = match ($page_type) {
            \App\Models\Page::PAGE_TYPE_CATEGORY => $db_category->seo_title ?? config('app.name'),
            \App\Models\Page::PAGE_TYPE_PRODUCT => $db_product->seo_title ?? config('app.name'),
            default => $page->seo_title ?? config('app.name'),
        };
    @endphp
    <title>{{ $page_title }}</title>
    @if ($page_render_type === \App\Models\Page::RENDER_TYPE_STATIC_PAGE)
        @if (isset($page->seo_fields) && count($page->seo_fields) !== 0)
            @foreach ($page->seo_fields as $seo_field)
                <meta name="{{ $seo_field['meta_tag'] }}" content="{{ $seo_field['content'] }}">
            @endforeach
        @endif
    @elseif($page_render_type === \App\Models\Page::RENDER_TYPE_DYNAMIC_PAGE)
        @if ($page_type === \App\Models\Page::PAGE_TYPE_PRODUCT && isset($db_product->seo) && count($db_product->seo) !== 0)
            @foreach ($db_product->seo as $seo_field)
                <meta name="{{ $seo_field['meta_tag'] }}" content="{{ $seo_field['content'] }}">
            @endforeach
        @endif
        @if ($page_type === \App\Models\Page::PAGE_TYPE_CATEGORY && isset($db_category->seo) && count($db_category->seo) !== 0)
            @foreach ($db_category->seo as $seo_field)
                <meta name="{{ $seo_field['meta_tag'] }}" content="{{ $seo_field['content'] }}">
            @endforeach
        @endif
    @endif
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    {{--		<link rel="shortcut icon" href="{{ url(asset('favicon.ico')) }}"> --}}


    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
    @livewireScripts

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @production
        <!-- Top.Mail.Ru counter -->
        <script type="text/javascript">
            var _tmr = window._tmr || (window._tmr = []);
            _tmr.push({
                id: "3478615",
                type: "pageView",
                start: (new Date()).getTime()
            });
            (function(d, w, id) {
                if (d.getElementById(id)) return;
                var ts = d.createElement("script");
                ts.type = "text/javascript";
                ts.async = true;
                ts.id = id;
                ts.src = "https://top-fwz1.mail.ru/js/code.js";
                var f = function() {
                    var s = d.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(ts, s);
                };
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })
            (document, window, "tmr-code");
        </script>
        <noscript>
            <div><img src="https://top-fwz1.mail.ru/counter?id=3478615;js=na" style="position:absolute;left:-9999px;"
                      alt="Top.Mail.Ru" /></div>
        </noscript>
        <!-- /Top.Mail.Ru counter -->

        <!-- Yandex.Metrika dataLayer -->
        <script type="text/javascript">
            (function(m, e, t, r, i, k, a) {
                m[i] = m[i] || function() {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(
                    k, a)
            })
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(96215006, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true,
                ecommerce: "dataLayer"
            });
            window.dataLayer = window.dataLayer || [];
        </script>
        <noscript>
            <div><img src="https://mc.yandex.ru/watch/96375810" style="position:absolute; left:-9999px;" alt="" />
            </div>
        </noscript>
        <!-- /Yandex.Metrika DataLayer -->
    @endproduction
</head>

<body
    x-data="bodyData"
    class="flex flex-col h-screen"
    x-init="init"
    :style="`--headerSize: ${headerSize}`"
>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bodyData', () => ({
            heightRunningTexts: 32,
            headerSizeDesktop: 56,
            headerSizeMobile: 48,
            headerSize: 56,
            isShowRunningTexts: !sessionStorage.getItem('hideRunningTexts'),

            init() {
                this.isMobile()
                this.toggleHeightRunningTexts()
            },

            toggleHeightRunningTexts() {
                if (!this.isShowRunningTexts) {
                    this.heightRunningTexts = 0
                } else {
                    this.heightRunningTexts = 32
                }

                if (this.isMobile()) {
                    this.headerSize = `${this.headerSizeMobile + this.heightRunningTexts}px`
                } else {
                    this.headerSize = `${this.headerSizeDesktop + this.heightRunningTexts}px`
                }
            },

            closeRunningTexts() {
                this.isShowRunningTexts = false;
                sessionStorage.setItem('hideRunningTexts', 'true');
                this.toggleHeightRunningTexts();
            },

            isMobile() {
                return window.innerWidth < 768
            }
        }));
    });
</script>

@production
    <script type="text/javascript">
        var digiScript = document.createElement('script');
        digiScript.src = '//aq.dolyame.ru/5503/client.js?ts=' + Date.now();
        digiScript.defer = true;
        digiScript.async = true;
        document.body.appendChild(digiScript);
    </script>
    <script type="text/javascript">
        ! function() {
            var t = document.createElement("script");
            t.type = "text/javascript", t.async = !0, t.src = 'https://vk.com/js/api/openapi.js?169', t.onload =
                function() {
                    VK.Retargeting.Init("VK-RTRG-1860770-4CqW9"), VK.Retargeting.Hit()
                }, document.head.appendChild(t)
        }();
    </script><noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1860770-4CqW9"
                            style="position:fixed; left:-999px;" alt="" /></noscript>
@endproduction
@yield('body')
@stack('scripts')

{{--    <livewire:cookie-popup />--}}
<script src="https://pay.yandex.ru/sdk/v1/pay.js" async></script>
</body>

</html>
