function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_tipo_ocorrencia:{
                required:true,
                minlength:3
            },
            ic_fechar_ocorrencia_auto:{
                required:true
            }

        },
        messages:{
            ds_tipo_ocorrencia:{
                required:"Por favor, informe Tipo ocorrência",
                minlength:"Tipo ocorrência deve ter pelo menos 3 caracteres"
            },
            ic_fechar_ocorrencia_auto:{
                required:"Por favor, informe  Fechar ocorrência"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var t_ds_tipo_ocorrencia = $("#ds_tipo_ocorrencia").val();
    var t_ic_fechar_ocorrencia_auto = $("#ic_fechar_ocorrencia_auto").val();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_tipo_ocorrencia": (t_ds_tipo_ocorrencia),
        "ic_fechar_ocorrencia_auto": (t_ic_fechar_ocorrencia_auto)        
    };    

    var arrEnviar = carregarController("tipo_ocorrencia", "salvar", objParametros);           
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost('tipo_ocorrencia','receptivo' ,objParametros);
    }
    else
    {
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro'); 
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('tipo_ocorrencia','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("tipo_ocorrencia", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_tipo_ocorrencia").val(arrCarregar.data[0]['ds_tipo_ocorrencia']);
            $("#ic_fechar_ocorrencia_auto").val(arrCarregar.data[0]['ic_fechar_ocorrencia_auto']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

$(document).ready(function()
    {
        var arrCarregar = permissao("tipo_ocorrencia", "ins");        

        if (arrCarregar.status != true){            
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
            return false;
        }
        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();

        $(document).on('click', '#cmdCancelar', fcCancelar);
    }
);
