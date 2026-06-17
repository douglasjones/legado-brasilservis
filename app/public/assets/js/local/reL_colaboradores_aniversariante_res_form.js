var tblResultado;

function fcCarregarGrid(){
    var ds_mes_aniversario = $("#ds_mes_aniversario option:selected").text();
    
    objParametros = {
        "mes_aniversario_pk": $("#ds_mes_aniversario").val(),
        "ds_mes_aniversario":ds_mes_aniversario
    }
    sendPost("colaborador","relAniversariantes",objParametros);
    //cria rota, voce vai colocar ela em colaboradores, por que é um relatorio que pega informação 
    //especifica de colaborador.
}

function fcCancelar(){
    var objParametros = {};
    sendPost('menu','rh' ,objParametros);
}


$(document).ready(function(){
    $(".chzn-select").chosen({ allow_single_deselect: true });  
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);



    $(".chzn-select").chosen({allow_single_deselect: true});
   
});