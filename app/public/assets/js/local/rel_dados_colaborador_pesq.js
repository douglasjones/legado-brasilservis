var tblResultado;
var click_id = 0;


function fcCarregarGrid(){
    var ds_colaborador = $("#colaborador_pk option:selected").text()
    var ic_status = $("#ic_status").val()
    
    objParametros = {
        ds_colaborador:ds_colaborador,
        colaborador_pk:$("#colaborador_pk").val(),
        ic_status:ic_status
    }
    sendPost('relatorio', 'receptivoDadosColaborador',objParametros);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','rh' ,objParametros);
}

function fcCarregarComboColaborador(){
    
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);    
    carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");
        
}

$(document).ready(function(){    
           
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    
    fcCarregarComboColaborador();
    $(".chzn-select").chosen({allow_single_deselect: true});
});


