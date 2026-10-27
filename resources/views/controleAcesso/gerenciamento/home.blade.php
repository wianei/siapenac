@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="mdi mdi-login"></i> Gerenciamento do Controle de Acesso</h3>
        <hr>
        <p>
            Gerenciamento das regras do controle de acesso.
        </p>
    </div>

    <form action="#">

        <div class="form-row">

            <div class="form-group col-sm-3">
                <label for="presidio">Unidade</label>
                <select id="presidio" name="presidio" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="1">
                        PEN 01
                    </option>
                    <option value="2">
                        CAD 02
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="Informe o CPF">
            </div>

            <div class="form-group col-sm-3">
                <label for="oab">OAB</label>
                <input type="text" name="oab" class="form-control" id="oab" placeholder="Se advogado, informe o número da OAB">
            </div>

            <div class="form-group col-sm-3">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Informe o nome">
            </div>

        </div>

        <button type="submit" id="btnSubmitForm" class="btn btn-primary mt-3"><i class="fas fa-search"></i> Buscar Registros</button>

    </form>

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Nome</th>
                <th scope="col">Data Última Entrada</th>
                <th scope="col">Horário</th>
                <th scope="col">Localização</th>
                <th scope="col">Tipo</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td>Fulano da silva beltrano santos</td>
                <td>11/09/2018</td>
                <td>10:31 - 10:48</td>
                <td>PEA</td>
                <td>Visitante externo</td>
                <td class="btn-acoes color_01579b">
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Ver Cadastro"><i class="mdi mdi-information-outline"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Suspender Entrada"><i class="mdi mdi-cancel"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Alerta"><i class="mdi mdi-alert"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Rastreamento"><i class="mdi mdi-map-marker-radius"></i></a>
                </td>
            </tr>
            </tbody>
        </table>

    </div>



@endsection


