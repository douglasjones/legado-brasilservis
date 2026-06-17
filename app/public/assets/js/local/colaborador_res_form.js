var tblResultado;
function fcPesquisar() {
    $(".chzn-select").chosen('destroy');
    tblResultado.clear().destroy();
    fcCarregarGrid();
     $(".chzn-select").chosen({ allow_single_deselect: true });

}
function fcVoltar(){
    var objParametros = {};
    sendPost('menu','operacional' ,objParametros);
}

function fcIncluir() {
    var objParametros = {
        "colaborador_pk":"",
        "local":$("#local").val()
      };
      sendPost('colaborador','cadForm',objParametros)    
  
}

function fcExcluirColaboradorGrid(v_pk, v_ds_colaborador) {
    var arrCarregar = permissao("colaborador", "del");
    if (arrCarregar.status != true){
        sweetMensagem('warning', 'Você não tem permissão!');
        return false;
    }
    utilsJS.jqueryConfirm('Excluir?', 'Deseja excluir o registro '+v_ds_colaborador+'?', function () {
        if (v_pk != "") {

            var objParametros = {
                "pk": v_pk
            };

            var arrExcluir = carregarController("colaborador", "excluir", objParametros);

            if (arrExcluir.status == true) {

                //Exibe a mensagem
                utilsJS.toastNotify(true,arrExcluir.message);

                // Reload datable
                tblResultado.ajax.reload();

            }
            else {
                utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
            }
        }
        else {
            sweetMensagem('warning',"Código não encontrado");
        }
    });
}

function fcEditar(v_pk) {
    var objParametros = {
        "colaborador_pk":v_pk,
        "local":$("#local").val()
      };
      sendPost('colaborador','cadForm',objParametros)    
}

function fcImpressao(v_pk) {
    var objParametros = {
        "pk":v_pk 
      };
      sendPost('colaborador','print',objParametros)
}

function fcCarregarGrid() {
    var ic_reserva = "";
    if ($('#ic_reserva').is(":checked")) {
        ic_reserva = 1;
    }
    var objParametros = {
        "pk": $("#colaborador_pk").val(),
        "ic_status": $("#ic_status").val(),
        "leads_pk": $("#leads_pk").val(),
        "ic_origem": $("#ic_origem").val(),
        "ds_pin": $("#ds_pin").val(),
        "ds_cpf": $("#ds_cpf").val(),
        "generos_pk": $("#generos_pk").val(),
        "ds_re": $("#ds_re").val(),
        "ic_status_app": $("#ic_status_app").val(),
        "ic_reserva": ic_reserva,
        "ds_produto_servico": $("#ds_produto_servico").val()
    };

    var v_url = routes_api("colaborador", "listarGrid", objParametros);
   
    //Trata a tabela
    tblResultado = $('#tblResultado').DataTable({
        searching: true,
        paging: true,
        scrollX: true,
        pageLength: 10,
        aLengthMenu: [10, 25, 50, 100],
        iDisplayLength: 10,
        processing: true,
        serverSide: true,
        ajax: v_url,
        responsive: true,
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },
        order: [
            [0, "asc"]
        ],
        columns: [
            {
                mRender: function (data, type, full) {
                    return full['t_pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_lead'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_colaborador'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_cpf'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_re'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_cel'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                var status = (full['ds_status_app'] || '').toLowerCase();
                var t_ic_status = (full['t_ic_status'] || '').toLowerCase();
                var corFundo;

                switch (status) {
                    case 'aguardando liberação':
                        corFundo = '#e74c3c'; // vermelho
                        break;
                    case 'nao fez o novo cadastro':
                    case 'não fez o novo cadastro':
                        corFundo = '#f1c40f'; // amarelo claro
                        break;
                    case 'acesso cancelado':
                        corFundo = '#bdc3c7'; // cinza claro
                        break;
                    case 'liberado':
                        corFundo = '#2ecc71'; // verde
                        break;
                    default:
                        corFundo = ''; // cinza neutro para status desconhecido
                }

                if (t_ic_status === "demitido") {
                    corFundo = '#bdc3c7'; // cinza claro
                    if(full['ds_status_app']!="Não fez o novo cadastro"){
                        full['ds_status_app'] = 'acesso cancelado';
                    }
                }

                // Função: só a primeira letra maiúscula
                function capitalizeFirstLetter(str) {
                    str = str.toLowerCase();
                    return str.charAt(0).toUpperCase() + str.slice(1);
                }

                var textoFormatado = capitalizeFirstLetter(full['ds_status_app'] || '');

                return '<span style="background-color:' + corFundo + '; color:white; font-weight:bold; padding: 2px 6px; border-radius: 4px;">' + textoFormatado + '</span>';
            }
            }
            ,
            {
                mRender: function (data, type, full) {
                    return full['t_ic_status'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    return full['t_ds_funcao'];
                },
                'orderable': true,
                'searchable': false,

            },
            {
                mRender: function (data, type, full) {
                    var buttonPainel = '<a class="function_painel"><i class="bi bi-speedometer2" style="font-size:18px; color:blue" title="Painel de controle"></i></a> ';
                    var buttonEdit = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                    var buttonPrintAll = '<a class="function_print_folha_ponto"><i class="bi bi-calendar" style="font-size:18px; color:blue"></i></a> ';
                    var buttonPrint = '<a class="function_print"><i class="bi bi-printer" style="font-size:18px; color:blue" title="Abrir Formulario para impressao"> </i></a> ';
                    var buttonOpcoes =  '<a class="function_folha"><i class="bi bi-alarm" style="font-size:18px;color:blue" title="Acomp Ponto"></i></a>&nbsp;';
                    var buttonDelete = '<a class="function_delete_colaborador"><i class="bi bi-x-circle" style="font-size:18px; color:blue" title="Excluir"></i></span></a>';

                    return buttonEdit+ buttonOpcoes + buttonPrintAll + buttonPrint + buttonDelete;
                },
                'orderable': false,
                'searchable': false,
            }
        ]
    });

   
    //Atribui os eventos na coluna ação.
    $('#tblResultado tbody').on('click', '.function_painel', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcEditar(data['t_pk']);
        //INC_PAINEL
        //fcAbrirPainelControle(data['t_pk'],data['t_ds_colaborador'],data['ds_cpf'],data['t_ic_status'],data['ds_status_app'],data['ds_lead'],data['ds_turno'],data['escala']);

    });
    $('#tblResultado tbody').on('click', '.function_edit', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcEditar(data['t_pk']);
    });
    
    $('#tblResultado tbody').on('click', '.function_print', function () {
        var data;
        
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcImpressao(data['t_pk']);
    });

    $('#tblResultado tbody').on('click', '.function_delete_colaborador', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcExcluirColaboradorGrid(data['t_pk'], data['t_ds_colaborador']);
    });
    $('#tblResultado tbody').on('click', '.function_opcoes', function () {
        var data;

        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirGridForulario(data['t_pk']);

    });

    $('#tblResultado tbody').on('click', '.function_folha', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirAcompanhamentoFolhaPonto(data['t_pk'],data['leads_pk'],data['agenda_colaborador_pk']);

    });

    $('#tblResultado tbody').on('click', '.function_print_folha_ponto', function () {
        var data;
        if (tblResultado.row($(this).parents('li')).data()) {
            data = tblResultado.row($(this).parents('li')).data();
        }
        else if (tblResultado.row($(this).parents('tr')).data()) {
            data = tblResultado.row($(this).parents('tr')).data();
        }
        fcAbrirModalPrintFolhaPontoAll(data['t_pk'],data['t_ds_colaborador']);

    });

}



function fcAbrirGridForulario(pk) {
    sendPost('colaborador','fcAbrirGridForulario', { "colaborador_pk": pk,"local":1 });
}
function fcPainel() {

    var objParametros = {
    "colaborador_pk":$("#colaborador_pk").val(),
    "local":$("#local").val()
    };
    sendPost('colaborador','painel',objParametros)   
}

function fcCarregarGenero() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("genero", "listarTodos", objParametros);
    carregarComboAjax($("#generos_pk"), arrCarregar, " ", "pk", "ds_genero");

}

function fcCarregarQualificacao() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
   // NewWindow(v_last_url);
    carregarComboAjax($("#ds_produto_servico"), arrCarregar, " ", "pk", "ds_produto_servico");
    //alert(1);
}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
    carregarComboAjax($("#leads_consulta_folha_pk"), arrCarregar, " ", "pk", "ds_lead");

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


