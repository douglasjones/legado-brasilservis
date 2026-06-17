var tblResultado;

function fcCancelar(){
    var objParametros = {};
    sendPost('menu','relatorio' ,objParametros);
}

function fcCarregarColaborador() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
}

function fcGeralRelatorio(){
    var ds_colaboradores = $("#colaboradores_pk option:selected").text();

    var objParametros = {
        colaboradores_pk: $("#colaboradores_pk").val(),
        dt_ini_ferias:$("#dt_ini_ferias").val(),
        dt_fim_ferias:$("#dt_fim_ferias").val(),
        "ds_colaboradores":ds_colaboradores
    }
    sendPost("relatorio","receptivoAcompanhamentoFerias",objParametros);
    //cria rota, voce vai colocar ela em colaboradores, por que é um relatorio que pega informação
    //especifica de colaborador.
}

$(document).ready(function(){    
           
    $(document).on('click', '#cmdEnviar', fcGeralRelatorio);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    
        //Datas
    $('#dt_ini_ferias').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_ini_ferias").keypress(function(){
        mascara(this,mdata);      
        $('#sandbox-container input').datepicker({ minDate: 0});
    });
    
    //Datas
    $('#dt_fim_ferias').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_fim_ferias").keypress(function(){
        mascara(this,mdata);      
        $('#sandbox-container input').datepicker({ minDate: 0});
    });
    
    
    fcCarregarColaborador();
    
    
    
    $(".chzn-select").chosen({allow_single_deselect: true});
    

});
