$(document).ready(function(){

    /**
     * busca agente
     * @type {*[]}
     */
    var agente = [];


    $('#agente').keyup(function(){


        $('#divRetornoAgentes').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#divRetornoAgentes').html('').css('display','none');
            return;
        }


        var regex = new RegExp(searchField, "i");
        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../facade/busca-servidor-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchAgente" data-name="'+val.nome+'" data-content="'+val.matricula+'">';
                    output += '<h6>' + val.nome + ' - Matrícula. '+val.matricula+ '</h6>';
                    output += '</div>';
                    count++;

                    agente = objt;

                });
                output += '</div>';
                $('#divRetornoAgentes').html(output);

            }
        });

    });


    $( document ).on( "click", ".resultSearchAgente", function() {


        //console.log(pavilhoesUnidade)

        $('#tableAgentes, .titleAgente').css('display','block');

        $('#divRetornoAgentes').html('').css('display','none');

        var nomeAgentes = $(this).data('name');
        var matriculaAgentes = $(this).data('content');

        $.each(agente, function(i, v) {

            if (v.nome == nomeAgentes) {

                $("#retornoAgente").append('<tr class="">'+
                    '<td scope="row">'+ v.matricula+'</td>'+
                    '<td>'+ v.nome+'</td>'+
                    '<td>'+ v.lotacao+'</td>'+
                    '<td scope="row"><select id="status" name="status[]" class="form-control parentesco" required>'+
                    '<option value="">Selecione</option>'+
                    '<option value="Em exercício">Em exercício</option>'+
                    '<option value="Plantão Extra 12h">Plantão Extra 12h</option>'+
                    '<option value="Plantão Extra 24h">Plantão Extra 24h</option>'+
                    '<option value="Atestado Médico">Atestado Médico</option>'+
                    '<option value="Falta">Falta</option>'+
                    '<option value="Licença">Licença</option>'+
                    '<option value="Férias">Férias</option>'+
                    '<option value="Concessão">Concessão</option>'+
                    '<option value="Afastamento">Afastamento</option>'+
                    '<option value="Ausente por Outro Motivo">Ausente por Outro Motivo</option>'+
                    '</select></td>'+
                    '<td scope="row" id="td'+v.matricula+'"><select id="lotacaoUnidade'+v.matricula+'" name="lotacaoUnidade[]" class="form-control parentesco lotacaoUnidade" required>'+
                    '<option value="">Selecione</option>'+
                    '<option value="Administrativo">Administrativo</option>'+
                    '<option value="Unidade">Unidade</option>'+
                    '</select></td>'+
                    '<td scope="row"><select id="escala" name="escala[]" class="form-control parentesco" required>'+
                    '<option value="">Selecione</option>'+
                    '<option value="Plantão">Plantão</option>'+
                    '<option value="Expediente">Expediente</option>'+
                    '</select></td>'+
                    '<td scope="row"><select id="funcao" name="funcao[]" class="form-control parentesco" required>'+
                    '<option value="">Selecione</option>'+
                    '<option value="Padrão">Padrão</option>'+
                    '<option value="Chefe do Administrativo">Chefe do Administrativo</option>'+
                    '<option value="Chefe do Cartório">Chefe do Cartório</option>'+
                    '<option value="Chefe de Arquivos e Prontuários">Chefe de Arquivos e Prontuários</option>'+
                    '<option value="Chefe de Expediente">Chefe de Expediente</option>'+
                    '<option value="Chefe de Visita">Chefe de Visita</option>'+
                    '<option value="Chefe de Assistência aos Internos">Chefe de Assistência aos Internos</option>'+
                    '<option value="Chefe de Equipe">Chefe de Equipe</option>'+
                    '<option value="Chefe de Disciplina">Chefe de Disciplina</option>'+
                    '<option value="Chefe de Segurança">Chefe de Segurança</option>'+
                    '<option value="Chefe de Plantão">Chefe de Plantão</option>'+
                    '<option value="Chefe da Armaria">Chefe da Armaria</option>'+
                    '<option value="Chefe de Manutenção">Chefe de Manutenção</option>'+
                    '<option value="Despachante">Despachante</option>'+
                    '<option value="Monitoramento de Câmeras">Monitoramento de Câmeras</option>'+
                    '</select></td>'+
                    '<td class="btn-acoes color_01579b">'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover da Lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                    '<input type="hidden" name="agentesHidden[]" id="agentesHidden" value="'+ v.matricula+'"/>'+
                    '</td>'+
                    '</tr>');


            }


        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var valor = 'pv'+$("#lotacaoAgente").val();

        $.ajax({
            type: "POST",
            url: "/../unidades/get-dados",
            data: {dado: valor},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);


                $.each(objt,function(key, value)
                {

                    $.each(value,function(chave, valor)
                    {
                        $('#lotacaoUnidade'+matriculaAgentes).append('<option value=' + valor + '>' + chave + '</option>');

                    });

                });

            }
        });


        $(this).css("display","none")

        $(".titleAgente, #tableAgentes").removeClass('displayNone');

        $("#agente").val("");

    });



    $( document ).on( "click", ".removerAgentes", function() {
        $(this).parent('td').parent('tr').remove();
        if($('.parentesco').length == 0){
            $('#tableAgentes, .titleAgente').css("display","none");
        }
    });

    $( document ).on( "click", ".removerAgentesCadastrados", function() {

        $(this).parent('td').parent('tr').remove();
        if($('input[name="agentesHidden[]"]').length == 0){
            $('#tableAgentes, .titleAgente').css("display","none");
        }
        var idAgente = $(this).data("content");
        $("#tableAgentes").append('<input type="hidden" name="agentesRemover[]" id="agentesRemover" value="'+ idAgente +'">')
    });



    // coloca a foto do servidor no modal
    $( document ).on( "click", ".imagemSrc", function() {
        $('#imagepreview').attr('src', $(this).attr('src'));
        $('#imagemodal').modal('show');
    });


    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../ocorrencia/get-fechamento-livro-ajax",
            data: {},
            beforeSend: function() {

            },
            success: function (dados) {

                var diasLiberados = [dados.inicioPlantao];

                function datasLiberadas(data) {
                    var sdate = $.datepicker.formatDate( 'dd/mm/yy', data)
                    if($.inArray(sdate, diasLiberados) != -1) {
                        return [true];
                    }
                    return [false];
                }

                $("#data").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    beforeShowDay: datasLiberadas,
                    autoclose: true,
                    orientation: "bottom auto"
                });


            }
        });


    });


});