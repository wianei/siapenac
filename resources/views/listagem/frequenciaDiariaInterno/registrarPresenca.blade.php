@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-check"></i> Registrar Presença</h3>
        <hr>
        <p>
            Registrar presença.
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

    @include('listagem.frequenciaDiariaInterno._includes._navMenuFrequencia')

    <form action="{{ route('listagem.frequenciaDiariaInternoRegistrarPresencaStore') }}" method="post" enctype="multipart/form-data" id="formRegistrarPresenca">
        {!! csrf_field() !!}

        <div class="form-row">
            <div class="form-group col-sm-9">
                <label for="lista">Lista de Frequência</label>
                <select id="lista" name="lista" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($listas as $lista)
                        <option value="{{ $lista->id }}">{{ $lista->nome }}</option>
                        @endforeach

                </select>
            </div>

            <div class="form-group col-3">
                <label for="data">Data</label>
                <input type="text" name="data" class="form-control" id="data" placeholder="Data" autocomplete="off">
            </div>

        </div>

        <span id="spanInternos" class="displayNone">
            <h5 class="titleInternos"><span id="txtTitulo"></span> <i class="mdi mdi-arrow-bottom-right"></i></h5>
            <div id="tableInternosCela" class="table-responsive">
                <table class="table mb-3" id="internosCela">
                    <thead class="btn-primary color_FFF">
                    <tr class="">
                        <th scope="col" width="50px">
                            <button type="button" class="btn btn-rounded btn-secondary btn-sm" id="btnPTodos">P <i class="mdi mdi-arrow-down"></i></button>
                        </th>
                        <th scope="col">Prontuário</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Localização</th>
                        <th scope="col">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="retornoInternos">

                    </tbody>
                </table>
            </div>

        </span>


        <div id="flashRetorno">

        </div>
        <button type="submit" id="btnSubmitForm" class="btn btn-primary"><i class="fas fa-check"></i> Registrar Presença</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ asset('css/listagem/aberto/listagemAberto.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/listagem/frequenciaDiariaInterno/registrarPresenca.js') }}"></script>
@endpush