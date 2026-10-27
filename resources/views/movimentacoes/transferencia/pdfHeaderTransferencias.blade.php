<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<div id="header" style="margin-bottom: 15px;">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width: 55px;">
                <img src="{{public_path('imagens/template/logo_estado.png')}}" height="80" alt="">
            </td>
            <td style="width: 485px; text-align: center;">
                <h5 style="width: 100%; font-size: 14px; font-weight: bold; text-align: center; font-family: Arial; letter-spacing: 1px">
                    GOVERNO DO ESTADO DO
                    {{ config('app.uf_extenso') }}</h5>
                <h6 style="width: 100%; font-size: 12px; text-align: center; margin-top: -20px;  font-family: Arial; letter-spacing: 1px">
                    {{ config('app.secretaria_extenso') }} - {{ config('app.secretaria') }}</h6>
                <h6 style="width: 100%; font-size: 12px; text-align: center; margin-top: -25px;  font-family: Arial; letter-spacing: 1px">
                    {{ $unidadeGeradora }}
                </h6>
                <br>
                <div style=" padding: 5px 0; background-color:#CCC; width: 100%; font-size: 13px; text-align: center; margin-top: -30px;  font-family: Arial; letter-spacing: 1px">
                    RELATÓRIO DE TRANSFERÊNCIA DE UNIDADE - ORIGEM: {{ $nomeUnidadeOrigem }} / DESTINO: {{ $nomeUnidadeDestino }}
                </div>
            </td>
            <td style="width: 55px;">
                <img src="{{public_path('imagens/template/logo_secretaria.png')}}" height="80" alt="">
            </td>
        </tr>
        </tbody>
    </table>
</div>

</body>
</html>