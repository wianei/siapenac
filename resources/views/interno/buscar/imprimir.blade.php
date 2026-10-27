<head xmlns:flex-direction="http://www.w3.org/1999/xhtml">
    <meta charset="UTF-8">
    <title>Prontuário</title>
</head>
<body>

<div id="infobasica">
    <div style="display:inline-block; padding-right: 15px">
        @if($fotoPerfilDados['nome_arquivo'] != 'storage/app/semfoto.jpg')
            <img src="{{storage_path('app/arquivos/internos/fotos/' . $interno['id'] . "/" . FotoService::getFotoPrincipalByIdTipo($interno['id'],'interno'))}}" width="200" height="300" />
        @else
            <img src="{{storage_path('app/semfoto.jpg')}}" width="200" height="300" />
        @endif
    </div>
    <div style="display:inline-block; vertical-align: top; width: 70%">
        <div >
            <strong>Nome: </strong> {{$interno['nome']}}
        </div>
        <div>
            @if( $interno['status'] == 'A')
                <strong>Status: </strong>{{ $interno['condicao_nome'] }}
            @elseif( $interno['status'] == 'I')
                <strong>Status: </strong>{{ $interno['condicao_nome'] }}
            @endif
        </div>
        <div>
            <strong>Prontuário: </strong> {{ $interno['id'] }}
        </div>
        <div>
            @if( $interno['status'] == 'A' )
                <strong>Unidade Prisional: </strong> {{ $interno['unidade_prisional_nome'] }}
            @elseif( $interno['status'] == 'I' )
                <strong>Última Unidade Prisional: </strong> {{ $interno['unidade_prisional_nome'] }}
            @endif
        </div>
        @if( $interno['status'] == 'A' )
            <div>
                <strong>Pavilhão: </strong> {{ $interno['pavilhao_nome'] }}
            </div>
            @if(PermissaoService::getTipoPermissao() == "I")
                <div>
                    <strong>Ala: </strong> {{ $interno['ala_nome'] }}
                </div>
                <div>
                    <strong>Cela: </strong> {{ $interno['cela_nome'] }}
                </div>
            @endif
        @elseif( $interno['status'] == 'I' )
            <div>
                <strong>Último Pavilhão: </strong> {{ $interno['pavilhao_nome'] }}
            </div>
            <div>
                <strong>Última Ala: </strong> {{ $interno['ala_nome'] }}
            </div>
            <div>
                <strong>Última Cela: </strong> {{ $interno['cela_nome'] }}
            </div>
            <div>
                <strong>Último Regime: </strong> {{ $interno['regime'] }}
            </div>
        @endif
        <div>
            <strong>Medida de Segurança: </strong>{{ $interno['medida_seguranca']=="S"?"Sim":"Não" }}
        </div>
        <div>
            <strong>Sentenciado: </strong> {{ $interno['sentenciado']=='N'?"Não":($interno['sentenciado']=='S'?"Sim":"Não Informado") }}
        </div>
        <div>
            <strong>Regime: </strong> {{ $interno['regime'] }}
        </div>
        {{--<strong>Tornozeleira: </strong> {{ $interno['tornozeleira'] }},<br/>--}}
        <div>
            <strong>Facção: </strong> {{ $interno['faccao_nome'] }}
        </div>
        <div>
            <strong>Liderança: </strong> {{ $interno['lideranca'] }}
        </div>
        <div>
            <strong>Periculosidade: </strong> {{ $interno['periculosidade'] }}
        </div>
    </div>
</div>

