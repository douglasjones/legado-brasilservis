function fcEnviar(){
    if($("#grupos_pk").val()==''){
        sweetMensagem('warning', 'Por favor informe o Grupo!');
        return false;
    }

    if($("#ds_usuario").val()==''){
        sweetMensagem('warning', 'Por favor informe o Usuário!');
        return false;
    }

    if($("#ds_email").val()==''){
        sweetMensagem('warning', 'Por favor informe o Login!');
        return false;
    }


    if($("#grupos_pk option:selected").text()=="Clientes"){
        if($('#leads_pk').val()==""){
            $("#alert_ds_lead").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_ds_lead").slideUp(500);
            });
            $('#leads_pk').focus();
            return false;
        }
    }

    
    var v_contas_pk = $("#contas_pk").val();
    var v_ds_usuario = $("#ds_usuario").val();
    var v_ds_login = $("#ds_email").val();
    var v_ds_senha = "";
    var v_ds_email = $("#ds_email").val();
    var v_ds_cel = $("#ds_cel").val();
    var v_ic_status = $("#ic_status").val();
    var v_grupos_pk = $("#grupos_pk").val();

    if($("#pk").val()==""){
         v_ds_senha = "gepros";
    }
    else{
        if($('#ic_senha').is(":checked")){
           v_ds_senha = "gepros";
        }
        else{
           v_ds_senha = $("#ds_senha").val();
        }
    }

    var objParametros = {
        "pk": $("#pk").val(),
        "contas_pk": (v_contas_pk),
        "ds_usuario": (v_ds_usuario),
        "ds_login": (v_ds_login),
        "ds_senha": (v_ds_senha),
        "ds_email": (v_ds_email),
        "ds_cel": (v_ds_cel),
        "ic_status": (v_ic_status),
        "grupos_pk": (v_grupos_pk),
        "leads_pk":$("#leads_pk").val()         
    };    

    var arrEnviar = carregarController("usuario", "salvar", objParametros);           

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        setTimeout(function(){
            var objParametros = {};
            sendPost('usuario','receptivo' ,objParametros);
        }, 1000);

    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('usuario','receptivo' ,objParametros);
}

function fcCarregar(){

if($("#pk").val() > 0){
    var objParametros = {
        "pk": $("#pk").val()
    };        
    
    var arrCarregar = carregarController("usuario", "listarPk", objParametros);

    if (arrCarregar.status == true){
        
        $("#ds_usuario").val(arrCarregar.data[0]['ds_usuario']);
        //$("#ds_login").val(arrCarregar.data[0]['ds_login']);
        $("#ds_senha").val(arrCarregar.data[0]['ds_senha']);
        $("#ds_email").val(arrCarregar.data[0]['ds_email']);
        $("#ds_cel").val(arrCarregar.data[0]['ds_cel']);
        $("#ic_status").val(arrCarregar.data[0]['ic_status']);
        $("#grupos_pk").val(arrCarregar.data[0]['grupos_pk']);
        
        if(arrCarregar.data[0]['leads_pk']!=null){
            $("#exibir_lead").show();
        }
        fcCarregarLeads();
        $("#leads_pk").val(arrCarregar.data[0]['leads_pk']);
        $("#contas_pk").val(arrCarregar.data[0]['contas_pk']);
        $(".chzn-select").chosen('destroy');
        $(".chzn-select").chosen({allow_single_deselect: true});
                   

    }
    else{
        utilsJS.toastNotify(false, 'Falha ao carregar o registro');
    }
}
}

function fcCarregarGrupos(){
//Carrega os grupos

var objParametros = {
    "pk": ""
};      

var arrCarregar = carregarController("grupo", "listarTodos", objParametros); 

carregarComboAjax($("#grupos_pk"), arrCarregar, " ", "pk", "ds_grupo");

}

function fcCarregarLeads(){    
var objParametros = {
    "pk": ""
};         
var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);    

carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");        
}

function fcCarregarContas(){
var objParametros = {
    "pk": ""
};         
var arrCarregar = carregarController("conta", "listarTodos", objParametros);    

carregarComboAjax($("#contas_pk"), arrCarregar, "", "pk", "ds_conta");        
}

$(document).ready(function()
{
  
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcEnviar);

    //Carregar contas
    fcCarregarContas();

    //Carregar o combo com os grupos.
    fcCarregarGrupos();

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();

    fcCarregarLeads();
    
    $("#grupos_pk").change(function(){
      if($("#grupos_pk option:selected").text()=="Clientes"){
          $("#exibir_lead").show();
          $(".chzn-select").chosen('destroy');
          $(".chzn-select").chosen({allow_single_deselect: true});
      }
      else{
          $("#exibir_lead").hide();
          $("#leads_pk").val("");
      }
    });
    
    $("#ds_cel").keypress(function(){
        mascara(this, mascaraTelefone);
    });
    

}
);
