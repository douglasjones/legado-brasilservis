function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_curso:{
                required:true
            },
            ic_status:{
                required:true
            }

        },
        messages:{
            ds_curso:{
                required:"Por favor, informe Curso"
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

}
function fcEnviar(){

    var t_ds_curso = $("#ds_curso").val();
    var t_ic_status = $("#ic_status").val();
    if(t_ds_curso == ""){
        sweetMensagem('warning', 'Por favor, o Exame/Curso');
        return false;
    }if(t_ic_status == ""){
        sweetMensagem('warning', 'Por favor, o Status');
        return false;
    }


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_curso": (t_ds_curso),
        "ic_status": (t_ic_status)        
    };    

    var arrEnviar = carregarController("curso", "salvar", objParametros);           
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true,arrEnviar.message);
        sendPost("curso", "receptivo", objParametros);
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost("curso", "receptivo", objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val(),
        };        
        
        var arrCarregar = carregarController("curso", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_curso").val(arrCarregar.data[0]['ds_curso']);
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
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
    }
);
