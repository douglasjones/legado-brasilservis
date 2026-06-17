function parseDateBR(dateStr) {
    const [dia, mes, ano] = dateStr.split("/");
    return new Date(`${ano}-${mes}-${dia}T00:00:00`);
}
function validarPeriodoUmMes(dtIni, dtFim) {
    // Diferença em milissegundos
    let diffMs = dtFim.getTime() - dtIni.getTime();

    // Se fim antes do início -> inválido
    if (diffMs < 0) return false;

    // Converte para dias
    let diffDias = diffMs / (1000 * 60 * 60 * 24);

    // Valida entre 28 e 31 dias (para cobrir fevereiro e meses maiores)
    return diffDias >= 28 && diffDias <= 31;
}
function fcAtualizar(){

    if($('#dt_ini').val()=="" && $('#dt_fim').val()==""){
        sweetMensagem('warning','Por favor preencha o perido!');
        return false;
    }
    else{
        var dtIni = parseDateBR($('#dt_ini').val());
        var dtFim = parseDateBR($('#dt_fim').val());

        console.log(dtIni);
        console.log(dtFim);
        if (isNaN(dtIni) || isNaN(dtFim)) {
            sweetMensagem('warning', 'Datas inválidas!');
            return false;
        }

        if (!validarPeriodoUmMes(dtIni, dtFim)) {
            sweetMensagem('warning', 'O período deve ser de exatamente 1 mês!');
            return false;
        }
    }
    if($("#leads_pk").val()==""){
        sweetMensagem('warning','Por favor preencha o posto de trabalho!');
        return false;
    }
    $(".loader").show();
    $("#carregar").show();
    $("#exibir").hide();
   

    setTimeout(() => {
        fcCarregarGrid();
    }, 1000);
    setTimeout(() => {
        $(".loader").hide();
        $("#carregar").hide();
        $("#exibir").show();
    }, 5000);
}

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
function formatDate(date) {
    var dia = String(date.getDate()).padStart(2, '0');
    var mes = String(date.getMonth() + 1).padStart(2, '0');
    var ano = date.getFullYear();
    return dia + '/' + mes + '/' + ano;
}

function fcGeralRelatorio(){
    var ds_colaboradores = $("#colaborador_pk option:selected").text();
    var ds_leads = $("#leads_pk option:selected").text();

    var objParametros = {
        "leads_pk": $('#leads_pk').val(),
        "colaborador_pk": $('#colaborador_pk').val(),
        "dt_inicio": $("#dt_ini").val(),
        "dt_fim": $("#dt_fim").val(),
        "ds_leads":ds_leads,
        "ds_colaboradores":ds_colaboradores
    }
    sendPost("relatorio","receptivoFechamento",objParametros);
    //cria rota, voce vai colocar ela em colaboradores, por que é um relatorio que pega informação
    //especifica de colaborador.
}
$(document).ready(function(){    
    $(document).on('click', '#cmdEnviar', fcGeralRelatorio);

    var hoje = new Date();
    var primeiroDia = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
    var ultimoDia = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);

     $('#dt_ini').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    }).datepicker('setDate', formatDate(primeiroDia));

    $("#dt_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_fim').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    }).datepicker('setDate', formatDate(ultimoDia));

    $("#dt_fim").keypress(function () {
        mascara(this, mdata);
    });

      fcCarregarLeads();
      fcCarregarColaborador();
      $("#leads_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });    

    $(".chzn-select").chosen({ allow_single_deselect: true });
});





