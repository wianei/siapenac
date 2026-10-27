<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-id-card"></i> Dados pessoais da visita</h3>
        <hr>
        <p>
            Dados pessoais do interno preenchidos pelo Sistema Penitenciário da {{ config('app.uf') }}.
        </p>

        @if(PermissaoService::getVerifyPermissionView("VisitaController","editar"))
        <a href="{{route('visita.editar',$visita['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Editar</a>
        @endif
        <!-- <a href="http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NMPARTE&tipoNuProcesso=UNIFICADO&dePesquisa={{str_replace("+", " ", \App\Http\Controllers\Interno\InternoController::removeSignals($visita['nome']))}}&pbEnviar=Pesquisar" target="_blank" data-toggle="tooltip" data-placement="top" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>TJRN 1º</a> -->

    </div>
</div>

<div class="pl-5 pr-5">
    <div class="mostrarNaImpressao" style="display: none">
        @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" data-content="{{$fotoPerfilDados['tipo']}},{{$fotoPerfilDados['legenda']}},{{$fotoPerfilDados['created_at']}}" class="mx-auto d-block imagemSrc cursorPointer text-center figure-img img-fluid" title="Clique para ampliar a foto" alt="{{$fotoPerfilDados['tipo']}}"/>
        @else
            <img src="{{asset($fotoPerfilDados['nome_arquivo'])}}" style="max-height: 8rem;" class="mx-auto d-block text-center figure-img img-fluid"/>
        @endif
        <h2 class="mt-4 text-left">{{  $visita['nome'] }}</h2>
        <strong>Carteirinha:</strong> {{$visita['id']}},<br/>
        <strong>CPF:</strong> {{$visita['cpf']}},<br/>
        <strong>Número do RG:</strong> {{$visita['rg_numero']}},<br/>
        <strong>Órgão e Estado Expedidor do RG:</strong> {{$visita['rg_orgaoexpedidor']}},<br/>
        <strong>Data de Emissão do RG:</strong> {{$visita['rg_data']}},<br/>
        <strong>Ocorrências Registradas:</strong> {{$visita['qtd_ocorrencias']}},<br/>
        <strong>Presos Visitados:</strong> {{$visita['qtd_presos_visitados']}},<br/>
        {{--Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}: <strong>{{ $visita['biometria_sejuc'] }}</strong>,<br/>--}}
    </div>
    <div class="row" style="font-size: 14px !important;">

        <div class="col-sm-3">
            <strong>Sexo:</strong> {{$visita['sexo']=='F'?"Feminino":($visita['sexo']=='M'?"Masculino":"Não Informado")}}
        </div>

        <div class="col-sm-12 mt-2">
            @if($visita['outrosnomes'] == [])
                <strong>Outros Nomes:</strong> Não Informado,<br/>
            @else
                <strong>Outros Nomes:</strong>
                @foreach($visita['outrosnomes'] as $outrosnomes)
                    {{$outrosnomes}},
                @endforeach
                <br/>
            @endif
        </div>
        @if($visita['contatos'] == [])
            <div class="col-sm-12 mt-2">
                <strong>Contato:</strong> Não Informado,<br/>
            </div>
        @else
            <div class="col-sm-12 mt-2">
                <strong>Contatos:</strong>
                @foreach($visita['contatos'] as $contato)
                    {{$contato}},
                @endforeach
            </div>
        @endif
    <div class="col-sm-3 mt-2">
        <strong>Data de Nascimento:</strong> {{$visita['data_nascimento']}}
    </div>
    <div class="col-sm-3 mt-2">
        <strong>Idade:</strong> {{$visita['idade']}}
    </div>
    <div class="col-sm-3 mt-2">
        <strong>Preferencial:</strong> {{ $visita['preferencial']=='N'?"Não":($visita['preferencial']=='S'?"Sim":"Não Informado") }}
    </div>
    <div class="col-sm-12 mt-2">
        <strong>E-Mail:</strong> {{$visita['email']}}
    </div>
    <div class="col-sm-6 mt-2">
        <strong>Mãe:</strong> {{$visita['mae']}}
    </div>
    <div class="col-sm-6 mt-2">
        <strong>Pai:</strong> {{$visita['pai']}}
    </div>
    
    @if ($visita['responsavel'] <> 0)
       
        @php $nomeresponsavel=DB::table('visita')->where('id', $visita['responsavel'])->get();@endphp
        
         <div class="col-sm-6 mt-2">
            <strong>Responsável para Visita:</strong> {{$nomeresponsavel[0]->nome}} - {{$nomeresponsavel[0]->cpf}}
         </div>
        
    @endif
    @if(PermissaoService::getVerifyPermissionView("VisitaController","editar"))
    @if($visita['enderecos'] == [])
        <div class="col-sm-12 mt-2">
            <strong>Endereço:</strong> Não Informado,<br/>
        </div>
    @else
        @foreach($visita['enderecos'] as $key=>$endereco)
            <div class="col-sm-12 mt-2">
                <strong>Endereço {{$key+1}}:</strong>
            </div>
            {{--<strong class="ml-4">País:</strong> {{$endereco['pais']}},<br/>--}}
            <div class="col-sm-3 mt-2">
                <strong>Cidade/Estado:</strong> {{$endereco['cidade']}}/{{$endereco['estado']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>Bairro:</strong> {{$endereco['bairro']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>CEP:</strong> {{$endereco['cep']}},<br/>
            </div>
            <div class="col-sm-12 mt-2">
                <strong>Logradouro:</strong> {{$endereco['logradouro']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>Número:</strong> {{$endereco['numero']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>Complemento:</strong> {{$endereco['complemento']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>Latitude:</strong> {{$endereco['latitude']}}
            </div>
            <div class="col-sm-3 mt-2">
                <strong>Longitude:</strong> {{$endereco['longitude']}}
            </div>
        @endforeach
    @endif
    @endif
    </div>
</div>