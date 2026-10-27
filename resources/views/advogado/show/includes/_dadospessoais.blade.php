<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-id-card"></i> Dados pessoais do advogado</h3>
        <hr>
        <p>
            Dados pessoais do advogado preenchidos pelo Sistema Penitenciário da {{ config('app.uf') }}.
        </p>
        <a href="{{route('advogado.editar',$advogado['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Editar</a>
    </div>
</div>

<div class="pl-5 pr-5">
    <div class="mostrarNaImpressao" style="display: none">
        <strong>OAB:</strong> {{$advogado['oab_numero']}}/{{$advogado['oab_uf']}},<br/>
        <strong>Status:</strong> {{$advogado['status']}},<br/>
        <strong>Observação:</strong> {{$advogado['obs']}},<br/>
        <strong>Ocorrências Registradas:</strong> {{$advogado['qtd_ocorrencias']}},<br/>
        <strong>Presos Visitados:</strong> {{$advogado['qtd_presos_visitados']}},<br/>
        {{--Biometria Registrada na {{ config('app.secretaria') }} {{ config('app.uf') }}: <strong>{{ $visita['biometria_sejuc'] }}</strong>,<br/>--}}
    </div>
    @if($advogado['contatos'] == [])
        <strong>Contato:</strong> Não Informado,<br/>
    @else
        <strong>Contatos:</strong>
        @foreach($advogado['contatos'] as $contato)
            {{$contato}},
        @endforeach
        <br/>
    @endif
    <strong>CPF:</strong> {{$advogado['cpf']}},<br/>
    <strong>Número do RG:</strong> {{$advogado['rg_numero']}},<br/>
    <strong>Órgão e Estado Expedidor do RG:</strong> {{$advogado['rg_orgaoexpedidor']}},<br/>
    <strong>Data de Emissão do RG:</strong> {{$advogado['rg_data']}},<br/>
    <strong>Observação:</strong> {{$advogado['obs']}},<br/>
    <strong>E-Mail:</strong> {{$advogado['email']}},<br/>
    <strong>Sexo:</strong> {{$advogado['sexo']=='M'?"Masculino":($advogado['sexo']=='F'?"Feminino":"Não Informado")}},<br/>
    <strong>Status:</strong> {{$advogado['status']}},<br/>
    {{--<strong>Cadastrado no {{ config('app.name') }}:</strong> {{$advogado['created_at']}},<br/>--}}
    {{--<strong>Atualizado no {{ config('app.name') }} pela última vez:</strong> {{$advogado['updated_at']}}--}}
</div>