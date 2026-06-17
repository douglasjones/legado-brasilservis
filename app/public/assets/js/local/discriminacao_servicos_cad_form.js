/*function fcValidarForm(){
    $("#form").validate({
        rules :{
            ds_discriminacao_servico:{
                required:true
            },
            ic_status:{
                required:true
            }

        },
        messages:{
            ds_discriminacao_servico:{
                required:"Por favor, informe Discriminação do Serviço"
            },
            ic_status:{
                required:"Por favor, informe Status"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}*/

function fcEnviar(){

    var t_ds_discriminacao_servico = $("#ds_discriminacao_servico").val();
    var t_ic_status = $("#ic_status").val();

    if(t_ds_discriminacao_servico == ""){
        sweetMensagem('warning', 'Por favor, o Discriminação Serviço');
        return false;
    }if(t_ic_status == ""){
        sweetMensagem('warning', 'Por favor, o Status');
        return false;
    }

    var objParametros = {
        "pk": $("#pk").val(),
        "ds_discriminacao_servico": (t_ds_discriminacao_servico),
        "ic_status": (t_ic_status)        
    };    

    var arrEnviar = carregarController("discriminacao_servicos", "salvar", objParametros);    
    
    if (arrEnviar.status == true){
        sendPost('discriminacao_servicos', 'receptivo', '');
        utilsJS.toastNotify(true, arrEnviar.message);
    }else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost("discriminacao_servicos", "receptivo", objParametros);
}

function fcCarregar(){
    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val(),
        };        
        
        var arrCarregar = carregarController("discriminacao_servicos", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_discriminacao_servico").val(arrCarregar.data[0]['ds_discriminacao_servico']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);

        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }
}


$(document).ready(function()
    {
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdEnviar', fcEnviar);

        //Atribui a validação do formulário dos campos obrigatórios
        //fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
    }
);
