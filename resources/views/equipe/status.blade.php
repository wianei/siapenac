@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-thumbtack"></i> Registrar status</h3>
        <hr>
        <p>
            Altere aqui os status dos agentes na presente data.
        </p>
    </div>

    <h3 class="mt-1">Alterar status dos agentes de serviço hoje:</h3>
    <form action="#">

        <div class="form-row">

            <div class="form-group col-md-9">
                <label for="unidade">Nome do agente penitenciário</label>
                <select id="unidade" name="unidade" class="form-control">
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

            <div class="form-group col-md-3">
                <label for="unidade">Novo Status</label>
                <select id="unidade" name="unidade" class="form-control">
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
            <div class="form-group col-md">
                <label for="arquivos">Arquivos</label>
                <input type="file" name="arquivos" class="form-control" id="arquivos" accept="application/pdf" multiple>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Registrar alteração</button>
    </form>


    <hr>

    <h3>Efetivo do dia: 5</h3>
    <br>
    <h4>Equipe Alfa</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Matrícula</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Pavilhão</th>
                <th scope="col">Contato</th>
                <th scope="col">Status</th>
                <th scope="col">Anexos</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td scope="row">222.222-2</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>ALEXANDRE ALVES DE SOUSA</td>
                <td>2</td>
                <td>84-98888-2222</td>
                <td>Em exercício</td>
                <td></td>
            </tr>
            <tr class="">
                <td scope="row">222.222-3</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>CARLOS TIAGO CÉSAR</td>
                <td>2</td>
                <td>84-98888-2223</td>
                <td>Atestado Médico</td>
            </tr>
            <tr class="">
                <td scope="row">222.222-4</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face2.jpg" class="" alt="Foto do Perfil"></td>
                <td>MARIA CLARA DINIZ</td>
                <td>2</td>
                <td>84-98888-2224</td>
                <td>Trocou com 222.222-6<br/>(trabalhará em 11/09/2018)</td>
            </tr>
            <tr class="">
                <td scope="row">222.222-5</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face2.jpg" class="" alt="Foto do Perfil"></td>
                <td>FABIANA DUARTE</td>
                <td>2</td>
                <td>84-98888-2225</td>
                <td>Falta</td>
            </tr>
            <tr class="">
                <td scope="row">222.222-7</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face2.jpg" class="" alt="Foto do Perfil"></td>
                <td>MARIA PAULA</td>
                <td>2</td>
                <td>84-98888-2227</td>
                <td>Concessão (Casamento)</td>
            </tr>
            <tr class="">
                <td scope="row">222.222-8</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>PEDRO PAULO ARAÚJO</td>
                <td>2</td>
                <td>84-98888-2228</td>
                <td>Em exercício</td>
            </tr>
            </tbody>
        </table>
    </div>

    <h4 class="mt-3">Trocas</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Matrícula</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Pavilhão</th>
                <th scope="col">Contato</th>
                <th scope="col">Troca realizada com</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td scope="row">222.222-6</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face2.jpg" class="" alt="Foto do Perfil"></td>
                <td>JOANA MARIA</td>
                <td>2</td>
                <td>84-98888-2226</td>
                <td>222.222-4<br>que não trabalhará em 11/09/2018</td>
                <td>Em exercício</td>
            </tr>
            </tbody>
        </table>
    </div>

    <h4 class="mt-3">Diárias Operacionais</h4>

    <div class="table-responsive-md mt-3">
        <table class="table mb-3">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Matrícula</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">Pavilhão</th>
                <th scope="col">Contato</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td scope="row">222.221-2</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>JOSÉ CÉSAR</td>
                <td>2</td>
                <td>84-98888-2322</td>
                <td>Em exercício</td>
            </tr>
            <tr class="">
                <td scope="row">222.225-2</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td>MÁRIO GONÇALVES</td>
                <td>2</td>
                <td>84-98888-2422</td>
                <td>Em exercício</td>
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