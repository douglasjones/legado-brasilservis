function fcCarregarDadosFaturamento(){
    try {
        var faturamento_pk = $('#faturamento_pk').val();
        var objParametros = {
            "pk": faturamento_pk
        };
        var arrCarregar = carregarController("faturamento", "listarDadosFaturamento", objParametros);

        if (arrCarregar.status == true) {  
            //Dados do Faturamento
    
            $('#dsUsuarioCadastro').append(arrCarregar.data[0].ds_usuario_cadastro);
            $('#dtCadastro').append(arrCarregar.data[0].dt_cadastro);
            $('#dsUsuarioAtualizacao').append(arrCarregar.data[0].ds_usuario_atualizacao);
            $('#dtAtualizacao').append(arrCarregar.data[0].dt_ult_atualizacao);
            $('#pkFaturamento').append(arrCarregar.data[0].pk);
            $('#periodoFaturamento').append("De "+arrCarregar.data[0].dt_faturamento_ini+" Até "+arrCarregar.data[0].dt_faturamento_fim);
            $('#dsStatusFaturamento').append(arrCarregar.data[0].ds_usatus_faturamento);
            $('#dsObs').append(arrCarregar.data[0].obs);
            //Dados Contratos
            var dsContratoFixo = "";
            if(arrCarregar.data[0].ic_contrato_fixo==1){
                dsContratoFixo = "Contratos Fixos";
            }
            var dsContratoAditivos = "";
            if(arrCarregar.data[0].ic_contrato_aditivo==1){
                dsContratoAditivos = "Contratos Aditivos";
            }
            var dsContratoExtras = "";
            if(arrCarregar.data[0].ic_contrato_servico_extra==1){
                dsContratoExtras = "Contratos Aditivos";
            }
            $('#dsTiposContratos').append(dsContratoFixo+"<br>"+dsContratoAditivos+"<br>"+dsContratoExtras );
    
            //Dados Emissões
            var dsFaturas = "";
            if(arrCarregar.data[0].ic_gerar_fatura==1){
                dsFaturas = "Gerar Faturas";
            }
            var dsNF = "";
            if(arrCarregar.data[0].ic_gerar_nota_fiscal==1){
                dsNF = "Gerar Notas Fiscais";
            }    
            var dsBoleto = "";
            if(arrCarregar.data[0].ic_gerar_boleto==1){
                dsBoleto = "Gerar Boletos";
            } 
            $('#dsEmissoes').append(dsFaturas+"<br>"+dsNF+"<br>"+dsBoleto);
            
            //Dados Contas
            var dsContas = "";
            var vhtml = "";
            for(var i=0; i < arrCarregar.data.length; i++){  
                $('#composicao_faturamento').append("<div id='container"+i+"' class='row'></div>");  
                $("#container"+i).append("<div id='margin"+i+"' class='col-1'>&nbsp;</div>"); 
                $("#container"+i).append("<div id='size"+i+"' class='col-10'></div>"); 
                $("#size"+i).append("<table width='100%' id='container_table"+i+"'></table>"); 
                for(var j=0; j < arrCarregar.data[i].DadosContas.length; j++){
                    $('#dsContas').append(arrCarregar.data[i].DadosContas[j]['ds_conta']+"<br>");
                    $("#container_table"+j).append("<tr id='tr"+j+"'></tr>"); 
                    $("#tr"+j).append("<td id='td"+j+"'></td>"); 
                    $("#td"+j).append("<i class='bi bi-node-plus' style='font-size:18px; color:blue'></i>&nbsp;")  
                    $("#td"+j).append("<input type='checkbox' onclick='fcMarcaConta("+j+")' id='conta"+j+"' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'> - "+arrCarregar.data[i].DadosContas[j]['ds_conta']);  
                    //CONTRATOS FIXOS
                    if(arrCarregar.data[0].ic_contrato_fixo==1){
                        $("#td"+j).append("<div id='lineContratoFixo"+j+"' style='display:none'></div>");
                        $("#lineContratoFixo"+j).append("<table width='100%' id='tableContratoFixo"+j+"'></table>");  
                        $("#tableContratoFixo"+j).append("<tr></tr>");
                        $("#tableContratoFixo"+j+" tr").append("<td width='25'>&nbsp</td>");
                        $("#tableContratoFixo"+j+" tr").append("<td id='tdContratoFixo"+j+"'></td>");
                        $("#tdContratoFixo"+j).append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-play-fill' style='font-size:18px; color:blue''></i>&nbsp;<input type='checkbox' onclick='fcMacarTipoContrato("+j+",1)' id='tipoContratoFixoConta"+j+"' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'> - Contratos Fixos");  
                        $("#tdContratoFixo"+j).append("<div id='lineCtfDados"+j+"1' style='display:none'></div>");  
                        $("#lineCtfDados"+j+"1").append("<table width='100%' id='tableCtfDados"+j+"1'><table>");
                        var vl_total_faturamento = 0.00;
                        for(var h=0; h < arrCarregar.data[i].DadosContratos.length; h++){
                            if(arrCarregar.data[i].DadosContratos[h]['ic_tipo_contrato']==1 && arrCarregar.data[i].DadosContratos[h]['contas_contratos_pk'] == arrCarregar.data[i].DadosContas[j]['contas_pk'] ){ 
                                var contratos_pk = arrCarregar.data[i].DadosContratos[h]['contratos_pk'];
                                var leads_pai_pk = arrCarregar.data[i].DadosContratos[h]['leads_pai_pk'] != null ? arrCarregar.data[i].DadosContratos[h]['leads_pai_pk'] : arrCarregar.data[i].DadosContratos[h]['leads_pk'];
                                var ds_cliente = arrCarregar.data[i].DadosContratos[h]['ds_cliente'] != null ? arrCarregar.data[i].DadosContratos[h]['ds_cliente'] : "";
                                var ds_endereco_lead = arrCarregar.data[i].DadosContratos[h]['ds_endereco_lead'] != null ? arrCarregar.data[i].DadosContratos[h]['ds_endereco_lead'] : "";
                                /*validarCnpj = arrValidarCnpj(arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj'])
                                console.log(validarCnpj);*/
                                $("#tableCtfDados"+j+"1").append("<tr id='trCtfDados"+j+"1"+h+"'></tr>");
                                $("#trCtfDados"+j+"1"+h).append("<td>&nbsp</td>");
                                $("#trCtfDados"+j+"1"+h).append("<td id='tdCtfDados"+j+"1"+h+"'></td>");
                                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<i class='bi bi-circle-fill' style='font-size:18px; color:blue'></i>");
                                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<input type='checkbox' onclick='fcAbrirDadosContrato("+j+","+contratos_pk+",1)' id='ContratoFixoLead"+j+"_"+contratos_pk+"_1' value='"+contratos_pk+"'> ");
                                $("#tdCtfDados"+j+"1"+h).append("- Cliente: "+ds_cliente + " - Posto de Trabalho: "+arrCarregar.data[i].DadosContratos[h]['ds_lead']+" - Contrato: "+ contratos_pk);
                                $("#tdCtfDados"+j+"1"+h).append("- <span id='ic_contrato"+contratos_pk+"' align='left'></span></div>");
                                $("#tdCtfDados"+j+"1"+h).append("<div align='right'> <span id='vl_total_contrato"+j+"_"+h+"' class='titulo_total_contrato"+contratos_pk+"' align='right'>R$ </span> </div>");
                                //$("#tdCtfDados"+j+"1"+h).append("<span style='justify-content: space-between;' id='vl_total_contrato"+j+"_"+h+"' class='titulo_total_contrato"+contratos_pk+"' align='right'>R$ </span>");
                                $("#tdCtfDados"+j+"1"+h).append("<div id='lineCtfDadosItem"+j+"_"+contratos_pk+"_1' style='display:none'></div>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='contratos_pk' type='hidden' value='"+contratos_pk+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='vl_total_contrato' id='vl_total_contrato"+contratos_pk+"' type='hidden' value=''>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pai_pk' type='hidden' value='"+leads_pai_pk+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pk' type='hidden' value='"+arrCarregar.data[i].DadosContratos[h]['leads_pk']+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='contas_pk' type='hidden' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'>");          
                                $("#tdCtfDados"+j+"1"+h).append("<input class='ic_tipo_contrato' type='hidden' value='"+arrCarregar.data[i].DadosContratos[j]['ic_tipo_contrato']+"'>");     
                                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_contratos_pk' type='hidden' value=''>");       
                                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_itens_pk' type='hidden' value=''>");       
                                $("#lineCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<table width='100%' id='tableCtfDadosItem"+j+"_"+contratos_pk+"_1' border='1'  class='table'></table>");  
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<tr></tr>");
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr").append("<td></td>");
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr td").append("<table id='containerCtfDadosItem"+j+"_"+contratos_pk+"_1' width='100%'></table>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<thead></thead>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr></tr>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead tr").append("<th colspan='4' style='text-align:center; margin:10px;background:#f5f5f5'>Composição do Contrato</th>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='10%'>Cód Contrato: "+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Usuário Cadastro: "+arrCarregar.data[i].DadosContratos[h]['ds_usuario_cadastro_contrato']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Dt Cadastro: "+arrCarregar.data[i].DadosContratos[h]['dt_cadastro']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Validade do Contrato: De "+arrCarregar.data[i].DadosContratos[h]['dt_inicio_contrato']+" Até "+arrCarregar.data[i].DadosContratos[h]['dt_fim_contrato']+"</td>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Razão Social: "+arrCarregar.data[i].DadosContratos[h]['ds_razao_social']+"</td>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>CNPJ: "+arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj']+"</td>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>Endereço: "+ds_endereco_lead+"</td>");
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
                                for(var k=0; k < arrCarregar.data[i].DadosContratosItens.length; k++){
                                    if(arrCarregar.data[i].DadosContratos[h]['contratos_pk']==arrCarregar.data[i].DadosContratosItens[k]['contratos_pk']){
                                        vl_total += parseFloat(arrCarregar.data[i].DadosContratosItens[k]['vl_total']);
                                        var vl_contrato = arrCarregar.data[i].DadosContratos[h]['vl_contrato'] == ('0,00' || '0') ? vl_total : arrCarregar.data[i].DadosContratos[h]['vl_contrato'];
                                        $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tbody").append("<tr id='trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1'></tr>")
                                        $("#trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1").append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['contratos_itens_pk']+"<input id='faturamento_contratos_itens_pk["+contratos_pk+"]' type='hidden' value=''></td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_servico_prestado']+"<input id='produto_servico_pk["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['produto_servico_pk']+"'></td>")
                                                                                    .append("<td><input type='number' class='n_qtde_colaborador["+contratos_pk+"]' id='n_qtde_colaborador_"+contratos_pk+"_"+k+"' onchange='fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")'  value='"+arrCarregar.data[i].DadosContratosItens[k]['n_qtde_colaborador']+"'></td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_carga_horaria_dia']+"<input id='ds_periodo["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['ds_carga_horaria_dia']+"'></td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_escala']+"<input id='n_qtde_dias_semana["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['ds_escala']+"'></td>")
                                                                                    .append("<td><input type='text' class='vl_unitario_produtos_servicos["+contratos_pk+"]' id='vl_unit_"+contratos_pk+"_"+k+"' onchange=''fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")'   onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[i].DadosContratosItens[k]['vl_unit'])+"'></td>")
                                                                                    .append("<td><input type='text' id='vl_total_item_"+contratos_pk+"_"+k+"' class='vl_total_item_"+contratos_pk+"'  onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[i].DadosContratosItens[k]['vl_total'])+"'></td>")
                                                                                    .append("<td></td>");
                                        
                                    }
                                }
                                $("#vl_total_contrato"+j+"_"+h).append(float2moeda(vl_contrato))
                                $("#vl_total_contrato"+contratos_pk).val(vl_contrato)
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1").append("<tfoot></tfoot>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='totalContratos"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Total Contratos</td>");
                                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' id='vl_contrato"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"' onkeypress='mascara(this, moeda)' value='"+float2moeda(vl_contrato)+"'></td>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtFaturamento"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Faturamento</td>");
                                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' onkeypress='mascara(this, mdata)' id='dt_faturamento'></td>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtVencimento"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Vencimento</td>");
                                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input onkeypress='mascara(this, mdata)' class='dt_vencimento' id='dt_vencimento'></td>");
                                
                                if($("#ic_contrato"+contratos_pk).html()=='validado'){
                                    vl_total_faturamento += parseFloat(vl_contrato);
                                }
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='textarea"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='2'><label>Observação Faturamento</label><textarea rows='4' class='obs_faturamento' cols='50' value=''></textarea></td>");
                                $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Financeiro</label><textarea rows='4' class='obs_lancamento' cols='50' value=''></textarea></td>");
                                //$("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Corpo da Nota Fiscal</label><textarea rows='4' class='obs_corpo_nota' cols='58' value=''></textarea></td>");
        
                                if(arrCarregar.data[0].ic_gerar_nota_fiscal==1){
                                    $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='NfseCtfDadosItem"+j+"_"+contratos_pk+"_1' align='left'></tr>");
                                    $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");
                                    $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<b>Gerar Nota Fiscal de Serviços Eletrônica? </b><select style='width:7em' class='fazer_nfse'  id='fazer_nfse"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"' onchange='fcFaturamentoNFSE("+j+", "+contratos_pk+", "+arrCarregar.data[i].DadosContas[j]['contas_pk']+", "+leads_pai_pk+", &#39;"+arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj']+"&#39;, &#39;&#39;)'><option></option>\n\
                                                                                                        <option value='1'>Sim</option>\n\
                                                                                                        <option value='2'>Não</option>\n\
                                                                                                </select><br>"); 
                                    $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<hr style='border-bottom: solid black 1px' >");
                                                                                            
                                    $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='formNotaFiscal"+j+"_"+contratos_pk+"_1' align='left'></tr>");
                                }
                                

                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='StatusCtfDadosItem"+j+"_"+contratos_pk+"_1' align='right'></tr>");
                                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");

                                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<input type='hidden' class='ic_status_validacao' id='ic_status_validacao"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"'>");
                                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<select class='ic_status'  id='ic_status"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"'><option></option>\n\
                                                                                                    <option value='1'>Validado</option>\n\
                                                                                                    <option value='2'>Pendente Análise</option>\n\
                                                                                                    <option value='3'>Não Faturar</option>\n\
                                                                                            </select>"); 
                                $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("&nbsp;&nbsp;<button height='10px' class='btn btn-success' onclick='fcValidarContrato("+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+")' align='right'>Aplicar</button>"); 

                            }
                            
                        }
                    }
                }
            }  
            $("#vl_total_geral_faturamento").append(vl_total_faturamento)
            
        }        
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

function fcFaturamentoNFSE(j, contratos_pk, contas_pk, leads_pk, ds_cpf_cnpj, faturamento_itens_pk){
    try {
        $("#formNotaFiscal"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");
        if($('#fazer_nfse'+contratos_pk).val() == 1){
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><h5>Nota Fiscal de Serviço Eletrônica</h5><br>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<div class='row'>\n\
                                                                        <div class='col-md-6'>\n\
                                                                            <label>Prestador de Serviço:</label>\n\
                                                                            <input type='hidden' id='pk_nota"+contratos_pk+"' class='form-control form-control-sm pk_nota' name='pk_nota'>\n\
                                                                            <input type='hidden' id='descricao_nfse_pk"+contratos_pk+"' class='form-control form-control-sm pk_nota' name='pk_nota'>\n\
                                                                            <select id='prestador_pk"+contratos_pk+"' class='form-control form-control-sm prestador_pk' name='prestador_pk' disabled>\n\
                                                                                <option></option>\n\
                                                                            </select>\n\
                                                                        </div>\n\
                                                                    </div>");                                                   
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><h6>Tomador de Serviços<h6><hr style='height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;'>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<div class='row'>\n\
                                                                        <div class='col-md-4'>\n\
                                                                            <label>Razão Social:</label>\n\
                                                                            <select id='tomador_pk"+contratos_pk+"' class='form-control form-control-sm tomador_pk' name='tomador_pk' disabled>\n\
                                                                                <option></option>\n\
                                                                            </select>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-3'>\n\
                                                                            <label>CNPJ:</label>\n\
                                                                            <input id='ds_cpf_cnpj"+contratos_pk+"' class='form-control form-control-sm ds_cpf_cnpj' name='ds_cpf_cnpj' disabled>\n\
                                                                        </div>\n\
                                                                    </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><h6>Código de Serviços<h6><hr style='height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;'>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<div class='row'>\n\
                                                                        <div class='col-md-6'>\n\
                                                                            <label>Lista do Serviço para Consulta:</label>\n\
                                                                            <select id='listaServicoConsulta"+contratos_pk+"' class='form-control form-control-sm listaServicoConsulta' name='listaServicoConsulta'>\n\
                                                                                <option></option>\n\
                                                                            </select>\n\
                                                                        </div>\n\
                                                                    </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<div class='row'>\n\
                                                                        <div class='col-md-2'>\n\
                                                                            <label>Código do Serviço:</label>\n\
                                                                            <input id='codigo_servico_pk"+contratos_pk+"' class='form-control form-control-sm codigo_servico_pk' name='codigo_servico_pk'>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-1'>\n\
                                                                            <br>\n\
                                                                            <input type='button' class='btn btn-primary btn-sm' id='cmdInformacoesSevicos"+contratos_pk+"' value='>>'>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-4'>\n\
                                                                            <label>Descrição do Serviço:</label>\n\
                                                                            <input id='ds_descricao_servico"+contratos_pk+"' class='form-control form-control-sm ds_descricao_servico' name='ds_descricao_servico'>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-4'>\n\
                                                                            <label>Aliquota:</label>\n\
                                                                            <input id='vl_aliquota"+contratos_pk+"' class='form-control form-control-sm vl_aliquota' name='vl_aliquota'>\n\
                                                                        </div>\n\
                                                                    </div>");
            /* $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><label><b>Detalhamento de Serviço para Corpo da NFSE:</b></label>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<div class='row'>\n\
                                                                        <div class='col-md-6'>\n\
                                                                            <select id='faturamento_nfse_servicos_pk"+contratos_pk+"' class='form-control form-control-sm faturamento_nfse_servicos_pk' name='faturamento_nfse_servicos_pk'>\n\
                                                                                <option></option>\n\
                                                                            </select>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-1'>\n\
                                                                            <input type='button' class='btn btn-primary btn-sm' id='cmdAbrirTextarea"+contratos_pk+"' value='>>'>\n\
                                                                        </div>\n\
                                                                        <div class='col-md-5'>\n\
                                                                            <textarea class='form-control form-control-sm' id='textarea"+contratos_pk+"'></textarea>\n\
                                                                        </div>\n\
                                                                    </div>");*/
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-3'>\n\
                                                                                <label>Vl. Total do Serviço R$:</label>\n\
                                                                                <input id='vl_total_servico"+contratos_pk+"' class='form-control form-control-sm vl_total_servico' name='vl_total_servico' disabled>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><h6>Impostos<h6><hr style='height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;'>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-3'>\n\
                                                                                <label>ISS Retido Pelo Tomador:</label><br>\n\
                                                                                <input type='radio' id='iss_retido1"+contratos_pk+"' class='iss_retido1' name='iss_retido' value='1' />\n\
                                                                                <label>Sim</label>&nbsp;&nbsp;&nbsp;\n\
                                                                                <input type='radio' id='iss_retido2"+contratos_pk+"' class='iss_retido2' name='iss_retido' value='2' />\n\
                                                                                <label>Não</label>&nbsp;&nbsp;&nbsp;\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>ISS Aliquota:</label>\n\
                                                                                <input id='iss_aliquota"+contratos_pk+"' class='form-control form-control-sm iss_aliquota' name='iss_retido'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor ISS:</label>\n\
                                                                                <input id='iss_valor"+contratos_pk+"' class='form-control form-control-sm iss_valor' name='iss_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>INSS Aliquota:</label>\n\
                                                                                <input id='inss_aliquota"+contratos_pk+"' class='form-control form-control-sm inss_aliquota' name='inss_aliquota'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor INSS:</label>\n\
                                                                                <input id='inss_valor"+contratos_pk+"' class='form-control form-control-sm inss_valor' name='inss_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>PIS Aliquota:</label>\n\
                                                                                <input id='pis_aliquota"+contratos_pk+"' class='form-control form-control-sm pis_aliquota' name='pis_aliquota'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor PIS:</label>\n\
                                                                                <input id='pis_valor"+contratos_pk+"' class='form-control form-control-sm pis_valor' name='pis_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Cofins Aliquota:</label>\n\
                                                                                <input id='cofins_aliquota"+contratos_pk+"' class='form-control form-control-sm cofins_aliquota' name='cofins_aliquota'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor Cofins:</label>\n\
                                                                                <input id='cofins_valor"+contratos_pk+"' class='form-control form-control-sm cofins_valor' name='cofins_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>IR Aliquota:</label>\n\
                                                                                <input id='ir_aliquota"+contratos_pk+"' class='form-control form-control-sm ir_aliquota' name='ir_aliquota'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor IR:</label>\n\
                                                                                <input id='ir_valor"+contratos_pk+"' class='form-control form-control-sm ir_valor' name='ir_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>CSLL Aliquota:</label>\n\
                                                                                <input id='csll_aliquota"+contratos_pk+"' class='form-control form-control-sm csll_aliquota' name='csll_aliquota'>\n\
                                                                            </div>\n\
                                                                            <div class='col-md-2'>\n\
                                                                                <label>Valor CSLL:</label>\n\
                                                                                <input id='csll_valor"+contratos_pk+"' class='form-control form-control-sm csll_valor' name='csll_valor'>\n\
                                                                            </div>\n\
                                                                        </div>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><label><b>Discriminação dos Serviços(Observação):</b></label>");
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").append("<br><div class='row'>\n\
                                                                            <div class='col-md-7'>\n\
                                                                                <textarea id='descricao_nfse"+contratos_pk+"' class='form-control form-control-sm descricao_nfse' name='descricao_nfse'></textarea>\n\
                                                                            </div>\n\
                                                                        </div>");
            if(faturamento_itens_pk > 0){
                var objParametros = {
                    "faturamento_item_pk": faturamento_itens_pk
                };
                var arrCarregar = carregarController("faturamento", "listarDadosFaturamentoNFSE", objParametros);
                if(arrCarregar.data.length > 0){
                    if(arrCarregar.data[0]['iss_retido_tomador'] == 1){
                        $("#iss_retido1"+contratos_pk).prop("checked", true);
                    }else{
                        $("#iss_retido2"+contratos_pk).prop("checked", true);
                    }

                    $("#pk_nota"+contratos_pk).val(arrCarregar.data[0]['pk'])
                    $("#descricao_nfse_pk"+contratos_pk).val(arrCarregar.data[0]['faturamento_nfse_servicos_pk'])
                    $("#faturamento_nfse_servicos_pk"+contratos_pk).val(arrCarregar.data[0]['faturamento_nfse_servicos_pk'])
                    $("#iss_aliquota"+contratos_pk).val(arrCarregar.data[0]['iss_aliquota'])
                    $("#iss_valor"+contratos_pk).val(arrCarregar.data[0]['iss_valor'])
                    $("#inss_aliquota"+contratos_pk).val(arrCarregar.data[0]['inss_aliquota'])
                    $("#inss_valor"+contratos_pk).val(arrCarregar.data[0]['inss_valor'])
                    $("#pis_aliquota"+contratos_pk).val(arrCarregar.data[0]['pis_aliquota'])
                    $("#pis_valor"+contratos_pk).val(arrCarregar.data[0]['pis_valor'])
                    $("#cofins_aliquota"+contratos_pk).val(arrCarregar.data[0]['cofins_aliquota'])
                    $("#cofins_valor"+contratos_pk).val(arrCarregar.data[0]['cofins_valor'])
                    $("#ir_aliquota"+contratos_pk).val(arrCarregar.data[0]['ir_aliquota'])
                    $("#ir_valor"+contratos_pk).val(arrCarregar.data[0]['ir_valor'])
                    $("#csll_aliquota"+contratos_pk).val(arrCarregar.data[0]['csll_aliquota'])
                    $("#csll_valor"+contratos_pk).val(arrCarregar.data[0]['csll_valor'])
                    $("#descricao_nfse"+contratos_pk).val(arrCarregar.data[0]['ds_descricao_corpo_nfse'])
                }
            }
            
            $("#textarea"+contratos_pk).hide();
            fcCarregarFuncoesNota(contratos_pk, j);
            ds_cpf_cnpj = ds_cpf_cnpj != 'null' ? ds_cpf_cnpj : "";
            $("#prestador_pk"+contratos_pk).val(contas_pk);  
            $("#tomador_pk"+contratos_pk).val(leads_pk);  
            $("#ds_cpf_cnpj"+contratos_pk).val(ds_cpf_cnpj);  
            $("#vl_total_servico"+contratos_pk).val($("#vl_contrato"+contratos_pk).val());  
            
            fcListarServico(contratos_pk, contas_pk);
            
            /*$("#cmdAbrirTextarea"+contratos_pk).click(function(){
                fcCarregarDescricao(contratos_pk)
            })*/

        }else{
            $("#formNotaFiscal"+j+"_"+contratos_pk+"_1 td").remove().draw();

        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
    
}

function fcCarregarDescricao(contratos_pk){
    $("#textarea"+contratos_pk).show();
    $("#textarea"+contratos_pk).val($("#faturamento_nfse_servicos_pk"+contratos_pk+" option:selected").text());
}

function fcCarregarFuncoesNota(contratos_pk, j){
    fcListarEmpresa(contratos_pk)
    fcCarregarLeads(contratos_pk)
    fcListarDetalhamentoCorpoNota(contratos_pk);
    //fcInformacoesSevicos(contratos_pk)    
    let valor_total = moeda2float($("#vl_contrato"+contratos_pk).val());
    let vl_iss = $('#iss_valor'+contratos_pk).val();
    let vl_inss = $('#inss_valor'+contratos_pk).val();
    let vl_pis = $('#pis_valor'+contratos_pk).val();
    let vl_cofins = $('#cofins_valor'+contratos_pk).val();
    let vl_ir = $('#ir_valor'+contratos_pk).val();
    let vl_csll = $('#csll_valor'+contratos_pk).val();
    let dtVencimento = $("#dtVencimento"+j+"_"+contratos_pk+"_1 #dt_vencimento").val();

    console.log(dtVencimento)
    
    let detalhamento_servico = "";
    
    $("#iss_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });
    
    $("#inss_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });
    
    $("#pis_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });
    
    $("#cofins_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });
    
    $("#ir_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });
    
    $("#csll_valor"+contratos_pk).keypress(function(){
        mascara(this,moeda);
    });

    $("#iss_aliquota"+contratos_pk).change(function(){
        vl_iss = moeda2float($("#vl_total_servico"+contratos_pk).val()) * (moeda2float($("#iss_aliquota"+contratos_pk).val()) / 100) ;
        $("#iss_valor"+contratos_pk).val(float2moeda(vl_iss));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#iss_valor"+contratos_pk).change(function(){
        vl_iss = $("#iss_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#inss_aliquota"+contratos_pk).change(function(){
        vl_inss = (moeda2float($("#vl_total_servico"+contratos_pk).val()) * 0.7) * (moeda2float($("#inss_aliquota"+contratos_pk).val()) / 100);
        $("#inss_valor"+contratos_pk).val(float2moeda(vl_inss));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#inss_valor"+contratos_pk).change(function(){
        vl_inss = $("#inss_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#pis_aliquota"+contratos_pk).change(function(){
        vl_pis = moeda2float($("#vl_total_servico"+contratos_pk).val()) * (moeda2float($("#pis_aliquota"+contratos_pk).val()) / 100);
        $("#pis_valor"+contratos_pk).val(float2moeda(vl_pis));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#pis_valor"+contratos_pk).change(function(){
        vl_pis = $("#pis_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#cofins_aliquota"+contratos_pk).change(function(){
        vl_cofins = moeda2float($("#vl_total_servico"+contratos_pk).val()) * (moeda2float($("#cofins_aliquota"+contratos_pk).val()) / 100);
        $("#cofins_valor"+contratos_pk).val(float2moeda(vl_cofins));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#cofins_valor"+contratos_pk).change(function(){
        vl_cofins = $("#cofins_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#ir_aliquota"+contratos_pk).change(function(){
        vl_ir = moeda2float($("#vl_total_servico"+contratos_pk).val()) * (moeda2float($("#ir_aliquota"+contratos_pk).val()) / 100);
        $("#ir_valor"+contratos_pk).val(float2moeda(vl_ir));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#ir_valor"+contratos_pk).change(function(){
        vl_ir = $("#ir_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#csll_aliquota"+contratos_pk).change(function(){
        vl_csll = moeda2float($("#vl_total_servico"+contratos_pk).val()) * (moeda2float($("#csll_aliquota"+contratos_pk).val()) / 100);
        $("#csll_valor"+contratos_pk).val(float2moeda(vl_csll));
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#csll_valor"+contratos_pk).change(function(){
        vl_csll = $("#csll_valor"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $("#cmdInformacoesSevicos"+contratos_pk).click(function(){
        fcInformacoesSevicos(contratos_pk);
        detalhamento_servico = $("#ds_descricao_servico"+contratos_pk).val() 
        fcArrumarTextarea(  contratos_pk,
            detalhamento_servico,
            dtVencimento,
            vl_iss,
            vl_inss,
            vl_pis,
            vl_cofins,
            vl_ir,
            vl_csll,
            valor_total
        )
    })
    
    $("#listaServicoConsulta"+contratos_pk).change(function(){
        detalhamento_servico =  $("#listaServicoConsulta"+contratos_pk+" option:selected").text();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });
    
    $("#textarea"+contratos_pk).change(function(){
        detalhamento_servico =  $("#textarea"+contratos_pk).val();
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });

    $('#dtVencimento'+j+'_'+contratos_pk+'_1 #dt_vencimento').change(function(){
        dtVencimento = $(this).val(); 
        fcArrumarTextarea(  contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        );
    });
}

function fcArrumarTextarea(contratos_pk,
                            detalhamento_servico,
                            dtVencimento,
                            vl_iss,
                            vl_inss,
                            vl_pis,
                            vl_cofins,
                            vl_ir,
                            vl_csll,
                            valor_total
                        ){

    $("#descricao_nfse"+contratos_pk).val(" ");

    let texto = "";
    if(detalhamento_servico != ''){
        texto += detalhamento_servico + "\n";
    }

    if(dtVencimento != '' && dtVencimento != '00/00/0000' && dtVencimento != 'undefined'){
        dt_vencimento = dtVencimento;
        dtVencimento = DataYMD(dtVencimento);
        data = new Date(dtVencimento);
        numeroMes = data.getMonth()+1;
        switch (numeroMes) {
            case 1:
                ds_mes = "JANEIRO";
                break;
            case 2:
                ds_mes = "FEVEREIRO";
                break;
            case 3:
                ds_mes = "MARÇO";
                break;
            case 4:
                ds_mes = "ABRIL";
                break;
            case 5:
                ds_mes = "MAIO";
                break;
            case 6:
                ds_mes = "JUNHO";
                break;
            case 7:
                ds_mes = "JULHO";
                break;
            case 8:
                ds_mes = "AGOSTO";
                break;
            case 9:
                ds_mes = "SETEMBRO";
                break;
            case 10:
                ds_mes = "OUTUBRO";
                break;
            case 11:
                ds_mes = "NOVEMBRO";
                break;
            case 12:
                ds_mes = "DEZEMBRO";
                break;
            default:
                ds_mes = "MÊS INVÁLIDO";
                break;
        }

        texto += "COMPETENCIA  " + ds_mes + "\n";
        texto += "DATA DE VENCIMENTO " + dt_vencimento + "\n";
    }
    
    texto += "VALOR DA NOTA FISCAL " + $("#vl_contrato"+contratos_pk).val() + "\n";
    
    if(vl_iss != '' && vl_iss != '0,00'){
        texto += "ISS RETIDO R$ " + parseFloat(vl_iss).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_iss);
    }
    
    if(vl_inss != '' && vl_inss != '0,00'){
        texto += "INSS RETIDO R$ " + parseFloat(vl_inss).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_inss);
    }

    if(vl_pis != '' && vl_pis != '0,00'){
        texto += "PIS R$ " + parseFloat(vl_pis).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_pis);
    }

    if(vl_cofins != '' && vl_cofins != '0,00'){
        texto += "COFINS R$ " + parseFloat(vl_cofins).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_cofins);
    }

    if(vl_ir != '' && vl_ir != '0,00'){
        texto += "IR R$ " + parseFloat(vl_ir).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_ir);
    }

    if(vl_csll != '' && vl_csll != '0,00'){
        texto += "CSLL R$ " + parseFloat(vl_csll).toFixed(2) + "\n";
        valor_total = parseFloat(valor_total) - parseFloat(vl_csll);
    }
    
    texto += "VALOR LIQUIDO R$ " + parseFloat(valor_total).toFixed(2) + "\n";

    $("#descricao_nfse"+contratos_pk).val(texto);
}

function fcListarDetalhamentoCorpoNota(contratos_pk) {
    var objParametros = {};
    var arrCarregar = carregarController("faturamento", "listarDetalhamentoCorpoNota", objParametros);
    carregarComboAjax($("#faturamento_nfse_servicos_pk"+contratos_pk), arrCarregar, " ", "pk", "ds_descricao_corpo_nfse");
}

function fcListarEmpresa(contratos_pk) {
    var objParametros = {};
    var arrCarregar = carregarController("conta_bancaria", "listarEmpresaContasAtivas", objParametros);
    carregarComboAjax($("#prestador_pk"+contratos_pk), arrCarregar, " ", "pk", "ds_conta");
}

function fcListarServico(contratos_pk, contas_pk){
    var objParametros = {
        'contas_pk': contas_pk
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarNfeServico", objParametros);
    carregarComboAjax($("#listaServicoConsulta"+contratos_pk), arrCarregar, " ", "num_codigo_servico", "ds_servico");
}

function fcInformacoesSevicos(contratos_pk){
    var objParametros = {
        'codigoServico':parseInt($('#codigo_servico_pk'+contratos_pk).val())
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarDadosServico", objParametros);
    $("#ds_descricao_servico"+contratos_pk).val(arrCarregar.data[0]['ds_servico'])
    $("#vl_aliquota"+contratos_pk).val(arrCarregar.data[0]['vl_aliquota'])
}

function fcCarregarLeads(contratos_pk) {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("lead", "listaLeadsClientes", objParametros);
    carregarComboAjax($("#tomador_pk"+contratos_pk), arrCarregar, " ", "pk", "ds_lead");
}

function fcCarregarDadosFaturamentoUpdate(){
    try {
        var faturamento_pk = $('#faturamento_pk').val();
        var objParametros = {
            "pk": faturamento_pk
        };
        var arrCarregar = carregarController("faturamento", "listarUpdateFaturamento", objParametros);
        if (arrCarregar.status == true) {  
            //Dados do Faturamento
    
            $('#dsUsuarioCadastro').append(arrCarregar.data[0].ds_usuario_cadastro); 
            $('#dtCadastro').append(arrCarregar.data[0].dt_cadastro);
            $('#dsUsuarioAtualizacao').append(arrCarregar.data[0].ds_usuario_atualizacao);
            $('#dtAtualizacao').append(arrCarregar.data[0].dt_ult_atualizacao);
            $('#pkFaturamento').append(arrCarregar.data[0].pk);
            $('#periodoFaturamento').append("De "+arrCarregar.data[0].dt_faturamento_ini+" Até "+arrCarregar.data[0].dt_faturamento_fim);
            $('#dsStatusFaturamento').append(arrCarregar.data[0].ds_usatus_faturamento);
            $('#dsObs').append(arrCarregar.data[0].obs);  
            //Dados Contratos
            var dsContratoFixo = "";
            if(arrCarregar.data[0].ic_contrato_fixo==1){
                dsContratoFixo = "Contratos Fixos";
            }
            var dsContratoAditivos = "";
            if(arrCarregar.data[0].ic_contrato_aditivo==1){
                dsContratoAditivos = "Contratos Aditivos";
            }
            var dsContratoExtras = "";
            if(arrCarregar.data[0].ic_contrato_servico_extra==1){
                dsContratoExtras = "Contratos Aditivos";
            }
            $('#dsTiposContratos').append(dsContratoFixo+"<br>"+dsContratoAditivos+"<br>"+dsContratoExtras );
    
            //Dados Emissões
            var dsFaturas = "";
            if(arrCarregar.data[0].ic_gerar_fatura==1){
                dsFaturas = "Gerar Faturas";
            }
            var dsNF = "";
            if(arrCarregar.data[0].ic_gerar_nota_fiscal==1){
                dsNF = "Gerar Notas Fiscais";
            }    
            var dsBoleto = "";
            if(arrCarregar.data[0].ic_gerar_boleto==1){
                dsBoleto = "Gerar Boletos";
            } 
            $('#dsEmissoes').append(dsFaturas+"<br>"+dsNF+"<br>"+dsBoleto);
            
            //Dados Contas
            var dsContas = "";
            var vhtml = "";
            for(var i=0; i < arrCarregar.data.length; i++){
                $('#composicao_faturamento').append("<div id='container"+i+"' class='row'></div>");  
                $("#container"+i).append("<div id='margin"+i+"' class='col-1'>&nbsp;</div>"); 
                $("#container"+i).append("<div id='size"+i+"' class='col-10'></div>"); 
                $("#size"+i).append("<table width='100%' id='container_table"+i+"'></table>"); 
                for(var j=0; j < arrCarregar.data[i].DadosContas.length; j++){
                    $('#dsContas').append(arrCarregar.data[i].DadosContas[j]['ds_conta']+"<br>");
                    $("#container_table"+j).append("<tr id='tr"+j+"'></tr>"); 
                    $("#tr"+j).append("<td id='td"+j+"'></td>"); 
                    $("#td"+j).append("<i class='bi bi-node-plus' style='font-size:18px; color:blue'></i>&nbsp;")  
                    $("#td"+j).append("<input type='checkbox' onclick='fcMarcaConta("+j+")' id='conta"+j+"' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'> - "+arrCarregar.data[i].DadosContas[j]['ds_conta']);  
                    $("#td"+j).append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button onclick='fcincluirContrato("+arrCarregar.data[i].DadosContas[j]['contas_pk']+", "+j+")' class='btn btn-primary btn-sm' id='cmdIncluirContrato"+j+"'>Incluir Contrato</button>");  
                    //CONTRATOS FIXOS
                    if(arrCarregar.data[0].ic_contrato_fixo==1){
                        $("#td"+j).append("<div id='lineContratoFixo"+j+"' style='display:none'></div>");
                        $("#lineContratoFixo"+j).append("<table width='100%' id='tableContratoFixo"+j+"' class='table'></table>");  
                        $("#tableContratoFixo"+j).append("<tr></tr>");
                        $("#tableContratoFixo"+j+" tr").append("<td width='25'>&nbsp</td>");
                        $("#tableContratoFixo"+j+" tr").append("<td id='tdContratoFixo"+j+"'></td>");
                        $("#tdContratoFixo"+j).append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class='bi bi-play-fill' style='font-size:18px; color:blue'></i>&nbsp;<input type='checkbox' onclick='fcMacarTipoContrato("+j+",1)' id='tipoContratoFixoConta"+j+"' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'> - Contratos Fixos");  
                        $("#tdContratoFixo"+j).append("<div id='lineCtfDados"+j+"1' style='display:none'></div>");  
                        $("#lineCtfDados"+j+"1").append("<table width='100%' id='tableCtfDados"+j+"1' border=0 class='table'><table>");
                        var vl_total_faturamento = 0.00;
                        for(var h=0; h < arrCarregar.data[i].DadosContratos.length; h++){
                            //if(arrCarregar.data[i].DadosContratos[h]['ic_tipo_contrato']==1 && arrCarregar.data[i].DadosContratos[h]['contas_contratos_pk'] == arrCarregar.data[i].DadosContas[j]['contas_pk'] ){ 
                                var contratos_pk = arrCarregar.data[i].DadosContratos[h]['contratos_pk'];
                                var leads_pai_pk = arrCarregar.data[i].DadosContratos[h]['leads_pai_pk'] != null ? arrCarregar.data[i].DadosContratos[h]['leads_pai_pk'] : arrCarregar.data[i].DadosContratos[h]['leads_pk']
                                
                                $("#tableCtfDados"+j+"1").append("<tr id='trCtfDados"+j+"1"+h+"' style='border-bottom: 1px groove;'></tr>");
                                $("#trCtfDados"+j+"1"+h).append("<td width='35'>&nbsp</td>");
                                $("#trCtfDados"+j+"1"+h).append("<td id='tdCtfDados"+j+"1"+h+"'></td>");
                                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<i class='bi bi-circle-fill' style='font-size:18px; color:blue'></i>")
                                $("#tdCtfDados"+j+"1"+h).append("&nbsp;<input type='checkbox' onclick='fcAbrirDadosContrato("+j+","+contratos_pk+",1)' id='ContratoFixoLead"+j+"_"+contratos_pk+"_1' value='"+contratos_pk+"'> ");
                                $("#tdCtfDados"+j+"1"+h).append("- Lead: "+arrCarregar.data[i].DadosContratos[h]['ds_lead']+" - Contrato: "+ contratos_pk);
                                $("#tdCtfDados"+j+"1"+h).append("- <span id='ic_contrato"+contratos_pk+"' align='left'></span>");
                                $("#tdCtfDados"+j+"1"+h).append("<div align='right'> <span id='vl_total_contrato"+j+"_"+h+"' align='right'>R$ </span> </div>");
                                $("#tdCtfDados"+j+"1"+h).append("<div id='lineCtfDadosItem"+j+"_"+contratos_pk+"_1' style='display:none'></div>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='contratos_pk' type='hidden' value='"+contratos_pk+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='vl_total_contrato' id='vl_total_contrato"+contratos_pk+"' type='hidden' value=''>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pai_pk' type='hidden' value='"+leads_pai_pk+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='leads_pk' type='hidden' value='"+arrCarregar.data[i].DadosContratos[h]['leads_pk']+"'>");
                                $("#tdCtfDados"+j+"1"+h).append("<input class='contas_pk' type='hidden' value='"+arrCarregar.data[i].DadosContas[j]['contas_pk']+"'>");          
                                $("#tdCtfDados"+j+"1"+h).append("<input class='ic_tipo_contrato' type='hidden' value='"+arrCarregar.data[i].DadosContratos[j]['ic_tipo_contrato']+"'>");          
                                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_contratos_pk' type='hidden' value='"+arrCarregar.data[i].DadosContratos[h]['faturamento_contratos_pk']+"'>");        
                                $("#tdCtfDados"+j+"1"+h).append("<input class='faturamento_itens_pk' type='hidden' value='"+arrCarregar.data[i].DadosContratos[h]['faturamento_itens_pk']+"'>");        
                                $("#lineCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<table width='100%' id='tableCtfDadosItem"+j+"_"+contratos_pk+"_1' border='1'  class='table'></table>");
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<tr></tr>");
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr").append("<td></td>");
                                $("#tableCtfDadosItem"+j+"_"+contratos_pk+"_1 tr td").append("<table id='containerCtfDadosItem"+j+"_"+contratos_pk+"_1' width='100%' border='0'></table>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<thead></thead>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr></tr>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead tr").append("<th colspan='4' style='text-align:center; margin:10px;background:#f5f5f5'>Composição do Contrato</th>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='10%'>Cód Contrato: "+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Usuário Cadastro: "+arrCarregar.data[i].DadosContratos[h]['ds_usuario_cadastro_contrato']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='20%'>Dt Cadastro: "+arrCarregar.data[i].DadosContratos[h]['dt_cadastro']+"</td>");
                                $("#trSuperiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Validade do Contrato: De "+arrCarregar.data[i].DadosContratos[h]['dt_inicio_contrato']+" Até "+arrCarregar.data[i].DadosContratos[h]['dt_fim_contrato']+"</td>");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 thead").append("<tr id='trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='35%'>Razão Social: "+arrCarregar.data[i].DadosContratos[h]['ds_razao_social']+"</td>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>CNPJ: "+arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj']+"</td>");
                                $("#trInferiorItensCtfDados"+j+"_"+contratos_pk+"_1").append("<td width='40%'>Endereço: "+arrCarregar.data[i].DadosContratos[h]['ds_endereco_lead']+"</td>");
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
                            
                                for(var k=0; k < arrCarregar.data[i].DadosContratosItens.length; k++){
                                    if(arrCarregar.data[i].DadosContratos[h]['contratos_pk']==arrCarregar.data[i].DadosContratosItens[k]['contratos_pk']){
                                        vl_total += parseFloat(arrCarregar.data[i].DadosContratosItens[k]['vl_total']);
                                        var vl_contrato = arrCarregar.data[i].DadosContratos[h]['vl_contrato'] == ('0,00' || '0') ? vl_total : arrCarregar.data[i].DadosContratos[h]['vl_contrato'];
                                        
                                        $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tbody").append("<tr id='trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1'></tr>")
                                        $("#trContratoItens"+j+"_"+k+"_"+contratos_pk+"_1").append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['faturamento_contratos_itens_pk']+"<input id='faturamento_contratos_itens_pk["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['faturamento_contratos_itens_pk']+"'</td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_servico_prestado']+"<input id='produto_servico_pk["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['produto_servico_pk']+"'></td>")
                                                                                    .append("<td><input type='number' class='n_qtde_colaborador["+contratos_pk+"]' id='n_qtde_colaborador_"+contratos_pk+"_"+k+"' onchange='fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")'  value='"+arrCarregar.data[i].DadosContratosItens[k]['n_qtde_colaborador']+"'></td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_carga_horaria_dia']+"<input id='ds_periodo["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['ds_carga_horaria_dia']+"'></td>")
                                                                                    .append("<td>"+arrCarregar.data[i].DadosContratosItens[k]['ds_escala']+"<input id='n_qtde_dias_semana["+contratos_pk+"]' type='hidden' value='"+arrCarregar.data[i].DadosContratosItens[k]['ds_escala']+"'></td>")
                                                                                    .append("<td><input type='text' class='vl_unitario_produtos_servicos["+contratos_pk+"]' id='vl_unit_"+contratos_pk+"_"+k+"'  onchange='fcCalcularTotalItens("+k+", "+contratos_pk+", "+j+", "+h+")'  onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[i].DadosContratosItens[k]['vl_unit'])+"'></td>")
                                                                                    .append("<td><input type='text'  id='vl_total_item_"+contratos_pk+"_"+k+"' class='vl_total_item_"+contratos_pk+"' onkeypress='mascara(this, moeda)' value='"+float2moeda(arrCarregar.data[i].DadosContratosItens[k]['vl_total'])+"'></td>")
                                                                                    .append("<td></td>");
                                        
                                    }
                                }

                                $("#vl_total_contrato"+j+"_"+h).append(float2moeda(vl_contrato))
                                $("#vl_total_contrato"+contratos_pk).val(vl_contrato)
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1").append("<tfoot></tfoot>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='totalContratos"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Total Contratos</td>");
                                $("#totalContratos"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' id='vl_contrato"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"' onkeypress='mascara(this, moeda)' value='"+float2moeda(vl_contrato)+"'></td>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtFaturamento"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Faturamento</td>");
                                $("#dtFaturamento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input type='text' onkeypress='mascara(this, mdata)' id='dt_faturamento' value='"+arrCarregar.data[i].DadosContratos[h]['dt_faturamento']+"'></td>");
                                $("#tbContratoItens"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='dtVencimento"+j+"_"+contratos_pk+"_1'></tr>");
                                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=6 align='right' >&nbsp;Data Vencimento</td>");
                                $("#dtVencimento"+j+"_"+contratos_pk+"_1").append("<td colspan=2><input onkeypress='mascara(this, mdata)' id='dt_vencimento' value='"+arrCarregar.data[i].DadosContratos[h]['dt_vencimento']+"'></td>");

                            //} 
                            $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='textarea"+j+"_"+contratos_pk+"_1'></tr>");
                            var obs_faturamento_contrato = arrCarregar.data[i].DadosContratos[h]['obs_faturamento_contrato'] == null? " ":arrCarregar.data[i].DadosContratos[h]['obs_faturamento_contrato'];
                            var obs_lancamento = arrCarregar.data[i].DadosContratos[h]['obs_lancamento'] == null? " ":arrCarregar.data[i].DadosContratos[h]['obs_lancamento'];
                            //var obs_corpo_nota = arrCarregar.data[i].DadosContratos[h]['obs_corpo_nota'] == null? " ":arrCarregar.data[i].DadosContratos[h]['obs_corpo_nota'];

                            $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='2'><label>Observação Faturamento</label><textarea rows='4' class='obs_faturamento' cols='50' value=''>"+obs_faturamento_contrato+"</textarea></td>");
                            $("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Financeiro</label><textarea rows='4' class='obs_lancamento' cols='50' value=''>"+obs_lancamento+"</textarea></td>");
                            //$("#textarea"+j+"_"+contratos_pk+"_1").append("<td colspan='3'><label>Observação Corpo da Nota Fiscal</label><textarea rows='4' class='obs_corpo_nota' cols='58' value=''>"+obs_corpo_nota+"</textarea></td>");
    
                            
                            if(arrCarregar.data[0].ic_gerar_nota_fiscal==1){
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='NfseCtfDadosItem"+j+"_"+contratos_pk+"_1' align='left'></tr>");
                                $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");
                                $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<b>Gerar Nota Fiscal de Serviços Eletrônica? </b><select style='width:7em' class='fazer_nfse' id='fazer_nfse"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"' onchange='fcFaturamentoNFSE("+j+", "+contratos_pk+", "+arrCarregar.data[i].DadosContas[j]['contas_pk']+", "+leads_pai_pk+", &#39;"+arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj']+"&#39;, "+arrCarregar.data[i].DadosContratos[h]['faturamento_itens_pk']+")'><option></option>\n\
                                                                                                    <option value='1'>Sim</option>\n\
                                                                                                    <option value='2'>Não</option>\n\
                                                                                            </select><br>"); 
                                $("#NfseCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<hr style='border-bottom: solid black 1px' >");
                                $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='formNotaFiscal"+j+"_"+contratos_pk+"_1' align='left'></tr>");
                                $('#fazer_nfse'+contratos_pk).val(arrCarregar.data[i].DadosContratos[h]['ic_gerar_nfse'])
                                if(arrCarregar.data[i].DadosContratos[h]['ic_gerar_nfse'] == 1){
                                    fcFaturamentoNFSE(j, contratos_pk, arrCarregar.data[i].DadosContas[j]['contas_pk'], leads_pai_pk, arrCarregar.data[i].DadosContratos[h]['ds_cpf_cnpj'], arrCarregar.data[i].DadosContratos[h]['faturamento_itens_pk']) 
                                }
                                
                            }
                            $("#containerCtfDadosItem"+j+"_"+contratos_pk+"_1 tfoot").append("<tr id='StatusCtfDadosItem"+j+"_"+contratos_pk+"_1' align='right'></tr>");
                            $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1").append("<td colspan='8'></td>");
                            $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<input type='hidden' class='ic_status_validacao' id='ic_status_validacao"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"'>");
                            $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("<select class='ic_status' id='ic_status"+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+"'><option></option>\n\
                                                                                                <option value='1'>Validado</option>\n\
                                                                                                <option value='2'>Pendente Análise</option>\n\
                                                                                                <option value='3'>Não Faturar</option>\n\
                                                                                        </select>"); 
                            $("#StatusCtfDadosItem"+j+"_"+contratos_pk+"_1 td").append("&nbsp;<button height='10px' class='btn btn-success' onclick='fcValidarContrato("+arrCarregar.data[i].DadosContratos[h]['contratos_pk']+")' align='right'>Aplicar</button>"); 
                            $('#ic_status'+contratos_pk).val(arrCarregar.data[i].DadosContratos[h]['ic_status'])
                            fcValidarContrato(contratos_pk);
                        }
                    }
                    $("#td"+j).append("<input type='hidden' class='h' id='h"+j+"' value='"+h+"'>");
                }
            }  
            
        }        
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

function fcSalvar(){
    try {
        var contratos_pk = $("input[class='contratos_pk']");
        var faturamento_itens_pk = $("input[class='faturamento_itens_pk']");
        var leads_pai_pk = $("input[class='leads_pai_pk']");
        var contas_pk = $("input[class='contas_pk']");
        var vl_total_lancamento = $("input[class='vl_total_contrato']");
        var obs_faturamento = $("textarea[class='obs_faturamento']");
        //var obs_corpo_nota = $("textarea[class='obs_corpo_nota']");
        var obs_lancamento = $("textarea[class='obs_lancamento']");
        var vl_total_geral_faturamento = $("#vl_total_geral_faturamento").html();
        var faturamento_pk = $("#faturamento_pk").val();
    
        let arrItens = [];
        let arrDadosNfse = [];
    
        for(i = 0; i < contratos_pk.length; i++){   
            arrItens[i] = {
                "faturamento_itens_pk": faturamento_itens_pk.get(i).value,
                "faturamento_pk": faturamento_pk,
                "contratos_pk":contratos_pk.get(i).value,
                "leads_pk":leads_pai_pk.get(i).value,
                "contas_pk":contas_pk.get(i).value,
                "vl_total_lancamento":vl_total_lancamento.get(i).value,
                "obs_faturamento":obs_faturamento.get(i).value,
                "obs_lancamento":obs_lancamento.get(i).value
            }; 
        
            var pk_nota = $("#pk_nota"+contratos_pk.get(i).value).val();
            var fazer_nfse = $("#fazer_nfse"+contratos_pk.get(i).value).val();
            var prestador_pk = $("#prestador_pk"+contratos_pk.get(i).value).val();
            var tomador_pk = $("#tomador_pk"+contratos_pk.get(i).value).val();
            var ds_cpf_cnpj = $("#ds_cpf_cnpj"+contratos_pk.get(i).value).val();
            var codigo_servico_pk = $("#codigo_servico_pk"+contratos_pk.get(i).value).val();
            var ds_descricao_servico = $("#ds_descricao_servico"+contratos_pk.get(i).value).val();
            var iss_retido_tomador = $("#iss_retido1"+contratos_pk.get(i).value).val() == true ? 1 : 0;
            var faturamento_nfse_servicos_pk = $("#faturamento_nfse_servicos_pk"+contratos_pk.get(i).value).val();
            var vl_total_servico = $("#vl_total_servico"+contratos_pk.get(i).value).val();
                vl_total_servico = vl_total_servico != undefined ? vl_total_servico : 0;
            var iss_aliquota = $("#iss_aliquota"+contratos_pk.get(i).value).val();
                iss_aliquota = iss_aliquota != undefined ? iss_aliquota : 0;
            var iss_valor = $("#iss_valor"+contratos_pk.get(i).value).val();
                iss_valor = iss_valor != undefined ? iss_valor : 0;
            var inss_aliquota = $("#inss_aliquota"+contratos_pk.get(i).value).val();
                inss_aliquota = inss_aliquota != undefined ? inss_aliquota : 0;
            var inss_valor = $("#inss_valor"+contratos_pk.get(i).value).val();
                inss_valor = inss_valor != undefined ? inss_valor : 0;
            var pis_aliquota = $("#pis_aliquota"+contratos_pk.get(i).value).val();
                pis_aliquota = pis_aliquota != undefined ? pis_aliquota : 0;
            var pis_valor = $("#pis_valor"+contratos_pk.get(i).value).val();
                pis_valor = pis_valor != undefined ? pis_valor : 0;
            var cofins_aliquota = $("#cofins_aliquota"+contratos_pk.get(i).value).val();
                cofins_aliquota = cofins_aliquota != undefined ? cofins_aliquota : 0;
            var cofins_valor = $("#cofins_valor"+contratos_pk.get(i).value).val();
                cofins_valor = cofins_valor != undefined ? cofins_valor : 0;
            var ir_aliquota = $("#ir_aliquota"+contratos_pk.get(i).value).val();
                ir_aliquota = ir_aliquota != undefined ? ir_aliquota : 0;
            var ir_valor = $("#ir_valor"+contratos_pk.get(i).value).val();
                ir_valor = ir_valor != undefined ? ir_valor : 0;
            var csll_aliquota = $("#csll_aliquota"+contratos_pk.get(i).value).val();
                csll_aliquota = csll_aliquota != undefined ? csll_aliquota : 0;
            var csll_valor = $("#csll_valor"+contratos_pk.get(i).value).val();
                csll_valor = csll_valor != undefined ? csll_valor : 0;
            var descricao_nfse = $("#descricao_nfse"+contratos_pk.get(i).value).val();
    
    
            arrDadosNfse [i] = {
                "pk": pk_nota,
                "fazer_nfse": fazer_nfse,
                "prestador_pk": prestador_pk,
                "faturamento_pk": faturamento_pk,
                "tomador_pk":tomador_pk,
                "ds_cpf_cnpj":ds_cpf_cnpj,
                //"ds_descricao_servico_add":ds_descricao_servico_add,
                "codigo_servico_pk":codigo_servico_pk,
                "ds_descricao_servico":ds_descricao_servico,
                //"vl_aliquota":moeda2float(vl_aliquota),
                "faturamento_nfse_servicos_pk":faturamento_nfse_servicos_pk,
                "iss_retido_tomador":iss_retido_tomador,
                "vl_total_servico":moeda2float(vl_total_servico),
                "iss_aliquota": moeda2float(iss_aliquota),
                "iss_valor": moeda2float(iss_valor),
                "inss_aliquota": moeda2float(inss_aliquota),
                "inss_valor": moeda2float(inss_valor),
                "pis_aliquota": moeda2float(pis_aliquota),
                "pis_valor": moeda2float(pis_valor),
                "cofins_aliquota": moeda2float(cofins_aliquota),
                "cofins_valor": moeda2float(cofins_valor),
                "ir_aliquota": moeda2float(ir_aliquota),
                "ir_valor": moeda2float(ir_valor),
                "csll_aliquota": moeda2float(csll_aliquota),
                "csll_valor": moeda2float(csll_valor),
                "descricao_nfse":descricao_nfse
            }; 
            
        }
    
        var contratos_pk = $("input[class='contratos_pk']");
        var leads_pk = $("input[class='leads_pk']");
        var vl_total_lancamento = $("input[class='vl_total_contrato']");
        var ic_tipo_contrato = $("input[class='ic_tipo_contrato']");
        //var obs_corpo_nota = $("textarea[class='obs_corpo_nota']");
        var faturamento_contratos_pk = $("input[class='faturamento_contratos_pk']");
        var ic_status = $("select[class='ic_status']");
        var dt_faturamento = $("input[id='dt_faturamento']");
        var dt_vencimento = $("input[id='dt_vencimento']");
        let arrContratos = [];
    
        for(l = 0; l < contratos_pk.length; l++){   
            var v_contratos_pk = contratos_pk.get(l).value  
            var produto_servico_pk = $("input[id='produto_servico_pk["+v_contratos_pk+"]']");
            var n_qtde_colaborador = $("input[class='n_qtde_colaborador["+v_contratos_pk+"]']");
            var vl_unitario_produtos_servicos = $("input[class='vl_unitario_produtos_servicos["+v_contratos_pk+"]']");
            var ds_periodo = $("input[id='ds_periodo["+v_contratos_pk+"]']");
            var n_qtde_dias_semana = $("input[id='n_qtde_dias_semana["+v_contratos_pk+"]']");
            var faturamento_contratos_itens_pk = $("input[id='faturamento_contratos_itens_pk["+v_contratos_pk+"]']");
            var fazer_nfse = $("#fazer_nfse"+v_contratos_pk).val();
    
            if(ic_status.get(l).value == 1){
                if(dt_faturamento.get(l).value == "" || dt_faturamento.get(l).value == "00/00/0000"){
                    sweetMensagem('warning', "Preencha o campo Data Faturamento no contrato: " + v_contratos_pk );
                    return;
                }
                if(dt_vencimento.get(l).value == "" || dt_vencimento.get(l).value == "00/00/0000"){
                    sweetMensagem('warning', "Preencha o campo Data Vencimento no contrato: " + v_contratos_pk );
                    return;
                }
            }
            
            let arrDadosContratos = [{
                "faturamento_contratos_pk": faturamento_contratos_pk.get(l).value,
                "faturamento_pk": faturamento_pk,
                "contratos_pk":v_contratos_pk,
                "fazer_nfse":fazer_nfse,
                "leads_pk":leads_pk.get(l).value,
                "ic_status":ic_status.get(l).value,
                "vl_total_lancamento":vl_total_lancamento.get(l).value,
                //"obs_corpo_nota":obs_corpo_nota.get(l).value,
                "dt_faturamento":dt_faturamento.get(l).value,
                "dt_vencimento":dt_vencimento.get(l).value,
                "vl_total_geral_faturamento": vl_total_geral_faturamento,
                "ic_tipo_contrato":ic_tipo_contrato.get(l).value
            }]; 
    
    
            let arrDadosContratosItens = []
            for(a = 0; a < produto_servico_pk.length; a++){ 
                if(produto_servico_pk.get(a).value == ''){
                    sweetMensagem('warning', "Preencha o campo Serviço Prestado no contrato: " + v_contratos_pk );
                    return;
                }
                if(n_qtde_colaborador.get(a).value == ''){
                    sweetMensagem('warning', "Preencha o campo Qtde Colaboradores no contrato: " + v_contratos_pk );
                    return;
                }
                if(n_qtde_dias_semana.get(a).value == ''){
                    sweetMensagem('warning', "Preencha o campo Carga HR Dia no contrato: " + v_contratos_pk );
                    return;
                }
                /*if(ds_periodo.get(a).value == ''){
                    utilsJS.toastNotify(false, "Preencha o campo Escala no contrato: " + v_contratos_pk );
                    return;
                }*/
                arrDadosContratosItens[a] = {
                    "contratos_pk":v_contratos_pk,
                    "produto_servico_pk":produto_servico_pk.get(a).value,
                    "n_qtde_colaborador":n_qtde_colaborador.get(a).value,
                    "vl_unitario_produtos_servicos":moeda2float(vl_unitario_produtos_servicos.get(a).value),
                    "ds_periodo":ds_periodo.get(a).value,
                    "n_qtde_dias_semana":n_qtde_dias_semana.get(a).value,
                    "faturamento_contratos_itens_pk":faturamento_contratos_itens_pk.get(a).value
                }; 
    
            };
    
            const arrContrato = arrDadosContratos.map(contrato => {
                const itensContratos = arrDadosContratosItens.filter(item=> item.contratos_pk === contrato.contratos_pk)
                return {
                    faturamento_contratos_pk: contrato.faturamento_contratos_pk,
                    faturamento_pk: contrato.faturamento_pk,
                    contratos_pk: contrato.contratos_pk,
                    fazer_nfse: contrato.fazer_nfse,
                    leads_pk: contrato.leads_pk,
                    ic_status: contrato.ic_status,
                    vl_total_lancamento: contrato.vl_total_lancamento,
                    vl_total_geral_faturamento: contrato.vl_total_geral_faturamento,
                    //obs_corpo_nota: contrato.obs_corpo_nota,
                    dt_faturamento: contrato.dt_faturamento,
                    dt_vencimento: contrato.dt_vencimento,
                    ic_tipo_contrato: contrato.ic_tipo_contrato,
                    arrItens: itensContratos
                };
            });
    
            arrContratos.push(arrContrato)
                
        }
        let JsonContratos = JSON.stringify(arrContratos);
        let JsonItens = JSON.stringify(arrItens);
        let JsonDadosNfse = JSON.stringify(arrDadosNfse);
    
        formdata.append('JsonItens', JsonItens);
        formdata.append('JsonContratos', JsonContratos);
        formdata.append('JsonDadosNfse', JsonDadosNfse);
    
        $.ajax({
            type: 'POST',
            url: '/api/faturamento/salvarItensContratos',
            data: formdata,
            processData: false,
            contentType: false,
            complete: function (response) {
                try {
                    var log = JSON.parse(response.responseText);
                    if(log.status==true){
                        utilsJS.toastNotify(true,'Registros salvos com sucesso!')
                        var objParametros = {};
                        sendPost('faturamento','receptivo' ,objParametros);
                    }
                } catch (e) {
                    sweetMensagem(false, "Ocorreu um erro na requisição <br /> Contate o suporte");
                }
            }
        }); 
    } catch (error) {
        console.log(error)
    }
    
    
}

function fcAddLinha(id, contratoPk, j, h){ 
    try {
        
    var linhas = $("#tbContratoItens"+id+"_"+contratoPk+"_1 tbody tr").length;
    linhas = linhas + linhas;

    $("#tbContratoItens"+id+"_"+contratoPk+"_1 tbody").append("<tr id='trContratoItens"+id+"_"+linhas+"_"+contratoPk+"_1'></tr>")
    $("#trContratoItens"+id+"_"+linhas+"_"+contratoPk+"_1").append("<td><input type='hidden' id='faturamento_contratos_itens_pk["+contratoPk+"]' value=''></td>")
                                                .append("<td><select class='produtos_servicos_pk"+linhas+"' onChange='fcAtribuirProdutosServicos("+linhas+")'></select><input type='hidden' class='produto_servico_input_pk"+linhas+"' id='produto_servico_pk["+contratoPk+"]'></td>")
                                                .append("<td><input type='number' class='n_qtde_colaborador["+contratoPk+"]' id='n_qtde_colaborador_"+contratoPk+"_"+linhas+"' onchange='fcCalcularTotalItens("+linhas+", "+contratoPk+", "+j+", "+h+")'></td>")
                                                .append("<td><select onChange='fcAtribuirCargaHoraria("+linhas+")' class='carga_hr_dia_pk_"+linhas+"'></select><input type='hidden' class='ds_periodo"+linhas+"' id='ds_periodo["+contratoPk+"]'></td>")
                                                .append("<td><select onChange='fcAtribuirEscalas("+linhas+")' class='escala_pk_"+linhas+"'></select><input type='hidden' class='n_qtde_dias_semana"+linhas+"' id='n_qtde_dias_semana["+contratoPk+"]'></td>")
                                                .append("<td><input type='text' class='vl_unitario_produtos_servicos["+contratoPk+"]' id='vl_unit_"+contratoPk+"_"+linhas+"' onchange='fcCalcularTotalItens("+linhas+", "+contratoPk+", "+j+", "+h+")'  onkeypress='mascara(this, moeda)' value='0'></td>")
                                                .append("<td><input type='text' id='vl_total_item_"+contratoPk+"_"+linhas+"' class='vl_total_item_"+contratoPk+"'  onkeypress='mascara(this, moeda)' value='0,00''></td>")
                                                .append("<td><i id='trash"+id+"_"+linhas+"_"+contratoPk+"' onclick='fcRemoverLinha("+id+", "+linhas+", "+contratoPk+")' width='10px' class='bi bi-trash'></i></td>");
    fcCarregarCargaHoraria(linhas)
    fcCarregarEscalas(linhas)
    fcCarregarProdutosServicos(linhas)
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

function fcCarregarEscalas(linha){
    $('.escala_pk_'+linha).append('<option></option>')
                            .append('<option>1D</option>')
                            .append('<option>2D</option>')
                            .append('<option>3D</option>')
                            .append('<option>4D</option>')
                            .append('<option>4x1</option>')
                            .append('<option>4x2</option>')
                            .append('<option>5x1</option>')
                            .append('<option>5x2</option>')
                            .append('<option>6x1</option>')
                            .append('<option>12x36</option>')
}

function fcCarregarProdutosServicos(linha){
    var objParametros = {
        "pk": ""
    };         
    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);   
    carregarComboAjax($(".produtos_servicos_pk"+linha), arrCarregar, " ", "pk", "ds_produto_servico");  
}

function fcRemoverLinha(id, linhas, contratoPk){
    $("#trContratoItens"+id+"_"+linhas+"_"+contratoPk+"_1").remove().draw();
}

function fcValidarContrato(contratoPk){
    $('#ic_contrato'+contratoPk).html("");
    vl_contrato =  $('#vl_total_contrato'+contratoPk).val();

    if($('#ic_status'+contratoPk).val() == 1){
        $('#ic_contrato'+contratoPk).append("<b>Validado</b>") 
        $('#ic_contrato'+contratoPk+" b").css("color", 'green');
        if($("#vl_total_geral_faturamento").html() != ''){
            var vl_total_geral_faturamento = parseFloat($("#vl_total_geral_faturamento").html()) + parseFloat(vl_contrato);
        }else{
            var vl_total_geral_faturamento = parseFloat(0.00) + parseFloat(vl_contrato);
        }
        $("#vl_total_geral_faturamento").html(vl_total_geral_faturamento)
        $("#ic_status_validacao"+contratoPk).val(1)

    }else if($('#ic_status'+contratoPk).val() == 2){
        $('#ic_contrato'+contratoPk).append("<b>Pendente Analise</b>") 
        $('#ic_contrato'+contratoPk+" b").css("color", 'red')
        if($("#ic_status_validacao"+contratoPk).val()==1){
            var vl_total_geral_faturamento = parseFloat($("#vl_total_geral_faturamento").html()) - parseFloat(vl_contrato);
            $("#vl_total_geral_faturamento").html(vl_total_geral_faturamento)
        }
        $("#ic_status_validacao"+contratoPk).val(0)

    }else if($('#ic_status'+contratoPk).val() == 3){
        $('#ic_contrato'+contratoPk).append("<b>Não Faturar</b>") 
        $('#ic_contrato'+contratoPk+" b").css("color", 'red') 
        $('.titulo_total_contrato'+contratoPk).html("") 
        if($("#ic_status_validacao"+contratoPk).val()==1){
            var vl_total_geral_faturamento = parseFloat($("#vl_total_geral_faturamento").html()) - parseFloat(vl_contrato);
            $("#vl_total_geral_faturamento").html(vl_total_geral_faturamento)
        }
        $("#ic_status_validacao"+contratoPk).val(0)

    }
}

function fcCalcularTotalItens(linha, contratoPk, j, h){
    var vl_total_item = $('#n_qtde_colaborador_'+contratoPk+'_'+linha).val() * moeda2float($('#vl_unit_'+contratoPk+'_'+linha).val())
    $('#vl_total_item_'+contratoPk+'_'+linha).val(float2moeda(vl_total_item)) 

    fcCalcularTotal(contratoPk, j, h)
    $("#vl_total_servico"+contratoPk).val($("#vl_contrato"+contratoPk).val());  
}

function fcCalcularTotal(contratoPk, j, h){
    var total = 0;
    $('.vl_total_item_'+contratoPk).each(function() {
        total += moeda2float($(this).val());
    }); 
    
    $('#vl_contrato'+contratoPk).val(float2moeda(total));
    $('#vl_total_contrato'+contratoPk).val(total);


    $("#vl_total_contrato"+j+"_"+h).html(" ")
    $("#vl_total_contrato"+j+"_"+h).append("R$ "+float2moeda(total))

    if($('#ic_status'+contratoPk).val() == 1){
        vl_total_geral_faturamento = $('#vl_total_geral_faturamento').val();
        $("#vl_total_geral_faturamento").html(" ")
        $("#vl_total_geral_faturamento").html(vl_total_geral_faturamento + total)
    }

}

function fcMarcaConta(id){
    if($("#conta"+id).is(":checked") == true) {
        $("#lineContratoFixo"+id).show();
        $("#lineContratoAditivo"+id).show();
        $("#lineContratoServicoExtra"+id).show();
    }else{
        $("#lineContratoFixo"+id).hide();
        $("#lineContratoAditivo"+id).hide();
        $("#lineContratoServicoExtra"+id).hide();
    }
}

function fcMacarTipoContrato(id,icTipoContrato){
    if($("#tipoContratoFixoConta"+id).is(":checked") == true) {
        $("#lineCtfDados"+id+icTipoContrato).show();
    }else{
        $("#lineCtfDados"+id+icTipoContrato).hide();
    }  
}

function fcAbrirDadosContrato(id,contratoPk,icTipoContrato){
    if($("#ContratoFixoLead"+id+"_"+contratoPk+"_"+icTipoContrato).is(":checked") == true) {
        $("#lineCtfDadosItem"+id+"_"+contratoPk+"_"+icTipoContrato).show();
        fcCalcularTotal(contratoPk)
    }else{
        $("#lineCtfDadosItem"+id+"_"+contratoPk+"_"+icTipoContrato).hide();
    }
}

function fcAtribuirProdutosServicos(linhas){
    $(".produto_servico_input_pk"+linhas).val($(".produtos_servicos_pk"+linhas).val())
}

function fcAtribuirEscalas(linhas){
    $(".n_qtde_dias_semana"+linhas).val($(".escala_pk_"+linhas).val())
}

function fcAtribuirCargaHoraria(linhas){
    $(".ds_periodo"+linhas).val($(".carga_hr_dia_pk_"+linhas).val())
}

function fcCarregarCargaHoraria(linhas){
    $('.carga_hr_dia_pk_'+linhas).append('<option></option>')
    for(var i=1; i<=12; i++){
        $('.carga_hr_dia_pk_'+linhas).append('<option>'+i+'</option>')
    }
    $('.carga_hr_dia_pk_'+linhas).append('<option>24</option>')
}

function fcProcessar(){
    utilsJS.jqueryConfirm('Processar?', 'Deseja processar esse faturamento?', function () {
        var objParametros = {
            "pk": $("#faturamento_pk").val()
        };
        
        var arrCarregar = carregarController("faturamento", "processar", objParametros);
        if(arrCarregar.status == true){
            utilsJS.toastNotify(true, 'Registros salvos com sucesso!');
            sendPost('faturamento','receptivo' ,objParametros);
        }else{
            utilsJS.toastNotify(false, "Ocorreu um erro na requisição <br /> Contate o suporte");
        }
    });
}

function fcVoltar() {
    var objParametros = {
        "pk":''
    };
    sendPost('faturamento', 'receptivo' ,objParametros);
}


var formdata;
$(document).ready(function(){
    formdata = new FormData();
    
    var acao = $('#acao').val();
    if(acao == '1'){
        fcCarregarDadosFaturamento()
    }else{
        fcCarregarDadosFaturamentoUpdate()
    }
    $(document).on('click', '#cmdSalvar', fcSalvar);
    $(document).on('click', '#cmdProcessar', fcProcessar);
    $(document).on('click', '#cmdVoltar', fcVoltar);
});
