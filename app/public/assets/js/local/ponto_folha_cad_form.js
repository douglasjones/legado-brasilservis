function fcValidarForm() {
    utilsJS.loading('Salvando as informações !!!');
    $("#dt_periodo_fim").change(function () {
        var dt_periodo_ini = $("#dt_periodo_ini").datepicker("getDate");
        var dt_periodo_fim = $("#dt_periodo_fim").datepicker("getDate");
        qtd_dias = (dt_periodo_fim - dt_periodo_ini) / (1000 * 60 * 60 * 24);
        qtd_dias = qtd_dias | 0;
        if(qtd_dias > 31){
            alert("Informe um período com até 31 dias")
            utilsJS.loaded();
            return false;
        }
        
    });
    
    if($('#dt_periodo_ini').val()==""){
        $("#alert_dt_periodo_ini").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_periodo_ini").slideUp(500);
        });
        $('#dt_periodo_ini').focus();
        utilsJS.loaded();
        return false;
    }
    if($('#dt_periodo_fim').val()==""){
        $("#alert_dt_periodo_fim").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_periodo_fim").slideUp(500);
        });
        $('#dt_periodo_fim').focus();
        utilsJS.loaded();
        return false;
    }
    setTimeout(() => {
        fcEnviar(); 
    }, 2000);
    
}
function fcEnviar() {
    
    //verifica se foi selecionado    
    // JavaScript para estruturar o array associativo de leads e colaboradores
    var data = tblResultado.rows().data();
    var dataFolha = tblFolha.rows().data();

    var v_marcador_lead_colaborador = {}; // Objeto para amarrar lead com colaboradores
    var v_marcador_lead_Folha = {}; // Objeto para amarrar lead com colaboradores

    for (var i = 0; i < data.length; i++) {
        if ($("#colaborador_pk_" + i).prop("checked") == true) { 
            var lead_id = $("#leads_pk_" + i).val();
            var colaborador_id = $("#colaborador_pk_" + i).val() + "|" + $("#agenda_colaborador_padrao_pk" + i).val();

            // Cria um array para cada lead_id e adiciona os colaboradores
            if (v_marcador_lead_colaborador[lead_id]) {
                v_marcador_lead_colaborador[lead_id].push(colaborador_id);
            } else {
                v_marcador_lead_colaborador[lead_id] = [colaborador_id];
            }
        }
    }
    for (var i = 0; i < dataFolha.length; i++) {
        if ($("#ponto_folha_pk" + i).prop("checked") == true) { 
            var lead_id = $("#leads_pk_folha_" + i).val();
            var ponto_folha = $("#ponto_folha_pk" + i).val() ;

            // Cria um array para cada lead_id e adiciona os colaboradores
            if (v_marcador_lead_Folha[lead_id]) {
                v_marcador_lead_Folha[lead_id].push(ponto_folha);
            } else {
                v_marcador_lead_Folha[lead_id] = [ponto_folha];
            }
        }
    }
    var jsonParametros = JSON.stringify(v_marcador_lead_colaborador);
    var jsonParametrosFolha = JSON.stringify(v_marcador_lead_Folha);
    
    var v_dt_periodo_ini = $("#dt_periodo_ini").val();
    var v_dt_periodo_fim = $("#dt_periodo_fim").val();
    var v_empresas_pk = $("#empresas_pk").val();
    var v_obs = $("#obs").val();

    var objParametros = {
        "dt_periodo_ini": v_dt_periodo_ini,
        "dt_periodo_fim": v_dt_periodo_fim,
        "leads_colaboradores": jsonParametros, // Estrutura com leads e seus colaboradores
        "folha_ponto": jsonParametrosFolha, // Estrutura com leads e seus colaboradores
        "empresas_pk": v_empresas_pk,
        "obs": v_obs
    };

    // Envia os dados para o controller
    var arrEnviar = carregarController("ponto_folha", "salvar", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.loaded();
        utilsJS.toastNotify(true, arrEnviar.message);
        sendPost('ponto_folha','receptivoPontoFolha' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }

}

//combos
function fcCarregarEmpresas() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarPk", objParametros);
    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_conta");
}

