var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    var ds_lead_clientes = $("#leads_clientes_pk option:selected").text();
    var ds_lead = $("#leads_pk option:selected").text();
    var ds_usuario_cadastro = $("#usuario_cadastro_pk option:selected").text();
    var ds_status = $("#ic_status option:selected").text();


    objParametros = {
        leads_clientes_pk:$("#leads_clientes_pk").val(),
        leads_pk:$("#leads_pk").val(),
        usuario_cadastro_pk:$("#usuario_cadastro_pk").val(),
        dt_ini:$("#dt_ini").val(),
        dt_fim:$("#dt_fim").val(),
        ic_status:$("#ic_status").val(),
        ds_lead:ds_lead,
        ds_lead_clientes:ds_lead_clientes,
        ds_status:ds_status,
        ds_usuario_cadastro:ds_usuario_cadastro
    }
    
    sendPost('relatorio', 'receptivoProposta',objParametros);
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
    carregarComboUsuarioCadastro()



    $('#dt_ini').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_ini").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_fim').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker(); 

    $("#dt_fim").keypress(function(){
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
