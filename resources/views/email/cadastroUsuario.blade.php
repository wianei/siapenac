<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p><strong>Dados para acesso à sua conta do {{ config('app.name') }}</strong></p>
Órgão: {{$orgao}}<br>
Nome: {{$nome}}<br>
<p style="width: 200px; border: 1px double #CCCCCC; padding: 5px">Login: {{$cpf}}<br>Senha: {{$senha}}</p>
<br>
<p style="font-style: italic">Senha gerada automaticamente. No seu primeiro acesso, altere-a.</p>

<br><br>
<p style="font-size: 11px">
    <a href="{{env('APP_URL')}}">siapenac.depen.gov.br</a><br>

    GOVERNO DO ESTADO DO {{ config('app.uf_extenso') }}<br>

    {{ config('app.secretaria_extenso') }} -IAPEN<br>

    {{ config('app.ti') }}<br>
</p>
<p style="font-size: 11px"><em>Em caso de divergência entrar em contato com a {{ config('app.ti') }}: siapenac@gmail.com</em></p>
</body>
</html>