<div style="margin-top:  50px; display: {{$exibirDados}}" id="dadospessoais">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>DADOS PESSOAIS</label>
    </div>
    <div style="display: inline-block; width: 100%">
        <div style="display: inline-block; width: 48%; ">
            <div>
                <strong>Sexo: </strong> {{$interno['sexo']=='M'?"Masculino":($interno['sexo']=='F'?"Feminino":"Não Informado")}}
            </div>
            <div>
                @if($interno['outrosnomes'] == [])
                    <strong>Outros Nomes: </strong> Não Informado,<br/>
                @else
                    <strong>Outros Nomes: </strong>
                    @foreach($interno['outrosnomes'] as $outrosnomes)
                        {{$outrosnomes}},
                    @endforeach
                @endif
            </div>
            <div>
                <strong>Data de Nascimento: </strong> {{$interno['data_nascimento']}}
            </div>
            <div>
                <strong>Idade: </strong> {{$interno['idade']}}
            </div>
            <div>
                <strong>Estado Civil: </strong> {{$interno['estado_civil']}}
            </div>
            <div>
                <strong>Nome da Mãe: </strong> {{$interno['mae']}}
            </div>
            <div>
                <strong>Nome do Pai: </strong> {{$interno['pai']}}
            </div>
            <div>
                <strong>Número do RG: </strong> {{$interno['rg_numero']}}
            </div>
            <div>
                <strong>Número do CPF: </strong> {{$interno['cpf']}}
            </div>
            <div>
                <strong>Órgão Expedidor do RG: </strong> {{$interno['rg_orgaoexpedidor']}}
            </div>
            <div>
                <strong>Data de Emissão do RG: </strong> {{$interno['rg_data']}}
            </div>
            <div>
                <strong>Nº do Passaporte: </strong> {{$interno['passaportenum']}}
            </div>
            <div>
                <strong>Nº do Cartão SUS: </strong> {{$interno['cartaosus']}}
            </div>
            <div>
                <strong>Profissão: </strong> {{$interno['profissao_nome']}}
            </div>
            <div >
                <strong>Escolaridade: </strong> {{$interno['escolaridade']}}
            </div>
        </div>
        <div style="display: inline-block; width: 48%; vertical-align: top">
            <div >
                <strong>Altura: </strong> {{$interno['altura']}}
            </div>
            <div>
                <strong>Peso: </strong> {{$interno['peso']}}
            </div>
            <div>
                <strong>Cor da Pele: </strong> {{$interno['etnia']}}
            </div>
            <div>
                <strong>Cor dos Olhos: </strong> {{$interno['cor_olhos']}}
            </div>
            <div>
                <strong>Cor do Cabelo: </strong> {{$interno['cor_cabelos']}}
            </div>
            <div>
                <strong>Opção Sexual: </strong> {{$interno['opcao_sexual']}}
            </div>
            <div>
                <strong>Religião: </strong> {{$interno['religiao']}}
            </div>
            <div>
                <strong>Facção: </strong> {{$interno['faccao_nome']}}
            </div>
            @if($interno['faccao'] != "Não Informado")
                <div>
                    <strong>Facção - Postos: </strong> {{$interno['faccao_posto']}}
                </div>
                <div>
                    <strong>Facção - Padrinho: </strong> {{$interno['faccao_padrinho']}}
                </div>
                <div>
                    <strong>Facção - Quebrada: </strong> {{$interno['faccao_quebrada']}}
                </div>
            @endif
            <div>
                <strong>Naturalidade - País: </strong> {{$interno['naturalidade_pais']}}
            </div>
            <div>
                <strong>Naturalidade - Estado: </strong> {{$interno['naturalidade_estado']}}
            </div>
            <div>
                <strong>Naturalidade - Cidade: </strong> {{$interno['naturalidade_cidade']}}
            </div>
            @if(PermissaoService::getTipoPermissao() == "I")
                @if($interno['enderecos'] == [])
                    <div>
                        <strong>Endereço: </strong> Não Informado
                    </div>
                @else
                    @foreach($interno['enderecos'] as $key=>$endereco)
                        <div>
                            <strong>Endereço {{$key+1}}: </strong>
                        </div>
                        <div>
                            <strong>Cidade/Estado: </strong> {{$endereco['cidade']}}/{{$endereco['estado']}}
                        </div>
                        <div>
                            <strong>Bairro: </strong> {{$endereco['bairro']}}
                        </div>
                        <div>
                            <strong>CEP: </strong> {{$endereco['cep']}}
                        </div>
                        <div>
                            <strong>Logradouro: </strong> {{$endereco['logradouro']}}
                        </div>
                        <div>
                            <strong>Número: </strong> {{$endereco['numero']}}
                        </div>
                        <div>
                            <strong>Complemento: </strong> {{$endereco['complemento']}}
                        </div>
                        <div>
                            <strong>Latitude: </strong> {{$endereco['latitude']}}
                        </div>
                        <div>
                            <strong>Longitude: </strong> {{$endereco['longitude']}}
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>

