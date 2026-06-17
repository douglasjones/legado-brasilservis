var tblAdicionarContratos;
function fcincluirContrato(conta_pk, j){
    
    $('#cliente_pk').val("");
    $('#posto_trabalho_pk').val("");
    $('#contrato_pk').val("");
    $('#dt_ini_contrato').val("");
    $('#dt_fim_contrato').val("");

    fcCarregarClientes();
    $('#cliente_pk').select2();

    fcCarregarLeads();
    $('#posto_trabalho_pk').select2();


    $('#dt_ini_contrato').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_ini_contrato").keypress(function(){
        mascara(this,mdata);
    });

    $('#dt_fim_contrato').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_fim_contrato").keypress(function(){
        mascara(this,mdata);
    });
    
    $("#cmdPesquisarContratos").click(function(){
        fcCarregarTblAdicionarContratos(conta_pk);
    });

    $("#cmdAdicionarContrato").click(function(){
        fcAdicionarContrato(conta_pk, j);
    });

    $("#cmdAdicionarContrato2").click(function(){
        fcAdicionarContrato(conta_pk, j);
    });

    $(document).on('click', '#cmdFecharModalContrato', fcFecharModalContrato);
    $(document).on('click', '#cmdFecharModalContrato2', fcFecharModalContrato);

    tblAdicionarContratos = $("#tblAdicionarContratos").DataTable({
        retrieve: true,
        paging: false
    });
    
    $('#event-modal').modal("show");
}

function fcFecharModalContrato(){
    tblAdicionarContratos.clear().destroy();
    $('#event-modal').modal("hide");
}

