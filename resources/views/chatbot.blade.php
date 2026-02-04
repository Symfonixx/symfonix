<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Inquiry Chatbot</title>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/botman-web-widget/build/assets/css/chat.min.css">
</head>
<body>
<main id="main-content">
    <noscript>{{ __('chat.lead.widget_intro') }}</noscript>
</main>
<script>
    window.botmanWidget = {
        title: '{{ config('app.name') }}',
        aboutText: '{{ __('chat.lead.widget_about') }}',
        introMessage: '{{ __('chat.lead.widget_intro') }}',
        chatServer: '{{ route('botman.handle') }}',
        frameEndpoint: '{{ route('chatbot.widget') }}',
        userId: 'web-visitor',
        mainColor: '#2563eb',
        bubbleBackground: '#1f2937',
        bubbleAvatarUrl: '',
    };
</script>
<script id="botmanWidget"
        src="https://cdn.jsdelivr.net/npm/botman-web-widget/build/js/chat.js"></script>
</body>
</html>