function fcAbrirAcompanhamentoFolhaPonto(colaborador_pk,leads_pk,agenda_colaborador_pk){
    //LISTAR O POSTO DE TRABALHO

    $("#leads_consulta_folha_pk").val("");
    $("#agenda_consulta_folha_colaborador_pk").val("");
    $("#colaborador_consulta_folha_pk").val("");
    $(".chzn-select").chosen('destroy');
       
    $("#leads_consulta_folha_pk").val(leads_pk);
    $("#agenda_consulta_folha_colaborador_pk").val(agenda_colaborador_pk);
    

    //LISTAR O COLABORADOR
    fcCarregarColaboradorFolhaPonto();
    
    $("#colaborador_consulta_folha_pk").val(colaborador_pk);

    const dataAtual = new Date();

    // Obtém o mês atual (os meses são indexados a partir de 0, então adicionamos 1)
    const mes = dataAtual.getMonth() + 1;
    if(mes<=9){
        mesAtual = "0"+mes;
    }
    else{
        mesAtual = mes;
    }


    // Obtém o ano atual
    const anoAtual = dataAtual.getFullYear();
    
    //$("#ic_consulta_folha_mes").val(mesAtual);
    //$("#ic_consulta_folha_ano").val(anoAtual);


    var html = "";
    for(i=parseInt(anoAtual);i >= parseInt(anoAtual-3);i--){
     
        html += "<option value='" + i + "'>" + i + "</option>";
    }
    $("select[name=ic_consulta_folha_ano]").html(html);


    setTimeout(function () {
        $(".chzn-select").chosen('destroy');
        $(".chzn-select").chosen({ allow_single_deselect: true });
    }, 2000);
    
    $("#grid_consulta_folha_ponto").html("");
    $("#grid_consulta_folha_ponto").append("");
    $("#consulta_folha_ponto").modal("show");
    setTimeout(() => {
        //fcCarregarGridPontoFolha();
    }, 2000);
   
    
}
function fcAbrirModalPrintFolhaPontoAll(colaborador_pk,ds_colaborador){
    
    $("#text-colaborador-all").text(ds_colaborador);
    $("#colaborador_pk_print_all").val(colaborador_pk);

    $("#printFolhaAll").modal("show");
   
    
}

function fcFecharConsultaFolhaPonto(){
    $("#consulta_folha_ponto").modal("hide");
}


