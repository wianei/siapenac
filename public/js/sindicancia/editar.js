$(document).ready(function(){

    CKEDITOR.replace( 'descricao' );

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });

    var interno = [
    ]

    //$( document ).on("blur", "#interno", function() {
    //    $('#filter-records').css('display','none');
    //});


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




    var celaInternos = [
    ];



    /**
     * Ao selecionar a cela adiciona os internos na lista
     */
    $( document ).on( "change", "#cela", function() {

        $("#spanInternosCela").css('display','block');
        $("#retornoInternosCela").html('');

        var cela = $("#cela option:selected").text();
        $("#txtCela").html('Internos da '+cela);


        var celaPost = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/listaInternosCela-ajax",
            data: {cela: celaPost},
            beforeSend: function() {
                $("#retornoInternosCela").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');
            },
            success: function (dados) {
                $("#retornoInternosCela").html('');
                var objt = (dados);

                $.each(objt, function(i, v) {


                    $("#retornoInternosCela").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="../'+ v.foto+'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla+' - '+ v.pvNome+' - '+ v.ala+' - '+ v.cela+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</td>'+
                        '</tr>');

                });

                // verifica se o interno já esta presente na lista de internos e remove da lista da cela
                $('input[name="internos[]"]').each(function() {
                    var idRemover = $(this).val();
                    $("#"+idRemover).remove();
                });

                celaInternos = objt;

            }


        });


    });


    /**
     * Marca todos os checkboxes da lista de internos por recebimento
     */
    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));
        $('#retornoInternosRecebimento').children().toggleClass('background_bddaff');
        if($('#retornoMensagens').children().hasClass('background_bddaff'))
        {
            $('#retornoMensagens').children().addClass('background_bddaff');
        }

    });

    /**
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternos", function() {
        $(".checkbox").prop('checked',$(this).is(":checked"));

        if($(this).is(":checked")){
            $('#retornoInternosCela').children().addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosCela').children().hasClass('background_bddaff')){
                $('#retornoInternosCela').children().removeClass('background_bddaff');
            }
        }

    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });


    /**
     * adiciona os internos na lista de internos
     */
    $( document ).on( "click", "#btnAddTodosInternos", function() {

        $('input[name="internosCelaCheck[]"]:checked').each(function() {

            var prontuario = this.value;
            //alert(this.value);

            $.each(celaInternos, function(key, val){

                if ((val.id == prontuario)) {

                    $("#retornoInterno").append('<tr class="" id="tr'+ val.id+'">'+
                        '<td scope="row">'+ val.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ val.foto+'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ val.nome+'</td>'+
                        '<td>'+ val.faccao+'</td>'+
                        '<td>'+ val.lideranca+'</td>'+
                        '<td>'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="#" data-content="'+ val.sigla+' - '+ val.pvNome+' - '+ val.ala+' - '+ val.cela+'" data-toggle="tooltip" data-placement="top" class="removerInterno" title="Remover desta Mudança de Cela"><i class="mdi mdi-close-circle-outline"></i></a>'+
                        '<input type="hidden" name="internos[]" class="hiddenInternoId" id="internos" value="'+ val.id+'"/>'+
                        '<a href="/interno/show/'+ val.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                    return;

                }
            });

            $('#tableInternos, .titleInterno').css('display','block');
            $("#spanInternosCela").css('display','none');
            //$("#cela").html('<option value="">Selecione</option>');
            $("#cela option[value='']").removeAttr('selected').attr('selected','selected');

        });

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
                    '<a href="#/" data-toggle="tooltip" data-placement="top" class="removerAgentes" title="Remover da Lista"><i class="mdi mdi-close-circle-outline"></i></a>'+
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

    $( function() {
        $("#data_incidente").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            autoclose: true,
            onSelect: function(selected) {
                $("#data_retorno").datepicker("option", "minDate", selected);
            },
            orientation: "bottom auto"
        });

    });

});
