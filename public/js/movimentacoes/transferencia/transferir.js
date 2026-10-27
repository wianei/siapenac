$(document).ready(function(){

    /**
     * Marca todos os checkboxes da lista de internos com transferência pendente
     */
    $( document ).on( "click", "#btnCheckCancelarTransfEnv", function() {
        $("#cancelarTransfInternosEnv .checkbox").prop('checked',$(this).is(":checked"));
        if($(this).is(":checked")){
            $('#retornoInternosCancelarTransfEnv tr').addClass('background_bddaff');
        }
        else{
            if($('#retornoInternosCancelarTransfEnv tr').hasClass('background_bddaff')){
                $('#retornoInternosCancelarTransfEnv tr').removeClass('background_bddaff');
            }
        }
    });

    $( document ).on("submit", "#cancelarTransfEnvForm", function() {
        $("#flashCancelarTransfEnv").html('');
        var control = 0;
        if($('input[name="internosCancelarTransfEnvCheck[]"]:checked').length == 0){
            $("#flashCancelarTransfEnv").append('<div class="flash-message"><p class="alert alert-danger">Escolha pelo menos um interno.</p></div>');
            control = 1;
        }

        var motivo = $('#obs').val();
        if(motivo == ""){
            $("#flashCancelarTransfEnv").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo do cancelamento.</p></div>');
            control = 1;
        }

        if(control == 1){
            return false;
        }
        $('#btnAddTodosInternosC').attr('disabled', true);
        $('#btnSubmitForm').attr('disabled', true);
    });

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });

    /**
     * Ao selecionar a cela adiciona os internos na lista
     */
    $( document ).on( "change", "#cela", function() {

        $("#spanInternosCela").css('display','block');
        $("#retornoInternosCela").html('');

        var cela = $("#cela option:selected").text();
        $("#txtCela").html('Internos da  '+cela);


        var celaPost = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../unidades/listaInternosCela-ajax",
            data: {cela: celaPost, restrito: true},
            beforeSend: function() {
                $("#retornoInternosCela").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');
            },
            success: function (dados) {

                $("#retornoInternosCela").html('');
                var objt = (dados);

                console.log(objt);

                $.each(objt, function(i, v) {
                    $("#retornoInternosCela").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row"><input type="checkbox" name="internosCelaCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ v.foto +'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.faccao+'</td>'+
                        '<td>'+ v.lideranca+'</td>'+
                        '<td>'+ v.sigla + ' - ' + v.pvNome + ' - ' + v.ala + ' - ' + v.cela + '</td>'+
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
     * adiciona os internos na lista de internos
     */
    $( document ).on( "click", "#btnAddTodosInternos", function() {

        $('input[name="internosCelaCheck[]"]:checked').each(function() {

            var prontuario = this.value;
            //alert(this.value);

            $.each(celaInternos, function(key, val){

                if ((val.id == prontuario)) {

                    $("#retornoInterno").append('<tr class="">'+
                        '<td scope="row">'+ val.id+'</td>'+
                        '<td><img style="text-align: center" src="'+ val.foto+'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ val.nome+'</td>'+
                        '<td>'+ val.faccao+'</td>'+
                        '<td>'+ val.lideranca+'</td>'+
                        '<td>'+ val.sigla + ' - ' + val.pvNome + ' - ' + val.ala + ' - ' + val.cela +'</td>'+
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
     * Marca todos os checkboxes da lista de internos por cela
     */
    $( document ).on( "click", "#btnCheckTodosInternosCela", function() {
        $("#internosCela .checkbox").prop('checked',$(this).is(":checked"));
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


    $( document ).on( "click", ".removerInterno", function() {
        $(this).parent('td').parent('tr').remove();
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos, .titleInterno').css("display","none");
        }
        $("#spanInternosCela").css('display','none');
        $("#cela option[value='']").attr('selected','selected');
        $("#cela option[value='']").removeAttr('selected');


    });

    $( document ).on( "click", "#btnCarregarMais", function() {

        var skipPost = $("#countSkip").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../movimentacoes/transferencia/internos-pendentes-confirmacao-ajax",
            data: {skip: skipPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = (dados);

                console.log(objt);

                $("#countSkip").val(parseInt(skipPost) + parseInt(5));

                $.each(objt, function(i, v) {
                    $("#retornoInternosCancelarTransfEnv").append('<tr class="" id="'+ v.id+'">'+
                    '<td scope="row"><input type="checkbox" name="internosCancelarTransfEnvCheck[]" class="checkbox" id="check_'+ v.id+'" value="'+ v.id+'"> </td>'+    
                    '<td scope="row">'+v.id+'</td>'+
                        // '<td scope="row"> --FOTO-- </td>'+
                        '<td><img style="text-align: center" src="'+ URL + '/' + v.foto +'" class="" alt="Foto do Perfil"></td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.destino+'</td>'+
                        '<td>'+ v.lotacao+'</td>'+
                        '<td>'+ v.data + '</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</tr>');
                });

            }


        });


    });


    $( document ).on("submit", "#transferirInternoForm", function() {
        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var destinoSelecionado = $('#destino').val();
        if(destinoSelecionado == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione a unidade de destino.</p></div>');
            control = 1;
        }
        var motivo = $('#motivo').val();
        if(motivo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o motivo da transferência.</p></div>');
            control = 1;
        }
        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var hora = $('#hora').val();
        if(hora.length != 5){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        var qtdDocumentos = $('#documentos')[0].files.length;
        if(qtdDocumentos == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Insira pelo menos um documento em anexo.</p></div>');
            control = 1;
        }

        if(control == 1){
            return false;
        }
        $('#btnAddTodosInternosC').attr('disabled', true);
        $('#btnSubmitForm').attr('disabled', true);
    });

    $('#data').mask('00/00/0000');
    $('#hora').mask('00:00', {reverse: true});

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
