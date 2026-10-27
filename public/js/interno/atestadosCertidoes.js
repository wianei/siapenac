$(document).ready(function (){

    $(document).on("change", "#tipo", function () {
        var tipo = $(this).val();
        if(tipo == "acc")
        {
            $("#divConduta").removeClass("displayNone");
            $("#divNumero").addClass("displayNone");
            $("#divVara").addClass("displayNone");
            $("#divProcesso").addClass("displayNone");
            $("#divJustificativa").addClass("displayNone");
        }
        if(tipo == "apc")
        {
            $("#divConduta").addClass("displayNone");
            $("#divNumero").removeClass("displayNone");
            $("#divVara").addClass("displayNone");
            $("#divProcesso").addClass("displayNone");
            $("#divJustificativa").addClass("displayNone");
        }
        if(tipo == "certidaocumprimento"){
            $("#divConduta").addClass("displayNone");
            $("#divNumero").addClass("displayNone");
            $("#divJustificativa").addClass("displayNone");
            $("#divVara").removeClass("displayNone");
            $("#divProcesso").removeClass("displayNone");
        }
        if(tipo == "certidaonaocumprimento"){
            $("#divConduta").addClass("displayNone");
            $("#divNumero").addClass("displayNone");
            $("#divJustificativa").removeClass("displayNone");
            $("#divVara").removeClass("displayNone");
            $("#divProcesso").removeClass("displayNone");
        }
        if(tipo != "")
        {
            document.getElementById('interno').disabled = false;
        } else {
            document.getElementById('interno').disabled = true;
        }
        while($('input[name="internos[]"]').length != 0){
            $('.removerInterno').parent('td').parent('tr').remove();
        }
        if($('input[name="internos[]"]').length == 0){
            $('#tableInternos').css("display","none");
        }
    });

    $( document ).on("submit", "#formAtestadoCertidao", function() {

        $("#flashRetorno").html('');
        var control = 0;

        var tipo = $("#tipo").val();
        if(tipo == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione o tipo de atestado.</p></div>');
            control = 1;
        }

        var internosSelecionados = $('.hiddenInternoId').length;
        if(internosSelecionados == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Selecione pelo menos um interno.</p></div>');
            control = 1;
        }
        var conduta = $('#conduta').val();
        if(conduta == "" && tipo == "acc"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a conduta do interno.</p></div>');
            control = 1;
        }
        var vara = $("#vara").val();
        var processo = $("#processo").val();
        if(tipo == "certidaocumprimento" || tipo == "certidaonaocumprimento"){
            if(vara == ""){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a vara criminal e a comarca.</p></div>');
                control = 1;
            }
            if(processo == ""){
                $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o número do processo.</p></div>');
                control = 1;
            }
        }
        var justificativa = $("#justificativa").val();
        if(tipo == "certidaonaocumprimento" && justificativa == ""){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe a justificativa do não cumprimento do alvará.</p></div>');
            control = 1;

        }

        var numero = $('#numero').val();
        if(numero == "0000/2019" && tipo == "apc"){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe o número do APC.</p></div>');
            control = 1;
        }
        if(control == 1) return false;
    });

});
