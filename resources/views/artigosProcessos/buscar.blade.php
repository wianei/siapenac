@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-note-text"></i>Buscar Artigos e Processos</h3>
        <hr>
        <p>
            Permite a busca de processos, inquéritos e condenações cadastradas.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <a href="{{ route('artigosProcessos.cadastro') }}" class="btn btn-secondary">+ Inserir Novo Inquérito, Processo ou Condenação</a>

    <form id="cadastroForm" action="{{ route('artigosProcessos.buscar') }}" method="get" class="mt-4">

        <div class="form-row" id="formRowDivInternos">
            <div class="form-group col-sm-12">
                <label for="interno">Interno(s)</label>
                <input type="text" name="interno" class="form-control" id="interno" placeholder="Informe o nome do interno" autocomplete="off">
                <div class="form-group col-sm-12" id="filter-records"></div>
            </div>
        </div>

        <h5 class="titleInterno">Internos <i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive-md">
            <table class="table mb-3">
                <thead class="background_30516f color_FFF">
                <tr class="">
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Ações</th>
                </tr>
                </thead>
                <tbody id="retornoInterno">

                </tbody>
            </table>
        </div>



        <div class="form-row mt-3 border1pxSolid_CCC p-3" id="divDispositivoLegal">

            <div class="form-group col-sm-6">
                <label for="dispositivoLegal">Dispositivo Legal</label>
                <select id="dispositivoLegal" name="dispositivoLegal[]" class="form-control dispositivoLegal">
                    <option value="">Selecione</option>
                    @foreach($dispositivoLegalLeis as $dispositivo)
                        <option value="{{ $dispositivo->id }}">{{ $dispositivo->nome }} - {{$dispositivo->texto}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6 displayNone divArtigo">
                <label for="artigo">Artigo</label>
                <select id="artigo" name="artigo[]" class="form-control artigo">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="form-group col-sm-12">
                <label for="grupoCrime">Grupo de Crimes</label>
                <select id="grupoCrime" name="grupoCrime" class="form-control selectpicker" data-live-search="true" data-style="selectStyle">
                    <option value=''>Selecione</option>
                    @foreach($grupoCrimes as $grupoCrime)
                        <option value="{{$grupoCrime->id}}">{{$grupoCrime->nome}}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="tipoSequencia[]" class="tipoSequencia" id="tipoSequencia" value="">
            <input type="hidden" name="tipoSequenciaUm[]" class="tipoSequenciaUm" id="tipoSequenciaUm" value="">
            <input type="hidden" name="tipoSequenciaDois[]" class="tipoSequenciaDois" id="tipoSequenciaDois" value="">

            <div class="form-group col-sm-6 displayNone divSubDois">
                <label for="subDois"><span class="txtSubDois"></span></label>
                <select id="subDois" name="subDois[]" class="form-control subDois">
                    <option value="">Selecione</option>
                </select>
            </div>

        </div>


        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar</button>

    </form>

    @if($tipoDado == "interno")
    @if($dadosProcesso != false)

    @if(count($dadosProcesso) > 0)
        <h6 class="mt-4"><i>Sua busca retornou
                {{ count($dadosProcesso) }} resultados
            </i>
        </h6>

        @if(isset($qtdInternosUnicos))
        
                @if($qtdInternosUnicos > 1)

                    <h6 class="mt-2"><i>Nos resultados,
                            {{ $qtdInternosUnicos }} internos são únicos.
                        </i>
                    </h6>

                @else
                
                    <h6 class="mt-2"><i>Nos resultados,
                            {{ $qtdInternosUnicos }} interno é único.
                        </i>
                    </h6>


                @endif

        @endif
        
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif


    <div class="table-responsive-md mt-5">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">Interno</th>
                <th scope="col">Tipo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tbodyList">
                @foreach($dadosProcesso as $dados)
                    <tr>
                        <td>{{ $dados["nomeInterno"] }}</td>
                        <td>{{ $dados["tipoNome"] }}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('artigosProcessos.editar',$dados["dadosProcesso"][0]["fk_processo"]) }}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>

                            <a href="{{ route('artigosProcessos.excluir',$dados["dadosProcesso"][0]["fk_processo"]) }}" data-toggle="tooltip" data-placement="top" title="Excluir {{ $dados["tipoNome"] }}"><i class="mdi mdi-delete color_red"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
    @endif

    @if($tipoDado == "lei")
    @if($dadosInterno != false)

    @if(count($dadosInterno) > 0)
        <h6 class="mt-4"><i>Sua busca retornou
                {{ count($dadosInterno) }} resultados
            </i>
        </h6>
    
            @if(isset($qtdInternosUnicos))
        
                @if($qtdInternosUnicos > 1)

                    <h6 class="mt-2"><i>Nos resultados,
                            {{ $qtdInternosUnicos }} internos são únicos.
                        </i>
                    </h6>

                @else
                
                    <h6 class="mt-2"><i>Nos resultados,
                            {{ $qtdInternosUnicos }} interno é único.
                        </i>
                    </h6>


                @endif
            

            @endif
    @else

        <h5 class="mt-5"><i>Sua busca não obteve resultados! </i>
        </h5>

    @endif


    <div class="table-responsive-md mt-5">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                {{--<th scope="col">#</th>--}}
                <th scope="col">Prontuário</th>
                <th scope="col">Interno</th>
                <th scope="col">Localização</th>
                <th scope="col">Tipo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody class="tbodyList">
                @foreach($dadosInterno as $dados)
                    <tr>
                        <td>{{ $dados["prontuario"] }}</td>
                        <td>{{ $dados["nomeInterno"] }}</td>
                        <td>{{ $dados["localizacao"] }}</td>
                        <td>{{ $dados["tipo"] }}</td>
                        <td class="btn-acoes color_01579b">
                            <a href="{{ route('artigosProcessos.editar',$dados["idProcesso"]) }}" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>

                            <a href="{{ route('artigosProcessos.excluir',$dados["idProcesso"]) }}" data-toggle="tooltip" data-placement="top" title="Excluir {{ $dados["tipo"] }}"><i class="mdi mdi-delete color_red"></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
    @endif


@endsection
@push('css')
<link href="{{ asset('css/ocorrencia/buscar/buscar.css') }}" rel="stylesheet">
<link href="{{ asset('css/artigosProcessos/buscar.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/artigosProcessos/buscar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@endpush