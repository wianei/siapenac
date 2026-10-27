<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-camera"></i> Fotos da visitante</h3>
        <hr>
        <p>
            Fotos da visitante, registradas no Sistema Penitenciário da {{ config('app.uf') }}.
        </p>

        @if(PermissaoService::getVerifyPermissionView("VisitaController","editar"))
        <a href="{{route('visita.editar',$visita['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="btn btn-primary text-right"><i class="fas fa-edit"></i>Inserir ou Editar</a>
        @endif

    </div>
</div>
<div class="row pl-5 pr-5">
    @if($visita['fotos'] == collect([]))
        <p>Sem fotos cadastradas.</p>
    @endif
    @foreach($visita['fotos'] as $foto)
        <div class="col-md-4" style="font-size: 12px !important;">
            <img style="width: 300px; height: 250px" src="{{asset('storage/app/arquivos/visitas/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo'])}}" data-content="{{$foto['tipo']}},{{$foto['legenda']}},{{$foto['created_at']}},{{$foto['updated_at']}}" class="imagemSrc cursorPointer text-center figure-img img-fluid rounded float-left mb-2" title="Clique para ampliar a foto" alt="{{$foto['tipo']}}"/>
            <div class="text-left" style="clear: both !important;">
                <b>Tipo</b>: {{$foto['tipo']}}<br/>
                <b>Descrição</b>: {{$foto['legenda']}}<br/>
                <b>Data da inserção da foto</b>: {{$foto['created_at']}}<br/>
                <b>Data da última alteração na foto</b>: {{$foto['updated_at']}}
            </div>
        </div>
    @endforeach
</div>