function fcCarregarColaboradorFolhaPonto() {
    //Carrega os grupos
    
    var objParametros = {
        "leads_pk": $("#leads_consulta_folha_pk").val()
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradorLead", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#colaborador_consulta_folha_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    carregarComboAjax($("#colaborador_pk_modal"), arrCarregar, " ", "pk", "ds_colaborador");

}
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

function fcCarregarGridPontoFolha(){
    $("#grid_consulta_folha_ponto").html("");
    $("#grid_consulta_folha_ponto").append("");
    if($('#agenda_consulta_folha_colaborador_pk').val()==""){
        sweetMensagem('warning','Esse colaborador não tem escala!');
        return false;
    }
    if($('#dt_ini_reloginho').val()=="" && $('#dt_fim_reloginho').val()==""){
        sweetMensagem('warning','Por favor preencha o perido!');
        return false;
    }
    else{
        var dtIni = parseDateBR($('#dt_ini_reloginho').val());
        var dtFim = parseDateBR($('#dt_fim_reloginho').val());

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
    if($('#leads_consulta_folha_pk').val()==""){
        sweetMensagem('warning','Preencha todos os campos!');
        return false;
    }
    if($('#colaborador_consulta_folha_pk').val()=="" || $('#colaborador_consulta_folha_pk').val()=="null" ||$('#colaborador_consulta_folha_pk').val()==null){
        sweetMensagem('warning', 'preencha todos os campos!');
        return false;
    }
    
    let objParametros = {
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
        "dt_inicio":$("#dt_ini_reloginho").val(),
        "dt_fim":$("#dt_fim_reloginho").val()
        //"ic_mes": $('#ic_consulta_folha_mes').val(),
        //"ic_ano": $('#ic_consulta_folha_ano').val()
    };
    let arrCarregar = carregarController("ponto_folha", "listarConsultaPontoColaborador", objParametros);
    var html ="";
    if (arrCarregar.status == true) {
        if(arrCarregar.data.length>0){
            console.log(arrCarregar.data[0]['ds_turno'])
            $("#ds_turno").text(arrCarregar.data[0]['ds_turno']);
            $("#turnos_pk").text(arrCarregar.data[0]['turnos_pk']);
            $("#ponto_folha_pk").val(arrCarregar.data[0]['ponto_folha_pk']);
            $("#ic_status_ponto_folha_pk").val(arrCarregar.data[0]['ic_status_ponto_folha_pk']);
           
            var v_dias_trabalhados = 0;
            
            $("#periodo_trabalho").text(arrCarregar.data[0]['hr_inicio_expediente']+" até "+arrCarregar.data[0]['hr_termino_expediente']);
            html+="<div class='row'>";
            html+="<div class='table-container'>";
            html+="<table  style='width:100%;overflow-y: scroll;height: 20px;' id='tblResultado1'>";
            html+="<thead>";
            html+="<tr>";
            html+="                    <th  style='  text-align: center'>";
            html+="                        Validado";
            html+="             </th>";
            html+="                    <th  style='  text-align: center'>";
            html+="                        Data";
            html+="             </th>";
            html+="             </th>";
            html+="                    <th  style='  text-align: center'>";
            html+="                        Posto de Trabalho";
            html+="             </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Dia Semana";
            html+="                            </th>";
            html+="       <th  style=' text-align: center'>";
            html+="                                Ini Exp ";
            html+="                            </th>";
            html+="                            <th  style=' text-align: center'>";
            html+="                                Ini Inter ";
            html+="                            </th>";
            html+="                            <th  style='  text-align: center'>";
            html+="                                Fim Inter";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Fim Exp";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             H.T";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             H.E";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             H.F";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Situação";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Apontamento";
            html+="                            </th>";
            html+="                             <th align='center' style=' text-align: center'>";
            html+="                             Ação";
            html+="                            </th>";
            html+="                        </tr>";
            html+="                    </thead>";
            html+="                    <tbody >";
            html += "   <input type='hidden' id='totalLinhas' size='3' value='" + arrCarregar.data.length + "'>";
            html += "   <input type='hidden' id='turnos_pk' size='3' value='" + arrCarregar.data[0]['turnos_pk'] + "'>";
            
            var v_ponto_batidos = 0;
            var v_dias_folga = 0;
            for(i=0;i<arrCarregar.data.length;i++){
                //-----------------------------//
                if(arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente']!=""){
                    v_dias_trabalhados++;
                }
                else{
                    v_dias_folga++;
                }
                //----------------------------------//
                if(arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente']!=" " ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo']!=" "  ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo']!=" "  ||
                    arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente']!=" " 
                ){
                    v_ponto_batidos++;
                }


                if(arrCarregar.data[i]['pontos_dia'][0]['ic_validacao_facial_ini_expediente']==2
                    ||arrCarregar.data[i]['pontos_dia'][0]['ic_validacao_facial_ini_intervalo']==2
                    ||arrCarregar.data[i]['pontos_dia'][0]['ic_validacao_facial_termino_intervalo']==2
                    ||arrCarregar.data[i]['pontos_dia'][0]['ic_validacao_facial_termino_expediente']==2
                ){
                    html+="<tr style='background-color:yellow'>";
                }
                else{
                    html+="<tr>";
                }
                
                html+='<th  style="text-align: center;">';
                
                if (arrCarregar.data[i]['arrVerificado'] && arrCarregar.data[i]['arrVerificado'][0] && arrCarregar.data[i]['arrVerificado'][0]['pk']) {
                    var checked = "";
                    var value = 1;
                    if(arrCarregar.data[i]['arrVerificado'][0]['ic_verificado']==1){
                        checked = "checked";
                        value = 0;
                    }

                    html += "   <input type='checkbox' id='ic_validado" + i + "'  "+checked+" value='"+value+"'  onclick='fcSalvarValidadoReloginho("+i+")'>";
                    html += "   <input type='hidden' id='verificado_pk" + i + "'  value='"+arrCarregar.data[i]['arrVerificado'][0]['pk']+"'>";
                }
                else if(arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento']!=0){
                    html += "   <input type='checkbox' id='ic_validado" + i + "' size='3' checked value='0' onclick='fcSalvarValidadoReloginho("+i+")'>";
                    html += "   <input type='hidden' id='verificado_pk" + i + "'  value=''>";
                }
                else {
                    html += "   <input type='checkbox' id='ic_validado" + i + "' size='3' value='1' onclick='fcSalvarValidadoReloginho("+i+")'>";
                    html += "   <input type='hidden' id='verificado_pk" + i + "'  value=''>";
                }
                html+='</th>';
                html+='<th  style="  text-align: center">';
                html+= arrCarregar.data[i]['dt_hora_ponto'];
                html += "   <input type='hidden' id='dt_hora_ponto" + i + "' size='3' value='" + arrCarregar.data[i]['dt_hora_ponto'] + "'>";
                html += "   <input type='hidden' id='dt_hora_ponto_usa" + i + "' size='3' value='" + arrCarregar.data[i]['dt_hora_ponto_usa'] + "'>";
                html += "   <input type='hidden' id='expediente_diario" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['expediente_diario'] + "'>";
                html+='</th>';
                html += '<th style="text-align: center">';
                if(arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'].slice(0, 5)==""){
                    html +="";
                }
                else{
                    html += arrCarregar.data[i]['pontos_dia'][0]['ds_lead'] ?? 'Apontamento';
                }
                
                html += '</th>';

                html+='<th  style="  text-align: center">';
                html+= arrCarregar.data[i]['dia_da_semana'];
                html += "         <input type='hidden' id='dt_dia_semana" + i + "' size='3' value='" + arrCarregar.data[i]['dia_da_semana'] + "'>";
                html+='</th>';
                if(arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento']!=0){
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ini']==1){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        
                        html += "<input disabled type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input disabled type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ini_int']==3){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input disabled type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input disabled type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_fim_int']==4){
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input disabled type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input disabled type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    
                    if(arrCarregar.data[i]['pontos_dia'][0]['tipo_ponto_pk']==1 && arrCarregar.data[i]['pontos_dia'][0]['ic_apontamento_ter']==2){
                        
                        html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                        html += "<input disabled type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    else{
                        html+='<th  style="  text-align: center">';
                        html += "<input disabled type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                        html+='</th>';
                    }
                    html += "    <th style=' text-align: centers;'>";
                    html += "<input disabled type='text'  id='hr_trabalhadas" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['horas_trabalhadas'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html += "    <th style=' text-align: center;'>";
                    html += "<input disabled type='text' id='hr_excedentes" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['hr_excedentes'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html += "    <th style=' text-align: center;'>";
                    html += "<input disabled type='type' id='hr_faltantes" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['hr_faltante'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html+='<th  style="  text-align: center;background-color:#ADD8E6">';
                    html += arrCarregar.data[i]['pontos_dia'][0]['situacao'] ;
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html+='<select class="form-control form-control-sm" id="tipo_ponto_pk'+i+'" onchange="habilitarCampos('+i+',this.value)">';
                    html+='        <option value="">Selecione</option>';
                    html+='    <optgroup label="PONTO">';
                    html+='        <option value="1">Ponto/Expediente</option>';
                    html+='    </optgroup>';
                    html+='    <optgroup label="FALTA">';
                    html+='        <option value="2">Falta</option>';
                    html+='        <option value="11">Abonada</option>';
                    html+="        <option value='16'>Atestado</option>";
                    html+="        <option value='18'>Declaração da defesa civil</option>";
                    html+="        <option value='28'>Apoio Operacional </option>";
                    html+="        <option value='29'>Atestado por acompanhar filho ate 5 anos</option>";
                    html+="        <option value='30'>Atestado por serviço Justiça Eleitoral</option>";
                    html+="        <option value='37'>Atestado de horas </option>";
                    html+="        <option value='31'>Doação de sangue</option>";
                    html+="        <option value='32'>Atraso</option>";
                    html+="        <option value='33'>Declaração de horas abonar</option>";
                    html+="        <option value='34'>Sem Justificativa</option>";
                    html+="        <option value='35'>Reciclagem</option>";
                    html+="        <option value='36'>Audiência </option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Folga">';
                    html+='        <option value="3">Folga</option>';
                    html+="        <option value='20'>Folga compensatória</option>";
                    html+="        <option value='21'>Folga de feriado</option>";
                    html+="        <option value='25'>Troca Folga</option>";
                    html+="        <option value='26'>Folga trabalhada</option>";
                    html+="        <option value='27'>Escala Errada</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Afastamento">';
                    html+="        <option value='5'>Afastamento</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Férias">';
                    html+="        <option value='6'>Férias</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Disciplina">';
                    html+="        <option value='8'>Disciplina</option>";
                    html+="        <option value='17'>Advertencia</option>";
                    html+="        <option value='19'>Demissão</option>";
                    html+="        <option value='22'>Justa causa</option>";
                    html+="        <option value='23'>Recisão indireta</option>";
                    html+="        <option value='24'>Suspensão</option>";
                    html+='    </optgroup>';
                    html+='</select>';
                    html+='</th>';
                    html+='</th>';
                    html += '<th style="text-align: center; white-space: nowrap;">';
                    html += '     <a onclick="fcExcluirApontamento(' + arrCarregar.data[i]['pontos_dia'][0]['apontamento_pk'] + ')"><i class="bi-solid bi-trash" style="color:red; cursor:pointer; margin-right: 8px;"></i></a>';
                    html += '     <a onclick="abrirModalHistorico(' + i + ')"><i class="bi bi-ui-checks-grid" style="cursor: pointer;"></i></a>';
                    html += '     <a onclick="fcPontoDiario(' + i + ')"><i class="bi bi-camera-fill" style="cursor: pointer;"></i></a>';
                    html += '</th>';
                    
                    
                }
                else{
                    html+='<th  style="  text-align: center">';
                    html += "<input disabled type='text' id='hr_ini_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                    html+='</th>';
                    html+='<th  style=" text-align: center">';
                    html += "<input disabled type='text' id='hr_ini_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_ini_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                    html+='</th>';
                    html+='<th  style=" text-align: center">';
                    html += "<input disabled type='text' id='hr_fim_intervalo" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_intervalo'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                    html+='</th>';
                    html+='<th  style="  text-align:">';
                    html += "<input disabled type='text' id='hr_fim_expediente" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['ponto_term_expediente'].slice(0, 5) + "' onkeypress='mascara(this,horamask)' onChange='calculoOnchange("+i+")'>";
                    html+='</th>';
                    html += "    <th style=' text-align: center;'>";
                    html += "<input disabled type='text'  id='hr_trabalhadas" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['horas_trabalhadas'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html += "    <th style=' text-align: center;'>";
                    html += "<input disabled type='text' id='hr_excedentes" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['hr_excedentes'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html += "    <th style=' text-align: center'>";
                    html += "<input disabled type='type' id='hr_faltantes" + i + "' size='3' value='" + arrCarregar.data[i]['pontos_dia'][0]['hr_faltante'] + "' onkeypress='mascara(this,horamask)' >";
                    html += "    </th>";
                    html+='<th  style="  text-align: center">';
                    html += arrCarregar.data[i]['pontos_dia'][0]['situacao'] ;
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html+='<select class="form-control form-control-sm" id="tipo_ponto_pk'+i+'" onchange="habilitarCampos('+i+',this.value)">';
                    html+='        <option value="">Selecione</option>';
                    html+='    <optgroup label="PONTO">';
                    html+='        <option value="1">Ponto/Expediente</option>';
                    html+="        <option value='37'>Atestado de horas </option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="FALTA">';
                    html+='        <option value="2">Falta</option>';
                    html+='        <option value="11">Abonada</option>';
                    html+="        <option value='16'>Atestado</option>";
                    html+="        <option value='18'>Declaração da defesa civil</option>";
                    html+="        <option value='28'>Apoio Operacional </option>";
                    html+="        <option value='29'>Atestado por acompanhar filho ate 5 anos</option>";
                    html+="        <option value='30'>Atestado por serviço Justiça Eleitoral</option>";
                    html+="        <option value='31'>Doação de sangue</option>";
                    html+="        <option value='32'>Atraso</option>";
                    html+="        <option value='33'>Declaração de horas abonar</option>";
                    html+="        <option value='34'>Sem Justificativa</option>";
                    html+="        <option value='35'>Reciclagem</option>";
                    html+="        <option value='36'>Audiência </option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Folga">';
                    html+='        <option value="3">Folga</option>';
                    html+="        <option value='20'>Folga compensatória</option>";
                    html+="        <option value='21'>Folga de feriado</option>";
                    html+="        <option value='25'>Troca Folga</option>";
                    html+="        <option value='26'>Folga trabalhada</option>";
                    html+="        <option value='27'>Escala Errada</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Afastamento">';
                    html+="        <option value='5'>Afastamento</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Férias">';
                    html+="        <option value='6'>Férias</option>";
                    html+='    </optgroup>';
                    html+='    <optgroup label="Disciplina">';
                    html+="        <option value='8'>Disciplina</option>";
                    html+="        <option value='17'>Advertencia</option>";
                    html+="        <option value='19'>Demissão</option>";
                    html+="        <option value='22'>Justa causa</option>";
                    html+="        <option value='23'>Recisão indireta</option>";
                    html+="        <option value='24'>Suspensão</option>";
                    html+='    </optgroup>';
                    html+='</select>';
                    html+='</th>';
                    html+='<th  style="  text-align: center">';
                    html += '     <a onclick="fcSalvarApontamentoReloginho('+i+')"><i class="bi bi-save-fill" style="color:green;cursor:pointer;size:15px"></i></a> ';
                    html += '     <a onclick="abrirModalHistorico(' + i + ')"><i class="bi bi-ui-checks-grid" style="cursor: pointer;"></i></a>';
                    html += '     <a onclick="fcPontoDiario(' + i + ')"><i class="bi bi-camera-fill" style="cursor: pointer;"></i></a>';
                    html+='</th>';
                    
                }
                html+='</tr>';
            }
            html += "<tr >";
            html+= "    <td  style=' text-align: center' colspan=6>";
            html+= "      &nbsp;";
            html+= "    </td>";
            html += "    <td  style=' text-align: center'>";
            html += "       <b>D.T</b><br>"+v_dias_trabalhados;
            html += "    </td>";
            html+= "    <td  style=' text-align: center'>";
            html+= "       <input type='text' id='ht_total' name='ht_total' size='3' disabled maxlength='8' value='' >";
            html+= "    </td>";
            html+= "    <td  style=' text-align: center'>";
            html+= "       <input type='text' id='he_total' name='he_total' size='3' disabled maxlength='6' value='' onkeypress='mascara(this,horamask)'>";
            html+= "    </td>";
            html+= "    <td  style=' text-align: center'>";
            html+= "       <input type='text' id='hf_total' name='hf_total' size='3' disabled maxlength='6' value='' onkeypress='mascara(this,horamask)'>";
            html+= "    </td>";
            html+= "    <td  style=' text-align: center'>";
            html+= "      &nbsp;";
            html+= "    </td>";
           
            html+= "    <td  style=' text-align: center'>";
            html+= "   &nbsp;";
            html+= "    </td>";
            html+= "    <td  style=' text-align: center'>";
            html+= "   &nbsp;";
            html+= "    </td>";
            html+= "</tr>";
                            
            html+='</tbody>';
            html+=' </table>';
            html+=' </div>';
            html+='</div>';

            setTimeout(function () {
                PreencherAutomatico();
            }, 2000);
        }
        else{
            if(arrCarregar.message!=""){
                html+='<h1 class="pulsing-text">'+arrCarregar.message+'</h1>';
            }
            else{
                html+='<h1 class="pulsing-text">Este colaborador não tem registros!</h1>';
            }
            
        }
        
    }
    
    $("#grid_consulta_folha_ponto").html(html);
    
    if($("#ic_status_ponto_folha_pk").val()==1){
        $(".print_folha").removeClass("btn-danger")
                         .addClass("btn-outline-warning")
                         .prop("disabled", false);
        $("#tblResultado1 input, #tblResultado1 select, #tblResultado1 a").prop("disabled", true);
        $("#tblResultado1 a i.bi-solid.bi-check").each(function() {
            $(this).parent().removeAttr("onclick").css({
                "pointer-events": "none",
                "opacity": "0.5"
            });
        });        
        $("#tblResultado1 a i.bi-solid.bi-trash").each(function() {
            $(this).parent().removeAttr("onclick").css({
                "pointer-events": "none",
                "opacity": "0.5"
            });
        });        
        $("#text_folha_finalizada").html('<div class="row"><div class="col-md-4"></div><div class="col-md-4"><h6 class="pulsing-text">Está folha já está finalizada e não pode ser alterada!</h6></div></div>');
    }
    else{
        /*$(".print_folha").removeClass("btn-outline-warning")
                         .addClass("btn-danger")
                         .prop("disabled", true);*/
        $("#text_folha_finalizada").html("");
    }

}

function hmToMins(str){
    if (typeof str !== 'undefined' && str !== null) {
        const [hh, mm] = str.split(':').map(nr => Number(nr) || 0);
        return hh * 60 + mm;
    }
}

function converHrs(hr){

    horas = (hr / 60)|0;
    min = hr % 60; 
    
    if(horas < 0){
        horas = horas * -1;
    }

    if(min < 0){
        min = min * -1;
    }

    if(min < 10){
        min = "0"+min;
    }

    if(horas < 10){
        horas = "0"+horas;
    }

    return hora = horas +":"+ min;  
}

function habilitarCampos(l,tipo_ponto){
   
    $("#hr_ini_expediente" + l).prop("disabled", false);
    $("#hr_fim_expediente" + l).prop("disabled", false);
    $("#expediente_diario" + l).prop("disabled", false);
    $("#hr_ini_intervalo" + l).prop("disabled", false);
    $("#hr_fim_intervalo" + l).prop("disabled", false);
    $("#hr_excedentes" + l).prop("disabled", false);
    $("#hr_faltantes" + l).prop("disabled", false);
    $("#hr_trabalhadas" + l).prop("disabled", false);


    //FOLGA DE FERIADO
    if(tipo_ponto==21){
        abrirModalFeriados(l);
    }
    

    if(tipo_ponto==""){
        $("#hr_ini_expediente" + l).prop("disabled", true);
        $("#hr_fim_expediente" + l).prop("disabled", true);
        $("#expediente_diario" + l).prop("disabled", true);
        $("#hr_ini_intervalo" + l).prop("disabled", true);
        $("#hr_fim_intervalo" + l).prop("disabled", true);
        $("#hr_excedentes" + l).prop("disabled", true);
        $("#hr_faltantes" + l).prop("disabled", true);
        $("#hr_trabalhadas" + l).prop("disabled", true);
    }
    

}
function PreencherAutomatico() {
    try {
        var v_li = $("#totalLinhas").val(); 
        var tolerancia = 5; // 5 minutos de tolerância

        for (l = 0; l < v_li; l++) {
            var hr_ini_expediente = $("#hr_ini_expediente" + l).val() || "00:00";
            var hr_fim_expediente = $("#hr_fim_expediente" + l).val() || "00:00";
            var expediente_diario = $("#expediente_diario" + l).val() || "00:00";
            var turnos_pk = $("#turnos_pk").val();
            if (turnos_pk == 3) {
                var hr_ini_intervalo = $("#hr_ini_intervalo" + l).val() || "00:01";
                var hr_fim_intervalo = $("#hr_fim_intervalo" + l).val() || "01:00";
            }
            else{
                var hr_ini_intervalo = $("#hr_ini_intervalo" + l).val() || "0";
                var hr_fim_intervalo = $("#hr_fim_intervalo" + l).val() || "0";

            }
            var hr_excedentes = "00:00";
            var hr_faltantes = "00:00";

            if (hr_ini_expediente != "00:00" && hr_fim_expediente != "00:00" ) {
                // Converte para minutos sem aplicar a tolerância ainda
                var hr_ini_expediente_mins = hmToMins(hr_ini_expediente);
                var hr_fim_expediente_mins = hmToMins(hr_fim_expediente);
                var expediente_diario_mins = hmToMins(expediente_diario);

                var hr_ini_intervalo_mins = (hr_ini_intervalo && hr_ini_intervalo !== "00:00") ? hmToMins(hr_ini_intervalo) : 0;
                var hr_fim_intervalo_mins = (hr_fim_intervalo && hr_fim_intervalo !== "00:00") ? hmToMins(hr_fim_intervalo) : 0;

                var hr_trabalhadas_reais = 0;

                // Calcula as horas trabalhadas reais
                if (hr_ini_intervalo_mins > 0 && hr_fim_intervalo_mins > 0) {
                    var hr_trabalhadas_manha = hr_ini_intervalo_mins - hr_ini_expediente_mins;
                    var hr_trabalhadas_tarde = hr_fim_expediente_mins - hr_fim_intervalo_mins;
                    hr_trabalhadas_reais = hr_trabalhadas_manha + hr_trabalhadas_tarde;
                } else {
                    hr_trabalhadas_reais = hr_fim_expediente_mins - hr_ini_expediente_mins;
                }

                if (hr_trabalhadas_reais < 0) {
                    hr_trabalhadas_reais += 24 * 60; // Vira a noite
                }

                // Calcula a diferença entre o trabalhado e o esperado
                var diff = hr_trabalhadas_reais - expediente_diario_mins;

                // Aplica a tolerância na diferença
                if (diff > tolerancia) {
                    hr_excedentes = converHrs(diff);
                    hr_faltantes = "00:00";
                } else if (diff < -tolerancia) {
                    hr_faltantes = converHrs(Math.abs(diff));
                    hr_excedentes = "00:00";
                } else {
                    // Dentro da tolerância, zera faltas e excedentes
                    hr_faltantes = "00:00";
                    hr_excedentes = "00:00";
                }
                
                var hr_trabalhadas_final = converHrs(hr_trabalhadas_reais);

                $("#hr_faltantes" + l).val(hr_faltantes);
                $("#hr_excedentes" + l).val(hr_excedentes);
                $("#hr_trabalhadas" + l).val(hr_trabalhadas_final);

                // Alterar cores
                if (hmToMins(hr_faltantes) > 0) {
                    $("#hr_faltantes" + l).css("background-color", "#ED1B24").css("color", "white");
                } else {
                    $("#hr_faltantes" + l).css("background-color", "").css("color", "black");
                }
                $("#hr_excedentes" + l).css("background-color", "").css("color", "black");
                $("#hr_trabalhadas" + l).css("background-color", "").css("color", "black");
            } else {
                $("#hr_excedentes" + l).val("");
                $("#hr_faltantes" + l).val("");
                $("#hr_trabalhadas" + l).val("");
            }
        }
        calcTotal();
    } catch (e) {
        alert(e);
    }
}

function calculoOnchange(l) {
    try {
        var hr_ini_expediente = $("#hr_ini_expediente" + l).val() || "00:00";
        var hr_fim_expediente = $("#hr_fim_expediente" + l).val() || "00:00";
        var expediente_diario = $("#expediente_diario" + l).val() || "00:00";
        var hr_ini_intervalo = $("#hr_ini_intervalo" + l).val() || "0";
        var hr_fim_intervalo = $("#hr_fim_intervalo" + l).val() || "0";
        var hr_excedentes = "00:00";
        var hr_faltantes = "00:00";
        var tolerancia = 5; // 5 minutos de tolerância

        if (hr_ini_expediente != "00:00" && hr_fim_expediente != "00:00") {
            // Converte para minutos sem aplicar a tolerância ainda
            var hr_ini_expediente_mins = hmToMins(hr_ini_expediente);
            var hr_fim_expediente_mins = hmToMins(hr_fim_expediente);
            var expediente_diario_mins = hmToMins(expediente_diario);

            var hr_ini_intervalo_mins = (hr_ini_intervalo && hr_ini_intervalo !== "00:00") ? hmToMins(hr_ini_intervalo) : 0;
            var hr_fim_intervalo_mins = (hr_fim_intervalo && hr_fim_intervalo !== "00:00") ? hmToMins(hr_fim_intervalo) : 0;

            var hr_trabalhadas_reais = 0;

            // Calcula as horas trabalhadas reais
            if (hr_ini_intervalo_mins > 0 && hr_fim_intervalo_mins > 0) {
                var hr_trabalhadas_manha = hr_ini_intervalo_mins - hr_ini_expediente_mins;
                var hr_trabalhadas_tarde = hr_fim_expediente_mins - hr_fim_intervalo_mins;
                hr_trabalhadas_reais = hr_trabalhadas_manha + hr_trabalhadas_tarde;
            } else {
                hr_trabalhadas_reais = hr_fim_expediente_mins - hr_ini_expediente_mins;
            }

            if (hr_trabalhadas_reais < 0) {
                hr_trabalhadas_reais += 24 * 60; // Vira a noite
            }

            // Calcula a diferença entre o trabalhado e o esperado
            var diff = hr_trabalhadas_reais - expediente_diario_mins;

            // Aplica a tolerância na diferença
            if (diff > tolerancia) {
                hr_excedentes = converHrs(diff);
                hr_faltantes = "00:00";
            } else if (diff < -tolerancia) {
                hr_faltantes = converHrs(Math.abs(diff));
                hr_excedentes = "00:00";
            } else {
                // Dentro da tolerância, zera faltas e excedentes
                hr_faltantes = "00:00";
                hr_excedentes = "00:00";
            }
            
            var hr_trabalhadas_final = converHrs(hr_trabalhadas_reais);

            $("#hr_faltantes" + l).val(hr_faltantes);
            $("#hr_excedentes" + l).val(hr_excedentes);
            $("#hr_trabalhadas" + l).val(hr_trabalhadas_final);

            if (hmToMins(hr_faltantes) > 0) {
                $("#hr_faltantes" + l).css("background-color", "#ED1B24").css("color", "white");
            } else {
                $("#hr_faltantes" + l).css("background-color", "").css("color", "black");
            }
            $("#hr_excedentes" + l).css("background-color", "").css("color", "black");
            $("#hr_trabalhadas" + l).css("background-color", "").css("color", "black");

        } else {
            $("#hr_excedentes" + l).val("");
            $("#hr_faltantes" + l).val("");
            $("#hr_trabalhadas" + l).val("");
        }
        calcTotal();
    } catch (e) {
        alert(e);
    }
}


function calcTotal(){ 
    var total_hr_trabalhadas = 0;
    var toral_hr_faltantes = 0;
    var total_hr_excedentes = 0;

    var v_li = $("#totalLinhas").val();

    for (l = 0; l < v_li; l++) {

        var hr_excedentes = $("#hr_excedentes" + l).val();
        var hr_faltantes = $("#hr_faltantes" + l).val();
        var hr_trabalhadas = $("#hr_trabalhadas" + l).val();

        
        if(hr_excedentes == ""){
            hr_excedentes = "00:00";
        }
        if(hr_faltantes == ""){
            hr_faltantes = "00:00";
        }
        if(hr_trabalhadas == ""){
            hr_trabalhadas = "00:00";
        }
        
        
        hr_excedentes = hmToMins(hr_excedentes);
        hr_faltantes = hmToMins(hr_faltantes);
        hr_trabalhadas = hmToMins(hr_trabalhadas);
    

        total_hr_trabalhadas += hr_trabalhadas;
        toral_hr_faltantes += hr_faltantes;
        total_hr_excedentes += hr_excedentes;
    
    }

    total_hr_trabalhadas = converHrs(total_hr_trabalhadas);
    toral_hr_faltantes = converHrs(toral_hr_faltantes);
    total_hr_excedentes = converHrs(total_hr_excedentes);
  

    $("#ht_total").val(total_hr_trabalhadas);
    $("#he_total").val(total_hr_excedentes);
    $("#hf_total").val(toral_hr_faltantes);
    setTimeout(() => {
        fcGerarFolhaPonto();
    }, 3000);
    
}

//FUNÇÃO PARA FUNCIONAR O RELOAD DO APONTAMENTO
function fcSalvarApontamentoReloginho(index){

    var data_apontamento = $("#dt_hora_ponto"+index).val();
    var hr_ini_expediente = $("#hr_ini_expediente"+index).val();
    var hr_ini_intervalo = $("#hr_ini_intervalo"+index).val();
    var hr_fim_intervalo = $("#hr_fim_intervalo"+index).val();
    var hr_fim_expediente = $("#hr_fim_expediente"+index).val();
    var tipo_ponto_pk = $("#tipo_ponto_pk"+index).val();
    var hr_trabalhadas = $("#hr_trabalhadas"+index).val();
    var hr_excedentes = $("#hr_excedentes"+index).val();
    var hr_faltantes = $("#hr_faltantes"+index).val();

    //QUANDO FOR FALTA MOTIVO FALTA DEFAULT 3 = Atestado
    var motivo_falta_pk = 3
    //QUANDO FOR FALTA MOTIVO AFASTAMENTO DEFAULT 1 = MOTIVOS_MEDICOS
    var motivo_afastamento_pk = 1


    if(tipo_ponto_pk==""){
        sweetMensagem('warning', 'Informe o Apontamento!');
        return false;
    }
    //SALVAR 
    var objParametros = {
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
        "dt_apontamento": data_apontamento,
        "tipo_apontamento_pk": tipo_ponto_pk,
        "hr_ini_expediente": hr_ini_expediente,
        "hr_ini_intervalo": hr_ini_intervalo,
        "hr_fim_intervalo": hr_fim_intervalo,
        "hr_fim_expediente": hr_fim_expediente,
        "hr_trabalhadas": hr_trabalhadas,
        "hr_excedentes": hr_excedentes,
        "hr_faltantes": hr_faltantes,
        "motivo_falta_pk": motivo_falta_pk,
        "motivo_afastamento_pk": motivo_afastamento_pk
    };
    var arrEnviar = carregarController("agenda_colaborador_apontamento", "salvarApontamentoReloginho", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        fcGerarFolhaPonto();
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#grid_consulta_folha_ponto").html("");
        $("#grid_consulta_folha_ponto").append("");
        
        fcCarregarGridPontoFolha();
            
    }
    else{

        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }

}
function fcSalvarFolgaFeriado(index){

    var data_apontamento = $("#dt_hora_ponto"+index).val();
    var tipo_ponto_pk = $("#tipo_ponto_pk"+index).val();

    //QUANDO FOR FALTA MOTIVO FALTA DEFAULT 3 = Atestado
    var motivo_falta_pk = 3
    //QUANDO FOR FALTA MOTIVO AFASTAMENTO DEFAULT 1 = MOTIVOS_MEDICOS
    var motivo_afastamento_pk = 1

    var feriadoSelecionado = $('input[name="feriado_pk"]:checked').val();
    if(tipo_ponto_pk==""){
        sweetMensagem('warning', 'Informe o Apontamento!');
        return false;
    }
    //SALVAR 
    var objParametros = {
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
        "dt_apontamento": data_apontamento,
        "tipo_apontamento_pk": tipo_ponto_pk,
        "tipo_apontamento_pk": tipo_ponto_pk,
        "feriado_pk": feriadoSelecionado,
        "motivo_afastamento_pk": motivo_afastamento_pk
    };
    var arrEnviar = carregarController("agenda_colaborador_apontamento", "salvarApontamentoReloginho", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        fcGerarFolhaPonto();
        $("#grid_consulta_folha_ponto").html("");
        $("#grid_consulta_folha_ponto").append("");
        fcCarregarGridPontoFolha();
        $('#feriadosModal').modal('hide');
            
    }
    else{

        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }

}
function fcSalvarValidadoReloginho(index){
    var dt_hora_ponto = $("#dt_hora_ponto"+index).val();
    var verificado_pk = $("#verificado_pk"+index).val();
    var ic_verificado = $("#ic_validado"+index).val();


    
    utilsJS.jqueryConfirm('Validar?', 'Ao validar, você está de acordo com as informações apresentadas nesta data. ', function () {
        
           
        
            //SALVAR 
            var objParametros = {
                "pk": verificado_pk,
                "leads_pk": $('#leads_consulta_folha_pk').val(),
                "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
                "dt_hora_ponto": dt_hora_ponto,
                "ic_verificado": ic_verificado
            };
            var arrEnviar = carregarController("agenda_colaborador_apontamento", "salvarValidadoReloginho", objParametros);
            if (arrEnviar.status == true){
                // Reload datable
                utilsJS.toastNotify(true, arrEnviar.message);
                fcGerarFolhaPonto();
                $("#grid_consulta_folha_ponto").html("");
                $("#grid_consulta_folha_ponto").append("");
                
                fcCarregarGridPontoFolha();
                    
            }
            else{

                utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
            }
        
        

    });

    if(ic_verificado==1){
        $("#ic_validado" + index).prop("checked", false);
    }
    else{
        $("#ic_validado" + index).prop("checked", true);
    }
    

}
function fcExcluirApontamento(apontamento_pk){

    utilsJS.jqueryConfirm('Remover?', 'Deseja realmente remover o apontamento ? ', function () {
        //SALVAR 
        var objParametros = {
            "apontamento_pk": apontamento_pk
        };
        var arrEnviar = carregarController("agenda_colaborador_apontamento", "desabilitarApontamento", objParametros);
        if (arrEnviar.status == true){
            // Reload datable
            
            utilsJS.toastNotify(true, arrEnviar.message);
            $("#grid_consulta_folha_ponto").html("");
            $("#grid_consulta_folha_ponto").append("");
            fcCarregarGridPontoFolha();
                
        }
        else{
            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    });

}


function fcGerarFolhaPonto(){
    var formdata = new FormData();
    var v_li = $("#totalLinhas").val();
    var arrEnviar = []; 
   

    for (i = 0; i < v_li; i++) {
        
        var v_dt_hora_ponto = $("#dt_hora_ponto" + i).val();
        var v_hr_ini_expediente = ($("#hr_ini_expediente" + i).val() != "") ? $("#hr_ini_expediente" + i).val() : null;
        var v_hr_ini_intervalo = ($("#hr_ini_intervalo" + i).val() != "") ? $("#hr_ini_intervalo" + i).val() : null;
        var v_hr_fim_intervalo = ($("#hr_fim_intervalo" + i).val() != "") ? $("#hr_fim_intervalo" + i).val() : null;
        var v_hr_fim_expediente = ($("#hr_fim_expediente" + i).val() != "") ? $("#hr_fim_expediente" + i).val() : null;
        var v_hr_trabalhadas = ($("#hr_trabalhadas" + i).val() != "") ? $("#hr_trabalhadas" + i).val() : null;
        var v_hr_excedentes = ($("#hr_excedentes" + i).val() != "") ? $("#hr_excedentes" + i).val() : null;
        var v_hr_faltantes = ($("#hr_faltantes" + i).val() != "") ? $("#hr_faltantes" + i).val() : null;
        var v_tipo_ponto_pk = ($("#tipo_ponto_pk" + i).val() != "") ? $("#tipo_ponto_pk" + i).val() : null;
        var ic_verificado = $("#ic_validado"+i).val();
        var objParamentros = {
            "leads_pk": $('#leads_consulta_folha_pk').val(),
            "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
            "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
            //"ic_mes": $('#ic_consulta_folha_mes').val(),
            //"ic_ano": $('#ic_consulta_folha_ano').val(),
            "dt_inicio":$("#dt_ini_reloginho").val(),
            "dt_fim":$("#dt_fim_reloginho").val(),
            "ic_status_ponto_folha_pk": $('#ic_status_ponto_folha_pk').val(),
            "dt_hora_ponto": v_dt_hora_ponto,
            "hr_ini_expediente": v_hr_ini_expediente,
            "hr_ini_intervalo": v_hr_ini_intervalo,
            "hr_fim_intervalo": v_hr_fim_intervalo,
            "hr_fim_expediente": v_hr_fim_expediente,
            "hr_trabalhadas": v_hr_trabalhadas,
            "hr_excedentes": v_hr_excedentes,
            "hr_faltantes": v_hr_faltantes,
            "ic_status": ic_verificado,
            "tipo_ponto_pk": v_tipo_ponto_pk
        };

        arrEnviar.push(objParamentros);
    }

    var JsonEnviar = JSON.stringify(arrEnviar);
    formdata.append('arrDados', JsonEnviar);

    $.ajax({
        type: 'POST',
        url: '/api/ponto_folha/gerarFolhaPontoByRelogio',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                
            } catch (e) {
                utilsJS.sweetMensagem(false, "Ocorreu um erro na requisição <br /> Contate o suporte");
            }
        }
    }); 

}


function abrirModalHistorico(i){

    tblListarPontoDia.clear().destroy();
    listarHistorico(i)
    $("#janela_historico").modal("show");
}
function fcFecharModalHistorico(){
    $("#janela_historico").modal("hide");
}
function listarHistorico(i){
   

    if(i>=0){
        // Obter o valor atual
  
   
        var objParametros = {
            "leads_pk": $('#leads_consulta_folha_pk').val(),
            "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
            "agenda_colaborador_padrao_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
            "dt_ponto": $('#dt_hora_ponto_usa'+i).val(),
            "ic_historico": 1
        };

        var v_url = routes_api("ponto_folha", "listarModalPonto", objParametros);

        tblListarPontoDia = $("#tblListarPontoDia").DataTable({
            searching: false,
            paging: false,
            processing: false,
            serverSide: false,
            ajax: v_url,
            responsive: true,
            scrollY: true,
            language: {
                emptyTable: "Não existem Dados cadastrados"
            },
            order: [
                [0, "asc"]
            ],
            columns: [
                {
                    mRender: function (data, type, full) {
                        return full['indice'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['dt_cadastro'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['hora'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['tipo_apontamento'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['usuario_cadastro'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                },
                {
                    mRender: function (data, type, full) {
                        return full['ds_status'];
                    },
                    'orderable': true,
                    'searchable': false,
                    width: '80px'

                }
            ]

        });
    }
    else{
        tblListarPontoDia = $("#tblListarPontoDia").DataTable({
            searching: false,
            paging: false        
        });
    }
    
}

function abrirModalFeriados(i){
    $('#feriadosModal').modal('show');
    tblFeriado.clear().destroy();
    fcFeriados(i);
}

function fcFeriados(i){
    var objParametros = {
        //"ic_mes": $('#ic_consulta_folha_mes').val(),
        //"ic_ano": $('#ic_consulta_folha_ano').val(),
        "dt_inicio":$("#dt_ini_reloginho").val(),
        "dt_fim":$("#dt_fim_reloginho").val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
    };     
    
    var v_url = routes_api("feriado", "listarFeriadoRelogio", objParametros);

    //Trata a tabela
        tblFeriado = $('#tblFeriado').DataTable({
            searching: false,
            paging: false,
            scrollX: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: false,
            serverSide: true,
            ajax: v_url,
            responsive: true,
            language: {
                emptyTable: "Não existem Dados cadastrados"
            },
            order: [
                [0, "asc"]
            ],
            columns: [
                {
                    mRender: function (data, type, full) {
                        return "<input type='radio' class='checks' name='feriado_pk' value='"+full['pk']+"' onclick='fcSalvarFolgaFeriado("+i+")'>";
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['nome'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['data_feriado'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['tipo'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['estado'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                },
                {
                    mRender: function (data, type, full) {
                        return full['cidade'];
                    },
                    'orderable': true,
                    'searchable': false,
    
                }
            ]
    });           
    
}
function fcPontoDiario(i){
    
    $("#janela_ponto_diario").modal("show");
    $("#grid_ponto_diario").append("");
    $("#grid_ponto_diario").html("");
    var strRetorno = "";
    var objParametrosP = {
        "dt_ini": $('#dt_hora_ponto'+i).val(),
        "dt_final": $('#dt_hora_ponto'+i).val(),
        "leads_pk": $('#leads_consulta_folha_pk').val(),
        "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
        "agenda_colaborador_padrao_pk": $('#agenda_consulta_folha_colaborador_pk').val()
        
    };
    
    var arrCarregarP = carregarController("ponto", "reloginhoHistoricoPonto", objParametrosP);
    if (arrCarregarP.status == true){
        if(arrCarregarP.data!=null){
            if(arrCarregarP.data.length > 0){
                strRetorno+="    <tbody id='tblPontoDiario'>";
                strRetorno+="       <tr  align=center style='background-color:f5f5f5;border-color:b4b4b4;border-style: solid;'>";
                strRetorno+="           <th >Legenda Registro Ponto</th>";
                strRetorno+="           <th >Hr Registro Ponto</th>";
                strRetorno+="           <th >Local Registro Ponto</th>";
                strRetorno+="           <th >Distância</th>";
                strRetorno+="           <th >IMG Ficha</th>";
                strRetorno+="           <th >IMG Ponto</th>";
                strRetorno+="           <th >Status Facial</th>";
                strRetorno+="           <th >Ação</th>";
                strRetorno+="       </tr>";
                for(j=0; j < arrCarregarP.data.length ;j++){
                    var pk = "";
                    var ds_localizacao = "";
                    var ds_imagem_entrada = "";
                    var ds_legenda = "";
                    var ic_validacao_facial = "";
                    var ds_distancia_ponto = "";
                    pk = arrCarregarP.data[j]['pontos_pk'];

                    if(arrCarregarP.data[j]['ds_localizacao']!= null){
                        ds_localizacao = arrCarregarP.data[j]['ds_localizacao'];
                        
                    }


                    if(arrCarregarP.data[j]['ds_imagem_entrada']==""){
                        ds_imagem_entrada='';
                    }else{
                        ds_imagem_entrada = arrCarregarP.data[j]['ds_imagem_entrada'];
                    }
                    if (arrCarregarP.data[j]['ds_legenda'] == null) {
                        ds_legenda = "";
                    } else {
                        ds_legenda = arrCarregarP.data[j]['ds_legenda'];
                    }
                    if (arrCarregarP.data[j]['ds_registro_ponto'] == null) {
                        ds_registro_ponto = "";
                    } else {
                        ds_registro_ponto = arrCarregarP.data[j]['ds_registro_ponto'];
                    }
                    
                    if (arrCarregarP.data[j]['ds_distancia_ponto'] == null) {
                        ds_distancia_ponto = "";
                    } else {
                        ds_distancia_ponto = arrCarregarP.data[j]['ds_distancia_ponto'];
                    }
                    if (arrCarregarP.data[j]['ic_validacao_facial'] == null) {
                        ic_validacao_facial = "";
                    } else {
                        ic_validacao_facial = arrCarregarP.data[j]['ic_validacao_facial'];
                    }
                    
                    strRetorno += "<tr align=center style='border-style: solid;'>";
                    strRetorno+="<td  width='10%'>"+ds_legenda+"</td>";
                    strRetorno+="<td  width='10%'>"+ds_registro_ponto+"</td>";
                    strRetorno+="<td  width='10%'>"+ds_localizacao.substring(0, 50)+"</td>";
                    strRetorno+="<td  width='10%'>"+ds_distancia_ponto+"</td>";
                    strRetorno+='<td align=center  width="10%" class="galeria">'+arrCarregarP.data[j]['img_colaborador_cadastro']+'</td>';
                    strRetorno+='<td align=center  width="10%" class="galeria">'+arrCarregarP.data[j]['img_ponto']+'</td>';
                    if(ic_validacao_facial==1){
                        strRetorno+='<td align=center  width="10%" >Válidado</td>';
                        strRetorno+='<td align=center  width="10%" ><input type="checkbox" checked disabled></td>';
                        
                    }
                    else{
                        strRetorno+='<td align=center  width="10%" >Inválido</td>';
                        strRetorno+='<td align=center  width="10%" ><input type="checkbox" onclick="fcValidarImgPonto('+pk+','+i+')"></td>';
                    }

                    
                    strRetorno+="</tr>";
                }
            }
        }
        
    }
    if (strRetorno != "") {
        $("#grid_ponto_diario").append(strRetorno);
    } else {
        $("#grid_ponto_diario").append("");
    }
    
}
function fcFecharPontoDiario(){
    $("#grid_ponto_diario").append("");
    $("#janela_ponto_diario").modal("hide");
}

function fcValidarImgPonto(pk,i){
    utilsJS.jqueryConfirm('Validar?', 'Ao validar a foto, você está confirmando a comparação das imagens. ', function () {
        //SALVAR 
        var objParametros = {
            "pk": pk
        };
        var arrEnviar = carregarController("ponto", "validarImgPonto", objParametros);
        if (arrEnviar.status == true){
            // Reload datable
            utilsJS.toastNotify(true, arrEnviar.message);
            fcPontoDiario(i);
            $("#grid_consulta_folha_ponto").html("");
            $("#grid_consulta_folha_ponto").append("");
            fcCarregarGridPontoFolha();
                
        }
        else{

            utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
        }
    
    

    });
}
function fcPrintFolha(){
    var v_pk = $("#ponto_folha_pk").val();
    var v_leads_pk = $("#leads_consulta_folha_pk").val();
    var v_colaborador_pk = $("#colaborador_consulta_folha_pk").val();
    var url = 'receptivoPrint?nocache=' + new Date().getTime();
    
    
    /*if($("#ic_status_ponto_folha_pk").val()!=1){
        sweetMensagem('warning',"Você só pode imprimir se a folha estiver Finalizada.");
    }
    else{*/
        var objParametros = {
            "leads_pk":v_leads_pk,
            "pk":v_pk,
            "colaborador_pk":v_colaborador_pk,
            "reloginho":1
        };
        sendPost('ponto_folha',url,objParametros)
    //}
}
function fcPrintAllByPeriodoColaborador(){
    var v_colaborador_pk = $("#colaborador_pk_print_all").val();
    var dt_inicio_print = $("#dt_inicio_print").val();
    var dt_fim_print = $("#dt_fim_print").val();
    var url = 'receptivoPrintByColaboradorPeriodo?nocache=' + new Date().getTime();
    
    
    if(dt_inicio_print=="" && dt_fim_print==""){
        sweetMensagem('warning',"Informe o período corretamente.");
    }
    else{
        var objParametros = {
            "colaborador_pk":v_colaborador_pk,
            "dt_inicio":dt_inicio_print,
            "dt_fim":dt_fim_print,
            "reloginho":1
        };
        sendPost('ponto_folha',url,objParametros)
    }
}
function fcFinalizarFolha(){

    var formdata = new FormData();
    var v_li = $("#totalLinhas").val();
    var arrEnviar = []; 
    var folha_validada = 0;
    for (i = 0; i < v_li; i++) {
        if($("#ic_validado"+i).val()==1){
            folha_validada = 1;
        }
    }
    /*if(folha_validada==1){
        sweetMensagem('warning','Para finalizar uma folha, você precisa validar todos os campos')
    }
    else{*/
        utilsJS.jqueryConfirm('Finalizar Folha ?', 'Você está de acordo com todos os dados preenchidos ? ', function () {
            for (i = 0; i < v_li; i++) {
        
                var v_dt_hora_ponto = $("#dt_hora_ponto" + i).val();
                var v_hr_ini_expediente = ($("#hr_ini_expediente" + i).val() != "") ? $("#hr_ini_expediente" + i).val() : null;
                var v_hr_ini_intervalo = ($("#hr_ini_intervalo" + i).val() != "") ? $("#hr_ini_intervalo" + i).val() : null;
                var v_hr_fim_intervalo = ($("#hr_fim_intervalo" + i).val() != "") ? $("#hr_fim_intervalo" + i).val() : null;
                var v_hr_fim_expediente = ($("#hr_fim_expediente" + i).val() != "") ? $("#hr_fim_expediente" + i).val() : null;
                var v_hr_trabalhadas = ($("#hr_trabalhadas" + i).val() != "") ? $("#hr_trabalhadas" + i).val() : null;
                var v_hr_excedentes = ($("#hr_excedentes" + i).val() != "") ? $("#hr_excedentes" + i).val() : null;
                var v_hr_faltantes = ($("#hr_faltantes" + i).val() != "") ? $("#hr_faltantes" + i).val() : null;
                var v_tipo_ponto_pk = ($("#tipo_ponto_pk" + i).val() != "") ? $("#tipo_ponto_pk" + i).val() : null;
                //ZERO É VALIDADO
                
            
                var ic_verificado = 0;
                var objParamentros = {
                    "leads_pk": $('#leads_consulta_folha_pk').val(),
                    "colaborador_pk": $('#colaborador_consulta_folha_pk').val(),
                    "agenda_colaborador_pk": $('#agenda_consulta_folha_colaborador_pk').val(),
                    //"ic_mes": $('#ic_consulta_folha_mes').val(),
                    //"ic_ano": $('#ic_consulta_folha_ano').val(),
                    "dt_inicio":$("#dt_ini_reloginho").val(),
                    "dt_fim":$("#dt_fim_reloginho").val(),
                    "ic_status_ponto_folha_pk": 1,
                    "dt_hora_ponto": v_dt_hora_ponto,
                    "hr_ini_expediente": v_hr_ini_expediente,
                    "hr_ini_intervalo": v_hr_ini_intervalo,
                    "hr_fim_intervalo": v_hr_fim_intervalo,
                    "hr_fim_expediente": v_hr_fim_expediente,
                    "hr_trabalhadas": v_hr_trabalhadas,
                    "hr_excedentes": v_hr_excedentes,
                    "hr_faltantes": v_hr_faltantes,
                    "ic_status": ic_verificado,
                    "tipo_ponto_pk": v_tipo_ponto_pk
                };
    
                arrEnviar.push(objParamentros);
            }
    
            var JsonEnviar = JSON.stringify(arrEnviar);
            formdata.append('arrDados', JsonEnviar);
    
            $.ajax({
                type: 'POST',
                url: '/api/ponto_folha/finalizarFolhaByReloginho',
                data: formdata,
                processData: false,
                contentType: false,
                complete: function (response) {
                    try {
                        utilsJS.sweetMensagem(true, "Folha Finalizada com sucesso !");
                        $("#grid_consulta_folha_ponto").html("");
                        $("#grid_consulta_folha_ponto").append("");
                        setTimeout(() => {
                            fcCarregarGridPontoFolha();    
                        }, 8000);
                        
                        
                    } catch (e) {
                        utilsJS.sweetMensagem(false, "Ocorreu um erro na requisição <br /> Contate o suporte");
                    }
                }
            }); 
        }); 
    //}
}

function fcPegarPostoByColaboradorPorMesAno(){
    var objParametros = {
        //"ic_mes": $("#ic_consulta_folha_mes").val(),
        //"ic_ano":$("#ic_consulta_folha_ano").val(),
        "dt_inicio":$("#dt_ini_reloginho").val(),
        "dt_fim":$("#dt_fim_reloginho").val(),
        "colaborador_pk":$("#colaborador_consulta_folha_pk").val()
    };
    
    var arrCarregar = carregarController("agenda_colaborador_padrao", "pegarPostoByColaboradorPorMesAno", objParametros);

    if(arrCarregar.data.length > 0){
    
        $(".chzn-select").chosen('destroy');
    
        $("#agenda_consulta_folha_colaborador_pk").val(arrCarregar.data[0]['pk']);
        $("#leads_consulta_folha_pk").val(arrCarregar.data[0]['leads_pk']);
        $(".chzn-select").chosen({ allow_single_deselect: true });
        
        
    }
}
function fcCarregarReloginho(){
    if($('#agenda_consulta_folha_colaborador_pk').val()==""){
        sweetMensagem('warning','Esse colaborador não tem escala!');
        return false;
    }
    if($('#dt_ini_reloginho').val()=="" && $('#dt_fim_reloginho').val()==""){
        sweetMensagem('warning','Por favor preencha o perido!');
        return false;
    }
    else{
        var dtIni = parseDateBR($('#dt_ini_reloginho').val());
        var dtFim = parseDateBR($('#dt_fim_reloginho').val());

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
    if($('#leads_consulta_folha_pk').val()==""){
        sweetMensagem('warning','Preencha todos os campos!');
        return false;
    }
    if($('#colaborador_consulta_folha_pk').val()=="" || $('#colaborador_consulta_folha_pk').val()=="null" ||$('#colaborador_consulta_folha_pk').val()==null){
        sweetMensagem('warning', 'preencha todos os campos!');
        return false;
    }
    $("#grid_consulta_folha_ponto").html("");
    $("#grid_consulta_folha_ponto").append("");
    fcCarregarGridPontoFolha();
}

function formatDate(date) {
    var dia = String(date.getDate()).padStart(2, '0');
    var mes = String(date.getMonth() + 1).padStart(2, '0');
    var ano = date.getFullYear();
    return dia + '/' + mes + '/' + ano;
}
$(document).ready(function () {

    var arrCarregar = permissao("colaborador", "cons");
    if (arrCarregar.status != true){
        sweetMensagem('warning', 'Você não tem permissão!');
        return false;
    }
    $("#leads_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });
    //faz a carga inicial do grid.
    fcCarregarGenero();

    fcCarregarLeads();
    fcCarregarColaborador();
    fcCarregarQualificacao();
    
    
    
    
    $("#ds_cpf").keypress(function(){
       chama_mascara(this);
    });
    fcCarregarGrid();
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdPesquisar', fcPesquisar);
    $(document).on('click', '#cmdFecharPonto', fcFecharModalHistorico);
    $(document).on('click', '#cmdIncluir', fcIncluir); 
    $(document).on('click', '#cmdVoltar', fcVoltar); 
    $(document).on('click', '#cmdFecharPontoDiario', fcFecharPontoDiario); 
    $(document).on('click', '#print_folha_all', fcPrintAllByPeriodoColaborador); 
    $(document).on('click', '#cmdCarregarReloginho', fcCarregarReloginho); 
    
    var hoje = new Date();
    var primeiroDia = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
    var ultimoDia = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);

     $('#dt_ini_reloginho').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    }).datepicker('setDate', formatDate(primeiroDia));

    $("#dt_ini_reloginho").keypress(function () {
        mascara(this, mdata);
    });

    $('#dt_fim_reloginho').datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked"
    }).datepicker('setDate', formatDate(ultimoDia));

    $("#dt_fim_reloginho").keypress(function () {
        mascara(this, mdata);
    });
    $("#leads_consulta_folha_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        //fcCarregarColaboradorFolhaPonto();

        //PEGA A AGENDA DO LEAD

       
        
        $(".chzn-select").chosen({ allow_single_deselect: true });

       
    });
    $("#colaborador_consulta_folha_pk").change(function () {

        if($("#colaborador_consulta_folha_pk").val()!=""){
            var objParametros = {
                "leads_pk": $("#leads_consulta_folha_pk").val(),
                "colaboradores_pk":$("#colaborador_consulta_folha_pk").val()
            };
         
            var arrCarregar = carregarController("agenda_colaborador_padrao", "pegarPostoDeTrabalhoPorLeadEColaborador", objParametros);
      
            if(arrCarregar.data.length > 0){
           
                $("#agenda_consulta_folha_colaborador_pk").val(arrCarregar.data[0]['pk']);
                
                
            }
            $("#grid_consulta_folha_ponto").html("");
            $("#grid_consulta_folha_ponto").append("");
            //fcCarregarGridPontoFolha();
        }
    });
    /*$("#ic_consulta_folha_mes").change(function () {

        $("#grid_consulta_folha_ponto").html("");
        $("#grid_consulta_folha_ponto").append("");

        //AQUI PEGA O POSTO DE TRABALHO DO COLABORADOR DE ACORDO COM O MES 

        fcPegarPostoByColaboradorPorMesAno();
        fcCarregarGridPontoFolha();

    });
    $("#ic_consulta_folha_ano").change(function () {
     
        $("#grid_consulta_folha_ponto").html("");
        $("#grid_consulta_folha_ponto").append("");
        fcPegarPostoByColaboradorPorMesAno();
        fcCarregarGridPontoFolha();
    });*/
    fcFeriados(0);

    listarHistorico(-1);


     $('#dt_inicio_print').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_inicio_print").keypress(function(){
        mascara(this,mdata);
    });
     $('#dt_fim_print').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_fim_print").keypress(function(){
        mascara(this,mdata);
    });

    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();

    $(".chzn-select").chosen({ allow_single_deselect: true });

});
