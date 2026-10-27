@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-library-books"></i> Abrir Chamado Com o Suporte da {{ config('app.ti') }}</h3>
        <hr>
        <p>
            Detalhe sua dúvida, solicitação ou sugestão.
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

    <div class="alert alert-warning">
        <strong>ATENÇÃO! </strong> Insira o máximo de informaçõoes relacionadas ao seu problema, dúvida ou sugestão! A riqueza dos detalhes fará com que nossa equipe resolva com mais agilidade o seu chamado.
       
    </div>
    
    <form action="{{ route('helpDesk.abrirChamadosStore') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-row">
            <div class="form-group col-sm-6">
                <label for="assunto">Assunto</label>
                    <input type="text" name="assunto" class="form-control" id="assunto" placeholder="Informe o assunto" autocomplete="off" required>
            </div>

            <div class="form-group col-sm-6">
                <label for="prioridade">Tipo de Atendimento</label>
                <select id="prioridade" name="prioridade" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                    <option value="C">
                        Sugestão
                    </option>
                    <option value="B">
                        Dúvida - Tenho dúvida quanto ao funcionamento de alguma ferramenta
                    </option>
                    <option value="A">
                        Erro no Sistema - Alguma ferramenta não está funcionando corretamente ou apresenta erro
                    </option>
                    <option value="D">
                        Unificação de Custodiados - Desejo solicitar a unificação de cadastros de custodiados duplicados.
                    </option>
                </select>
            </div>

        </div>

        <div class="form-row">
            <label for="descricao">Mensagem</label>
        </div>

        <textarea name="descricao" id="descricao" class="form-control"></textarea>

        <div class="form-row mt-3">
            <div class="form-group col-sm-3">
                <label for="inputFile">Arquivos</label>
                <input type="file" name="inputFile[]" class="form-control" id="inputFile" accept="application/pdf, image/*" multiple>
            </div>
        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="mdi mdi-library-plus"></i> Criar Chamado</button>


    </form>

@endsection

@push('css')
<link href="{{ asset('css/mensagem/enviar/enviar.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
<script src="{{ asset('js/helpdesk/chamado/abrir.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
@endpush