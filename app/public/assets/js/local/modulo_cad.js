function fcCarregarTiposModulos() {
    var objParametros = {};
    var arrCarregar = carregarController("modulo", "listarTipoModulo", objParametros);
    carregarComboAjax($("#tipo_modulo_pk"), arrCarregar, " ", "pk", "ds_tipo_modulo");
}

function fcCancelar() {
    var objParametros = {};
    sendPost('modulo', 'receptivo' ,objParametros);
}

function fcValidarForm(){
    try {
        $("#form").validate({
            rules :{
                tipo_modulo_pk:{
                    required:true
                },
                ds_dominio:{
                    required:true
                }
            },
            messages:{
                tipo_modulo_pk:{
                    required:"Por favor, informe o Módulo"    
                },
                ds_dominio:{
                    required:"Por favor, informe o Permissão"    
                }
    
            },
            submitHandler: function(form){
                fcEnviar();	   //Se a validação deu certo, faz o envio do formulario.
                return false;
            }
        });
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
   
    
}


function fcEnviar(){
    try {

        var v_tipo_modulo_pk = $("#tipo_modulo_pk").val();
        var v_ds_modulo = $("#tipo_modulo_pk option:selected").text();
        var v_ds_dominio = $("#ds_dominio").val();
        var v_ds_obs = $("#ds_obs").val();

        var objParametros = {
            "pk": $("#pk").val(),
            "tipo_modulo_pk": (v_tipo_modulo_pk),
            "ds_dominio": (v_ds_dominio),
            "ds_modulo": (v_ds_modulo),
            "ds_obs": (v_ds_obs)
        };
        var arrEnviar = carregarController("modulo", "salvar", objParametros);
        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true, arrEnviar.message);
            var objParametros = {};
            sendPost('modulo','receptivo' ,objParametros);
        }else{

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    }catch (error) {

        utilsJS.toastNotify(false, error);
    }

}

function fcCarregarModulo(){
    try {
        let pk = $('#pk').val()
        if(pk > 0){
    
            var objParametros = {
                "pk": pk
            };        
            
            var arrCarregar = carregarController("modulo", "listarPk", objParametros);
            
            if (arrCarregar.status == true){
                $("#tipo_modulo_pk").val(arrCarregar.data[0]['tipo_modulo_pk']);
                $("#ds_dominio").val(arrCarregar.data[0]['ds_dominio']);
                $("#ds_obs").val(arrCarregar.data[0]['ds_obs']);
               
            }
            else{
                utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
            }
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
   
}

$(document).ready(function () {
    fcCarregarTiposModulos();
    fcCarregarModulo();
    $(document).on('click', '#cmdCancelar', fcCancelar);
    fcValidarForm();
    $(document).on('click', '#cmdEnviar', fcValidarForm);
})