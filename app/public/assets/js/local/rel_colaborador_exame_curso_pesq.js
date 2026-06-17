var tblResultado;
var click_id = 0;

function fcCarregarGrid(){
    var ds_curso = $("#cursos_pk option:selected").text();
    var ds_colaborador = $("#colaboradores_pk option:selected").text();
    
    objParametros = {
        colaboradores_pk: $("#colaboradores_pk").val(),
        cursos_pk: $("#cursos_pk").val(),
        dt_execucao_ini:$("#dt_execucao_ini").val(),
        dt_execucao_fim:$("#dt_execucao_fim").val(),
        dt_validacao_ini:$("#dt_validacao_ini").val(),
        dt_validacao_fim:$("#dt_validacao_fim").val(),
        ds_curso:ds_curso,
        ds_colaborador:ds_colaborador
    }
    sendPost('relatorio', 'receptivoColaboradorExameCurso',objParametros);
}

function fcCarregarColaborador(){
    
    var objParametros = {
        "pk": ""
    };      
    
    var arrCarregar = carregarController("colaborador", "listarTodosRel", objParametros);    
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
        
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','rh' ,objParametros);
}
function fcCarregarCursos(){    
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("curso", "listarTodosAtivo", objParametros);    
    
    carregarComboAjax($("#cursos_pk"), arrCarregar, " ", "pk", "ds_curso");         
}
function fcCarregarColaboradores(){    
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("colaborador", "listarTodos", objParametros);    
   
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");         
}

$(document).ready(function(){    
    /*var arrCarregar = permissao("rel_colaborador", "cons");        

    if (arrCarregar.result != 'success'){            
        alert('Falhar ao carregar o registro');
        return false;
    }*/
           
    $(document).on('click', '#cmdEnviar', fcCarregarGrid);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    
        //Datas
    $('#dt_execucao_ini').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_execucao_ini").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    $('#dt_execucao_fim').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_execucao_fim").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    $('#dt_validacao_ini').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_validacao_ini").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    $('#dt_validacao_fim').datepicker({

        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: false,
        todayBtn: "linked",
        minDate: new Date()       
    });
    $("#dt_validacao_fim").keypress(function(){
        mascara(this,mdata);      
        //$('#sandbox-container input').datepicker({ minDate: 0});
    });
    
   
    
   
    
    
   
    fcCarregarCursos();

    fcCarregarColaboradores();

    $(".chzn-select").chosen({allow_single_deselect: true});
});


