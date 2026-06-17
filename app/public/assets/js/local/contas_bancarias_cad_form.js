function fcValidarForm(){

    $("#form").validate({
        rules :{
           

        },
        messages:{
                     

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}
function fcEnviar(){

    var v_ds_conta_bancaria = $("#ds_conta_bancaria").val();
    var v_ds_agencia = $("#ds_agencia").val();
    var v_ds_conta = $("#ds_conta").val();
    var v_tipo_conta_pk = $("#tipo_conta_pk").val();
    var v_vl_saldo_inicial = $("#vl_saldo_inicial").val();
    var v_ic_status = $("#ic_status").val();
    var v_bancos_pk = $("#bancos_pk").val();
    var v_empresas_pk = $("#empresas_pk").val();
    
    
    if(v_tipo_conta_pk!=4){
        if(v_bancos_pk==""){
            
            $("#alert_banco").fadeTo(2000, 500).slideUp(500, function(){
               $("#alert_banco").slideUp(500);
           });
            return false;
        }
        if(v_ds_agencia==""){
            
            $("#alert_agencia").fadeTo(2000, 500).slideUp(500, function(){
               $("#alert_agencia").slideUp(500);
           });
            return false;
        }
        if(v_ds_conta==""){
            $("#alert_conta").fadeTo(2000, 500).slideUp(500, function(){
               $("#alert_conta").slideUp(500);
           });
            return false;
        }
    }
    
    
    if(v_ds_conta_bancaria==""){
        $("#alert_ds_conta").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_conta").slideUp(500);
        });
        return false;
    }
    
    
    if(v_vl_saldo_inicial==""){
        $("#alert_vl_inicial").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_vl_inicial").slideUp(500);
        });
        return false;
    }
    
    

    var objParametros = {
        "pk": $("#pk").val(),
        "ds_conta_bancaria": (v_ds_conta_bancaria),
        "ds_agencia": (v_ds_agencia),
        "ds_conta": (v_ds_conta),
        "tipo_conta_pk": (v_tipo_conta_pk),
        "vl_saldo_inicial": moeda2float(v_vl_saldo_inicial),
        "ic_status": (v_ic_status),
        "empresas_pk": (v_empresas_pk),
        "bancos_pk": (v_bancos_pk)        
    };    

    var arrEnviar = carregarController("conta_bancaria", "salvar", objParametros);           

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost('contas_bancarias','receptivo' ,objParametros);    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('contas_bancarias','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("conta_bancaria", "listarPk", objParametros);
        
        if (arrCarregar.status == true){        
            $("#ds_conta_bancaria").val(arrCarregar.data[0]['ds_conta_bancaria']);
            $("#ds_agencia").val(arrCarregar.data[0]['ds_agencia']);
            $("#ds_conta").val(arrCarregar.data[0]['ds_conta']);
            $("#tipo_conta_pk").val(arrCarregar.data[0]['tipo_conta_pk']);
            $("#vl_saldo_inicial").val(float2moeda(arrCarregar.data[0]['vl_inicial_conta']));
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#empresas_pk").val(arrCarregar.data[0]['empresas_pk']);
            $(".chzn-select").chosen('destroy');
            fcCarregarBancos();
            $("#bancos_pk").val(arrCarregar.data[0]['bancos_pk']);
            $(".chzn-select").chosen({allow_single_deselect: true});
    
            
            
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

function fcCarregarBancos(){    
    var objParametros = {
        "pk": ""
    };          
    var arrCarregar = carregarController("banco", "listarTodos", objParametros);    
    carregarComboAjax($("#bancos_pk"), arrCarregar, " ", "pk", "ds_banco");        

}
function carregarComboEmpresa(){
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);   
   
    carregarComboAjax($("#empresas_pk"), arrCarregar, "", "pk", "ds_razao_social");
}


$(document).ready(function() {
    //Combo Bancos    
    fcCarregarBancos();
    carregarComboEmpresa();
    
    $(".chzn-select").chosen({allow_single_deselect: true});
    
    //$("#vl_saldo_inicial").keypress(mascaraValor);
    
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdCancelar2', fcCancelar);

    //Atribui a validação do formulário dos campos obrigatórios
    fcValidarForm();
    $("#vl_saldo_inicial").keypress(function(){
       mascara(this,moeda);
    });
    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();
});
