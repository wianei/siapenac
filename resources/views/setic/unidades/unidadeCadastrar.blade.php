@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        @if(isset($dados))
            <h3><i class="mdi mdi-pencil-circle-outline"></i> Alterar Dados da Unidade</h3>
            <hr>
            <p>
                Realiza a alteração no cadastro da unidade.
            </p>
        @else
            <h3><i class="fas fa-plus"></i> Cadastrar Unidade</h3>
            <hr>
            <p>
                Realiza o cadastro de uma unidade no sistema
            </p>
        @endif
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    @if(isset($dados))
        <form action="{{ route('setic.unidadesEditarStore', $dados[0]->id) }}" id="formDados" method="post">
            @else
                <form action="{{ route('setic.unidadesCadastrarStore') }}" id="formDados" method="post">
                    @endif

                    {!! csrf_field() !!}
                    <div class="form-row">

                        <div class="form-group col-sm-6">
                            <label for="nome">Nome</label>
                            <input type="text" name="nome" value="{{ $dados[0]->nome or old('nome') }}" class="form-control" id="nome" placeholder="Nome" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="sigla">Sigla</label>
                            <input type="text" name="sigla" value="{{ $dados[0]->sigla or old('sigla') }}" class="form-control" id="sigla" placeholder="Sigla" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="cidade">Cidade</label>
                            <input type="text" name="cidade" value="{{ $dados[0]->cidade or old('cidade') }}" class="form-control" id="cidade" placeholder="informe a cidade" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="qtdHorasPlantao">Quantidade de Horas por Plantão</label>
                            <input type="text" name="qtdHorasPlantao" value="{{ $dados[0]->qtdHorasPlantao or old('qtdHorasPlantao') }}" class="form-control" id="qtdHorasPlantao" placeholder="informe a quantidade de horas que cada plantão terá" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="horaInicioPlantao">Horário de Início do Primeiro Plantão</label>
                            <input type="text" name="horaInicioPlantao" value="{{ $dados[0]->horaInicioPlantao or old('horaInicioPlantao') }}" class="form-control" id="horaInicioPlantao" placeholder="Horário de inicio do primeiro plantão" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="diasSemAtendimentoAdv">Dias que <strong>não</strong> tem atendimento de advogado ("0","1","2"...)</label>
                            <input type="text" name="diasSemAtendimentoAdv" value="{{ $dados[0]->diasSemAtendimentoAdv or old('diasSemAtendimentoAdv') }}" class="form-control" id="diasSemAtendimentoAdv" placeholder="0 = domingo, 1 = segunda, 2 = terça..." required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="qtdParlatorio">Quantidade de Parlatórios</label>
                            <input type="text" name="qtdParlatorio" value="{{ $dados[0]->qtdParlatorio or old('qtdParlatorio') }}" class="form-control" id="qtdParlatorio" placeholder="Quantidade de parlatório">
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="horarioInicioAtendimentoAdv">Horário de Início de Atendimento aos Advs</label>
                            <input type="text" name="horarioInicioAtendimentoAdv" value="{{ $dados[0]->horarioInicioAtendimentoAdv or old('horarioInicioAtendimentoAdv') }}" class="form-control" id="horarioInicioAtendimentoAdv" placeholder="08:00" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="horarioTerminoAtendimentoAdv">Horário de Témino de Atendimento aos Advs</label>
                            <input type="text" name="horarioTerminoAtendimentoAdv" value="{{ $dados[0]->horarioTerminoAtendimentoAdv or old('horarioTerminoAtendimentoAdv') }}" class="form-control" id="horarioTerminoAtendimentoAdv" placeholder="17:00" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="horarioInicioIntervaloAlmoco">Horário de Início do Intervalo do Almoço</label>
                            <input type="text" name="horarioInicioIntervaloAlmoco" value="{{ $dados[0]->horarioInicioIntervaloAlmoco or old('horarioInicioIntervaloAlmoco') }}" class="form-control" id="horarioInicioIntervaloAlmoco" placeholder="12:00" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="horarioTerminoIntervaloAlmoco">Horário de Témino do Intervalo do Almoço<</label>
                            <input type="text" name="horarioTerminoIntervaloAlmoco" value="{{ $dados[0]->horarioTerminoIntervaloAlmoco or old('horarioTerminoIntervaloAlmoco') }}" class="form-control" id="horarioTerminoIntervaloAlmoco" placeholder="13:00" required>
                        </div>



                    @if(isset($dados))
                            <div class="form-group col-sm-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="" @if( isset( $dados ) && $dados[0]->status == "" ) selected @endif>Selecione</option>
                                    <option value="1" @if( isset( $dados ) && $dados[0]->status == "1" ) selected @endif>Ativo</option>
                                    <option value="0" @if( isset( $dados ) && $dados[0]->status == "0" ) selected @endif>Inativo</option>
                                </select>
                            </div>
                    @endif

                        <div class="form-group col-sm-3">
                            <label for="troca">Entra na lista de troca de unidade</label>
                            <select name="troca" id="troca" class="form-control" required>
                                <option value="" @if( isset( $dados ) && $dados[0]->troca == "" ) selected @endif>Selecione</option>
                                <option value="1" @if( isset( $dados ) && $dados[0]->troca == "1" ) selected @endif>Sim</option>
                                <option value="0" @if( isset( $dados ) && $dados[0]->troca == "0" ) selected @endif>Não</option>
                            </select>
                        </div>

                    </div>

                    <div id="flashRetorno"></div>
                    @if(isset($dados))
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
                    @else
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Unidade</button>
                    @endif

                </form>

                @endsection

                @push('script')
                <script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
                <script src="{{ asset('js/setic/unidadeCadastrar.js') }}"></script>
        @endpush