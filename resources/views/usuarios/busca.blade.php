@extends('template.tema_geral.theme')

@section('content')
    <div class="titlePage color_365f80 mb-5">
        <h3><i class="fas fa-users-cog"></i> Usuários</h3>
        <hr>
        <p>
            Busca de usuários do {{ config('app.name') }}.
        </p>
    </div>

    <form action="#">

        <div class="cf"></div>
        <div class="form-row">

            <div class="form-group col-sm-9">
                <label for="nome">Nome</label>
                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome">
            </div>

            <div class="form-group col-sm-3">
                <label for="matricula">Matrícula</label>
                <input type="text" name="matricula" class="form-control" id="matricula" placeholder="Matrícula">
            </div>

        </div>

        <div class="form-row">

            <div class="form-group col-sm-4">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control" id="cpf" placeholder="CPF">
            </div>

            <div class="form-group col-sm-4">
                <label for="rg">RG</label>
                <input type="text" name="rg" class="form-control" id="rg" placeholder="RG">
            </div>

            <div class="form-group col-sm-4">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="M">
                        Masculino
                    </option>
                    <option value="F">
                        Feminino
                    </option>

                </select>
            </div>

        </div>

        <div class="form-row">

            <div class="form-group col-sm-4">
                <label for="lotacao">Lotação</label>
                <select id="lotacao" name="lotacao" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="PEA">
                        PEA
                    </option>
                    <option value="CPJC">
                        CPJC
                    </option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="cargo">Cargo</label>
                <select id="cargo" name="cargo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="1">
                        01
                    </option>
                    <option value="2">
                        02
                    </option>

                </select>
            </div>

            <div class="form-group col-sm-4">
                <label for="cargo">Função</label>
                <select id="cargo" name="cargo" class="form-control">
                    <option value="">
                        Selecione
                    </option>
                    <option value="Diretor">
                        Diretor
                    </option>
                    <option value="Vice-Diretor">
                        Vice-Diretor
                    </option>

                </select>
            </div>

        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Usuário</button>

    </form>

    <div class="table-responsive-md">
        <table class="table mt-5">
            <thead class="background_30516f color_FFF">
            <tr class="">
                <th scope="col">Matrícula</th>
                <th scope="col">Foto</th>
                <th scope="col">Nome</th>
                <th scope="col">RG</th>
                <th scope="col">CPF</th>
                <th scope="col">E-Mail</th>
                <th scope="col">Lotação</th>
                <th scope="col">Cargo</th>
                <th scope="col">Função</th>
                <th scope="col">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr class="">
                <td>222.222-2</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face2.jpg" class="" alt="Foto do Perfil"></td>
                <td scope="row">ERINALDA MARIA DE ARAUJO</td>
                <td>00000000</td>
                <td>81277750459</td>
                <td>erinalda@rn.gov.br</td>
                <td>CPJC</td>
                <td>Agente Penitenciário</td>
                <td>Diretora</td>
                <td class="btn-acoes color_01579b">
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                </td>
            </tr>
            <tr class="">
                <td>222.222-2</td>
                <td><img style="text-align: center" src="http://localhost:8000/imagens/usuario/face1.jpg" class="" alt="Foto do Perfil"></td>
                <td scope="row">JOÃO MARIA DE ARAÚJO</td>
                <td>00000001</td>
                <td>81277750458</td>
                <td>joao@rn.gov.br</td>
                <td>PEA</td>
                <td>Agente Penitenciário</td>
                <td></td>
                <td class="btn-acoes color_01579b">
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-pencil-circle-outline"></i></a>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

@endsection

@push('script')
<script src="{{ asset('js/visita/buscar.js') }}"></script>
@endpush