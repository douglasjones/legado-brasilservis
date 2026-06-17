function fcListarContas(){
    var pk = $('#pk').val();
    var objParametros = {
        "pk": pk
    };        
    
    var arrCarregar = carregarController("conta", "listarPk", objParametros);

    if (arrCarregar.status == true){
        var vhtml = "";
        var contador = 0;
        for (i = 0; i < arrCarregar.data.length; i++) {
            contador ++;           
            vhtml += "<input type='checkbox' id='conta"+i+"' name='conta"+i+"' value="+arrCarregar.data[i].pk+"> - "+arrCarregar.data[i].ds_razao_social+"<br>";
        }  
        $("#qtde_contas").val(contador)  
        $("#listar_contas").html(vhtml)          
    }   
}


function fcEnviar(){
    try {
        var v_dt_faturamento_ini = $("#dt_faturamento_ini").val();
        var v_dt_faturamento_fim = $("#dt_faturamento_fim").val();

        var v_ic_contrato_fixo = "";
        if($("#ic_contrato_fixo").is(":checked")==true ){
            v_ic_contrato_fixo = 1;
        } 
        
        var v_ic_contrato_aditivo = "";
        if($("#ic_contrato_aditivo").is(":checked")==true ){
            v_ic_contrato_aditivo = 1;
        } 

        var v_ic_contrato_servico_extra = "";
        if($("#ic_contrato_servico_extra").is(":checked")==true ){
            v_ic_contrato_servico_extra = 1;
        } 

        var v_ic_gerar_boleto = "";
        if($("#ic_gerar_boleto").is(":checked")==true ){
            v_ic_gerar_boleto= 1;
        } 

        var v_ic_gerar_nota_fiscal = "";
        if($("#ic_gerar_nota_fiscal").is(":checked")==true ){
            v_ic_gerar_nota_fiscal = 1;
        } 

        var v_ic_gerar_nota_fatura = "";
        if($("#ic_gerar_nota_fatura").is(":checked")==true ){
            v_ic_gerar_nota_fatura = 1;
        } 

        var v_obs = $("#obs").val();
        var v_ic_status = $("#ic_status").val();

        var v_faturamento_pk = ""; 
        if($("#faturamento_pk").val()!=""){
            v_faturamento_pk = $("#faturamento_pk").val();
        }

        var arrConta = [];

        for (i = 0; i < $("#qtde_contas").val(); i++) { 
            var v_contas_pk = ""
            if($("#conta"+i).is(":checked") == true){  
                v_contas_pk = $("#conta"+i).val();
            }    
            arrConta[i] = [v_contas_pk];
        }

        var objParametros = {
            "pk": v_faturamento_pk,
            "dt_faturamento_ini": v_dt_faturamento_ini,
            "dt_faturamento_fim": v_dt_faturamento_fim,
            "ic_contrato_fixo": v_ic_contrato_fixo,
            "ic_contrato_aditivo": v_ic_contrato_aditivo,
            "ic_contrato_servico_extra": v_ic_contrato_servico_extra,
            "ic_gerar_boleto": v_ic_gerar_boleto,
            "ic_gerar_nota_fiscal": v_ic_gerar_nota_fiscal,
            "ic_gerar_nota_fatura": v_ic_gerar_nota_fatura,
            "obs": v_obs,
            "arrConta": arrConta,
            "ic_status": v_ic_status        
        };    

        var arrEnviar = carregarController("faturamento", "salvar", objParametros);           
        
        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true,arrEnviar.message);
            var objParametros = {
                "faturamento_pk": arrEnviar.data,
                "acao": 1
            };
            sendPost('faturamento', 'faturamentoItens' ,objParametros);
        }else{
            utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
    
}

function fcCancelar(){
    var objParametros = {};
    sendPost('faturamento','receptivo' ,objParametros);
}

function fcPermissãoNotaFiscal(){
    $('#exibir_nota_fiscal').hide()
    $('#exibir_boleto').hide()
    
    var arrCarregar = carregarController("conta", "configModulo", '');
    if (arrCarregar.data[0]['ic_nf_gerar'] == 1){
        $('#exibir_nota_fiscal').show()
    }
    if (arrCarregar.data[0]['ic_boleto'] == 1){
        $('#exibir_boleto').show()
    }
}

$(document).ready(function(){
    fcListarContas();
    $('#exibir_nota_fiscal').hide()
    $('#exibir_boleto').hide()

    fcPermissãoNotaFiscal();

    $('#dt_faturamento_ini').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_apontamento_ini").keypress(function(){
        mascara(this,mdata);      
    });
    
    //Datas
    $('#dt_faturamento_fim').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_faturamento_fim").keypress(function(){
        mascara(this,mdata);      
    });  

    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdCancelar2', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcEnviar);
    $(document).on('click', '#cmdEnviar2', fcEnviar);
})