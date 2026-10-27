/**
 * 
 * @uthor saulo.ricardo_@hotmail.com
 * @date 2020-10-23
 * @company Departamento Penitenciario Nacional - DEPEN
 * 
 */
$(document).ready(function(){
    
    $('#cpf').mask('00000000000');
   
    var tipo = $("#visualiza_outras_up option:selected" ).text();
        if(tipo == "Sim - ALGUMAS")
        {
            $("#titleUnidadesVisualizacao, #divUnidadesVisualizacao").removeClass('displayNone');
        }
        
        var tipoPermissao = $("#tipo option:selected" ).text();
        
        if(tipoPermissao.indexOf("Externo") > 1)
        {
            $("#divVisualizaOutrasUnidade").addClass('displayNone');
        }else{
            $("#divVisualizaOutrasUnidade").removeClass('displayNone');
        }
        
    $(document).on("change", "#visualiza_outras_up", function () {
        
        var tipo = $("#visualiza_outras_up option:selected" ).text();
        
        if(tipo == "Sim - ALGUMAS")
        {
            $("#titleUnidadesVisualizacao, #divUnidadesVisualizacao").removeClass('displayNone');
        }else{
            $("#titleUnidadesVisualizacao, #divUnidadesVisualizacao").addClass('displayNone');
            $("input:checkbox").prop('checked',$(this).is("false"));
        }
        
    });
    
    $(document).on("change", "#tipo", function () {
        
        var tipo = $("#tipo option:selected" ).text();
        
        if(tipo.indexOf("Externo") > 1)
        {
            $("#divVisualizaOutrasUnidade").addClass('displayNone');
            $("#visualiza_outras_up").val('N');
        }else{
            $("#divVisualizaOutrasUnidade").removeClass('displayNone');
        }
        
    });
    
    $( document ).on("submit", "#formCadastro", function() {
        
        $("#flashRetorno").html('');
        var control = 0;
        
        var tipo = $("#visualiza_outras_up option:selected" ).text();
        var naoCheck = $('input[type="checkbox"]').filter(":checked").length;
        
        if(tipo == "Sim - ALGUMAS" && naoCheck == 0){
            $("#flashRetorno").append('<div class="flash-message"><p class="alert alert-danger">Informe as Unidades Liberadas.</p></div>');
            control = 1;
        }
        
        if(control == 1){
            return false;
        }

    });
    
});
