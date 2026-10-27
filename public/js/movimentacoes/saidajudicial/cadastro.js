$(document).ready(function() {
    $(document).on("change", ".selectUnidadeAlocacao", function () {
        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect) {
            case "unidade_prisionalAlocacao":
                $('#pavilhaoAlocacao, #alaAlocacao, #celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'pv' + valorSelect;
                var selectOp = 'pavilhaoAlocacao';

                break;

            case "pavilhaoAlocacao":
                $('#alaAlocacao, #celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'al' + valorSelect;
                var selectOp = 'alaAlocacao';
                break;

            case "alaAlocacao":
                $('#celaAlocacao').html('<option value="">Selecione</option>');
                var valor = 'ce' + valorSelect;
                var selectOp = 'celaAlocacao';
                break;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/get-dados",
            data: {dado: valor},
            beforeSend: function () {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);

                $.each(objt, function (key, value) {
                    $.each(value, function (k, v) {
                        $('#' + selectOp).append('<option value=' + v + '>' + k + '</option>');
                    });

                });

            }
        });

    });

    var interno = [];



    $(document).on("keyup", "#interno", function () {

        $('#filter-records').css('display', 'block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if (searchField === '') {
            $('#filter-records').css('display', 'none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'saidajudicial'},
            beforeSend: function () {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function (key, val) {

                    output += '<div class="w-100 resultSearch" data-name="' + val.nome + '" data-content="' + val.id + '">';
                    output += '<h6>' + val.nome + ' - Prontuário.: ' + val.id + ' - Regime: ' + val.regime + '</h6>';
                    output += '</div>';
                    count++;
                    interno = objt;
                });
                output += '</div>';
                $('#filter-records').html(output);

            }
        });


    });


    $(document).on("click", ".resultSearch", function () {

        $('#tableInternos').css('display', 'block');

        $('#filter-records').html('').css('display', 'none');

        document.getElementById('interno').disabled = true;

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr" + idInterno).remove();

        $.each(interno, function (i, v) {

            // verifica se o interno ja esta na lista
            if ($('.hiddenInternoId').val() != v.id) {

                if ($('.hiddenInternoId').val() != v.id) {
                    if (v.nome == nomeInterno) {
                        $("#retornoInterno").append('<tr id="tr' + v.id + '">' +
                            '<td id="interno_id" scope="row">' + v.id + '</td>' +
                            '<td><img id="foto_frame'+v.id+'" style="text-align: center" src="" class="" alt="Foto Perfil"></td>' +
                            '<td>' + v.nome + '</td>' +
                            '<td>' + v.regime + '</td>' +
                            '<td>' + v.sigla + '</td>' +
                            '<td class="btn-acoes color_01579b">' +
                            '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover"><i class="mdi mdi-close-circle-outline"></i></a>' +
                            '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="' + v.id + '"/>' +
                            '<a href="/interno/show/' + v.id + '" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>' +
                            '</td>' +
                            '</tr>');
                        var idInterno = v.id;
                        //window.alert(idInterno);



                        $.ajax({
                            type: "POST",
                            url: "/../interno/interno-fotoprincipal-ajax",
                            data: {idInterno: idInterno},
                            beforeSend: function () {

                            },
                            success: function (dados) {

                                var objt = (dados);
                                document.getElementById('foto_frame'+v.id).src = dados;

                            }
                        });
                    }


                }
                return;
            }

        });

        $(this).css("display", "none")

        $("#interno").val("");

    });

    $(document).on("click", ".removerInterno", function () {
        $(this).parent('td').parent('tr').remove();
        document.getElementById('interno').disabled = false;
        if ($('input[name="internos[]"]').length == 0) {
            $('#tableInternos').css("display", "none");
        }
    });

    $(document).on("submit", "#saidaJudicialForm", function () {

        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if (internosSelecionados == 0) {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o interno.</p></div>');
            control = 1;
        }
        var tipo = $('#tipo').val();
        var qtdDocumentos = $('#documentos')[0].files.length;
        if (qtdDocumentos == 0) {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if (data == "") {
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        if (control == 1) {
            return false;
        }

        $('#saijudBtn').attr('disabled', true);
    });

    $('#dias').mask('000');
    $('#data').mask('00/00/0000');
    $('#hora').mask('00:00');

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
            beforeSend: function () {

            },
            success: function (dados) {

                var diasLiberados = [dados.inicioPlantao, dados.terminoPlantao];

                function datasLiberadas(data) {
                    var sdate = $.datepicker.formatDate('dd/mm/yy', data)
                    if ($.inArray(sdate, diasLiberados) != -1) {
                        return [true];
                    }
                    return [false];
                }

                $("#data").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
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