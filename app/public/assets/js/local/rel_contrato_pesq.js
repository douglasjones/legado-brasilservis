var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    var ds_empresa = $("#empresa_pk option:selected").text();
    var ds_lead_clientes = $("#leads_clientes_pk option:selected").text();
    var ds_lead = $("#leads_pk option:selected").text();
    var ds_usuario_cadastro = $("#usuario_cadastro_pk option:selected").text();
    var ds_status = $("#ic_status option:selected").text();
    var ds_contrato = $("#tp_contrato option:selected").text();


    objParametros = {
        empresa_pk:$("#empresa_pk").val(),
        leads_clientes_pk:$("#leads_clientes_pk").val(),
        leads_pk:$("#leads_pk").val(),
        usuario_cadastro_pk:$("#usuario_cadastro_pk").val(),
        dt_ini_cadastro:$("#dt_ini_cadastro").val(),
        dt_fim_cadastro:$("#dt_fim_cadastro").val(),
        dt_ini_contrato:$("#dt_ini_contrato").val(),
        dt_fim_contrato:$("#dt_fim_contrato").val(),
        ds_cpf_cnpj:$("#ds_cpf_cnpj").val(),
        tp_contrato:$("#tp_contrato").val(),
        ic_status:$("#ic_status").val(),
        ds_empresa:ds_empresa,
        ds_lead:ds_lead,
        ds_lead_clientes:ds_lead_clientes,
        ds_status:ds_status,
        ds_contrato:ds_contrato,
        ds_usuario_cadastro:ds_usuario_cadastro
    }
    
    sendPost('relatorio', 'receptivoContrato',objParametros);
}

function fcCarregarEmpresa(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#empresa_pk"), arrCarregar, " ", "pk", "ds_conta");
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
        "ic_cliente": $("#ic_status").val()
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function carregarComboUsuarioCadastro(){
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("usuario", "listarTodos", objParametros);   
   
    carregarComboAjax($("#usuario_cadastro_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','comercial' ,objParametros);
}

$(document).ready(function () {
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);

    fcCarregarLeads();
    fcCarregarClientes();
    carregarComboUsuarioCadastro();
    fcCarregarEmpresa();



    $('#dt_ini_cadastro').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_ini_cadastro").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_fim_cadastro').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_fim_cadastro").keypress(function(){
        mascara(this,mdata);
    });



    $('#dt_ini_contrato').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_ini_contrato").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_fim_contrato').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_fim_contrato").keypress(function(){
        mascara(this,mdata);
    });
    
    $("#ds_cpf_cnpj").keypress(function(){
        chama_mascara(this);
    });

    $("#leads_clientes_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });

    $(".chzn-select").chosen({ allow_single_deselect: true });

});
