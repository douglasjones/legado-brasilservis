function fcMascaraFormAfastamento(){
    $('#dt_ini_afastamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#dt_ini_afastamento").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_fim_afastamento').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#dt_fim_afastamento").keypress(function () {
        mascara(this, mdata);
    });

}

function fcLimparFormAfastamento(){
    $("#motivo_afastamento_pk").val("");
    $("#dt_ini_afastamento").val("");
    $("#dt_fim_afastamento").val("");
    $("#colaborador_cobertura_afastamento_pk").val("");
    $("#ds_obs_afastamento").val("");

}

function fcCarregarCoberturaAfastamento(colaborador_pk) {
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
        carregarComboAjax($("#colaborador_cobertura_afastamento_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
}

$(document).ready(function () {
    fcMascaraFormAfastamento();
});