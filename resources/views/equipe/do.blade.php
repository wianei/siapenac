@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fa fa-user-plus"></i> Registrar diária operacional</h3>
        <hr>
        <p>
            Registre e consulte as diárias operacionais registradas para os próximos dias.
        </p>
    </div>

    <form action="#">

        <div class="form-row">

            <div class="form-group col-md-4">
                <label for="unidadeO">Unidade de origem do agente</label>
                <select id="unidadeO" name="unidadeO" class="form-control">
                    <option value="PEA">
                        PEA
                    </option>
                    <option value="PEP">
                        PEP
                    </option>
                    <option value="CPJC">
                        CPJC
                    </option>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="unidadeD">Unidade de realização da DO</label>
                <select id="unidadeD" name="unidadeD" class="form-control" disabled>
                    <option value="PEA">
                        PEA
                    </option>
                    <option value="PEP">
                        PEP
                    </option>
                    <option value="CPJC">
                        CPJC
                    </option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="pavilhao">Pavilhão de realização da DO</label>
                <select id="pavilhao" name="pavilhao" class="form-control">
                    <option value="1">
                        1
                    </option>
                    <option value="2">
                        2
                    </option>
                    <option value="3">
                        3
                    </option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="dataA">Data</label>
                <input type="text" name="dataA" class="form-control" id="dataA" placeholder="Data">
            </div>
            <div class="form-group col-md-4">
                <label for="horainicio">Horário do Início</label>
                <input type="text" name="horainicio" class="form-control" id="horainicio" placeholder="Horário do Início">
            </div>
            <div class="form-group col-md-4">
                <label for="horafim">Horário do Fim</label>
                <input type="text" name="horafim" class="form-control" id="horafim" placeholder="Horário do Fim">
            </div>
        </div>


        <div class="form-row">
            <div class="form-group col-md">
                <label for="unidade">Agente</label>
                <select id="unidade" name="unidade" class="form-control">
                    <option value="124">
                        João
                    </option>
                    <option value="313">
                        Maria
                    </option>
                    <option value="555">
                        José
                    </option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Registrar D.O.</button>
    </form>

    <hr>

    <h4 class="mt-3">Diárias operacionais registradas para os próximos dias</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Data</th>
                <th scope="col">Hora Início/Fim</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome / Matrícula</th>
                <th scope="col">Contato</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td scope="row">10/09/2018</td>
                <td scope="row">08h00 / 14h00</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>ALEXANDRE ALVES DE SOUSA (222.222-2)</td>
                <td>84-98888-2222</td>
                <td class="text-center"><a href="#" data-toggle="tooltip" data-placement="top" title="Cancelar D.O."><i class="fas fa-trash-alt color_red"></i></a></td>
            </tr>
            <tr class="">
                <td scope="row">10/09/2018</td>
                <td scope="row">08h00 / 14h00</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>ALEXANDRE ALVES DE SOUSA (222.222-2)</td>
                <td>84-98888-2222</td>
                <td class="text-center"><a href="#" data-toggle="tooltip" data-placement="top" title="Cancelar D.O."><i class="fas fa-trash-alt color_red"></i></a></td>
            </tr>
            <tr class="">
                <td scope="row">10/09/2018</td>
                <td scope="row">08h00 / 14h00</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>ALEXANDRE ALVES DE SOUSA (222.222-2)</td>
                <td>84-98888-2222</td>
                <td class="text-center"><a href="#" data-toggle="tooltip" data-placement="top" title="Cancelar D.O."><i class="fas fa-trash-alt color_red"></i></a></td>
            </tr>
            </tbody>
        </table>
    </div>

@endsection
@push('css')
<link href="{{ asset('css/agenda/consulta.css') }}" rel="stylesheet">
@endpush

@push('script')
<script src="{{ asset('js/vendor/jquery.mask.js') }}"></script>
@endpush