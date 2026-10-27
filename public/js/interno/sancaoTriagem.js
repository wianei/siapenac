$(document).ready(function (){

    $('#dataTriagem').mask('00/00/0000');
    $('#horaTriagem').mask('00:00', {reverse: true});
    $('#dataSancao').mask('00/00/0000');
    $('#horaSancao').mask('00:00', {reverse: true});
    
    /**
     * busca pavilhao, ala e cela
     */
    $( document ).on( "change", ".selectUnidadet", function() {

        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "unidade_prisionalt":
                $('#pavilhaot, #alat, #celat').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhaot';

                break;
            case "pavilhaot":
                $('#alat, #celat').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'alat';
                break;

            case "alat":
                $('#celat').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'celat';
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
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);


                $.each(objt,function(key, value)
                {
                    $.each(value,function(k, v)
                    {
                        $('#'+selectOp).append('<option value=' + v + '>' + k + '</option>');
                    });

                });

            }
        });

    });


    $(document).on("change","#pavilhaoFiltro", function (e) {

        $(".retornoInternos tr").removeClass('displayNone');

        var pavilhao = $(this).val();

        if(pavilhao == "all")
        {
            $(".retornoInternos tr").removeClass('displayNone');

        }else {

            $(".retornoInternos tr").map(function () {

                if (!$(this).hasClass(pavilhao)) {
                    $(this).addClass('displayNone');
                }

            });

        }

    });

    /**
     * ao clicar no checkbox, muda a cor da tr
     */
    $( document ).on( "click", ".checkbox", function() {
        $(this).parent().parent().toggleClass('background_bddaff');
    });

    
    $( document ).on( "submit", "#formSancao", function() {
        $("#flashRetornoSancao").html('');
        var control = 0;

        var checkMarcados = $('.checkBoxSancao:checked').length;
        if(checkMarcados == 0)
        {
            $("#flashRetornoSancao").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var dataSancao = $('#dataSancao').val();
        if(dataSancao.length != 10){
            $("#flashRetornoSancao").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var horaSancao = $('#horaSancao').val();
        if(horaSancao.length != 5){
            $("#flashRetornoSancao").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        if(control == 0){
            $('#btnRetirarSancao').attr('disabled', true);
        } else{
            return false;
        }
    });

    
    $( document ).on( "submit", "#formTriagem", function() {
        $("#flashRetornoTriagem").html('');
        var control = 0;
        

        var checkMarcados = $('.checkBoxTriagem:checked').length;
        if(checkMarcados == 0)
        {
            $("#flashRetornoTriagem").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var dataTriagem = $('#dataTriagem').val();
        if(dataTriagem.length != 10){
            $("#flashRetornoTriagem").append('<div class="flash-message"><p class="alert alert-danger">Insira a data.</p></div>');
            control = 1;
        }
        var horaTriagem = $('#horaTriagem').val();
        if(horaTriagem.length != 5){
            $("#flashRetornoTriagem").append('<div class="flash-message"><p class="alert alert-danger">Insira o horário no formato 00:00, com os 0 à esquerda, quando necessário.</p></div>');
            control = 1;
        }
        
        if(control == 0){
            $('#btnRetirarTriagem').attr('disabled', true);
        } else{
            return false;
        }
    });

});
