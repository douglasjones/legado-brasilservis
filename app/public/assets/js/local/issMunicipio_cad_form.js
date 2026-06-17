
function fcEnviar(){

    var ds_cidade = $("#ds_cidade").val();
    var ds_uf = $("#ds_uf").val();
    var vl_aliquota_iss = $("#vl_aliquota_iss").val();
    var t_ic_status = $("#ic_status").val();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_uf": (ds_uf),
        "ds_cidade": (ds_cidade),
        "vl_aliquota_iss": moeda2float(vl_aliquota_iss),
        "ic_status": (t_ic_status)        
    };    

    var arrEnviar = carregarController("iss_municipio", "salvar", objParametros);    
    
    if (arrEnviar.status == true){
        sendPost('iss_municipio', 'receptivo', '');
        utilsJS.toastNotify(true, arrEnviar.message);
    }else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost("iss_municipio", "receptivo", objParametros);
}

function fcCarregar(){
    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val(),
        };        
        
        var arrCarregar = carregarController("iss_municipio", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
            listarCidade();
            $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);

            $("#vl_aliquota_iss").val(float2moeda(arrCarregar.data[0]['vl_aliquota_iss']));
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);

        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }
}

function listarCidade(){
    
    var objParametros = {
        'ds_uf': $('#ds_uf').val()
    };        
    var arrCarregar = carregarController("iss_municipio", "listarCidade", objParametros);
    carregarComboAjax($("#ds_cidade"), arrCarregar, " ", "cidade", "cidade");
}



$(document).ready(function(){
    //faz a carga inicial do grid.

    
    $("#ds_uf").change(function () {
        utilsJS.loading('Buscando Cidades');
        $(".chzn-select").chosen('destroy');
        listarCidade();
        $(".chzn-select").chosen({ allow_single_deselect: true });
        utilsJS.loaded();
    });

    $("#vl_aliquota_iss").keypress(function () {
        mascara(this, moeda);
    });
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcEnviar);

    //Atribui a validação do formulário dos campos obrigatórios
    //fcValidarForm();

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();
    $(".chzn-select").chosen({ allow_single_deselect: true });
    

});


