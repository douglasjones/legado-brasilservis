function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_beneficio:{
                required:true,
                minlength:3
            },
            ic_status:{
                required:true
            }

        },
        messages:{
            ds_beneficio:{
                required:"Por favor, informe Benefícios",
                minlength:" deve ter pelo menos 3 caracteres"
            },
            ic_status:{
                required:"Por favor, informe "
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){
    var v_ds_beneficio = $("#ds_beneficio").val();
    var v_ic_status = $("#ic_status").val();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_beneficio": (v_ds_beneficio),
        "ic_status": (v_ic_status)        
    };    

    var arrEnviar = carregarController("beneficio", "salvar", objParametros);           
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost("beneficio", "receptivo", objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost("beneficio", "receptivo", objParametros);
}

function fcCarregar(){
    
    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        var arrCarregar = carregarController("beneficio", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_beneficio").val(arrCarregar.data[0]['t_ds_beneficio']);
            $("#ic_status").val(arrCarregar.data[0]['t_ic_status']);

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
        $(document).on('click', '#cmdEnviar', fcEnviar);

        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
    }
);
