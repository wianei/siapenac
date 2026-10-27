@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-account-plus"></i> Cancelamento de Agendamentos de Advogados</h3>
        <hr>
        <p>
            Cancela os agendamentos de atendimentos realizados pelos advogados.
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

    <form action="{{ route('advogado.cancelarAgendamentosStore') }}" id="formCadastro" enctype="multipart/form-data" method="post">
        {!! csrf_field() !!}
        <div class="form-row">
            <div class="form-group col-sm-3">
                <label for="dataAtendimento">Data do atendimento</label>
                <input type="text" class="form-control" name="dataAtendimento" id="dataAtendimento" placeholder="Data do atendimento" autocomplete="off" required>
            </div>


            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3 displayNone" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

        </div>

        <input type="hidden" value="{{$diasAtendimento}}" id="diasAtendimentoUnidade">

        <div class="form-row">
            <label for="motivacao">Motivação</label><br>
        </div>
        <div class="alert alert-warning cf fontSize-13px">A motivação informada será enviada, por e-mail, aos advogados que tiveram seus agendamentos cancelados.</div>

        <textarea name="motivacao" id="motivacao" class="form-control" required></textarea>

        <div id="flashRetorno" class="mt-3"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cancelar Agendamentos</button>

    </form>

@endsection

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@push('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/advogado/cancelarAgendamentos.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush
