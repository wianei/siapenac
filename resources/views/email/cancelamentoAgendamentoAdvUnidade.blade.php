<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p><strong>Cancelamento de agendamento - Atendimento a Interno</strong></p>

<p style="width: 100%; border: 1px double #CCCCCC; padding: 5px; display: table">
    Unidade: {{$unidade}}<br>
    Interno: {{$interno}}<br>
    Data: {{$data}}-{{$horario}}<br>
    Motivação: {!! $mensagem !!}
</p>
<br><br>
<p style="font-size: 11px">
    GOVERNO DO ESTADO DO {{ config('app.uf_extenso') }}<br>

    {{ config('app.secretaria_extenso') }} -SEAP<br>

   {{$unidade}}C<br>
</p>
</body>
</html>