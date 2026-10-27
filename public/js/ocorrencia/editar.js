$(document).ready(function(){

    CKEDITOR.replace( 'descricao' );


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    var opcao = $( "#natureza option:selected" ).val();
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "POST",
        url: "/../ocorrencia/get-natureza-objeto",
        data: {tipo: opcao},
        beforeSend: function() {

        },
        success: function (dados) {

            var idTipoObjeto = $('#idTipoObjeto').val();

            $('#tipo_objeto').append('<option value="">Selecione</option>');
            $.each(dados,function(key, value)
            {

                if(idTipoObjeto == value.id){
                    $('#tipo_objeto').append('<option value=' + value.id + ' selected>' + value.nome + '</option>');
                }else{
                    $('#tipo_objeto').append('<option value=' + value.id + '>' + value.nome + '</option>');
                }
                
            });

        }

    });
    
    $( document ).on( "change", "#natureza", function() {

        var opcao = $(this).val();

        $("#natureza").css("border","1px solid green");
        $('#tipo_objeto').html('');

       
        if(opcao == 1 || opcao == 47){
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "/../ocorrencia/get-natureza-objeto",
                data: {tipo: opcao},
                beforeSend: function() {

                },
                success: function (dados) {

                   // var objt = jQuery.parseJSON(dados);

                    $('#tipo_objeto').append('<option value="">Selecione</option>');
                    $.each(dados,function(key, value)
                    {

                        $('#tipo_objeto').append('<option value=' + value.id + '>' + value.nome + '</option>');
                    });
                
                }
                
            });
            
            $("#divTipoObjeto").removeClass('displayNone');
        }else{
            
            $("#divTipoObjeto").addClass('displayNone');
        }

    });

    var countServidor = $('#retornoAgente tr').length;
    if(countServidor == 0)
    {
        $('#tableAgentes, .titleAgente').css('display','none');
    }

    var countInterno = $('#retornoInterno tr').length;
    if(countInterno == 0)
    {
        $('#tableInternos, .titleInterno').css('display','none');
    }

    var countVisita = $('#retornoVisita tr').length;
    if(countVisita == 0)
    {
        $('#tableVisitas, .titleVisita').css('display','none');
    }

    var countAdvogado = $('#retornoAdvogado tr').length;
    if(countAdvogado == 0)
    {
        $('#tableAdvogados, .titleAdvogado').css('display','none');
    }

    var countArquivos = $('#arquivosOcorrencia tr').length;
    if(countArquivos == 0)
    {
        $('#divArquivosOcorrencia').css('display','none');
    }


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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../interno/busca-ajax",
            data: {nome: searchField, tipo: 'restrito'},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearch" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - Prontuário.: '+val.id+ ' - Localização: '+val.sigla+'</h6>';
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

        $('#tableInternos').css('display','block');

        $('#filter-records').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#tr"+idInterno).remove();

        $.each(interno, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenInternoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoInterno").append('<tr id="tr'+ v.id+'">'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" src="'+ v.foto+'" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" data-content="'+ v.id+'" class="removerInterno" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
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

    $( document ).on( "click", ".removerInternoCadastrado", function() {

        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
        var id = $(this).data("content");
        $("#tableInternos").append('<input type="hidden" name="internosRemover[]" id="internosRemover" value="'+ id +'">')
    });


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

                    output += '<div class="w-100 resultSearchAgente" data-name="'+val.nome+'" data-content="'+val.cpf+'">';
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

        $('#tableAgentes, .titleAgente').css('display','block');

        $('#divRetornoAgentes').html('').css('display','none');

        var nomeAgentes = $(this).data('name');
        var cpfAgente = $(this).data('content');


        $.each(agente, function(i, v) {
            if (v.nome == nomeAgentes) {
                $("#retornoAgente").append('<tr class="">'+
                    '<td scope="row">'+ v.matricula+'</td>'+
                    '<td><img id="'+ cpfAgente+'img" src="'+URL+'/imagens/usuario/avatarInterno.png" class="imagemSrc cursorPointer" title="Clique para ampliar a foto" alt="Foto do Servidor"/></td>'+
                    '<td>'+ v.nome+'</td>'+
                    '<td>'+ v.lotacao+'</td>'+
                    '<td class="btn-acoes color_01579b">'+
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover desta Escolta"><i class="mdi mdi-close-circle-outline"></i></a>'+
                    '<input type="hidden" name="agentesHidden[]" id="agentesHidden" value="'+ v.matricula+'"/>'+
                    '</td>'+
                    '</tr>');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/../facade/get-foto-servidor",
                    data: {cpf:cpfAgente},
                    beforeSend: function() {

                    },
                    success: function (dados) {
                        $("#"+cpfAgente+"img").attr('src', dados);
                    }
                });

                return;
            }
        });

        $(this).css("display","none")

        $(".titleAgente, #tableAgentes").removeClass('displayNone');

        $("#agente").val("");

    });

    $( document ).on( "click", ".removerAgentes", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="agentesHidden[]"]').length == 0){
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



    var visitas = [
    ]

    $( document ).on( "keyup", "#visita", function() {

        $('#filter-records-visita').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records-visita').css('display','none');
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
            url: "/../visita/busca-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchVisita" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - CPF: '+val.cpf+ ' - Status: '+val.status+'</h6>';
                    output += '</div>';
                    count++;

                    visitas = objt;

                });
                output += '</div>';
                $('#filter-records-visita').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearchVisita", function() {

        $('#tableVisitas').css('display','block');

        $('#filter-records-visita').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#trVisita"+idInterno).remove();

        $.each(visitas, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenVisitaId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoVisita").append('<tr id="trVisita'+ v.id+'">'+
                        '<td><img id="foto_frame'+v.id+'" style="text-align: center" src="'+ v.foto+'" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.cpf+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerVisita" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="visitas[]" class="hiddenVisitaId" id="visitas" value="'+ v.id+'"/>'+
                        '<a href="/visita/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                
                    $.ajax({
                        type: "POST",
                        url: "/../visita/visita-fotoprincipal-ajax",
                        data: {idInterno: idInterno},
                        beforeSend: function () {

                        },
                        success: function (dados) {

                            var objt = (dados);
                            document.getElementById('foto_frame'+v.id).src = dados;

                        }
                    });

                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#visita").val("");

    });

    $( document ).on( "click", ".removerVisita", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="visitas[]"]').length == 0){
            $('#tableVisitas').css("display","none");
        }
    });

    $( document ).on( "click", ".removerVisitasCadastradas", function() {

        $(this).parent('td').parent('tr').remove();
        if($('input[name="visitas[]"]').length == 0){
            $('#tableVisitas, .titleVisita').css("display","none");
        }
        var idVisita = $(this).data("content");
        $("#tableVisitas").append('<input type="hidden" name="visitasRemover[]" id="visitasRemover" value="'+ idVisita +'">')
    });



    var advogados = [
    ]

    $( document ).on( "keyup", "#advogado", function() {

        $('#filter-records-advogado').css('display','block');

        var searchField = $(this).val();

        // quando nao houver caracteres digitados
        if(searchField === '')  {
            $('#filter-records-advogado').css('display','none');
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
            url: "/../advogado/busca-ajax",
            data: {nome: searchField},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                $.each(objt, function(key, val){

                    output += '<div class="w-100 resultSearchAdvogado" data-name="'+val.nome+'" data-content="'+val.id+'">';
                    output += '<h6>' + val.nome + ' - OAB: '+val.oab_numero+ ' - Status: '+val.status+'</h6>';
                    output += '</div>';
                    count++;

                    advogados = objt;

                });
                output += '</div>';
                $('#filter-records-advogado').html(output);

            }
        });
    });


    $( document ).on( "click", ".resultSearchAdvogado", function() {

        $('#tableAdvogados').css('display','block');

        $('#filter-records-advogado').html('').css('display','none');

        var nomeInterno = $(this).data('name');
        var idInterno = $(this).data('content');

        //verifica se o interno já esta presente na lista de internos e remove da lista da cela
        $("#trAdv"+idInterno).remove();

        $.each(advogados, function(i, v) {

            // verifica se o interno ja esta na lista
            if($('.hiddenAdvogadoId').val() != v.id){
                if (v.nome == nomeInterno) {
                    $("#retornoAdvogado").append('<tr id="trAdv'+ v.id+'">'+
                        '<td><img style="text-align: center" src="'+ v.foto+'" class="" alt=""></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.oab_numero+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-toggle="tooltip" data-placement="top" class="removerAdvogado" title="Remover da lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="advogados[]" class="hiddenAdvogadoId" id="advogados" value="'+ v.id+'"/>'+
                        '<a href="/advogado/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;
                }

            }

        });

        $(this).css("display","none")

        $("#advogado").val("");

    });

    $( document ).on( "click", ".removerAdvogado", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="advogados[]"]').length == 0){
            $('#tableAdvogados').css("display","none");
        }
    });

    $( document ).on( "click", ".removerAdvogadosCadastrados", function() {

        $(this).parent('td').parent('tr').remove();
        if($('input[name="advogados[]"]').length == 0){
            $('#tableAdvogados, .titleAdvogado').css("display","none");
        }
        var idAdv = $(this).data("content");
        $("#tableAdvogados").append('<input type="hidden" name="advogadosRemover[]" id="advogadosRemover" value="'+ idAdv +'">')
    });


    /**
     * Validacoes e mascaras
     */
    $('#hr_ocorrencia').mask('00:00');

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

                $("#dt_ocorrencia").datepicker({
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