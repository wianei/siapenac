<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-id-card"></i> Dados pessoais do interno</h3>
        <hr>
        <p>
            Dados pessoais do interno preenchidos pelo {{ config('app.sistema_estado') }}.
        </p>
        @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 600)
        <a href="{{route('interno.editar',$interno['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Editar</a>
        @endif
    </div>
</div>
<div class="pl-5 pr-5">
    <div class="mostrarNaImpressao" style="display: none">
        @if( $interno['status'] == 'A')<h5 class="mt-4 text-center text-green">{{ $interno['condicao_nome'] }}</h5>
        @elseif( $interno['status'] == 'I')<h5 class="mt-4 text-center text-red">{{ $interno['condicao_nome'] }}</h5>@endif
        <strong>Prontuário:</strong> {{ $interno['id'] }},<br/>
        @if( $interno['status'] == 'A' ) <strong>Unidade Prisional:</strong> {{ $interno['unidade_prisional_nome'] }},<br/>
        @elseif( $interno['status'] == 'I' ) <strong>Última Unidade Prisional:</strong> {{ $interno['unidade_prisional_nome'] }},<br/>@endif
        @if( $interno['status'] == 'A' )
            <strong>Pavilhão:</strong> {{ $interno['pavilhao_nome'] }},<br/>
            @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                <strong>Ala:</strong> {{ $interno['ala_nome'] }},<br/>
                <strong>Cela:</strong> {{ $interno['cela_nome'] }},<br/>
            @endif
        @elseif( $interno['status'] == 'I' )
            <strong>Último Pavilhão:</strong> {{ $interno['pavilhao_nome'] }},<br/>
            @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)
                <strong>Última Ala:</strong> {{ $interno['ala_nome'] }},<br/>
                <strong>Última Cela:</strong> {{ $interno['cela_nome'] }},<br/>
            @endif
            <strong>Último Regime:</strong> {{ $interno['regime'] }},<br/>
        @endif
        <strong>Sentenciado:</strong> {{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }},<br/>
        <strong>Regime:</strong> {{ $interno['regime'] }},<br/>
        {{--<strong>Tornozeleira:</strong> {{ $interno['tornozeleira'] }},<br/>--}}
        <strong>Facção:</strong> {{ $interno['faccao_nome'] }},<br/>
        <strong>Liderança:</strong> {{ $interno['lideranca'] }},<br/>
        <strong>Periculosidade:</strong> {{ $interno['periculosidade'] }},<br/>
        {{--<strong>Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}:</strong> {{ $interno['biometria_sejuc'] }},<br/>--}}
        {{--<strong>Identidade Criminal Biométricamente Confirmada na SESED-RN:</strong> {{ $interno['identificacao_criminal_sesed'] }}--}}
    </div>
    <div class="row bgNoPrint" style="font-size: 14px !important;">
        {{--<div id="background">--}}
            {{--<p id="bg-text">--}}
                {{--@for($i =0; $i < 4; $i++)--}}
                    {{--{{ \Illuminate\Support\Facades\Auth::user()->cpf }}--}}
                {{--@endfor--}}

            {{--</p>--}}
        {{--</div>--}}

        <div class="col-sm-3">
            <strong>Sexo:</strong> {{$interno['sexo']=='M'?"Masculino":($interno['sexo']=='F'?"Feminino":"Não Informado")}}
        </div>
        <div class="col-sm-12 mt-2">
            @if($interno['outrosnomes'] == [])
                <strong>Outros Nomes:</strong> Não Informado,<br/>
            @else
                <strong>Outros Nomes:</strong>
                @foreach($interno['outrosnomes'] as $outrosnomes)
                    {{$outrosnomes}},
                @endforeach
            @endif
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Data de Nascimento:</strong> {{$interno['data_nascimento']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Idade:</strong> {{$interno['idade']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Estado Civil:</strong> {{$interno['estado_civil']}}
        </div>
        <div class="col-sm-12 mt-2">
            <strong>Nome da Mãe:</strong> {{$interno['mae']}}
        </div>
        <div class="col-sm-12 mt-2">
            <strong>Nome do Pai:</strong> {{$interno['pai']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Número do RG:</strong> {{$interno['rg_numero']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Número do CPF:</strong> {{$interno['cpf']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Órgão e Estado Expedidor do RG:</strong> {{$interno['rg_orgaoexpedidor']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Data de Emissão do RG:</strong> {{$interno['rg_data']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Profissão:</strong> {{$interno['profissao_nome']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Escolaridade:</strong> {{$interno['escolaridade']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Altura:</strong> {{$interno['altura']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Peso:</strong> {{$interno['peso']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Cor da Pele:</strong> {{$interno['etnia']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Cor dos Olhos:</strong> {{$interno['cor_olhos']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Cor do Cabelo:</strong> {{$interno['cor_cabelos']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Opção Sexual:</strong> {{$interno['opcao_sexual']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Religião:</strong> {{$interno['religiao']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Facção:</strong> {{$interno['faccao_nome']}}
        </div>
    @if($interno['faccao'] != "Não Informado")
        <div class="col-sm-4 mt-2">
            <strong>Facção - Postos:</strong> {{$interno['faccao_posto']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Facção - Padrinho:</strong> {{$interno['faccao_padrinho']}}
        </div>
        <div class="col-sm-4 mt-2">
            <strong>Facção - Quebrada:</strong> {{$interno['faccao_quebrada']}}
        </div>
    @endif
    <div class="col-sm-4 mt-2">
        <strong>Naturalidade - País:</strong> {{$interno['naturalidade_pais']}}
    </div>
    <div class="col-sm-4 mt-2">
        <strong>Naturalidade - Estado:</strong> {{$interno['naturalidade_estado']}}
    </div>
    <div class="col-sm-4 mt-2">
        <strong>Naturalidade - Cidade:</strong> {{$interno['naturalidade_cidade']}}
    </div>

    {{--@if(\Illuminate\Support\Facades\Auth::user()->tipo >= 300)--}}
        @if($interno['enderecos'] == [])
            <div class="col-sm-12 mt-2">
                <strong>Endereço:</strong> Não Informado
            </div>
        @else
            @foreach($interno['enderecos'] as $key=>$endereco)
                <div class="col-sm-12 mt-2">
                    <strong>Endereço {{$key+1}}:</strong>
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Cidade/Estado:</strong> {{$endereco['cidade']}}/{{$endereco['estado']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Bairro:</strong> {{$endereco['bairro']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>CEP:</strong> {{$endereco['cep']}}
                </div>
                <div class="col-sm-12 mt-2">
                    <strong>Logradouro:</strong> {{$endereco['logradouro']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Número:</strong> {{$endereco['numero']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Complemento:</strong> {{$endereco['complemento']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Latitude:</strong> {{$endereco['latitude']}}
                </div>
                <div class="col-sm-4 mt-2">
                    <strong>Longitude:</strong> {{$endereco['longitude']}}
                </div>
            @endforeach
        @endif
    {{--@endif--}}

    <div class="col-sm-12 mt-2">
        <h4>Observações: </h2> 
        {{$interno['observacao']}}
        </div>
    </div>
</div>