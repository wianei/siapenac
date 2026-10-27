$(document).ready(function(){

    var interno = [
    ]


    $( document ).on( "keyup", "#interno", function() {

        $('#filter-records').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records').css('display','none');
            return;
        }

        var output = '<div class="row">';
        var count = 1;
        var tipo = $("#tipo").val();
        var consulta = 'restrAtivInat';

        if(tipo == "certidaocumprimento"){
            consulta = 'restrAtivInat';
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: consulta},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário.: '+val.id+' - Regime: '+val.regime+' - Localização: '+val.sigla+'</h6>';
                    output += '</div>';
                    count++;
                    interno = objt;
                });
                output += '</div>';
                $('#filter-records').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearch", function() {

        $('#tableInternos, .titleInterno').css('display','block');

        $('#filter-records').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //http://siapen2.sejuc.rn.gov.br/storage/app/arquivos/internos/fotos/2511997/2511997.jpg

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(interno, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoId').val() != v.id){
                if (v.id == idInterno) {
                    $("#retornoInterno").append('<tr id="tr'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" class="imagemSrc cursorPointer" src="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ v.id+'"/>'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');


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


                    if($("#tipo").val() == 'certidaocumprimento' || $("#tipo").val() == 'certidaonaocumprimento'){
                        document.getElementById('interno').disabled = true;
                    }

                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#interno").val("");

    });

    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });



    $( document ).on( "change", "#obitoSispern", function() {

        var obitoSispern = $(this).val();

        if(obitoSispern == "S")
        {
            $("#horaDiv").removeClass('displayNone');
            $("#btnObitoeOcorrencia").removeClass('displayNone');
            $("#btnSomenteObito").html("<i class='fas fa-save'></i> Registrar Somente o Óbito");

            $( function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../ocorrencia/get-fechamento-livro-ajax/",
                    data: {},
                    beforeSend: function() {

                    },
                    success: function (dados) {
                        $('#data').datepicker('destroy');

                        var diasLiberados = [dados.inicioPlantao,dados.terminoPlantao];

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
        }
        else if(obitoSispern == "N")
        {
            $("#horaDiv").addClass('displayNone');
            $("#btnObitoeOcorrencia").addClass('displayNone');
            $("#btnSomenteObito").html('<i class="fas fa-save"></i> Registrar Óbito');

            $( function() {
                $('#data').datepicker('destroy');
                $("#data").datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    autoclose: true,
                    maxDate: 0,
                    orientation: "bottom auto"
                });

            });
        }

    });

    $( document ).on( "click", "#btnObitoeOcorrencia", function() {

       var actionForm = $("#formSaveObito").attr("action");

        $("#formSaveObito").attr("action", actionForm+'/1');

        $('#btnSomenteObito').attr('disabled', true);
        $('#btnObitoeOcorrencia').attr('disabled', true);

    });

    $( document ).on("submit", "#obitoForm", function() {

        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var obitoSispern = $('#obitoSispern').val();
        if(obitoSispern == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe se o óbito ocorreu no Sistema Penitenciário ou não.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var hora = $('#hora').val();
        if(hora.length != 5 && obitoSispern == "S"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
        $('#btnSomenteObito').attr('disabled', true);
        $('#btnObitoeOcorrencia').attr('disabled', true);
    });


    $('#hora').mask('00:00', {reverse: true});
    $('#data').mask('00/00/0000');
    
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

                var diasLiberados = [dados.inicioPlantao,dados.terminoPlantao];

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
