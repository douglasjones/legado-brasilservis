function fcAbrirModalAddServico(contas_pk){
    $("#num_codigo_servico").val("")
    $("#ds_servico").val("")

    $("#contas_servico_pk").val(contas_pk);
    $("#janela_servicos").modal("show");
}

function fcFecharModalAddServico(){
    $("#janela_servicos").modal("hide");
}

function fcSalvarServico(){
    var v_num_codigo_servico = $("#num_codigo_servico").val();
    var v_ds_servico = $("#ds_servico").val();
    var v_contas_servico_pk = $("#contas_servico_pk").val();
    var v_contas_leads_pk = $("#contas_lead_pk").val();
    var codigo_tributacao = $("#codigo_tributacao").val();


    var objParametros = {
        "contas_pk": v_contas_servico_pk,
        "num_codigo_servico": (v_num_codigo_servico),
        "ds_servico": (v_ds_servico),   
        "contas_leads_pk": (v_contas_leads_pk),
        'codigo_tributacao':codigo_tributacao   
    };    

    var arrEnviar = carregarController("certificados_empresas", "salvarNfeServico", objParametros);           
    
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        fcCarregarListarServicos();
        $("#servico_pk").val(arrEnviar.data)
        fcFecharModalAddServico();
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}