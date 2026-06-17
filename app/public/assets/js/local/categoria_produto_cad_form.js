function fcValidarForm(){
    $("#form").validate({
        rules :{
            ds_categoria:{
                required:true
            },
            ic_status:{
                required:true
            }

        },
        messages:{
            ds_categoria:{
                required:"Por favor, informe a categoria "
            },
            ic_status:{
                required:"Por favor, informe o status"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_categoria = $("#ds_categoria").val();
    var v_ic_status = $("#ic_status").val();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_categoria": (v_ds_categoria),
        "ic_status": (v_ic_status)        
    };    

    var arrEnviar = carregarController("categoria_produto", "salvar", objParametros);           
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost('categoria_produto','receptivo' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('categoria_produto','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("categoria_produto", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_categoria").val(arrCarregar.data[0]['ds_categoria']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

$(document).ready(function()
    {
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);

        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
    }
);