function fcCarregarLeads() {
    //Carrega os grupos
    var objParametros = {
        "ic_tipo_lead": 2,
        "ic_cliente": 2
    };

    var arrCarregar = carregarController("lead", "listarTodosPostTrabalho", objParametros);
    carregarComboAjax($("#posto_trabalho_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarClientes() {
    //Carrega os grupos
    var objParametros = {
        "ic_tipo_lead": 1,
        "ic_cliente": 1
    };

    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#cliente_pk"), arrCarregar, " ", "pk", "ds_lead");
}


function fcVertificarContratos(){
    var arrDados = [];

    var i = 0;
    var j = 0;
    $('#tblAdicionarContratos tbody tr').each(function () {
        addContratoPk = $("input[id='addContratoPk']");
        pkContrato = $("input[id='pkContrato']");

        if(addContratoPk.get(i).checked){
            arrDados[j] = [pkContrato.get(i).value];
            j++
        }
        i++

    })

    return arrDados;
}

function fcAdicionarContrato(conta_pk, j){
    try {

        var arrContratosPk = fcVertificarContratos()
        var h = $("#h"+j).val() + 1;

        for(var i=0; i < arrContratosPk.length; i++){
            var objParametros = {
                "contratos_pk": arrContratosPk[i]
            };
            var arrCarregar = carregarController("faturamento", "listarContratoFaturamento", objParametros); 
                var contratos_pk = arrCarregar.data[0].DadosContratos[0]['contratos_pk'];
                var leads_pai_pk = arrCarregar.data[0].DadosContratos[0]['leads_pai_pk'] != null ? arrCarregar.data[0].DadosContratos[0]['leads_pai_pk'] : arrCarregar.data[0].DadosContratos[0]['leads_pk'];    

                $("#tableCtfDados"+j+"1").append("<tr id='trCtfDados"+j+"1"+h+"' style='border-bottom: 1px groove;'</tr>");
                $("#trCtfDados"+j+"1"+h).append("<td width='35'>&nbsp</td>");
                $("#trCtfDados"+j+"1"+h).append("<td id='tdCtfDados"+j+"1"+h+"'></td>");
                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<i class='bi bi-circle-fill' style='font-size:18px; color:blue'></i>")
                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<input type='checkbox' onclick='fcAbrirDadosContrato("+j+","+contratos_pk+",1)' id='ContratoFixoLead"+j+"_"+contratos_pk+"_1' value='"+contratos_pk+"'> ");
                $("#tdCtfDados"+j+"1"+h).append("- Lead: "+arrCarregar.data[0].DadosContratos[0]['ds_lead']+" - Contrato: "+ contratos_pk);
                $("#tdCtfDados"+j+"1"+h).append("- <span id='ic_contrato"+contratos_pk+"' align='left'></span>");
                $("#tdCtfDados"+j+"1"+h).append("<div align='right'> <span id='vl_total_contrato"+j+"_"+h+"' align='right'>R$ </span> </div>");
                $("#tdCtfDados"+j+"1"+h).append("<div id='lineCtfDadosItem"+j+"_"+contratos_pk+"_1' style='display:none'></div>");
                $("#tdCtfDados"+j+"1"+h).append("<input class='contratos_pk' type='hidden' value='"+contratos_pk+"'>");
                $("#tdCtfDados"+j+"1"+h).append("<input class='vl_total_contrato' id='vl_total_contrato"+contratos_pk+"' type='hidden' value=''>");
                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pai_pk' type='hidden' value='"+leads_pai_pk+"'>");
                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pk' type='hidden' value='"+arrCarregar.data[0].DadosContratos[0]['leads_pk']+"'>");
                $("#tdCtfDados"+j+"1"+h).append("<input class='contas_pk' type='hidden' value='"+conta_pk+"'>");          
                $("#tdCtfDados"+j+"1"+h).append("<input class='ic_tipo_contrato' type='hidden' value='"+arrCarregar.data[0].DadosContratos[0]['ic_tipo_contrato']+"'>");          
                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_contratos_pk' type='hidden' value=''>");        
                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_itens_pk' type='hidden' value=''>");        
                $("#lineCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<table width='100%' id='tableCtfDadosItem"+j+"_"+contratos_pk+"_1' border='1'  class='table'></table>");
                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<tr></tr>");
                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr").append("<td></td>");
                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr td").append("<table id='containerCtfDadosItem"+j+"_"+contratos_pk+"_1' width='100%' border='0'></table>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<thead></thead>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr></tr>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead tr").append("<th colspan='4' style='text-align:center; margin:10px;background:#f5f5f5'>Composição do Contrato</th>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='10%'>Cód Contrato: "+arrCarregar.data[0].DadosContratos[0]['contratos_pk']+"</td>");
                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Usuário Cadastro: "+arrCarregar.data[0].DadosContratos[0]['ds_usuario_cadastro_contrato']+"</td>");
                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Dt Cadastro: "+arrCarregar.data[0].DadosContratos[0]['dt_cadastro']+"</td>");
                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Validade do Contrato: De "+arrCarregar.data[0].DadosContratos[0]['dt_inicio_contrato']+" Até "+arrCarregar.data[0].DadosContratos[0]['dt_fim_contrato']+"</td>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Razão Social: "+arrCarregar.data[0].DadosContratos[0]['ds_razao_social']+"</td>");
                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>CNPJ: "+arrCarregar.data[0].DadosContratos[0]['ds_cpf_cnpj']+"</td>");
                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>Endereço: "+arrCarregar.data[0].DadosContratos[0]['ds_endereco_lead']+"</td>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr><td width='40%' colspan=6><button class='btn btn-primary' onclick='fcAddLinha("+j+","+contratos_pk+", "+j+", "+h+")' align='left' >Adicionar Linha</button></td></tr><br>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<tbody></tbody>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tbody").append("<tr></tr>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tbody tr").append("<td colspan='4'></td>");
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tbody tr td").append("<table width='100%' border='1' class='table table-striped' id='tbContratoItens"+j+"_"+contratos_pk+"_1'></table>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1").append("<thead></thead>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead").append("<tr></tr>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Cód</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Serviço Prestado</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Qtde Colaboradores</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Carga HR Dia</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Escala</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Vl Unitário</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Vl Total</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 thead tr").append("<th>Ação</th>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1").append("<tbody></tbody>");
                var vl_total = 0.00; 
                var vl_contrato = 0.00; 

                if(arrCarregar.data[0].DadosContratosItens != null){
                    for(var k=0; k < arrCarregar.data[0].DadosContratosItens.length; k++){
                        if(arrCarregar.data[0].DadosContratos[0]['contratos_pk']==arrCarregar.data[0].DadosContratosItens[k]['contratos_pk']){
                            vl_total += parseFloat(arrCarregar.data[0].DadosContratosItens[k]['vl_total']);
                            vl_contrato = arrCarregar.data[0].DadosContratos[0]['vl_contrato'] == '0,00' ? vl_total : arrCarregar.data[0].DadosContratos[0]['vl_contrato'];
                            $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tbody").append("<tr id='trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1'></tr>")
                            $("#trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1").append("<td>"+arrCarregar.data[0].DadosContratosItens[k]['contratos_itens_pk']+"<input id='faturamento_contratos_itens_pk["+contratos_pk+"]' type='hidden' value=''></td>")
                                                                        .append("<td>"+arrCarregar.data[0].DadosContratosItens[k]['ds_servico_prestado']+"<input id='produto_servico_pk["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[0].DadosContratosItens[k]['produto_servico_pk']+"'></td>")
                                                                        .append("<td><input type='number' class='n_qtde_colaborador["+contratos_pk+"]' id='n_qtde_colaborador_"+contratos_pk+"_"+k+"' onchange='fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")' value='"+arrCarregar.data[0].DadosContratosItens[k]['n_qtde_colaborador']+"'></td>")
                                                                        .append("<td>"+arrCarregar.data[0].DadosContratosItens[k]['ds_carga_horaria_dia']+"<input id='ds_periodo["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[0].DadosContratosItens[k]['ds_carga_horaria_dia']+"'></td>")
                                                                        .append("<td>"+arrCarregar.data[0].DadosContratosItens[k]['ds_escala']+"<input id='n_qtde_dias_semana["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[0].DadosContratosItens[k]['ds_escala']+"'></td>")
                                                                        .append("<td><input type='text' class='vl_unitario_produtos_servicos["+contratos_pk+"]' id='vl_unit_"+contratos_pk+"_"+k+"'  onchange='fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")'  onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[0].DadosContratosItens[k]['vl_unit'])+"'></td>")
                                                                        .append("<td><input type='text'  id='vl_total_item_"+contratos_pk+"_"+k+"' class='vl_total_item_"+contratos_pk+"' onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[0].DadosContratosItens[k]['vl_total'])+"'></td>")
                                                                        .append("<td></td>");
                            
                        }
                    }
                }
                var dt_faturamento = arrCarregar.data[0].DadosContratos[0]['dt_faturamento'] != 'null' ? ' ' : arrCarregar.data[0].DadosContratos[0]['dt_faturamento'];
                var dt_vencimento = arrCarregar.data[0].DadosContratos[0]['dt_vencimento'] != 'null' ? ' ' : arrCarregar.data[0].DadosContratos[0]['dt_vencimento'];

                $("#vl_total_contrato"+j+"_"+h).append(float2moeda(vl_contrato))
                $("#vl_total_contrato"+contratos_pk).val(vl_contrato)
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1").append("<tfoot></tfoot>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='totalContratos"+j+"_"+contratos_pk+"_1'></tr>");
                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Total Contratos</td>");
                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' id='vl_contrato"+arrCarregar.data[0].DadosContratos[0]['contratos_pk']+"' onkeypress='mascara(this, moeda)' value='"+float2moeda(vl_contrato)+"'></td>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtFaturamento"+j+"_"+contratos_pk+"_1'></tr>");
                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Faturamento</td>");
                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' onkeypress='mascara(this, mdata)' id='dt_faturamento' value='"+dt_faturamento+"'></td>");
                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtVencimento"+j+"_"+contratos_pk+"_1'></tr>");
                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Vencimento</td>");
                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input onkeypress='mascara(this, mdata)' id='dt_vencimento' value='"+dt_vencimento+"'></td>");
        
            
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='textarea"+j+"_"+contratos_pk+"_1'></tr>");
                var obs_faturamento_contrato = arrCarregar.data[0].DadosContratos[0]['obs_faturamento_contrato'] == null? " ":arrCarregar.data[0].DadosContratos[0]['obs_faturamento_contrato'];
                var obs_lancamento = arrCarregar.data[0].DadosContratos[0]['obs_lancamento'] == null? " ":arrCarregar.data[0].DadosContratos[0]['obs_lancamento'];
                var obs_corpo_nota = arrCarregar.data[0].DadosContratos[0]['obs_corpo_nota'] == null? " ":arrCarregar.data[0].DadosContratos[0]['obs_corpo_nota'];
            
                $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='2'><label>Observação Faturamento</label><textarea rows='4' class='obs_faturamento' cols='50' value=''>"+obs_faturamento_contrato+"</textarea></td>");
                $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Financeiro</label><textarea rows='4' class='obs_lancamento' cols='50' value=''>"+obs_lancamento+"</textarea></td>");
                $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Corpo da Nota Fiscal</label><textarea rows='4' class='obs_corpo_nota' cols='58' value=''>"+obs_corpo_nota+"</textarea></td>");
            
                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='StatusCtfDadosItem"+j+"_"+contratos_pk+"_1' align='right'></tr>");
                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");
                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<input type='hidden' class='ic_status_validacao' id='ic_status_validacao"+arrCarregar.data[0].DadosContratos[0]['contratos_pk']+"'>");
                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<select class='ic_status' id='ic_status"+arrCarregar.data[0].DadosContratos[0]['contratos_pk']+"'><option></option>\n\
                                                                                    <option value='1'>Validado</option>\n\
                                                                                    <option value='2'>Pendente Análise</option>\n\
                                                                                    <option value='3'>Não Faturar</option>\n\
                                                                            </select>"); 
                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("&nbsp;<button height='10px' class='btn btn-success' onclick='fcValidarContrato("+arrCarregar.data[0].DadosContratos[0]['contratos_pk']+", "+vl_contrato+")' align='right'>Aplicar</button>"); 
                $('#ic_status'+contratos_pk).val(arrCarregar.data[0].DadosContratos[0]['ic_status'])
                fcValidarContrato(contratos_pk, vl_contrato);
    
                h++;
        }

        tblAdicionarContratos.clear().destroy();
        $('#event-modal').modal("hide");
        
    } catch (error) {
        console.log(error)
    }
                        

}

function fcCarregarTblAdicionarContratos(conta_pk) {
    
    if($("#dt_ini_contrato").val()==""){
        $("#alert_dt_ini_contrato").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_ini_contrato").slideUp(500);
        });
        $('#dt_ini_contrato').focus();
        return false;
    }
    
    
    if($("#dt_fim_contrato").val()==""){
        $("#alert_dt_fim_contrato").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_fim_contrato").slideUp(500);
        });
        $('#dt_fim_contrato').focus();
        return false;
    }

    tblAdicionarContratos.clear().destroy();

    var objParametros = {
        "empresas_pk": conta_pk,
        "dt_ini_contrato": $("#dt_ini_contrato").val(),
        "dt_fim_contrato": $("#dt_fim_contrato").val(),
        "cliente_pk": $("#cliente_pk").val(),
        "posto_trabalho_pk": $("#posto_trabalho_pk").val(),
        "faturamento_pk": $("#faturamento_pk").val(),
        "contrato_pk": $("#contrato_pk").val()
    };

    var v_url = routes_api("faturamento", "listarContratos", objParametros);

    tblAdicionarContratos = $("#tblAdicionarContratos").DataTable({
        searching: false,
        paging: true,
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
                    var buttonAdd = "<a class='function_add'><input type='checkbox' id='addContratoPk'><input type='hidden' id='pkContrato' value='"+full['pk']+"'></a>";

                    return buttonAdd;
                },
                'orderable': false,
                'searchable': false
            },
            {
                mRender: function (data, type, full) {
                    return full['n_contador'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['pk'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_tipo_contrato'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_lead'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['ds_identificacao_area'];
                },
                'orderable': true,
                'searchable': false

            },
            {
                mRender: function (data, type, full) {
                    return full['vl_contrato'];
                },
                'orderable': true,
                'searchable': false

            }
        ]

    });

}