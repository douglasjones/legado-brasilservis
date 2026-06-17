function fcMascarasCamposFerias(){
    $('#dt_ini_ferias').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $('#dt_fim_ferias').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });


    $("#dt_fim_ferias").keypress(function () {
        mascara(this, mdata);
    });

    $("#dt_ini_ferias").keypress(function () {
        mascara(this, mdata);
    });

}

function fcCarregarCoberturaFerias(colaborador_pk) {
    if(colaborador_pk > 0){
        var objParametros = {
            pk: colaborador_pk
        };
        var arrCarregar = carregarController("colaborador", "RelatorioDadosColaborador", objParametros);

        var qualificacao = arrCarregar.data[0]['ds_qualificacao'];
        qualificacao = qualificacao.replace(/,/g , "");

        var objParametros = {
            ds_produtos_servicos: qualificacao,
            colaborador_pk: colaborador_pk
        };

        var arrCarregar = carregarController("colaborador", "listarColaboradoresQualificacao", objParametros);
        carregarComboAjax($("#colaborador_cobertura_ferias_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
}

function fcLimparFormFerias(){

    $("#dt_ini_ferias").val("");
    $("#dt_fim_ferias").val("");
    $("#colaborador_cobertura_ferias_pk").val("");
    $("#ds_obs_ferias").val("");
}

$(document).ready(function () {
    fcMascarasCamposFerias();

});