<div style="margin-top: 50px; display:{{$exibirVisita}}"; id="visitantes">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>VISITANTES</label>
    </div>
    <div>
        @if($interno['visitas'] == [])
            <label>Sem visitantes cadastrados.</label>
        @endif
        @foreach($interno['visitas'] as $visita)
            <div style="display: inline-block; margin-left: 5px">
                @if($visita['dadosVisita']['fotoPrincipal']['nome_arquivo'] != 'storage/app/semfoto.jpg')
                    <img src="{{storage_path(substr($visita['dadosVisita']['fotoPrincipal']['nome_arquivo'],8))}}" width="90" height="100"/>
                @else
                    <img src="{{public_path('imagens/usuario/avatarInterno.png')}}" width="90" height="100" />
                @endif
                <div style="display: block;">
                    <div>
                        <strong>Nome: </strong> {{$visita['dadosVisita']['nome']}}
                    </div>
                    <div>
                        <strong>Grau de parentesco: </strong> {{$visita['grau_parentesco']}}
                    </div>
                    <div>
                        <strong>Status: </strong> {{ $visita['statusSuspensao'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div style="margin-top: 25px; margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>ATENDIMENTO DE VISITA</label>
    </div>
    <div>
        @if($visitanteHistorico == [])
            <label>Sem atendimentos cadastrados.</label>
        @endif
        @foreach($visitanteHistorico as $historico)
            <div style="display: inline-block; margin-left: 5px; margin-bottom:10px;">
                <div style="display: block;">
                    <div>
                        <strong>Nome: </strong> {{ $historico['nomeVisita'] }}
                    </div>
                    <div>
                        <strong>Data da visita: </strong> {{ substr($historico['dataEntrada'],0,2) }} de {{ strtoupper($historico['mesData']) }} de {{ substr($historico['dataEntrada'],6,4) }}
                    </div>
                    <div>
                        <strong>Relação: </strong> {{ $historico['relacao'] }}
                    </div>
                    <div>
                        <strong>Horário de entrada na unidade prisional: </strong> {{ $historico['horaEntrada'] }}
                    </div>
                    <div>
                        <strong>Horário de saída na unidade prisional: </strong> {{ $historico['horaSaida'] }}
                    </div>
                    <div>
                        <strong>Duração do contato com o interno: </strong> {{ $historico['duracao'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div style="margin-top: 50px; display: {{$exibirAdvogado}}" id="advogados">

    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>ADVOGADOS</label>
    </div>

    <div>
        @if($interno['advogados'] == [])
            <label>Sem advogados cadastrados.</label>
        @endif
        @foreach($interno['advogados'] as $advogado)
            <div style="display: inline-block; margin-left: 5px">
                @if($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'] != 'storage/app/semfoto.jpg')
                    <img src="{{storage_path(substr($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'], 8))}}" width="90" height="100"/>
                @else
                    <img src="{{storage_path(substr($advogado['dadosAdvogado']['fotoPrincipal']['nome_arquivo'], 8))}}" width="90" height="100"/>
                @endif
                <div style="display: block">
                    <div>
                        <strong>Nome: </strong>{{$advogado['dadosAdvogado']['nome']}}
                    </div>
                    <div>
                        <label>OAB: </label> {{$advogado['dadosAdvogado']['oab_numero']}}/{{$advogado['dadosAdvogado']['oab_uf']}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div>
        <div style="margin-top: 25px; margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
            <label>ATENDIMENTO DE ADVOGADOS</label>
        </div>
        @if($advogadoHistorico == [])
            <label>Sem atendimentos.</label>
        @endif
        @foreach($advogadoHistorico as $historico)
            <div style="display: inline-block; margin-left: 5px; margin-bottom:10px;">
                <div style="display: block">
                    <div>
                        <strong>OAB: </strong> {{ $historico['oab'] }}
                    </div>
                    <div>
                        <strong>Data: </strong> {{ substr($historico['data'],0,2) }} de {{ strtoupper($historico['mesData']) }} de {{ substr($historico['data'],6,4) }}
                    </div>
                    <div>
                        <strong>Horário de início do atendimento: </strong> {{ $historico['horaInicio'] }}
                    </div>
                    <div>
                        <strong>Horário de término do atendimento: </strong> {{ $historico['horaTermino'] }}
                    </div>
                    <div>
                        <strong>Duração do contato com o interno: </strong> {{ $historico['duracao'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<div style="margin-top: 50px; display: {{$exibirHistorico}}" id="historico">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>HISTÓRICO</label>
    </div>

    @foreach($historicos as $historico)
            <!--movimentação de TRANSFERENCIA -->
    @if($historico['tipo'] == 'transferencia')
        <div style="display: block; margin-top: 10px">
            <div>
                <strong>Tipo: </strong>Transferência
            </div>
            <div>
                <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
            </div>
            <div>
                <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'] }}
            </div>
            <div>
                <strong>Origem: </strong> {{ $historico['nomeUp_anterior'] }}
            </div>
            <div>
                <strong>Motivo: </strong> {{ $historico['motivo'] }}
            </div>
            <div>
                <strong>Status: </strong> {{ $historico['status'] }}
            </div>
            @if($historico['log'] != null)
                <div>
                    <strong>Enviado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                </div>
            @endif
        </div>
        @endif
                <!--movimentação de PROGRESSÃO PARA O ABERTO -->
        @if($historico['tipo'] == 'progressaoaberto')
            <div style="display: block; margin-top: 10px">
                <div>
                    <strong>Tipo: </strong>Progressão para o Aberto
                </div>
                <div>
                    <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                </div>
                <div>
                    <strong>Motivo: </strong> {{ $historico['motivo'] }}
                </div>
                <div>
                    <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                </div>
                @if($historico['log'] != null)
                    <div>
                        <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                    </div>
                @endif
            </div>
            @endif
                    <!--movimentação de PROGRESSÃO PARA O SEMIABERTO -->
            @if($historico['tipo'] == 'progressaosemiaberto')
                <div style="display: block; margin-top: 10px">
                    <div>
                        <strong>Tipo: </strong>Progressão para o Semiaberto
                    </div>
                    <div>
                        <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                    </div>
                    <div>
                        <strong>Motivo: </strong> {{ $historico['motivo'] }}
                    </div>
                    <div>
                        <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                    </div>
                    @if($historico['log'] != null)
                        <div>
                            <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                        </div>
                    @endif
                </div>
                @endif
                        <!-- movimentação MUDANÇA DE CELA-->
                @if($historico['tipo'] == 'mudancacela')
                    <div style="display: block; margin-top: 10px">
                        <div>
                            <strong>Tipo: </strong>Mudança de Cela
                        </div>
                        <div>
                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                        </div>
                        <div>
                            <strong>Motivo: </strong> {{ $historico['motivo'] }}
                        </div>
                        <div>
                            <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                        </div>
                        <div>
                            <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                        </div>
                        @if($historico['log'] != null)
                            <div>
                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($historico['tipo'] == 'mudancafechado' || $historico['tipo'] == 'mudancasemiaberto' || $historico['tipo'] == 'mudancaaberto' || $historico['tipo'] == 'mudancaprovisorio')
                    <div style="display: block; margin-top: 10px">
                        <div>
                            <strong>Tipo: </strong>Mudança
                            @if($historico['tipo'] == 'mudancafechado')
                                de Provisório para o Regime Fechado
                            @elseif($historico['tipo'] == 'mudancasemiaberto')
                                de Provisório para o Regime Semiaberto
                            @elseif($historico['tipo'] == 'mudancaaberto')
                                de Provisório para o Regime Aberto
                            @elseif($historico['tipo'] == 'mudancaprovisorio')
                                de Sentenciado para Provisório
                            @endif
                        </div>
                        <div>
                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                        </div>
                        <div>
                            <strong>Motivo: </strong> {{ $historico['motivo'] }}
                        </div>
                        @if($historico['log'] != null)
                            <div>
                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                            </div>
                        @endif
                    </div>
                    @endif
                            <!-- movimentação RECAMBIAMENTO-->
                    @if($historico['tipo'] == 'recambiamento')
                        <div style="display: block; margin-top: 10px">
                            <div>
                                <strong>Tipo: </strong>Recambiamento
                            </div>
                            <div>
                                <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                            </div>
                            <div>
                                <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'] }}
                            </div>
                            <div>
                                <strong>Motivo e Observações: </strong> {{ $historico['motivo'] }}
                            </div>
                            @if($historico['log'] != null)
                                <div>
                                    <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                </div>
                            @endif
                        </div>
                        @endif
                                <!-- movimentação REGRESSÃO SEMIABERTO-->
                        @if($historico['tipo'] == 'regressaosemiaberto')
                            <div style="display: block; margin-top: 10px">
                                <div>
                                    <strong>Tipo: </strong>Regressão Semiaberto
                                </div>
                                <div>
                                    <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                </div>
                                <div>
                                    <strong>Motivo: </strong> {{ $historico['motivo'] }}
                                </div>
                                <div>
                                    <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                </div>
                                <div>
                                    <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                </div>
                                @if($historico['log'] != null)
                                    <div>
                                        <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                    </div>
                                @endif
                            </div>
                            @endif
                                    <!-- movimentação REGRESSÃO FECHADO-->
                            @if($historico['tipo'] == 'regressaofechado')
                                <div style="display: block; margin-top: 10px">
                                    <div>
                                        <strong>Tipo: </strong>Regressão fechado
                                    </div>
                                    <div>
                                        <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                    </div>
                                    <div>
                                        <strong>Motivo: </strong> {{ $historico['motivo'] }}
                                    </div>
                                    <div>
                                        <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                    </div>
                                    <div>
                                        <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                    </div>
                                    @if($historico['log'] != null)
                                        <div>
                                            <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                        </div>
                                    @endif
                                </div>
                                @endif
                                        <!-- movimentação EXTINÇÃO PUNIBILIDADE-->
                                @if($historico['tipo'] == 'extincaopunibilidade')
                                    <div style="display: block; margin-top: 10px">
                                        <div>
                                            <strong>Tipo: </strong>Extinsão da Punibilidade
                                        </div>
                                        <div>
                                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                        </div>
                                        <div>
                                            <strong>Observação: </strong> {{ $historico['motivo'] }}
                                        </div>
                                        <div>
                                            <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                        </div>
                                        @if($historico['log'] != null)
                                            <div>
                                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                            <!-- movimentação PRISÃO DOMICILIAR-->
                                    @if($historico['tipo'] == 'prisaodomiciliar')
                                        <div style="display: block; margin-top: 10px">
                                            <div>
                                                <strong>Tipo: </strong>Prisão Domiciliar
                                            </div>
                                            <div>
                                                <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                            </div>
                                            <div>
                                                <strong>Motivo e Observação: </strong> {{ $historico['motivo'] }}
                                            </div>
                                            <div>
                                                <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                            </div>
                                            @if($historico['log'] != null)
                                                <div>
                                                    <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                                <!-- movimentação OBITO-->
                                        @if($historico['tipo'] == 'obito')
                                            <div style="display: block; margin-top: 10px">
                                                <div>
                                                    <strong>Tipo: </strong>Óbito
                                                </div>
                                                <div>
                                                    <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                </div>
                                                <div>
                                                    <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                </div>
                                                <div>
                                                    <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                </div>
                                                @if($historico['log'] != null)
                                                    <div>
                                                        <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                    </div>
                                                @endif
                                            </div>
                                            @endif
                                                    <!-- movimentação LIVRAMENTO CONDICIONAL-->
                                            @if($historico['tipo'] == 'livramentocondicional')
                                                <div style="display: block; margin-top: 10px">
                                                    <div>
                                                        <strong>Tipo: </strong>Livramento Condicional
                                                    </div>
                                                    <div>
                                                        <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                    </div>
                                                    <div>
                                                        <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                    </div>
                                                    <div>
                                                        <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                    </div>
                                                    @if($historico['log'] != null)
                                                        <div>
                                                            <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                                @endif
                                                        <!-- movimentação ALARÁ-->
                                                @if($historico['tipo'] == 'alvara')
                                                    <div style="display: block; margin-top: 10px">
                                                        <div>
                                                            <strong>Tipo: </strong>Alvará de Soltura
                                                        </div>
                                                        <div>
                                                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                        </div>
                                                        <div>
                                                            <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                        </div>
                                                        <div>
                                                            <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                        </div>
                                                        @if($historico['log'] != null)
                                                            <div>
                                                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                        <!-- movimentação EXTRADIÇÃO-->
                                                @if($historico['tipo'] == 'extradicao')
                                                    <div style="display: block; margin-top: 10px">
                                                        <div>
                                                            <strong>Tipo: </strong>Extradição
                                                        </div>
                                                        <div>
                                                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                        </div>
                                                        <div>
                                                            <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                        </div>
                                                        <div>
                                                            <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                        </div>
                                                        @if($historico['log'] != null)
                                                            <div>
                                                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                        <!-- movimentação SAÍDA JUDICIAL-->
                                                @if($historico['tipo'] == 'saidajudicial')
                                                    <div style="display: block; margin-top: 10px">
                                                        <div>
                                                            <strong>Tipo: </strong>Saída Judicial
                                                        </div>
                                                        <div>
                                                            <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                        </div>
                                                        <div>
                                                            <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                        </div>
                                                        <div>
                                                            <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                        </div>
                                                        @if($historico['log'] != null)
                                                            <div>
                                                                <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @endif
                                                            <!-- movimentação FUGA-->
                                                    @if($historico['tipo'] == 'fuga')
                                                        <div style="display: block; margin-top: 10px">
                                                            <div>
                                                                <strong>Tipo: </strong>Fuga
                                                            </div>
                                                            <div>
                                                                <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                            </div>
                                                            <div>
                                                                <strong>Observação: </strong> {{ $historico['motivo'] }}
                                                            </div>
                                                            <div>
                                                                <strong>Origem: </strong> {{ $historico['nomeUp_anterior'].", " .$historico['nomePv_anterior'].", " .$historico['nomeAla_anterior'].", "  .$historico['nomeCela_anterior'].", " }}
                                                            </div>
                                                            @if($historico['log'] != null)
                                                                <div>
                                                                    <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @endif
                                                                <!-- movimentação ENTRADA NA UNIDADE-->
                                                        @if($historico['tipo'] == 'entradaunidadeprisional' || $historico['tipo'] == 'transferenciaentrada')
                                                            <div style="display: block; margin-top: 10px">
                                                                <div>
                                                                    <strong>Tipo: </strong>Entrada na Unidade Prisional
                                                                </div>
                                                                <div>
                                                                    <strong>Ocorrido em: </strong>{{ substr($historico['created_at'],8,2) }} DE {{ strtoupper($historico['mesData']) }} DE {{ substr($historico['created_at'],0,4) }}
                                                                </div>
                                                                <div>
                                                                    <strong>Observação e Motivo: </strong> {{ $historico['motivo'] }}
                                                                </div>
                                                                <div>
                                                                    <strong>Destino: </strong> {{ $historico['nomeUnidade_destino'].", " .$historico['nomePavilhao_destino'].", " .$historico['nomeAla_destino'].", "  .$historico['nomeCela_destino'].", " }}
                                                                </div>
                                                                @if($historico['log'] != null)
                                                                    <div>
                                                                        <strong>Registrado por: </strong>{{$historico['log'][0]['realizadoPor']." em ".$historico['log'][0]['data']." às ".$historico['log'][0]['hora'] }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @endforeach

</div>

<div style="margin-top: 50px; display: {{$exibirCompanheiros}}" id="companheiroscela">

    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>COMPANHEIROS DE CELA</label>
    </div>

    @if($companheirosCela == null)
        <label>Não há companheiros para mostrar!</label>
    @else
        @if(count($companheirosCela) == 0)
            <label>Sem companheiros de cela.</label>
        @endif
        @foreach($companheirosCela as $companheiros)
            <div style="display: inline-block; margin-left: 15px; vertical-align: top; margin-top: 15px">
                <div>
                    <strong>Nome: </strong>{{ $companheiros['nome'] }}
                </div>
                @if($companheiros['outrosnomes'] != [])
                    <div>
                        <strong>Outros Nomes: </strong>
                        @foreach($companheiros['outrosnomes'] as $outronome)
                            @if($outronome['nome'] != '')
                                {{ $outronome['nome'] }},
                            @endif
                        @endforeach
                    </div>
                @endif
                <div>
                    <strong>Dias na Cela: </strong>{{ $companheiros['diasCela'] }}
                </div>
                <div>
                    <strong>Facção: </strong> {{ $companheiros['faccao'] }}
                </div>
                <div>
                    <strong>Liderança: </strong>{{ $companheiros['lideranca'] }}
                </div>
                <div>
                    <strong>Periculosidade: </strong>{{ $companheiros['periculosidade'] }}
                </div>
            </div>
        @endforeach
    @endif

</div>

<div style="margin-top: 50px; display: {{$exibirFotos}}" id="fotos">

    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>FOTOS</label>
    </div>

    @if($interno['fotos'] == collect([]) || $interno['fotos'] == [])
        <label>Sem fotos cadastradas.</label>
    @endif
    @foreach($interno['fotos'] as $foto)
        <div style="display: inline-block; margin-top: 10px; margin-left: 10px; vertical-align: top">
            <img src="{{storage_path('app/arquivos/internos/fotos/'.$foto['fk_id'].'/'.$foto['nome_arquivo'])}}" width="370" height="420"/>
            <div style="display: block;">
                <div>
                    <strong>Tipo</strong>: {{ strtoupper($foto['tipo'])}}
                </div>
                <div>
                    <strong>Descrição</strong>: {{strtoupper($foto['legenda'])}}
                </div>
                <div>
                    <strong>Data da inserção da foto</strong>: {{$foto['created_at']}}
                </div>
                <div>
                    <strong>Data da última alteração na foto</strong>: {{$foto['updated_at']}}
                </div>
            </div>
        </div>
    @endforeach
</div>

<div style="margin-top: 50px; display: {{$exibirEscoltas}}" id="escolta">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>ESCOLTAS</label>
    </div>
    @if($escolta == [] || $escolta == collect([]))
        <label>Sem escoltas cadastradas.</label>
    @endif
    @foreach($escolta as $esc)
        <div style="display: inline-block; margin-top: 10px; margin-left: 10px; vertical-align: top">
            <div>
                <strong>Tipo: </strong>{{ strtoupper($esc['dadosEscolta'][0]['tipo']) }}
            </div>
            <div>
                <strong>Origem: </strong> {{ $esc['dadosEscolta'][0]['unidade'] }}
            </div>
            <div>
                <strong>Destino: </strong> {{ $esc['dadosEscolta'][0]['destino'] }}
            </div>
            @if($esc['dadosAgentes'] != null)
                <div>
                    <strong>Escoltantes: </strong>
                    @foreach($esc['dadosAgentes'] as $agentes)
                        {{ $agentes['nomeAgente']." (".$agentes['matricula']."), " }}
                    @endforeach
                </div>
            @endif
            @if(count($esc['dadosEscolta'][0]['internosEscolta']) > 0)
                <div>
                    <strong>Internos na mesma escolta: </strong>
                    @foreach($esc['dadosEscolta'][0]['internosEscolta'] as $interEsc)
                        {{ $interEsc['nome']." (".$interEsc['prontuario']."), " }}
                    @endforeach
                </div>
            @endif
            <div>
                <strong>Saída: </strong> {{ $esc['dadosEscolta'][0]['dataSaida']." às ".$esc['dadosEscolta'][0]['horaSaida'] }}
            </div>
            <div>
                <strong>Retorno: </strong> {{ $esc['dadosEscolta'][0]['dataRetorno']." às ".$esc['dadosEscolta'][0]['horaRetorno'] }}
            </div>
            @if($esc['dadosEscolta'][0]['vtrPlaca'] != null)
                <div>
                    <strong>Placa da Viatura: </strong> {!!  $esc['dadosEscolta'][0]['vtrPlaca']." / <strong>Motorista: </strong>".$esc['dadosEscolta'][0]['vtrMotorista'] !!}
                </div>
            @endif
            <div>
                <strong>Observação: </strong> {{ $esc['dadosEscolta'][0]['obs'] }}
            </div>
            @if($esc['dadosArquivos'] != null)
                <div>
                    <strong>Documentos: </strong>
                    @foreach($esc['dadosArquivos'] as $arquivo)
                        {{$arquivo['nomeArquivo']}},
                    @endforeach
                </div>
            @endif
            @if($esc['dadosLog'] != null)
                <div>
                    <strong>Registrado por: </strong> {{ $esc['dadosLog'][0]['realizadoPor']." em ".$esc['dadosLog'][0]['data']." às ".$esc['dadosLog'][0]['hora'] }}
                </div>
            @endif
        </div>
    @endforeach
</div>

<div style="margin-top: 50px; display: {{$exibirSaude}}" id="saude">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>ATENDIMENTO ASSISTENCIAL</label>
    </div>
    @if($saudeAssist == collect([]) || $saudeAssist == [])
        <label>Sem atendimentos cadastradas.</label>
    @endif
    <div style="display: inline-block; margin-top: 10px; margin-left: 10px; vertical-align: top">
        @foreach($saudeAssist as $saude)
            <div>
                <strong>Realizado em: </strong>{{ substr($saude['dataAtendimento'],0,2) }} de {{ strtoupper($saude['mesData']) }} de {{ substr($saude['dataAtendimento'],6,4) }}
            </div>
            <div>
                <strong>Setor: </strong> {{ $saude['setor'] }}
            </div>
            @if($saude['crm'] && $saude['nomeMedico'])
                <div>
                    <strong>Profissional: </strong> {{ $saude['nomeMedico'] }} [{{$saude['crm']}}]
                </div>
            @endif
            <div>
                <strong>Horário de atendimento: </strong> {{ $saude['horaAtendimento']  }}
            </div>
            <div>
                <strong>Descrição do Atendimento</strong> {!! $saude['descricao'] !!}
            </div>
            <div>
                <strong>{{ $saude['log'] }}</strong>
            </div>
        @endforeach
    </div>

</div>

<div style="margin-top: 50px; display: {{$exibirFalta}}" id="falta">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>FALTA DISCIPLINAR</label>
    </div>
    @if($faltas == collect([]) || $faltas == [])
        <label>Sem faltas disciplinares cadastradas.</label>
    @endif
    <div>
        @foreach($faltas as $falta)
            <div>
                <strong>Registrado em: </strong>{{ substr($falta['dadosSindicancia'][0]['dataIncidente'],8,2) }} de {{ strtoupper($falta['dadosSindicancia'][0]['mesData']) }} de {{ substr($falta['dadosSindicancia'][0]['dataIncidente'],0,4) }}
            </div>


            <div class="col-md">
                <strong>Natureza da falta: </strong>{{ $falta['dadosSindicancia'][0]['gravidadeFalta'] }}
            </div>
            <div>
                <strong>Título: </strong> {{ $falta['dadosSindicancia'][0]['titulo'] }}
            </div>
            <div>
                <strong>Status: </strong> {{ $falta['dadosSindicancia'][0]['status'] }}
            </div>
            <div>
                <strong>Unidade: </strong> {{ $falta['dadosSindicancia'][0]['unidade'] }}
            </div>
            @if($falta['dadosSindicanciaAgentes'] != null)
                <div>
                    <strong>Agentes Envolvidos: </strong>
                    @foreach($falta['dadosSindicanciaAgentes'] as $agentes)
                        {{ $agentes['nomeAgente']." (".$agentes['matricula']."), " }}
                    @endforeach
                </div>
            @endif
            @if($falta['dadosSindicanciaArquivos'] != null)
                <div>
                    <strong>Documentos: </strong>
                    @foreach($falta['dadosSindicanciaArquivos'] as $arquivosSind)
                        {{$arquivosSind['nomeArquivo']}},
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

<div style="margin-top: 50px; display: {{$exibirProcessos}}" id="processosCrimes">
    <div style="margin-bottom: 40px; font-weight: bold; text-align: center; font-size: 30px">
        <label>PROCESSOS E CRIMES</label>
    </div>
    @if($processos == collect([]) || $processos == [])
        <label>Sem processos e crimes cadastradas.</label>
    @endif
    <div>

            @foreach($processos as $processo)
                <div class="col-12 p-3 border1pxSolidRGBA0125 mb-4">
                    <h4 class="color_365f80"><i class="mdi mdi-bookmark"></i>{{ $processo['tipo'] }}</h4>

                    @if($processo['tipo'] == "Inquérito Policial")
                        <div class="row">
                            <div class="col-12">
                                <strong>
                                @if(!is_null($processo['dadosProcesso']->delegacia))
                                 <em>{{ $processo['dadosProcesso']->delegacia.", " }}</em>
                                @endif

                                @if(!is_null($processo['dadosProcesso']->uf))
                                 <em>{{ $processo['dadosProcesso']->municipio."-".$processo['dadosProcesso']->uf }}</em>
                                @endif
                                </strong>
                            </div>

                            @if(!is_null($processo['dadosProcesso']->motivo_prisao))
                                <div class="col-sm-4 mt-2">
                                    <strong>Motivo da Prisão:</strong> {{ $processo['dadosProcesso']->motivo_prisao }}
                                </div>
                            @endif

                            <div class="col-sm-4 mt-2">
                                <strong>Número:</strong> {{ $processo['dadosProcesso']->numero }}
                            </div>

                            @if(!is_null($processo['dadosProcesso']->dataPrisao))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data da Prisão:</strong> {{ $processo['dadosProcesso']->dataPrisao }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->dataInquerito))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data do Inquérito:</strong> {{ $processo['dadosProcesso']->dataInquerito }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->dataFato))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data do Fato:</strong> {{ $processo['dadosProcesso']->dataFato }}
                                </div>
                            @endif
                        </div>
                        <h6 class="mt-2">Dispositivos Legais</h6>
                            <div class="row">

                                @foreach($processo['dispositivoLegal'] as $lei)
                                    <div class="col-12 mb-2 text" style="font-style: italic">
                                        <strong>{{ $lei["lei"] }}</strong><br>
                                        {{ $lei["artigo"] }}<br>
                                        {{ $lei["nome"] }}<br>
                                        {{ $lei["glossario"] }}<br>
                                    </div>
                                @endforeach
                            </div>
                    @endif

                    @if($processo['tipo'] == "Processo")
                        <div class="row">
                            <div class="col-12">
                                <strong>
                                    @if(!is_null($processo['dadosProcesso']->vara))
                                        <em>{{ $processo['dadosProcesso']->vara.", " }}</em>
                                    @endif

                                    @if(!is_null($processo['dadosProcesso']->uf))
                                        <em>{{ $processo['dadosProcesso']->municipio."-".$processo['dadosProcesso']->uf }}</em>
                                    @endif
                                </strong>
                            </div>

                        @if(!is_null($processo['dadosProcesso']->motivo_prisao))
                                <div class="col-sm-4 mt-2">
                                    <strong>Motivo da Prisão:</strong> {{ $processo['dadosProcesso']->motivo_prisao }}
                                </div>
                            @endif

                            <div class="col-sm-4 mt-2">
                                <strong>Número do Processo:</strong> {{ $processo['dadosProcesso']->numero }}
                            </div>

                            @if(!is_null($processo['dadosProcesso']->dataProcesso))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data do Processo:</strong> {{ $processo['dadosProcesso']->dataProcesso }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->guia_recolhimento))
                                <div class="col-sm-4 mt-2">
                                    <strong>Guia de Recolhimento:</strong> {{ $processo['dadosProcesso']->guia_recolhimento }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->esfera_judicial))
                                <div class="col-sm-4 mt-2">
                                    <strong>Esfera Judicial:</strong> {{ $processo['dadosProcesso']->esfera_judicial }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->reu))
                                <div class="col-sm-4 mt-2">
                                    <strong>Tipo de Réu:</strong> {{ $processo['dadosProcesso']->reu }}
                                </div>
                            @endif
                        </div>
                        <h6 class="mt-2">Dispositivos Legais</h6>
                            <div class="row">

                                @foreach($processo['dispositivoLegal'] as $lei)
                                    <div class="col-12 mb-2 text" style="font-style: italic">
                                        <strong>{{ $lei["lei"] }}</strong><br>
                                        {{ $lei["artigo"] }}<br>
                                        {{ $lei["nome"] }}<br>
                                        {{ $lei["glossario"] }}<br>
                                    </div>
                                @endforeach
                            </div>
                    @endif

                    @if($processo['tipo'] == "Condenação")
                        <div class="row">
                            <div class="col-12">
                                <strong>
                                    @if(!is_null($processo['dadosProcesso']->quantidade_anos))
                                        <em>{{ $processo['dadosProcesso']->quantidade_anos." Anos, " }}</em>
                                    @endif

                                    @if(!is_null($processo['dadosProcesso']->quantidade_meses))
                                        <em>{{ $processo['dadosProcesso']->quantidade_meses." Meses, "}}</em>
                                    @endif

                                    @if(!is_null($processo['dadosProcesso']->quantidade_dias))
                                        <em>{{ $processo['dadosProcesso']->quantidade_dias." Dias "}}</em>
                                    @endif
                                </strong>
                            </div>

                        @if(!is_null($processo['dadosProcesso']->tipo_prisao))
                                <div class="col-sm-4 mt-2">
                                    <strong>Tipo de Prisão:</strong> {{ $processo['dadosProcesso']->tipo_prisao }}
                                </div>
                            @endif

                            <div class="col-sm-4 mt-2">
                                <strong>Número do Processo:</strong> {{ $processo['dadosProcesso']->numero }}
                            </div>

                            @if(!is_null($processo['dadosProcesso']->dataSentenca))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data da Sentença:</strong> {{ $processo['dadosProcesso']->dataSentenca }}
                                </div>
                            @endif
                            @if(!is_null($processo['dadosProcesso']->dataTransitoJulgado))
                                <div class="col-sm-4 mt-2">
                                    <strong>Data do Trânsito em Julgado:</strong> {{ $processo['dadosProcesso']->dataTransitoJulgado }}
                                </div>
                            @endif
                        </div>
                        <h6 class="mt-2">Dispositivos Legais</h6>
                            <div class="row">

                                @foreach($processo['dispositivoLegal'] as $lei)
                                    <div class="col-12 mb-2 text" style="font-style: italic">
                                        <strong>{{ $lei["lei"] }}</strong><br>
                                        {{ $lei["artigo"] }}<br>
                                        {{ $lei["nome"] }}<br>
                                        {{ $lei["glossario"] }}<br>
                                    </div>
                                @endforeach
                            </div>
                    @endif
                </div>
            <hr>
            @endforeach
    </div>
</div>
    
</body>