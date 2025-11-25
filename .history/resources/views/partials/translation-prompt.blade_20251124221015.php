<div
    id="trilinux-translation-banner"
    class="hidden fixed bottom-6 right-6 z-[60] max-w-md rounded-2xl border border-zinc-200 bg-white/95 px-5 py-4 shadow-2xl shadow-zinc-900/10 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80"
    role="alert"
    aria-live="polite"
>
    <div class="flex items-start gap-4">
        <div class="flex-1 text-sm leading-relaxed text-zinc-800 dark:text-zinc-100">
            <p class="font-semibold text-zinc-900 dark:text-white">O Trilinux quer ajudar na tradução.</p>
            <p>Receba uma versão traduzida da interface sem depender do Chrome.</p>
        </div>
        <div class="flex flex-shrink-0 items-center gap-2">
            <button
                id="trilinux-translate-now"
                class="inline-flex items-center rounded-full bg-indigo-600 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-500"
            >
                Traduzir agora
            </button>
            <button
                id="trilinux-translate-dismiss"
                class="inline-flex items-center rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-zinc-600 transition hover:border-zinc-300 dark:border-zinc-600 dark:text-zinc-300"
            >
                Lembrar depois
            </button>
        </div>
    </div>
</div>

<div
    id="google_translate_element"
    class="hidden fixed bottom-6 left-6 z-[60] w-full max-w-[320px] rounded-2xl border border-zinc-200 bg-white/95 p-4 text-xs shadow-2xl shadow-zinc-900/10 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80"
></div>

<script>
    (function () {
        const banner = document.getElementById('trilinux-translation-banner');
        const widget = document.getElementById('google_translate_element');
        if (!banner || !widget) {
            return;
        }

        const storageKey = 'trilinux.translationBannerHiddenUntil';
        const hideForMs = 60 * 60 * 1000; // 1 hour

        const shouldShowBanner = () => {
            const stored = Number(localStorage.getItem(storageKey));
            return !stored || stored < Date.now();
        };

        const showBanner = () => banner.classList.remove('hidden');
        const hideBanner = () => banner.classList.add('hidden');

        const renderTranslateWidget = () => {
            if (widget.dataset.loaded) {
                return;
            }

        if (window.google && window.google.translate) {
            new window.google.translate.TranslateElement(
                {
                    pageLanguage: 'pt',
                    layout: window.google.translate.TranslateElement.InlineLayout.SIMPLE,
                    multilanguagePage: true,
                },
                'google_translate_element'
            );
            widget.dataset.loaded = '1';
            return;
        }
        window.googleTranslateElementInit = function () {
            new window.google.translate.TranslateElement(
                {
                    pageLanguage: 'pt',
                    layout: window.google.translate.TranslateElement.InlineLayout.SIMPLE,
                    multilanguagePage: true,
                },
                'google_translate_element'
            );
            widget.dataset.loaded = '1';
            };

            if (!document.querySelector('script[src*="translate.google.com/translate_a/element.js"]')) {
                const script = document.createElement('script');
                script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                script.async = true;
                document.head.appendChild(script);
            }
        };

        const showTranslateWidget = () => {
            widget.classList.remove('hidden');
            renderTranslateWidget();
        };

        const handleDismiss = () => {
            localStorage.setItem(storageKey, String(Date.now() + hideForMs));
            hideBanner();
        };

        const handleTranslateClick = () => {
            hideBanner();
            showTranslateWidget();
        };

        const init = () => {
            if (shouldShowBanner()) {
                showBanner();
            }

            document.getElementById('trilinux-translate-now')?.addEventListener('click', handleTranslateClick);
            document.getElementById('trilinux-translate-dismiss')?.addEventListener('click', handleDismiss);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
