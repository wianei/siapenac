$(document).ready(function(){

    /**
     * busca pavilhao, ala e cela
     */
    $( document ).on( "change", ".selectUnidade", function() {

        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();

        switch (idSelect){
            case "unidade_prisional":
                $('#pavilhao, #ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhao';
                break;

            case "pavilhao":
                $('#ala, #cela').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'ala';
                break;

            case "ala":
                $('#cela').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'cela';
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

    $( document ).on( "change", ".selectUnidadev", function() {

        var idSelect = $(this).attr('id');

        var valorSelect = $(this).val();


        switch (idSelect){
            case "unidade_prisionalv":
                $('#pavilhaov, #alav, #celav').html('<option value="">Selecione</option>');
                var valor = 'pv'+ valorSelect;
                var selectOp = 'pavilhaov';
                break;

            case "pavilhaov":
                $('#alav, #celav').html('<option value="">Selecione</option>');
                var valor = 'al'+ valorSelect;
                var selectOp = 'alav';
                break;

            case "alav":
                $('#celav').html('<option value="">Selecione</option>');
                var valor = 'ce'+ valorSelect;
                var selectOp = 'celav';
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



});