
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sindicância</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        body{
            font-family:Verdana;
        }
    </style>
</head>
<body>

<div class="certificado">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width: 55px;">
                <img src="{{public_path('imagens/template/logo_estado.png')}}" height="85" alt="">
            </td>
            <td style="width: 485px; text-align: center;">
                <h5 style="width: 100%; font-size: 16px; font-weight: bold; text-align: center;">GOVERNO DO ESTADO DO {{ config('app.uf_extenso') }}</h5>
                <h6 style="width: 100%; font-size: 13px; text-align: center;">{{ config('app.secretaria_extenso') }} - {{ config('app.secretaria') }}</h6>
            </td>
            <td style="width: 55px;">
                <img src="{{public_path('imagens/template/logo_secretaria.png')}}" height="65" alt="">
            </td>
        </tr>
        </tbody>
    </table>
    <div style="width:100%; border-top: 1px solid #CCC; margin-top: 5px; height: 15px"></div>

    {{--<div style="font-size: 15px; font-weight: bold; text-align: center">Termo de Responsabilidade de Cautela Especial de Arma de Fogo</div>--}}
    <table style="width: 665px; border: 1px solid #ccc; padding: 5px" cellpadding="5">
        <tr>
            <td style="width: 10px; text-align: center; font-size: 13px; font-weight: bold;" colspan="2">SINDICÂNCIA</td>
        </tr>
        <tbody style="font-size: 13px">
        <tr style="">
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Processo nº:</strong> 00$numeroCautela</td>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC">Data do Processo: $dataCautela </td>
        </tr>
        -- responsaveis --
        {{--@if(!empty($nomeUnidade))--}}
            {{--<tr>--}}
                {{--<td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Unidade:</strong>  $nomeUnidade </td>--}}
            {{--</tr>--}}
        {{--@endif--}}
        {{--<tr>--}}
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Responsável:</strong> $responsavel[0]["nome"]</td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Matrícula nº:</strong> $responsavel[0]["matricula"]</td>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>CPF nº:</strong>  $responsavel[0]["cpf"]  </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Lotação:</strong>  $responsavel[0]["lotacao"]</td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Endereço:</strong>
                {{--@if(!empty($responsavel[0]["endereco"]))--}}
                     {{--$responsavel[0]["endereco"]--}}
                {{--@else--}}
                    {{--** ENDREÇO NÃO INFORMADO AO RH-{{ config('app.secretaria') }} {{ config('app.uf') }} **--}}
                {{--@endif--}}
            </td>
        </tr>
        {{--@if($responsavel[0]["fone"] != false)--}}
            {{--<tr>--}}
                {{--@foreach($responsavel[0]["fone"] as $f)--}}
                    {{--<td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong> $f['numero'] </td>--}}
                {{--@endforeach--}}
            {{--</tr>--}}
        {{--@else--}}
            <tr>
                <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Telefone: </strong> ** NÃO INFORMADO AO RH-IAPEN **</td>
            </tr>
        {{--@endif--}}
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC" colspan="2"><strong>Período da Cautela:  </strong> $periodoCautela 
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: justify; font-size: 11px" colspan="2">
                I– Termo de Responsabilidade: Recebo a arma de fogo em perfeito estado de funcionamento e assumo total
                responsabilidade com a arma de fogo, munições e acessórios pela guarda, conservação e manutenção destes, estando sujeito
                as sanções administrativas, cíveis e criminais quanto a sua inobservância. Comprometendo-me ao ressarcimento do valor
                atual da arma de fogo ou suas respectivas peças danificadas, munições e acessórios, para o Estado da {{ config('app.uf_extenso') }}/IAPEN.
                I.I – Em caso de dano, roubo, furto na sua forma simples ou qualificada, ou qualquer outra forma de extravio, por dolo ou
                culpa, caso fortuito ou força maior, enquadra-se ao ressarcimento conforme item anterior;
                I.II – Realizar o registro de Boletim de Ocorrência junto aos órgãos competentes quando efetuado(s) disparo(s) com a arma
                de fogo e as munições desta Secretaria e encaminhar cópia a Gerência de Armas e Logística Penitenciária – GALP.
                II – Desde já fica Notificado(a) o(a) agente penitenciário(a) quanto a entrega da arma de fogo, munições e assessórios
                quando solicitado pela GALP, e/ou nos casos da cessação ou suspensão da cautela especial de arma de fogo e/ou suspensão
                ou perda do porte de arma de fogo, será obrigatório a imediata descautela do material abaixo especificado junto a GALP, sob
                pena de responsabilização administrativa, cível e criminal.
                III – Caso o(a) agente penitenciário(a) solicite a Renovação desta Cautela, deverá imediatamente informar a GALP,
                encaminhando cópia do requerimento devidamente protocolado
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                EQUIPAMENTOS:
            </td>
        </tr>
        {{--@foreach($equipamentos as $equipamento)--}}
            {{--<tr>--}}
                {{--<td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 12px; font-weight: bold; font-style: italic" colspan="2">--}}

                    {{--@php--}}
                    {{--$equip = explode("/",$equipamento);--}}
                    {{--echo $equip[2];--}}
                    {{--@endphp--}}
                    {{--<br>--}}

                {{--</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                Li e estou de acordo com os itens citados neste termo de cautela de arma de fogo
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 13px; font-weight: bold" colspan="2">
                ** CAUTELA **
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="border-bottom: 1px solid #CCCCCC; width: 30%"><strong>Data: </strong> ___/___/20___   </td>
                        <td style="border-bottom: 1px solid #CCCCCC;  border-left: 1px solid #CCCCCC; width: 70%"><strong>Nome: </strong> $dados[0]->nome </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                Autorizo a Cautela de arma de fogo e materiais bélicos citados.
            </td>
        </tr>

        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Ass. Responsável:</strong></td>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>Ass. GALP: </strong></td>
        </tr>

        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: center; font-size: 13px; font-weight: bold" colspan="2">
                ** DESCAUTELA **
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="border-bottom: 1px solid #CCCCCC; width: 30%"><strong>Data: </strong> ___/___/20___  </td>
                        <td style="border-bottom: 1px solid #CCCCCC;  border-left: 1px solid #CCCCCC; width: 70%"><strong>Nome: </strong> $dados[0]->nome </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; text-align: left; font-size: 13px; font-weight: bold" colspan="2">
                Confirmo a descautela de arma de fogo e materiais bélicos citados.

            </td>
        </tr>

        <tr>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC"><strong>Ass. Responsável:</strong></td>
            <td style="width: 50%; border-bottom: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC"><strong>Ass. GALP: </strong></td>
        </tr>


        </tbody>
    </table>
</div><!--Certificado-->

</body>
</html>
