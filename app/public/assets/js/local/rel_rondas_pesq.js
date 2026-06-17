var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    var ds_lead_clientes = $("#leads_clientes_pk option:selected").text();
    var ds_lead = $("#leads_pk option:selected").text();
    /*if($("#leads_clientes_pk").val()=="" && $("#leads_pk").val()==""){
        utilsJS.toastNotify(false,'Informe Cliete ou Lead!');
        return false;
    }*/

    objParametros = {
        leads_clientes_pk:$("#leads_clientes_pk").val(),
        leads_pk:$("#leads_pk").val(),
        dt_ini_ronda:$("#dt_ini_ronda").val(),
        dt_fim_ronda:$("#dt_fim_ronda").val(),
        ds_lead:ds_lead,
        ds_lead_clientes:ds_lead_clientes
    }
    
    sendPost('relatorio', 'receptivoRondas',objParametros);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','operacional' ,objParametros);
}

function fcCarregarClientes() {
    //Carrega os grupos
    var objParametros = {
        "ic_tipo_lead": 1,
        "ic_cliente": $("#ic_status").val()
    };

    var arrCarregar = carregarController("lead", "listarTodosClientes", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#leads_clientes_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "ic_tipo_lead": 2,
        "leads_pai_pk": $("#leads_clientes_pk").val(),
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);

    fcCarregarClientes();
    fcCarregarLeads();

    $('#dt_ini_ronda').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_ini_ronda").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_fim_ronda').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_fim_ronda").keypress(function(){
        mascara(this,mdata);
    });
    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });
    


    $(".chzn-select").chosen({ allow_single_deselect: true });

});
