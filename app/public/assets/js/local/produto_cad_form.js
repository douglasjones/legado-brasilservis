function fcValidarForm(){

    $("#form").validate({
        rules :{
            categorias_produto_pk:{
                required:true
            },
            ds_produto:{
                required:true

            },
            tipo_unidade_pk:{
                required:true
            }

        },
        messages:{
            categorias_produto_pk:{
                required:"Por favor, selecione a categoria "
            },
            ds_produto:{
                required:"Por favor, informe o nome do produto"
            },
            tipo_unidade_pk:{
                required:"Por favor, selecione a unidade "
            }
        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_produto = $("#ds_produto").val();
    var v_obs = $("#obs").val();
    var v_ic_status = $("#ic_status").val();
    var v_categorias_produto_pk = $("#categorias_produto_pk").val();
    var v_tipo_unidade_pk = $("#tipo_unidade_pk").val();
    var v_ic_tempo_troca = $("#ic_tempo_troca").val();
    var v_qtde_minima = 1;


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_produto": (v_ds_produto),
        "obs": (v_obs),
        "ic_status": (v_ic_status),
        "categorias_produto_pk": (v_categorias_produto_pk),
        "ic_tempo_troca": (v_ic_tempo_troca),
        "qtde_minima": (v_qtde_minima),
        "tipo_unidade_pk": (v_tipo_unidade_pk)
    };

    var arrEnviar = carregarController("produto", "salvar", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost("produtos", "receptivo",objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('produtos','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };

        var arrCarregar = carregarController("produto", "listarPk", objParametros);

        if (arrCarregar.status == true){

            $("#ds_produto").val(arrCarregar.data[0]['ds_produto']);
            $("#obs").val(arrCarregar.data[0]['obs']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);
            $("#categorias_produto_pk").val(arrCarregar.data[0]['categorias_produto_pk']);
            $("#tipo_unidade_pk").val(arrCarregar.data[0]['tipo_unidade_pk']);
            $("#ic_tempo_troca").val(arrCarregar.data[0]['ic_tempo_troca']);
            $("#qtde_minima").val(arrCarregar.data[0]['qtde_minima']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}


function fcCarregarCategorias(){

    var objParametros = {
        "pk": $("#pk").val()
    };
    var arrCarregar = carregarController("categoria_produto", "listarTodos", objParametros);
    carregarComboAjax($("#categorias_produto_pk"), arrCarregar, " ", "pk", "ds_categoria");
}
$(document).ready(function()
    {
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);

        fcCarregarCategorias()
        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
        $("#qtde_minima").keypress(function(){
            mascara(this,soNumeros);
        });
    }
);
