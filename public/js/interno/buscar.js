$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $("#sentenciado").change(function(){
        if($("#sentenciado").val() == 'N'){
            $("#provReg").addClass('displayNone');
        } else{
            $("#provReg").removeClass('displayNone');
        }
    });

    $("#unidade_prisional").change(function(){
        $("#divPavilhao").removeClass('displayNone');
    });

    $("#pavilhao").change(function(){
        $("#divAla").removeClass('displayNone');
    });

    $("#ala").change(function(){
        $("#divCela").removeClass('displayNone');
    });


    $("#btnBuscaAvancada").click(function(){
        $("#divBuscaAvancada").toggleClass('displayNone');
    });


    // foto modal
    $(".imagemSrc").on("click", function() {
        $('#imagepreview').attr('src', $(this).attr('src'));
        $('#imagemodal').modal('show');
        var datacontent = $(this).attr("data-content").split(',');
        var fototipo = datacontent[0];
        var fotolegenda = datacontent[1];
        var fotodatainserida = datacontent[2];
        $('#fototipo').html("Tipo: "+fototipo+"<br/>");
        $('#fotolegenda').html("Descrição: "+fotolegenda+"<br/>");
        $('#fotodatainserida').html("Data da inserção da foto: "+fotodatainserida+"<br/>");
    });

    $("#cadastrarMesmoAssim").on("click", function(){
        $("#cadastrarMesmoAssim").attr('disabled', true);
        $("#form_cadastro").submit();
    });


    /**
     * busca o municipio pelo select do estado
     */
    $( document ).on( "change", ".selectEstados", function() {

        var idSelect = $(this).attr('id');

        switch (idSelect){
            case "endereco_estado":
                var selectCidade = "endereco_cidade";
                break;
        }

        $('#'+selectCidade).html("");

        var ufPost = $("#"+idSelect).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/../municipios/pesquisa-ajax",
            data: {uf: ufPost},
            beforeSend: function() {

            },
            success: function (dados) {

                var objt = jQuery.parseJSON(dados);

                $.each(objt,function(key, value)
                {
                    $('#'+selectCidade).append('<option value=' + value + '>' + value + '</option>');
                });

            }
        });

    });
    // -------------------------- !! ------------------------------------------------------------------

    /**
     * Validacoes e mascaras
     */

    //$('#nome, #mae').mask('Z',{translation: {'Z': {pattern: /[a-zA-Z ]/, recursive: true}}});
    $('#prontuario, #diasTriagem').mask('0#');
    $('#nascimento_de, #nascimento_ate, #ultima_visita, #ultimo_advogado, #ate, #apos').mask('00/00/0000');

});
