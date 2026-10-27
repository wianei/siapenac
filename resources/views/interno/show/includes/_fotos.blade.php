<div class="container">
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-camera"></i> Fotos do interno</h3>
        <hr>
        <p>
            Fotos do interno registradas no {{ config('app.sistema_estado') }}.
        </p>
        @if(\Illuminate\Support\Facades\Auth::user()->tipo >= 600)
        <a href="{{route('interno.editar',$interno['id'])}}" data-toggle="tooltip" data-placement="top" title="Inserir ou Editar" class="esconderNaImpressao btn btn-primary text-right"><i class="fas fa-edit"></i>Inserir ou Editar</a>
        @endif
    </div>
</div>
<div class="row pl-5 pr-5">
    @if($interno['fotos'] == collect([]))
       <p>Sem fotos cadastradas.</p>
    @endif
    @foreach($interno['fotos'] as $foto)
            <div class="col-md-4 mt-3" style="font-size: 12px !important;">
            <img src="{{asset('storage/app/arquivos/internos/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo'])}}" data-content="{{$foto['tipo']}},{{$foto['legenda']}},{{$foto['created_at']}},{{$foto['updated_at']}}" class="imagemSrc cursorPointer text-center figure-img img-fluid rounded float-left mb-2 fotoInterno" title="Clique para ampliar a foto" alt="{{$foto['tipo']}}" style="float: none !important; height: 300px;"/>
            <div class="text-left">
                <b>Tipo</b>: {{ strtoupper($foto['tipo'])}}<br/>
                <b>Descrição</b>: {{strtoupper($foto['legenda'])}}<br/>
                <b>Data da inserção da foto</b>: {{$foto['created_at']}}<br/>
                <b>Data da última alteração na foto</b>: {{$foto['updated_at']}}
            </div>
        </div>
    @endforeach
</div>