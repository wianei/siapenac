@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-exit-to-app"></i> Internos para Sair da Sanção Disciplinar ou Triagem</h3>
        <hr>
        <p>
            Internos que irão sair da sanção disciplinar ou da triagem.
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

    <div class="form-row">

        <div class="form-group col-sm-3">
            <label for="pavilhaoFiltro">Pavilhão</label>
            <select id="pavilhaoFiltro" name="pavilhaoFiltro" class="form-control">
                <option value="all">
                    Todos
                </option>
                @foreach($pavilhao as $pv)
                    <option value="{{ str_replace(" ", "", $pv['nome']) }}">
                        {{ $pv['nome'] }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    <hr/>
    <form action="{{ route('interno.saidaSancaoTriagemStore', 'sancao') }}" id="formSancao" method="post">
        {{ csrf_field() }}
        <h5 class="titleInterno mt-4">Internos para Sair da Sanção Disciplinar<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="bg-github color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Data de Saída</th>
                </tr>
                </thead>
                <tbody id="retornoInterno" class="retornoInternos">
                @foreach($saidaSancao as $sancao)
                    <tr class="{{ str_replace(" ","",$sancao['pvNome']) }}">
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxSancao" id="check_{{$sancao['id']}}" value="{{$sancao['id']}}-{{ $sancao['idTriagem'] }}">
                        </td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['id'] }}</td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            @if($sancao['foto'] != null)
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $sancao['id'] . "/". $sancao['foto'])}}" class="imagemSrc cursorPointer"  alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif

                        </td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['nome'] }}</td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['facNome'] }}</td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['lideranca'] }}</td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            {{--Atual:<br>--}}
                            {{ $sancao['sigla'] }}<br>
                            <em class="fontSize-10px">Anterior:<br>
                                {{ $sancao['lotacaoAnterior'] }}
                            </em>
                        </td>
                        <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger cm-strong" @endif>{{ $sancao['dataFim'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h6>Retirar Selecionados da Sanção Disciplinar e Alocar na Cela Abaixo<i class="mdi mdi-arrow-bottom-right"></i></h6>
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="unidade_prisional">Presídio</label>
                <select id="unidade_prisional" name="unidade_prisional" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadeServidor as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-sm-3" id="divPavilhao">
                <label for="pavilhao">Pavilhão</label>
                <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divAla">
                <label for="ala">Ala</label>
                <select id="ala" name="ala" class="form-control selectUnidade">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCela">
                <label for="cela">Cela</label>
                <select id="cela" name="cela" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="dataSancao">Data</label>
                <input type="text" name="dataSancao" class="form-control" id="dataSancao" placeholder="Data" autocomplete="off" required>
            </div>
            <div class="form-group col-6">
                <label for="horaSancao">Horário</label>
                <input type="text" name="horaSancao" class="form-control" id="horaSancao" placeholder="Hora" autocomplete="off" required>
            </div>
        </div>
        <div id="flashRetornoSancao"></div>
        <button type="submit" id="btnRetirarSancao" class="btn btn-primary"><i class="fas fa-check"></i>Retirar da Sanção e Alocar na Cela</button>
    </form>

    <hr/>

    <h5 class="titleInternoT mt-4">Internos que estão cumprindo sanção disciplinar com regressão de regime temporária nesta unidade<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternosT" class="table-responsive">
        <table class="table mb-3">
            <thead class="bg-github color_FFF">
            <tr class="">
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Regime Oficial</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Localização</th>
                <th scope="col">Data de Saída</th>
            </tr>
            </thead>
            <tbody id="retornoInternoT" class="retornoInternosT">
            @foreach($saidaSancaoT as $sancao)
                <tr class="{{ str_replace(" ","",$sancao['pvNome']) }}">
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['id'] }}</td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                        @if($sancao['foto'] != null)
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $sancao['id'] . "/". $sancao['foto'])}}" class="imagemSrc cursorPointer"  alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif

                    </td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['nome'] }}</td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['regime'] }}</td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['facNome'] }}</td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $sancao['lideranca'] }}</td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                        {{--Atual:<br>--}}
                        {{ $sancao['sigla'] }}<br>
                        <em class="fontSize-10px">Anterior:<br>
                            {{ $sancao['lotacaoAnterior'] }}
                        </em>
                    </td>
                    <td @if($sancao['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger cm-strong" @endif>{{ $sancao['dataFim'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="alert alert-warning">
        ATENÇÃO: Para registrar o fim da sanção disciplinar destes internos realize uma transferência de unidade, progressão de regime, alvará de soltura, livramento condicional, fuga, recambiamento, óbito, extinção de punibilidade, ou prisão domiciliar, preenchendo adequadamente os campos solicitados.
    </div>

    <hr/>

    <form action="{{ route('interno.saidaSancaoTriagemStore', 'triagem') }}" id="formTriagem" method="post">
        {{ csrf_field() }}
        <h5 class="titleInterno mt-4">Internos para Sair da Triagem<i class="mdi mdi-arrow-bottom-right"></i></h5>
        <div id="tableInternos" class="table-responsive">
            <table class="table mb-3">
                <thead class="bg-github color_FFF">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Prontuário</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Facção</th>
                    <th scope="col">Liderança</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Data de Saída</th>
                </tr>
                </thead>
                <tbody id="retornoInterno" class="retornoInternos">
                @foreach($saidaTriagem as $triagem)
                    <tr class="{{ str_replace(" ","",$triagem['pvNome']) }}">
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            <input type="checkbox" name="internosCelaCheck[]" class="checkbox checkBoxTriagem" id="check_{{$triagem['id']}}" value="{{$triagem['id']}}-{{ $triagem['idTriagem'] }}">
                        </td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $triagem['id'] }}</td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            @if($triagem['foto'] != null)
                                <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $triagem['id'] . "/". $triagem['foto'])}}" class="imagemSrc cursorPointer"  alt="Foto do Perfil">
                            @else
                                <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                            @endif

                        </td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $triagem['nome'] }}</td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $triagem['facNome'] }}</td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>{{ $triagem['lideranca'] }}</td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger" @endif>
                            {{--Atual:<br>--}}
                            {{ $triagem['sigla'] }}<br>
                            <em class="fontSize-10px">Anterior:<br>
                                {{ $triagem['lotacaoAnterior'] }}
                            </em>
                        </td>
                        <td @if($triagem['dataInteiro'] <= date("Ymd")) class="bg-inverse-danger cm-strong" @endif>{{ $triagem['dataFim'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h6>Retirar Selecionados da Triagem e Alocar na Cela Abaixo<i class="mdi mdi-arrow-bottom-right"></i></h6>
        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="unidade_prisionalt">Presídio</label>
                <select id="unidade_prisionalt" name="unidade_prisionalt" class="form-control selectUnidadet">
                    <option value="">
                        Selecione
                    </option>
                    @foreach($unidadeServidor as $unidade)
                        <option value="{{ $unidade->id }}">
                            {{ $unidade->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-sm-3" id="divPavilhao">
                <label for="pavilhaot">Pavilhão</label>
                <select id="pavilhaot" name="pavilhaot" class="form-control selectUnidadet">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divAla">
                <label for="alat">Ala</label>
                <select id="alat" name="alat" class="form-control selectUnidadet">
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3" id="divCela">
                <label for="celat">Cela</label>
                <select id="celat" name="celat" class="form-control" required>
                    <option value="">
                        Selecione
                    </option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="dataTriagem">Data</label>
                <input type="text" name="dataTriagem" class="form-control" id="dataTriagem" placeholder="Data" autocomplete="off" required>
            </div>
            <div class="form-group col-6">
                <label for="horaTriagem">Horário</label>
                <input type="text" name="horaTriagem" class="form-control" id="horaTriagem" placeholder="Hora" autocomplete="off" required>
            </div>
        </div>
        
        <div id="flashRetornoTriagem"></div>
        <button type="submit" id="btnRetirarTriagem" class="btn btn-primary"><i class="fas fa-check"></i>Retirar da Triagem e Alocar na Cela</button>
    </form>

@endsection
@push('script')
    <script src="{{ asset('js/outros/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/interno/sancaoTriagem.js') }}"></script>
    <script src="{{ asset('js/outros/UnidadePvAlaCela.js') }}"></script>
@endpush