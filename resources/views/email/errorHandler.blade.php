<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p><strong>ERROR</strong></p>
Nome: {{$nome}} <br>
CPF: {{ $cpf }} <br>
Unidade: {{$unidade}} <br>
Data e Hora: {{$time}}
<br><br>
Mensagem: {{$mensagem->getMessage()}}
<br>

@if(!is_null($mensagem->getCode()))
Código: {{$mensagem->getCode()}}
<br>
@endif

@if(!is_null($mensagem->getFile()))
Arquivo: {{$mensagem->getFile()}}
<br>
@endif

@if(!is_null($mensagem->getLine()))
Linha: {{$mensagem->getLine()}}
<br>
@endif


@if(method_exists($mensagem, 'severity'))
Tipo: {{$mensagem->getSeverity()}}
<br>
@endif

Árvore de Erros:
<br>
@php
    $valores = $mensagem->getTrace();
@endphp
@foreach($trace as $item)
    {{$item}}
    <br>
@endforeach
<p style="font-size: 11px">
    GOVERNO DO ESTADO DO {{ config('app.uf_extenso') }}<br>

    {{ config('app.secretaria_extenso') }} -IAPEN<br>

    {{ config('app.ti') }}<br>
</p>
<p style="font-size: 11px"><em>Em caso de divergência entrar em contato com a {{ config('app.ti') }}: siapenac@gmail.com</em></p>
</body>
</html>