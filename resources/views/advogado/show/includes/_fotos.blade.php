<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-camera"></i> Fotos do advogado</h3>
        <hr>
        <p>
            Fotos do advogado, registradas no Sistema Penitenciário da {{ config('app.uf') }}.
        </p>
        <a href="{{route('advogado.editar',$advogado['id'])}}" data-toggle="tooltip" data-placement="top" title="Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Inserir ou Editar</a>
    </div>
</div>
<div class="row pl-5 pr-5">
    @if($advogado['fotos'] == collect([]))
        <p>Sem fotos cadastradas.</p>
    @endif
    @foreach($advogado['fotos'] as $foto)
        <div class="col-md-4">
            <img style="width: 300px; height: 250px" src="{{asset('storage/app/arquivos/advogados/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo'])}}" data-content="{{$foto['tipo']}},{{$foto['legenda']}},{{$foto['created_at']}},{{$foto['updated_at']}}" class="imagemSrc cursorPointer text-center figure-img img-fluid rounded float-left mb-2" title="Clique para ampliar a foto" alt="{{$foto['tipo']}}"/>
            <br/>
            <div class="text-left" style="clear: both;">
                <b>Tipo</b>: {{$foto['tipo']}}<br/>
                <b>Descrição</b>: {{$foto['legenda']}}<br/>
                <b>Data da inserção da foto</b>: {{$foto['created_at']}}<br/>
            </div>
        </div>
    @endforeach
</div>