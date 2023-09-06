<!DOCTYPE html>
<html lang="it">

@include('partials.head')

<!-- Google Tag Manager (noscript) -->

<!-- End Google Tag Manager (noscript) -->

@include('partials.header')

@if (!Route::is('home'))
    @include('partials.hero-secondary')
@endif

<main>
    @yield('content')
</main>


<!-- # CONTATTAMI -->
@include('partials.contact-form')

<!-- ! FOOTER -->

@include('partials.footer')

@if ($status || $errors->any())
    <script defer>
        setTimeout(() => {
            document.getElementById('response-anchor').scrollIntoView(false);
        }, 50);
    </script>
@endif


<!-- iubenda -->
<script type="text/javascript">
    var _iub = _iub || [];
    _iub.csConfiguration = {
        "consentOnContinuedBrowsing": false,
        "cookiePolicyId": 29156312,
        "countryDetection": true,
        "floatingPreferencesButtonDisplay": "bottom-right",
        "gdprAppliesGlobally": false,
        "invalidateConsentWithoutLog": true,
        "perPurposeConsent": true,
        "siteId": 2614419,
        "whitelabel": false,
        "lang": "it",
        "banner": {
            "acceptButtonCaptionColor": "#FFFFFF",
            "acceptButtonColor": "#0073CE",
            "acceptButtonDisplay": true,
            "backgroundColor": "#FFFFFF",
            "closeButtonRejects": true,
            "customizeButtonCaptionColor": "#4D4D4D",
            "customizeButtonColor": "#DADADA",
            "customizeButtonDisplay": true,
            "explicitWithdrawal": true,
            "fontSize": "16px",
            "listPurposes": true,
            "logo": null,
            "position": "float-bottom-center",
            "textColor": "#000000",
            "content": "Noi e terze parti selezionate utilizziamo cookie o tecnologie simili per finalità tecniche come specificato nella cookie policy",
            "customizeButtonCaption": "Ulteriori informazioni"
        }
    };
</script>
<script type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8"  async></script>
