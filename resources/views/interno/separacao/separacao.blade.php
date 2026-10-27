@extends('template.tema_geral.theme')

@section('content')

    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-format-list-numbers"></i> Internos para Separação</h3>
        <hr>
        <p>
            Internos que terão atendimento com advogado.
        </p>
    </div>

    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->


    <div class="form-row">
        <div class="form-group col-sm-2">
            @if($dia == 1)
                <i class="mdi mdi-arrow-left-box display-3 color_C5C5C5"></i>
                @else
                <a href="{{ route('interno.separacao', (abs($dia-2))) }}">
                    <i class="mdi mdi-arrow-left-box display-3 color_C5C5C5"></i>
                </a>
            @endif
        </div>
        <div class="form-group col-sm-8 text-center display-4">
            {{ $dataDisplay }}
        </div>
        <div class="form-group col-sm-2">
            <a href="{{ route('interno.separacao', ($dia)) }}">
                <i class="mdi mdi-arrow-right-box float-right display-3 color_C5C5C5"></i>
            </a>
        </div>
    </div>

    <div class="form-row">

        <div class="form-group col-sm-3">
            <label for="pavilhao">Pavilhão</label>
            <select id="pavilhao" name="pavilhao" class="form-control selectUnidade">
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


    <h5 class="titleInterno mt-5">Internos para Atendimento Advogado<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_e65251 color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Localização</th>
                <th scope="col">Horário</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @foreach($atendimentoAdv as $atdAdv)
                <tr class="{{ str_replace(" ","",$atdAdv->pvNome) }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $atdAdv->id }}</td>
                    <td>
                        @if($atdAdv->foto != null)
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $atdAdv->id . "/$atdAdv->foto")}}" class="imagemSrc cursorPointer"  alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif

                    </td>
                    <td>{{ $atdAdv->nome }}</td>
                    <td>{{ $atdAdv->facNome }}</td>
                    <td>{{ $atdAdv->lideranca }}</td>
                    <td>{{ $atdAdv->sigla }}</td>
                    <td>{{ $atdAdv->atendHorario }}</td>
                </tr>
                <tr class="{{ str_replace(" ","",$atdAdv->pvNome) }}">
                    <td colspan="7" class="border1pxSolid_CCC">
                        Advogado: {{ $atdAdv->advNome }} / Email: {{ $atdAdv->advEmail }} / CPF: {{ $atdAdv->advCpf }}
                    </td>
                    
                    @if($dia == 1)
                        <td colspan="1" class="border1pxSolid_CCC">
                            <a href="{{ route('advogado.confirmarRealizacaoAdvAgendamento', $atdAdv->idAgendamento) }}" class="btn-acoes fontSize-18px color_green" title="Confirmar Realização">
                                <i class="color_green mdi mdi-marker-check"></i>
                            </a>

                            <a href="{{ route('advogado.registrarFaltaAdvAgendamento', $atdAdv->idAgendamento) }}" onclick="return confirm('Deseja realmente registrar a FALTA no atendimento do Advogado?');"  class="btn-acoes fontSize-18px color_red" title="Registrar Falta">
                                <i class="color_red mdi mdi-window-close"></i>
                            </a>

                        </td>
                    @endif

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

<!--    <h5 class="titleInterno mt-5">Internos para Escolta do FTPEN<i class="mdi mdi-arrow-bottom-right"></i></h5>
    <div id="tableInternos" class="table-responsive-md">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">#</th>
                <th scope="col">Prontuário</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Facção</th>
                <th scope="col">Liderança</th>
                <th scope="col">Localização</th>
            </tr>
            </thead>
            <tbody id="retornoInterno" class="retornoInternos">
            @foreach($escoltaGep as $escolta)
                <tr class="{{ str_replace(" ","",$escolta->pvNome) }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $escolta->id }}</td>
                    <td>
                        @if($escolta->foto != null)
                            <img style="text-align: center" src="{{asset('storage/app/arquivos/internos/fotos/' . $escolta->id . "/$escolta->foto")}}" class="imagemSrc cursorPointer"  alt="Foto do Perfil">
                        @else
                            <img style="text-align: center" src="{{ asset('imagens/usuario/avatarInterno.png') }}" class="" alt="Foto do Perfil">
                        @endif

                    </td>
                    <td>{{ $escolta->nome }}</td>
                    <td>{{ $escolta->facNome }}</td>
                    <td>{{ $escolta->lideranca }}</td>
                    <td>{{ $escolta->sigla }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>-->


@endsection

@push('script')
<script src="{{ asset('js/interno/separacaoInterno.js') }}"></script>
@endpush