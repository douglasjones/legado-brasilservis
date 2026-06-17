function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_produto_servico:{
                required:true,
                minlength:3
            }

        },
        messages:{
            ds_produto_servico:{
                required:"Por favor, informe Produto/Serviço",
                minlength:"Produto/Serviço deve ter pelo menos 3 caracteres"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_produto_servico = $("#ds_produto_servico").val();
    var v_ds_cbo = $("#ds_cbo").val();
    var v_vl_servico = $("#vl_servico").val();
    var v_ic_status = $("#ic_status").val();
    if(v_ds_produto_servico == ""){
        sweetMensagem('warning', 'Por favor, informe Produto/Serviços');
        return false;
    }

    var objParametros = {
        "pk": $("#pk").val(),
        "ds_produto_servico": (v_ds_produto_servico),
        "ds_cbo": (v_ds_cbo),  
        "ic_status":v_ic_status,
        "vl_servico": (v_vl_servico)  
    };    

    var arrEnviar = carregarController("servico", "salvar", objParametros);           
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        var objParametros = {};
        sendPost('servico','receptivo' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {
        "rh":$("#rh").val()
    };
    sendPost('servico','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("servico", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_produto_servico").val(arrCarregar.data[0]['ds_produto_servico']);
            $("#ds_cbo").val(arrCarregar.data[0]['ds_cbo']);
            $("#vl_servico").val(arrCarregar.data[0]['vl_servico']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

$(document).ready(function()
    {
        var arrCarregar = permissao("produto_servico", "ins");        

        if (arrCarregar.status != true){            
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
            return false;
        }
        
        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();

        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdEnviar', fcEnviar);
    
    }
);
