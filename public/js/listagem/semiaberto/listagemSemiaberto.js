$(document).ready(function(){

    var celaInternos = [
    ];


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
        orientation: "bottom auto",

    });

    $( document ).on("submit", "#formSemiaberto", function() {

        $("#flashRetorno").html('');
        var control = 0;
        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }

        var mesImpressao = $('#mesImpressao').val();
        if(mesImpressao == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o mês de impressão.</p></div>');
            control = 1;
        }

        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data de início das assinaturas.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }
        $('#submitMudancaCela').attr('disabled', true);
    });


});
