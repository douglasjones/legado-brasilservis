function fcCarregarCoberturaFalta(colaborador_pk) {
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
        carregarComboAjax($("#colaborador_cobertura_falta_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }
}

function fcAbrirSelectsFalta(){
    if($("#colaborador_cobertura_falta_pk") != ""){
        $('#motivo_cobertura_pk').css('display', 'inline')
        $('#dv_vl_ft_falta').css('display', 'inline')
    }
}

function fcLimparFormFalta(){
    $("#ds_obs_falta").val("");
    $("#colaborador_cobertura_falta_pk").val("");
    $("#motivo_falta_pk").val("");
    $("#vl_ft_falta").val("");
    $("#motivo_cobertura_falta_pk").val("");
}

function fcMascarasDataFalta(){
    $('#dt_inicio_atestado').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#dt_inicio_atestado").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_fim_atestado').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#dt_fim_atestado").keypress(function () {
        mascara(this, mdata);
    });
    $(".mask_hour").keypress(function () {
        mascara(this, horamask);
    });


}

$(document).ready(function () {
    $('#motivo_cobertura_pk').css('display', 'none');
    $("#exibir_data_atestado").css('display','none');
    $("#exibir_hr_atestado").css('display','none');
    $('#dv_vl_ft_falta').css('display', 'none');



    $('#motivo_falta_pk').change(function(){
        $("#exibir_data_atestado").css('display','none');
        $("#exibir_hr_atestado").css('display','none');
        if(this.value==3 || this.value==12 || this.value==13 || this.value==14){
            $("#exibir_data_atestado").css('display','inline');
            fcMascarasDataFalta();
        }
        if(this.value==18 || this.value==19){
            $("#exibir_hr_atestado").css('display','inline');
            fcMascarasDataFalta();
        }

        //vai fazer outro if igual o que está em cima, porém passando os valroes 18 e 19 
    });

  

    

});