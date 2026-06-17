function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarColaborador() {
    //Carrega os grupos
    
    var objParametros = {
        "leads_pk": $("#leads_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_pk"), arrCarregar, " ", "pk", "ds_colaborador");

}
function fcGeralRelatorio(){
    if($('#ic_status_app').val()==""){
         sweetMensagem('warning',"Selecione o status");
         return false;
    }
    var ds_status_app = $("#ic_status_app option:selected").text();
    var ds_status = $("#ic_status option:selected").text();
    var ds_colaboradores = $("#colaborador_pk option:selected").text();
    var ds_leads = $("#leads_pk option:selected").text();
    var objParametros = {
        "ic_status_app": $('#ic_status_app').val(),
        "ds_status_app":ds_status_app,
        "leads_pk": $('#leads_pk').val(),
        "ic_status": $('#ic_status').val(),
        "ds_status": ds_status,
        "colaborador_pk": $('#colaborador_pk').val(),
        "ds_leads":ds_leads,
        "ds_colaboradores":ds_colaboradores
    }
    sendPost("relatorio","receptivoStatusColaborador",objParametros);
    //cria rota, voce vai colocar ela em colaboradores, por que é um relatorio que pega informação
    //especifica de colaborador.
}
$(document).ready(function(){    
    $(document).on('click', '#cmdEnviar', fcGeralRelatorio);

    fcCarregarLeads();
    fcCarregarColaborador();
    $("#leads_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });    

    $(".chzn-select").chosen({ allow_single_deselect: true });
});





