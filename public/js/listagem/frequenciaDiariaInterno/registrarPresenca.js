$(document).ready(function(){

    $.jMaskGlobals.watchDataMask = true;

    $( document ).on( "change", "#lista", function() {

        $("#retornoInternos").html('');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var idListaPost = $(this).val();

        $.ajax({
            type: "POST",
            url: "/../listagem/get-internos-lista",
            data: {idLista: idListaPost},
            beforeSend: function() {
                $("#retornoInternos").append('<tr class="" id="">'+
                    '<td scope="row"><strong>Aguarde, carregando Informações... </strong><i class="mdi mdi-spin mdi-loading" style="font-size: 28px !important;"></i></td>'+
                    '</tr>');

            },
            success: function (dados) {

                $("#retornoInternos").html('');

                var nomeLista = $("#lista option:selected").html();
                $("#spanInternos").removeClass('displayNone');
                $("#txtTitulo").html('Internos da lista: '+ nomeLista);


                var objt = (dados);

                $.each(objt, function(i, v) {

                    $("#retornoInternos").append('<tr class="" id="'+ v.id+'">'+
                        '<td scope="row"><input type="text" value="" data-mask="SSS" name="tipoPresenca[]" class="form-control text-uppercase valorPresenca"><input type="hidden" class="hiddenIdStatus" name="idStatus[]" data-num="'+ v.id+'" value="'+ v.id+'"></td>'+
                        '<td scope="row">'+ v.id+'</td>'+
                        '<td>'+ v.nome+'</td>'+
                        '<td>'+ v.localizacao+'</td>'+
                        '<td class="btn-acoes color_01579b">'+
                        '<a href="/interno/show/'+ v.id+'" target="_blank" data-toggle="tooltip" data-placement="top" title="Detalhes"><i class="mdi mdi-information-outline"></i></a>'+
                        '</td>'+
                        '</td>'+
                        '</tr>');

                });

            }


        });

    });

    $( document ).on( "click", "#btnPTodos", function() {

        $(".valorPresenca").each(function() {
            var element = $(this);
            if (element.val() == "") {
                $(this).val('P');
                var valorIrmao = $(this).next().data('num');
                $(this).next().val('P,'+valorIrmao);

                if(element.val() == 'P')
                {
                    $(this).css('background-color','#80e3ad')
                }
            }
        });
    });


    $( document ).on( "keyup", ".valorPresenca", function() {
            this.value = this.value.toUpperCase();
    });

    $( document ).on( "blur", ".valorPresenca", function() {

        $(".valorPresenca").each(function() {
            var element = $(this);
            var valorElemento = $(this).val();
            var valorIrmao = $(this).next().data('num');
                if(element.val() == 'P')
                {
                    $(this).next().val('P,'+valorIrmao);
                    $(this).css('background-color','#80e3ad');
                }else{
                    $(this).next().val(valorElemento+','+valorIrmao);
                    $(this).css('background-color','#fffb77')
                }
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


    $( document ).on("submit", "#formRegistrarPresenca", function() {

        var control = 0;

        $(".valorPresenca").each(function() {
            var element = $(this);
            if(element.val() == '')
            {
                $(this).css('background-color','red').focus();
                control = 1;
                return false;
            }
        });

        var lista = $('#lista').val();
        if(lista == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a lista.</p></div>');
            control = 1;
        }

        var data = $('#data').val();
        if(data == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a data.</p></div>');
            control = 1;
        }

        if(control == 1) {
            return false;
        }


    });

});