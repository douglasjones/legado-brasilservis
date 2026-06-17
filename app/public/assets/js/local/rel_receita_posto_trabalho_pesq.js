var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    var ds_lead_clientes = $("#leads_clientes_pk option:selected").text();
    var ds_lead = $("#leads_pk option:selected").text();
    var ds_contratos = $("#contratos_pk_combo option:selected").text();

    objParametros = {
        leads_clientes_pk:$("#leads_clientes_pk").val(),
        leads_pk:$("#leads_pk").val(),
        contratos_pk_combo:$("#contratos_pk_combo").val(),
        ds_lead:ds_lead,
        ds_lead_clientes:ds_lead_clientes,
        ds_contratos:ds_contratos
    }
    
    sendPost('relatorio', 'receptivoReceitaPostoTrabalho',objParametros);
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

function fcComboContratos(leads_pk) {

    var v_leads_pk = "";

    if (leads_pk != '' && typeof leads_pk != 'undefined') {
        v_leads_pk = leads_pk;
    }


    var objParametros = {
        "leads_pk": v_leads_pk
    };

    var arrCarregar = carregarController("contrato", "listarLeadsPk", objParametros);

    carregarComboAjax($("#contratos_pk_combo"), arrCarregar, " ", "pk", "ds_combo_contrato");
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','financeiro' ,objParametros);
}

$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);

    fcCarregarClientes();
    fcCarregarLeads();
    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });
    $("#leads_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcComboContratos($("#leads_pk").val());//COMBO DE CONTRATOS
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });



    $(".chzn-select").chosen({ allow_single_deselect: true });

});
