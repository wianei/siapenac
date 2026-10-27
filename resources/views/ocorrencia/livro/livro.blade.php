@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-file-document"></i> Livro de Ocorrência do Plantão</h3>
        <hr>
        <p>
            {{ $stringInfoPlantao }}
        </p>

        @if(PermissaoService::getVerifyPermissionView("OcorrenciaController","livroFechamento"))
            <a href="{{ route('ocorrencia.livroFechamento') }}" data-toggle="tooltip" data-placement="top" title="Fechar Livro de Ocorrência" class="btn btn-danger btn-primary text-right"><i class="mdi mdi-lock"></i>Fechar Livro de Ocorrência</a>
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

<h6>I - Informações Sobre as Chefias do Plantão</h6>
    <a href="{{ route('equipe.gerenciarequipes', ['returnLivro' => true]) }}" title="Inserir Chefia" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i> </a>

    <div id="tableAgentes" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_1c2331 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Matrícula</th>
                <th scope="col">Função</th>
                <th scope="col">Nome</th>
                <th scope="col">Escala</th>
                <th scope="col">Lot. na Unidade</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody id="retornoAgente">
            @foreach($dadosEquipe as $dA)
                @if($dA['funcao'] != "Padrão" && $dA['funcao'] != null)
                <tr class="">
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td scope="row">{{ $dA['matricula'] }}</td>
                    <td>{{ $dA['funcao'] }}</td>
                    <td>{{ $dA['nome'] }}</td>
                    <td>{{ $dA['escala'] }}</td>
                    <td>{{ $dA['lotacaoUnidade'] }}</td>
                    <td>{{ $dA['status'] }}</td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>II - Componentes da Equipe</h6>
    <a href="{{ route('equipe.gerenciarequipes', ['returnLivro' => true]) }}" title="Inserir Componentes da Equipe" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i> </a>
    <div id="tableAgentes" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_1c2331 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Matrícula</th>
                <th scope="col">Função</th>
                <th scope="col">Nome</th>
                <th scope="col">Escala</th>
                <th scope="col">Lot. na Unidade</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody id="retornoAgente">
            @foreach($dadosEquipe as $dA)
                @if($dA['funcao'] == "Padrão")
                    <tr class="">
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td scope="row">{{ $dA['matricula'] }}</td>
                        <td>{{ $dA['funcao'] }}</td>
                        <td>{{ $dA['nome'] }}</td>
                        <td>{{ $dA['escala'] }}</td>
                        <td>{{ $dA['lotacaoUnidade'] }}</td>
                        <td>{{ $dA['status'] }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>III - Escala de Postos Diurnos</h6>

    @if(count($escalaPostoDiurno) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 27, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
    @else
        @foreach($escalaPostoDiurno as $escalaDiurna)
            <a href="{{ route('ocorrencia.editar', ['id'=>$escalaDiurna['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>
            <div class="fontSize-13px">
                {!! $escalaDiurna['descricao'] !!}
            </div>
        @endforeach
    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>IV - Escala de Postos Noturnos</h6>
    @if(count($escalaPostoNoturno) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 28, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($escalaPostoNoturno as $escalaNoturno)
            <a href="{{ route('ocorrencia.editar', ['id'=>$escalaNoturno['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>
            <div class="fontSize-13px">
                {!! $escalaNoturno['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>V - Servidores Responsáveis Pela Ronda</h6>
    @if(count($rondas) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 29, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($rondas as $ronda)

            <a href="{{ route('ocorrencia.editar', ['id'=>$ronda['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $ronda['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>VI - Conferência dos Internos</h6>
    @if(count($confere) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 30, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($confere as $conf)

            <a href="{{ route('ocorrencia.editar', ['id'=>$conf['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $conf['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>VII - Revistas nas Celas</h6>
    @if(count($revistas) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 31, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($revistas as $revista)
            <a href="{{ route('ocorrencia.editar', ['id'=>$revista['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $revista['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>VIII - Escala de Banho de Sol</h6>
    @if(count($escalaBanhoSol) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 32, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($escalaBanhoSol as $banhoSol)
            <a href="{{ route('ocorrencia.editar', ['id'=>$banhoSol['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $banhoSol['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>IX - Revistas nos Pátios</h6>
    @if(count($revistaPatios) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 33, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($revistaPatios as $revistaPatio)
            <a href="{{ route('ocorrencia.editar', ['id'=>$revistaPatio['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $revistaPatio['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>X - Alimentação</h6>
    @if(count($alimentacao) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 36, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($alimentacao as $aliment)
            <a href="{{ route('ocorrencia.editar', ['id'=>$aliment['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $aliment['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>
    <div class="cf"></div>
    <br>
    <h6>XI - Videoconferência</h6>
    @if(count($videoconferencia) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 43, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
        @else

        @foreach($videoconferencia as $video)
            <a href="{{ route('ocorrencia.editar', ['id'=>$video['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $video['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XII - Ocorrências Diversas</h6>
    @if(empty($dadosOcorrencia))
        -- Sem Informações
        @else

        @foreach($dadosOcorrencia as $ocorrencia)
            <a href="{{ route('ocorrencia.editar', ['id'=>$ocorrencia['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="ocorrencias mt-2 fontSize-13px p-2 border1pxSolid_CCC">

                <div class="row ocorrencia">
                    <div class="col-md-3">
                        {{ $ocorrencia['data'] }}  às {{ $ocorrencia['horario'] }}
                    </div>
                    <div class="col-md-3">
                        Nº ocorrência: {{ $ocorrencia['id'] }}
                    </div>
                    <div class="col-md-6">
                        Natureza: {{ $ocorrencia['natureza'] }}
                    </div>
                    <div class="cf"></div>
                    <div class="col-md-12 mt-3 textP15px">
                        <p class="fontSize-15px">
                            {!! $ocorrencia['descricao'] !!}
                        </p>
                    </div>
                    <div class="col-md-12 fontSize-13px">
                        <i>
                            @if(strpos($ocorrencia['envol'],'inter') != false)
                                <span>Internos Envolvidos:</span>
                                @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                    @if(isset($envolvidos['interno']))
                                        @foreach($envolvidos['interno'] as $interno)
                                            {{$interno['nome'].", "}}
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                            @if(strpos($ocorrencia['envol'],'agnt,') >= 0)

                                <br>
                                <span>Agentes Envolvidos: </span>
                                @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                    @if(isset($envolvidos['servidor']))
                                        @foreach($envolvidos['servidor'] as $servidor)
                                            {{$servidor->nome.", "}}
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif


                            @if(strpos($ocorrencia['envol'],'adv') != false)
                                <br>
                                <span>Advogados Envolvidos: </span>
                                @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                    @if(isset($envolvidos['advogado']))
                                        @foreach($envolvidos['advogado'] as $advogado)
                                            {{$advogado->nome.", "}}
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif

                            @if(strpos($ocorrencia['envol'],'vis') != false)
                                <br>
                                <span>Visitas Envolvidas: </span>
                                @foreach($ocorrencia['envolvidos'] as $envolvidos)
                                    @if(isset($envolvidos['visita']))
                                        @foreach($envolvidos['visita'] as $visita)
                                            {{$visita->nome.", "}}
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif

                        </i>
                    </div>

                    <div class="col-md-12 mt-4">
                        <div class="float-left  blockquote-footer">
                            <i>{{ $ocorrencia['registro'] }}</i>
                            @if($ocorrencia['atualizacao'] != false)
                                <br>
                                <i class="color_red">- Última atualização em {{ $ocorrencia['atualizacao'] }}</i>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @endforeach

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XIII - Escoltas</h6>
    @if(count($escolta) == 0)
        -- Sem Informações
        @else

        @foreach($escolta as $esc)

            <div class="ocorrencias border1pxDashedCCC paddingBottom15px fontSize-13px mb-3">

                <div class="row ocorrencia">
                    <div class="col-md-12">
                        Nº {{ $esc['id'] }} | Tipo: {{ strtoupper($esc['tipo']) }}
                    </div>
                    <div class="col-md-6">
                        @if($esc['tipo'] != "gep")
                            Data de Saída: {{ $esc['data_saida'] }}  às {{ $esc['hora_saida'] }}
                            <i class="mdi mdi-ray-start-arrow fontSize-18px mr-2 ml-2"></i>
                            Data de Retorno: {{ $esc['data_retorno'] }}  às {{ $esc['hora_retorno'] }}
                        @else
                            Data da Escolta {{ $esc['data_saida'] }}
                        @endif
                    </div>
                    <div class="col-md-6">
                        {{ $esc['destino'] }}
                    </div>
                    <div class="cf"></div>
                    <div class="col-md-12 mt-3 textP15px">
                        <p class="fontSize-15px">
                            {!! $esc['motivo'] !!}
                        </p>
                    </div>
                    <div class="col-md-12 fontSize-13px">
                        <i>
                            @if(count($esc['agentes']) > 0)
                                <span>Agentes Envolvidos:</span>
                                @foreach($esc['agentes'] as $agente)
                                    {{ $agente['nome'].", " }}
                                        {{--@foreach($envolvidos['interno'] as $interno)--}}
                                            {{--{{$interno['nome'].", "}}--}}
                                        {{--@endforeach--}}
                                @endforeach
                            @endif

                                <br>
                                <span>Internos Envolvidos: </span>
                                @foreach($esc['internos'] as $interno)
                                    {{ $interno['nome'].", " }}
                                    {{--@if(isset($envolvidos['servidor']))--}}
                                        {{--@foreach($envolvidos['servidor'] as $servidor)--}}
                                            {{--{{$servidor->nome.", "}}--}}
                                        {{--@endforeach--}}
                                    {{--@endif--}}
                                @endforeach



                        </i>
                    </div>

                </div>
            </div>
        @endforeach

    @endif

    @if(count($escoltaGep) != 0)
        
        <span style="font-size: 14px">Escoltas realizadas pelo FTPEN</span><br>
        <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
            <thead>
            <tr>
                <th style="width: 10%; text-align: center" scope="col">#</th>
                <th style="width: 30%;" scope="col">Interno</th>
                <th style="width: 15%; text-align: center" scope="col">Saída</th>
                <th style="width: 15%; text-align: center" scope="col">Retorno</th>
                <th style="width: 15%; text-align: center" scope="col">VTR</th>
                <th style="width: 15%; text-align: center" scope="col">Destino</th>
            </tr>
            </thead>
            <tbody>
            @foreach($escoltaGep as $gep)
                <tr>
                    <td colspan="6">
                        <table style="width: 100%;" cellspacing="1" cellpadding="1">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                <td style="width: 30%; border-right: 1px solid #808080">{{ $gep->nome }}</td>
                                <td style="width: 15%; text-align: center; border-right: 1px solid #808080;">{{ $gep->data_saida." - ".$gep->hora_saida }}</td>
                                <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->data_retorno." - ".$gep->hora_retorno }}</td>
                                <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->vtr_placa }}</td>
                                <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $gep->destino }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

    @endif

    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XIV - Quantitativo Prisional</h6>
    @if(empty($qtdPavilhao))
        -- Sem Informações
    @else
{{--{{ dd($qtdPavilhao) }}--}}
        @foreach($qtdPavilhao as $qtdPv)

            <table style="width: 400px; font-size: 12px !important; border: 1px solid #000; margin-bottom: 10px">
                <tbody>
                <tr>
                    <td style="width: 100%; text-align: center; background-color: deepskyblue;" colspan="4"><strong>{{$qtdPv['nomePavilhao']}}</strong></td>
                </tr>
                <tr>
                    <td style="width: 30%; text-align: center;"><strong>Ala</strong></td>
                    <td style="width: 70%;  border-right: 1px solid #000"><strong>Quantidade</strong></td>
                </tr>
                <tr>
                    <td colspan="4">
                        @foreach($qtdAla as $qtdAl)
                            @if($qtdAl['pv'] == $qtdPv['idPavilhao'])
                                <table style="width: 100%;">
                                    <tbody>
                                    <tr>
                                        <td style="width: 30%; text-align: center;">{{ $qtdAl['nomeAla'] }}</td>
                                        <td style="width: 70%;">{{ $qtdAl['qtdAla'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px;"><strong>Total:</strong></td>
                    <td style="width: 496px;" colspan="3">{{ $qtdPv['qtdPavilhao'] }}</td>
                </tr>
                </tbody>
            </table>

        @endforeach

    @endif
    <h6>Total de Internos na Unidade: {{ $internosUnidadeTotal }}</h6>
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XV - Visitantes</h6>
    @if(json_decode($visitantes) == [])
        -- Sem Informações de visitas a internos
    @else
{{--{{ dd($qtdPavilhao) }}--}}
            <h5>Visitas a Internos</h5>
            <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
                <thead>
                <tr>
                    <th style="width: 10%; text-align: center" scope="col">#</th>
                    <th style="width: 30%;" scope="col">Visitante</th>
                    <th style="width: 30%;" scope="col">Interno</th>
                    <th style="width: 15%; text-align: center" scope="col">Hor&aacute;rio Entrada</th>
                    <th style="width: 15%; text-align: center" scope="col">Hor&aacute;rio Sa&iacute;da</th>
                </tr>
                </thead>
                <tbody>
                @foreach($visitantes as $visitante)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%;" cellspacing="1" cellpadding="1">
                                <tbody>
                                <tr>
                                    <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $visitante['nomeVisitante'] }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $visitante['nomeInterno'] }}</td>
                                    <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $visitante['horarioEntrada'] }}</td>
                                    <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $visitante['horarioSaida'] }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>


    @endif
    @if(json_decode($visitantesExterno) != [])

        <h5>Visitantes Externos</h5>
        <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Visitante</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Motivo</th>
                <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Entrada</th>
                <th style="width: 15%; text-align: center; background-color: #CCCCCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Sa&iacute;da</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visitantesExterno as $visitantesExt)
                <tr>
                    <td colspan="5">
                        <table style="width: 100%;" cellspacing="1" cellpadding="1">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCC">{{ $loop->iteration }}</td>
                                <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesExt['nomeVisitante'] }}</td>
                                <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesExt['motivo'] }}</td>
                                <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesExt['horarioEntrada'] }}</td>
                                <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesExt['horarioSaida'] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>


    @endif
    @if(json_decode($visitantesServidor) != [])

        <h5>Servidores</h5>
        <table style="width: 100%; font-size: 12px !important;" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Servidor</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Motivo</th>
                <th style="width: 15%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Entrada</th>
                <th style="width: 15%; text-align: center; background-color: #CCCCCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hor&aacute;rio Sa&iacute;da</th>
            </tr>
            </thead>
            <tbody>
            @foreach($visitantesServidor as $visitantesServ)
                <tr>
                    <td colspan="5">
                        <table style="width: 100%;" cellspacing="1" cellpadding="1">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: center; border-bottom: 1px solid #CCC">{{ $loop->iteration }}</td>
                                <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesServ['nomeVisitante'] }}</td>
                                <td style="width: 30%; border-bottom: 1px solid #CCC">{{ $visitantesServ['motivo'] }}</td>
                                <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesServ['horarioEntrada'] }}</td>
                                <td style="width: 15%; text-align: center; border-bottom: 1px solid #CCC">{{ $visitantesServ['horarioSaida'] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>


    @endif

    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XVI - Advogados</h6>
    @if(json_decode($advogados) == [])
        -- Sem Informações
    @else

            <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
                <thead>
                <tr>
                    <th style="width: 10%; text-align: center" scope="col">#</th>
                    <th style="width: 30%;" scope="col">Advogado</th>
                    <th style="width: 30%;" scope="col">Interno</th>
                    <th style="width: 15%; text-align: center" scope="col">Atendimento</th>
                    <th style="width: 15%; text-align: center" scope="col">Duração</th>
                </tr>
                </thead>
                <tbody>
                @foreach($advogados as $advogado)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%;" cellspacing="1" cellpadding="1">
                                <tbody>
                                <tr>
                                    <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $advogado['nomeAdvogado'] }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $advogado['nomeInterno'] }}</td>
                                    <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $advogado['inicioAtendimento']." - ".$advogado['terminoAtendimento'] }}</td>
                                    <td style="width: 15%; text-align: center; border-right: 1px solid #808080">{{ $advogado['duracao'] }}min</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>


    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XVII - Entrada de Internos na Unidade</h6>
    @if( $entradaInterno == [])
        -- Sem Informações
    @else

            <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
                <thead>
                <tr>
                    <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                    <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
                    <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
                    <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
                    <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Tipo</th>
                    <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
                </tr>
                </thead>
                <tbody>
                @foreach($entradaInterno as $entrada)
                    <tr>
                        <td colspan="6">
                            <table style="width: 100%;" cellspacing="1" cellpadding="1">
                                <tbody>
                                <tr>
                                    <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $entrada['nome'] }}</td>
                                    <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $entrada['prontuario'] }}</td>
                                    <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $entrada['pvAnteior']." - ".$entrada['alaAnteior']." - ".$entrada['celaAnteior'] }}</td>
                                    <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ strtoupper($entrada['tipo']) }}</td>
                                    <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ strtoupper($entrada['horario']) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XVIII - Saída de Internos na Unidade</h6>
    @if( count($saidaInterno) == 0)
        -- Sem Informações
    @else

            <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
                <thead>
                <tr>
                    <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                    <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
                    <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
                    <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
                    <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Tipo</th>
                    <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
                </tr>
                </thead>
                <tbody>
                @foreach($saidaInterno as $saida)
                    <tr>
                        <td colspan="6">
                            <table style="width: 100%;" cellspacing="1" cellpadding="1">
                                <tbody>
                                <tr>
                                    <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                    <td style="width: 30%; border-right: 1px solid #808080">{{ $saida['nome'] }}</td>
                                    <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $saida['prontuario'] }}</td>
                                    <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $saida['pvAnteior']." - ".$saida['alaAnteior']." - ".$saida['celaAnteior'] }}</td>
                                    <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ strtoupper($saida['tipo']) }}</td>
                                    <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ strtoupper($saida['horario']) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XVIV - Movimentação Interna</h6>
    @if( $dadosMudancaCela == [])
        -- Sem Informações
    @else

        <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
            <thead>
            <tr>
                <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
                <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
                <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Origem</th>
                <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Destino</th>
                <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dadosMudancaCela as $mudancaCela)
                <tr>
                    <td colspan="6">
                        <table style="width: 100%;" cellspacing="1" cellpadding="1">
                            <tbody>
                            <tr>
                                <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                <td style="width: 30%; border-right: 1px solid #808080">{{ $mudancaCela['nome'] }}</td>
                                <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $mudancaCela['prontuario'] }}</td>
                                <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $mudancaCela['pvAnteior']." - ".$mudancaCela['alaAnteior']." - ".$mudancaCela['celaAnteior'] }}</td>
                                <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $mudancaCela['pvDestino']." - ".$mudancaCela['alaDestino']." - ".$mudancaCela['celaDestino'] }}</td>
                                <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $mudancaCela['horario'] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>


    @endif
    <hr>
    <br>
    
    <div class="cf"></div>
    <br>
    <h6>XX - Atendimentos Assistenciais</h6>
    @if(count($atendimentosSaudeAssistencia) == 0)
        -- Sem Informações <br>
    @else

        <table style="width: 100%; font-size: 12px !important; " border="1" cellspacing="0" cellpadding="1">
            <thead>
            <tr>
                <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">#</th>
                <th style="width: 30%; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Interno</th>
                <th style="width: 10%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Prontuário</th>
                <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Setor</th>
                <th style="width: 25%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Localização</th>
                <th style="width: 5%; text-align: center; background-color: #CCC; color: #000; border: 1px solid #CCCCCC; padding: 5px" scope="col">Hora</th>
            </tr>
            </thead>
            <tbody>
            @foreach($atendimentosSaudeAssistencia as $atendimento)
                <tr>
                    <td colspan="6">
                        <table style="width: 100%;" cellspacing="1" cellpadding="1">
                            <tbody>
                            <tr>
                                <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $loop->iteration }}</td>
                                <td style="width: 30%; border-right: 1px solid #808080">{{ $atendimento['internoNome'] }}</td>
                                <td style="width: 10%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['internoProntuario'] }}</td>
                                <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['setor'] }}</td>
                                <td style="width: 25%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['internoLocalizacao'] }}</td>
                                <td style="width: 5%; text-align: center; border-right: 1px solid #808080">{{ $atendimento['horarioAtendimento'] }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

    @endif
    <hr>
    <br>

    <div class="cf"></div>
    <br>
    <h6>XXI - Armamento, Munições e Demais Materiais</h6>
    @if(count($materialBelico) == 0)
        -- Sem Informações <a href="{{ route('ocorrencia.cadastro', ['id' => 34, 'returnLivro' => true]) }}" title="Inserir Ocorrência" target="_self" class=""><i class="mdi mdi-file-plus fontSize-20px color_green"></i></a><br>
    @else

        @foreach($materialBelico as $matBel)
            <a href="{{ route('ocorrencia.editar', ['id'=>$matBel['id'], 'returnLivro' => true]) }}" title="Alterar Ocorrência" target="_self" class=""><i class="mdi mdi-pencil-circle-outline fontSize-20px color_BE8900"></i> </a>

            <div class="fontSize-13px">
                {!! $matBel['descricao'] !!}
            </div>

        @endforeach

    @endif
    <hr>
    <br>


@endsection

@push('css')
{{--<link href="{{ asset('css/ocorrencia/cadastro/cadastro.css') }}" rel="stylesheet">--}}
{{--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">--}}
@endpush

@push('script')
{{--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>--}}
{{--<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>--}}
{{--<script src="{{ asset('js/ocorrencia/cadastro.js') }}"></script>--}}
@endpush

