var tblResultado;
var click_id = 0;

function fcGerarRelatorio(){
    var ds_colaborador = $("#colaboradores_pk option:selected").text();
    var ds_lead = $("#leads_pk option:selected").text();
    
    objParametros = {
        colaboradores_pk: $("#colaboradores_pk").val(),
        leads_pk: $("#leads_pk").val(),
        dt_ini: $("#dt_ini").val(),
        dt_fim: $("#dt_fim").val(),
        ds_colaborador: ds_colaborador,
        ds_lead: ds_lead
    }
    sendPost("relatorio","receptivoColaboradorPostoTrabalho",objParametros);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('relatorio','operacional' ,objParametros);
}

function fcCarregarColaboradores(){    
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };         
    var arrCarregar = carregarController("colaborador", "listarColaboradorLeadCalendario", objParametros);
   
    carregarComboAjax($("#colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");       
}
function fcCarregarLeads(){    
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);    
   
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");         
}

$(document).ready(function(){    
    /*var arrCarregar = permissao("rel_colaborador", "cons");        

    if (arrCarregar.result != 'success'){            
        alert('Falhar ao carregar o registro');
        return false;
    }*/
           
    $(document).on('click', '#cmdEnviar', fcGerarRelatorio);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $("#leads_pk").change(function () {
        $(".chzn-select").chosen('destroy');
        fcCarregarColaboradores();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });
    fcCarregarColaboradores();
    fcCarregarLeads();

    $(".chzn-select").chosen({allow_single_deselect: true});
});


