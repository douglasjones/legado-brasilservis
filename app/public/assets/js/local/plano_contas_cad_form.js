function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_tipo_operacao:{
                required:true
            },
            ic_status:{
                required:true
            },
            categorias_financeiras_pk:{
                required:true
            }

        },
        messages:{
            ds_tipo_operacao:{
                required:"Por favor, informe Tipo Operação"
            },
            ic_status:{
                required:"Por favor, informe Status"
            },
            categorias_financeiras_pk:{
                required:"Por favor, informe Categoria"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_tipo_operacao = $("#ds_tipo_operacao").val();
    var v_ic_status = $("#ic_status").val();
    var v_categorias_financeiras_pk = $("#categorias_financeiras_pk").val();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_tipo_operacao": (v_ds_tipo_operacao),
        "ic_status": (v_ic_status),
        "categorias_financeiras_pk": (v_categorias_financeiras_pk)        
    };    

    var arrEnviar = carregarController("plano_contas", "salvar", objParametros);           
      
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost('plano_contas','receptivo' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('plano_contas','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("plano_contas", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_tipo_operacao").val(arrCarregar.data[0]['ds_tipo_operacao']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#categorias_financeiras_pk").val(arrCarregar.data[0]['categorias_financeiras_pk']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}
function fcCarregarCategoriasFinanceiras(){

    var objParametros = {
        "pk": ""
    };          
   
    var arrCarregar = carregarController("categoria_financeira", "listarTodos", objParametros);   
 
    carregarComboAjax($("#categorias_financeiras_pk"), arrCarregar, " ", "pk", "ds_categoria");    
   
}
$(document).ready(function(){
    
    fcCarregarCategoriasFinanceiras()
    
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);

    //Atribui a validação do formulário dos campos obrigatórios
    fcValidarForm();

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();
});
