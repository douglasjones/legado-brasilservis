function fcAbrirNovaSelect(){
    var motivo_folga_pk = $("#motivo_folga_pk").val();

    if(motivo_folga_pk == 1){
        $("#dv_motivo_folga_trabalhada").show();
        $("#dv_leads_ft").show();
        $("#dv_vl_ft").show();
    }
}

function fcLimparFormFolga(){

    $("#dv_motivo_folga_trabalhada").hide();
    $("#motivo_folga_pk").val("");
    $("#motivo_ft_pk").val("");
    $("#ds_obs_folga").val("");
    $("#dv_motivo_folga_trabalhada").hide();
    $("#dv_leads_ft").hide();
    $("#dv_vl_ft").hide();
}

function fcCarregarLeadsFT() {
    var objParametros = {
        pk: ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#lead_cobertura_pk"), arrCarregar, " ", "pk", "ds_lead");
}

$(document).ready(function () {
    fcAbrirNovaSelect();
    $("#vl_ft").keypress(function(){
        mascara(this,moeda);
    });

});