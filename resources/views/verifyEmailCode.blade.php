<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verificação de E-mail</title>
</head>
<body>
@component('mail::message')

    <p>Saudações {!!$name!!} seu código de verificação </p>

    @component('mail::panel')
        {!!$verifyEmailCode!!}
    @endcomponent

    @component('mail::button', ['url' => 'https://geral.sisgesc.net'])
        Visitar Agora
    @endcomponent

@endcomponent
</body>
</html>