function fcCarregarLeads() {
    var objParametros = {
        "empresas_pk": $("#empresas_pk").val()
    };
    var arrCarregar = carregarController("lead", "listarLeadsPorEmpresa", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarGridColaborador() {
    try {
        var objParametros = {
            "empresas_pk": $("#empresas_pk").val(),
            "leads_pk": $("#leads_pk").val(),
            "ic_escala": $("#ic_escala").val()
        };
    
        var arrCarregar = carregarController("colaborador", "listarColaboradorFolha", objParametros);
        //Trata a tabela           
    
        if (arrCarregar.status == true) {
            for (i = 0; i < arrCarregar.data.length; i++) {
    
                tblResultado.row.add(
                    ["<input type='hidden' id='leads_pk_" + i + "' value='" + arrCarregar.data[i]['leads_pk'] + "'><input type='checkbox' id='colaborador_pk_" + i + "' value='" + arrCarregar.data[i]['colaborador_pk'] + "'><input type='hidden' id='agenda_colaborador_padrao_pk"+i+"' value='" + arrCarregar.data[i]['agenda_colaborador_padrao_pk'] + "'>",
                        arrCarregar.data[i]['ds_lead'],
                        arrCarregar.data[i]['ds_colaborador'],
                        arrCarregar.data[i]['ds_status_colaborador'],
                        arrCarregar.data[i]['ds_produto_servico'],
                        arrCarregar.data[i]['dt_ini_escala']+' - '+arrCarregar.data[i]['dt_fim_escala'],
                        arrCarregar.data[i]['n_qtde_dias_semana'],
                        arrCarregar.data[i]['hr_inicio_expediente'] +' - '+ arrCarregar.data[i]['hr_termino_expediente'],
                        arrCarregar.data[i]['ds_status_escala'],
                        arrCarregar.data[i]['dt_cancelamento']
                    ]
                ).draw(false);
            }
        }
    } catch (error) {
        console.log(error)
    }
    
}
function fcCarregarFolhaPorPeriodoELeads() {
    try {
        var objParametros = {
            "empresas_pk": $("#empresas_pk").val(),
            "leads_pk": $("#leads_pk").val(),
            "dt_periodo_ini": $("#dt_periodo_ini").val(),
            "dt_periodo_fim": $("#dt_periodo_fim").val()
        };
        
        var arrCarregar = carregarController("ponto_folha", "listarFolhaPorPeriodoByLeads", objParametros);
        //Trata a tabela  
        if (arrCarregar.status == true) {
            for (i = 0; i < arrCarregar.data.length; i++) {
    
                tblFolha.row.add(
                    ["<input type='hidden' id='leads_pk_folha_" + i + "' value='" + arrCarregar.data[i]['leads_pk'] + "'><input type='checkbox' id='ponto_folha_pk" + i + "' value='" + arrCarregar.data[i]['pk'] + "' checked>",
                        arrCarregar.data[i]['pk'],
                        arrCarregar.data[i]['ds_lead']
                    ]
                ).draw(false);
            }
        }
    } catch (error) {
        console.log(error)
    }
    
}

function fcFormatarGrid() {
    tblResultado = $("#tblResultado").DataTable({
        "scrollX": false,
        "scrollY": true,
        "responsive": true,
        "searching": false,
        "paging": false,
        "bFilter": false,
        "bInfo": false,
        "columnDefs": [{
            orderable: false,
            targets: [1, 2, 3, 4, 5, 6, 7, 8]
        }],
        "language": {
            "url": "../inc/js/datatables/pt_br.php",
            "type": "GET"
        }
    });
    tblFolha = $("#tblFolha").DataTable({
        "scrollX": false,
        "responsive": true,
        "searching": false,
        "paging": false,
        "bFilter": false,
        "bInfo": false,
        "language": {
            "url": "../inc/js/datatables/pt_br.php",
            "type": "GET"
        }
    });
    return false;

}

function fcPesquisar() {
    try {

        if ($("#empresas_pk").val() == "") {
            alert('Selecione a Empresa!');
            return false;
        }
        tblFolha.clear();
        tblResultado.clear();

        //VERIFICA SE EXISTE A FOLHA DE PONTO PARA ESSE LEAD E ESSE PERIODO
        fcCarregarFolhaPorPeriodoELeads();
        
        fcCarregarGridColaborador();
    } catch (error) {
        console.log(error) 
    }
}

function fcMarcarTodos() {
    var data = tblResultado.rows().data();

    if (data.length == 0) {
        alert('Pesquise antes para listar os colaboradores dos postos de trabalho!');
        return false;
    }

    for (i = 0; i < data.length; i++) {//calcula o valor total
        $("#colaborador_pk_" + i).prop("checked", true);
    }
}

function fcCancelar() {
    var objParametros = {};
    sendPost('ponto_folha','receptivoPontoFolha',objParametros)
}

$(document).ready(function(){
    //Combo e mascaras
    fcCarregarEmpresas();

    $(".chzn-select").chosen({allow_single_deselect: true});
    
    $("#empresas_pk").change(function(){
        
        $(".chzn-select").chosen('destroy');
        fcCarregarLeads();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });

    $('#dt_periodo_ini').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_periodo_ini").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_periodo_fim').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_periodo_fim").keypress(function () {
        mascara(this, mdata);
    });


    fcFormatarGrid(); 

    //Atribui os eventos
    $(document).on('click', '#cmdPesquisarDadosFolha', fcPesquisar);
    $(document).on('click', '#cmdMarcarTodos', fcMarcarTodos);
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviarContato', fcValidarForm);

    //Atribui a validação do formulário dos campos obrigatórios
    //fcValidarForm();

});
