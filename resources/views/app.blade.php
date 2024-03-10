<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Experimente a excelência operacional em um só lugar! Nosso software de gestão integra funcionalidades avançadas, desde o eficiente PDV e controle financeiro simplificado até a gestão otimizada de compras e o completo módulo de gerenciamento de stock e inventário. Inovação, segurança e eficiência em cada aspecto do seu negócio, reunidos em uma solução única para impulsionar o sucesso da sua empresa. SIGESC-SOFT">
        <title inertia>{{ 'SIGESC-SOFT Software de Gestão Integrado comercial' }}</title>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-339234288"></script>
        <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-339234288'); </script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased" style="box-sizing: border-box">
        <h1 class="fixed z-0 text-sm truncate text-center w-full text-gray-200">SIGESC-SOFT Software de Gestão Integrado comercial Software de gestão de Stock, Inventário, Funcionários, Ponto de venda POS PDV , Faturamento, Gerenciamento de compras, Melhore software de gestão integrado comercial</h1>
        @inertia
    </body>
</html>
