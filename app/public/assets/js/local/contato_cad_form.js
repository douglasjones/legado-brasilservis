function fcValidarFormContato(){
    $("#form_contato").validate({
        rules :{
            ds_contato:{
                required:true,
                minlength:3
            },
            ic_whatsapp:{
                required:true
            },
            ds_cel:{
                required:true,
                minlength:13
            },
            ds_tel_contato:{
                minlength:13
            },
            ds_email:{
                email: true
            }

        },
        messages:{
            ds_contato:{
                required:"Por favor, informe Contato",
                minlength:"Contato deve ter pelo menos 3 caracteres"
            },
            ds_cel:{
                required:"Por favor, informe Celular",
                minlength:"Por favor, informe Celular válido"
            },
            ds_tel_contato:{
                minlength:"Por favor, informe Telefone válido"
            },
            ic_whatsapp:{
                required:"Por favor, informe WhatsApp"
            },
            ds_email:{
                email:"Por favor, informe E-mail válido"
            }

        },
        submitHandler: function(form){
            fcEnviarContato(); //Se a validação deu certo, faz o envio do formulario.

            return false;
        }
    });

}

function fcEnviarContato(){
    if($("#leads_pk").val()== ""){
        if($("#acao").val() == "ins"){
            fcIncluirContatoSemPk();
        }
        else if($("#acao").val() == "upd"){
            fcEditarContatoSemPk();
        }
    }else{
        fcSalvarContato();
    }
    $("#janela_contatos").modal("hide");
}

function fecharModalContato(){
    $("#janela_contatos").modal("hide");
}

function fcIncluirContatoSemPk(){
    tblContatos.row.add(
        {
            "pk":"",
            "ds_contato":$("#ds_contato").val(),
            "ds_email":$("#ds_email").val(),
            "ds_cel":$("#ds_cel").val(),
            "ic_whatsapp":$("#ic_whatsapp").val(),
            "ds_whatsapp":$("#ic_whatsapp option:selected").text(),
            "ds_tel":$("#ds_tel_contato").val(),
            "cargos_pk":$("#cargos_pk").val(),
            "ds_cargos_pk":$("#cargos_pk option:selected").text(),
            "t_functions":""
        }
    ).draw();

    return false;
}

function fcSalvarContato(){
    var leads_pk = $("#leads_pk").val();

    //atualiza o registro no DB, pois já existe uma PK para contatos no banco.
    var objParametros = {
        "pk": $("#contatos_pk").val(),
        "leads_pk": leads_pk,
        "ds_contato": $("#ds_contato").val(),
        "ds_email": $("#ds_email").val(),
        "ds_cel": $("#ds_cel").val(),
        "ds_tel": $("#ds_tel_contato").val(),
        "ic_whatsapp": $("#ic_whatsapp").val(),
        "cargos_pk": $("#cargos_pk").val()
    };
    var arrEnviar = carregarController("contato", "salvar", objParametros);

    if (arrEnviar.status == true){
        tblContatos.ajax.reload();
    }else{

        utilsJS.toastNotify(true,arrEnviar.message);
    }

}


function fcEditarContatoSemPk(){
    fcIncluirContatoSemPk();
    tblContatos.row(rLinhaSelecionada).remove().draw();
    return false;
}

function fcCarregarContato(){

    if(pk > 0){
        var objParametros = {
            "pk": pk
        };
        var arrCarregar = carregarController("contato", "listarPk", objParametros);

        if (arrCarregar.result == 'success'){

            $("#ds_contato").val(arrCarregar.data[0]['ds_contato']);
            $("#ds_cel").val(arrCarregar.data[0]['ds_cel']);
            $("#ic_whatsapp").val(arrCarregar.data[0]['ic_whatsapp']);
            $("#ds_email").val(arrCarregar.data[0]['ds_email']);
            $("#ds_tel").val(arrCarregar.data[0]['ds_tel']);
            $("#cargos_pk").val(arrCarregar.data[0]['cargos_pk']);
            $("#leads_pk").val(arrCarregar.data[0]['leads_pk']);
        }
        else{

            utilsJS.toastNotify(false, 'Falhou ao carregar Registro');
        }
    }
}

function fcCarregarCargo(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("cargo", "listarTodos", objParametros);
    carregarComboAjax($("#cargos_pk"), arrCarregar, " ", "pk", "ds_cargo");
}


$(document).ready(function(){
    $("#ds_cel").on('keyup', function () {
        mascara(this,mascaraTelefone);
    });

    $("#ds_tel_contato").on('keyup', function () {
        mascara(this,mascaraTelefone);
    });

    fcCarregarCargo();
    fcValidarFormContato();
    //Verifica se o registro é para alteracao e puxa os dados.

    // fcCarregarContato();

});
