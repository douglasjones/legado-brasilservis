var tblDocumentos;
var tblServico;

function fcEnviar(){
    
    var v_contas_pk = $("#ds_empresa").val();
    var v_contas_lead_pk = $("#contas_lead_pk").val();
    var v_ds_razao_social = $("#ds_razao_social").val();
    var v_ds_nome_fantasia = $("#ds_nome_fantasia").val();
    var v_ds_cnpj = $("#ds_cnpj").val();
    var v_ds_inscricao_estadual = $("#ds_inscricao_estadual").val();
    var v_ds_inscricao_municipal = $("#ds_inscricao_municipal").val();
    var v_ic_regime_tributario = $("#ic_regime_tributario").val();
    var v_ic_regime_tributario_especial = $("#ic_regime_tributario_especial").val();
    var ds_tel = $("#ds_tel").val();
        arr_telefone = ds_tel.split(')')
    var v_ds_tel = arr_telefone[1]
    var v_ds_ddd =  arr_telefone[0].replace('(', '')
    
    var v_ic_incentivo_cultural = $("#ic_incentivo_cultural").is(':checked')==true?1:0;
    var v_ic_incentivo_fiscal = $("#ic_incentivo_fiscal").is(':checked')==true?1:0;
    var v_ic_simples_nacional = $("#ic_simples_nacional").is(':checked')==true?1:0;

    var v_ds_email = $("#ds_email").val();
    var v_ds_cep = $("#ds_cep").val();
    var v_ds_uf = $("#ds_uf").val();
    var v_ds_endereco = $("#ds_endereco").val();
    var v_ds_cidade = $("#ds_cidade").val();
    var v_ds_numero = $("#ds_numero").val();
    var v_ds_complemento = $("#ds_complemento").val();
    var v_ds_tipo_logradouro = $("#ds_tipo_logradouro").val();
    var v_ds_bairro = $("#ds_bairro").val();
    var v_arq_certificado = $("#arq_certificado").val();
    var v_n_certificado = $("#n_certificado").val();
    var v_ds_senha_certificado = $("#ds_senha_certificado").val();
    var v_dt_criacao_certificado = $("#dt_criacao_certificado").val();
    var v_dt_vencimento_certificado = $("#dt_vencimento_certificado").val();
    var v_ds_login_prefeitura = $("#ds_login_prefeitura").val();
    var v_ds_senha_prefeitura = $("#ds_senha_prefeitura").val();
    var v_ds_ult_numero_nota = $("#ds_ult_numero_nota").val();
    var v_dt_vencimento_certificado = $("#dt_vencimento_certificado").val();
    var v_ds_serie_nota = $("#ds_serie_nota").val();
    var v_ds_lote_nota = $("#ds_lote_nota").val();
    var v_ic_status = $("#ic_status").val();
    var dadosServico = fcFormatarDadosServico();

    
    formdata.append("pk",$("#pk").val());
    formdata.append("contas_leads_pk",v_contas_lead_pk);
    formdata.append("contas_config_pk",v_contas_pk);
    formdata.append("razaoSocial",v_ds_razao_social);
    formdata.append("nomeFantasia",v_ds_nome_fantasia);
    formdata.append("cpfCNPJ",v_ds_cnpj);
    formdata.append("inscricaoEstadual",v_ds_inscricao_estadual);
    formdata.append("inscricaoMunicipal",v_ds_inscricao_municipal);
    formdata.append("regimeTributario",v_ic_regime_tributario);
    formdata.append("incentivoFiscal",v_ic_incentivo_fiscal);
    formdata.append("incentivadorCultural",v_ic_incentivo_cultural);
    formdata.append("simplesNacional",v_ic_simples_nacional);
    formdata.append("regimeTributarioEspecial",v_ic_regime_tributario_especial);
    formdata.append("ds_telefone",v_ds_tel);
    formdata.append("ddd",v_ds_ddd);
    formdata.append("email",v_ds_email);
    formdata.append("cep",v_ds_cep);
    formdata.append("logradouro",v_ds_endereco);
    formdata.append("estado",v_ds_uf);
    formdata.append("numero",v_ds_numero);
    formdata.append("complemento",v_ds_complemento);
    formdata.append("codigoCidade",v_ds_cidade);
    formdata.append("tipoLogradouro",v_ds_tipo_logradouro);
    formdata.append("bairro",v_ds_bairro);
    formdata.append("certificado",v_arq_certificado);
    formdata.append("ds_id_certificado",v_n_certificado);
    formdata.append("ds_senha_certificado",v_ds_senha_certificado);
    formdata.append("dt_criacao_certificado",v_dt_criacao_certificado);
    formdata.append("dt_vencimento_certificado",v_dt_vencimento_certificado);
    formdata.append("loginPrefeitura",v_ds_login_prefeitura);
    formdata.append("senhaPrefeitura",v_ds_senha_prefeitura);
    formdata.append("numeroUltNota",v_ds_ult_numero_nota);
    formdata.append("serieNota",v_ds_serie_nota);
    formdata.append("loteNota",v_ds_lote_nota);
    formdata.append("ic_status",v_ic_status);
    formdata.append("dadosServico",dadosServico);

    $.ajax({
        type: 'POST',
        url: '/api/certificados_empresas/contaConfigSalvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true, log.message);
                    sendPost('certificados_empresas','receptivo' ,'');
                }else{
                    utilsJS.toastNotify(false,log.message);
                }
                utilsJS.loaded;
            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}

function fcEnviarDocs(){
    fcValidarCampos();

    var v_contas_pk = $("#ds_empresa").val();
    var v_contas_lead_pk = $("#contas_lead_pk").val();
    var v_ds_razao_social = $("#ds_razao_social").val();
    var v_ds_nome_fantasia = $("#ds_nome_fantasia").val();
    var v_ds_cnpj = $("#ds_cnpj").val();
    var v_ds_inscricao_estadual = $("#ds_inscricao_estadual").val();
    var v_ds_inscricao_municipal = $("#ds_inscricao_municipal").val();
    var v_ic_regime_tributario = $("#ic_regime_tributario").val();
    var v_ic_regime_tributario_especial = $("#ic_regime_tributario_especial").val();
    var ds_tel = $("#ds_tel").val();
        arr_telefone = ds_tel.split(')')
    var v_ds_tel = arr_telefone[1]
    var v_ds_ddd =  arr_telefone[0].replace('(', '')
    
    var v_ic_incentivo_cultural = $("#ic_incentivo_cultural").is(':checked')==true?1:0;
    var v_ic_incentivo_fiscal = $("#ic_incentivo_fiscal").is(':checked')==true?1:0;

    var v_ds_email = $("#ds_email").val();
    var v_ds_cep = $("#ds_cep").val();
    var v_ds_uf = $("#ds_uf").val();
    var v_ds_endereco = $("#ds_endereco").val();
    var v_ds_estado = $("#ds_estado").val();
    var v_ds_numero = $("#ds_numero").val();
    var v_ds_complemento = $("#ds_complemento").val();
    //var v_ds_tipo_logradouro = $("#ds_tipo_logradouro").val();
    var v_ds_bairro = $("#ds_bairro").val();
    var v_arq_certificado = $("#arq_certificado").val();
    var v_n_certificado = $("#n_certificado").val();
    var v_ds_senha_certificado = $("#ds_senha_certificado").val();
    var v_dt_criacao_certificado = $("#dt_criacao_certificado").val();
    var v_dt_vencimento_certificado = $("#dt_vencimento_certificado").val();
    var v_ds_login_prefeitura = $("#ds_login_prefeitura").val();
    var v_ds_senha_prefeitura = $("#ds_senha_prefeitura").val();
    var v_ds_ult_numero_nota = $("#ds_ult_numero_nota").val();
    var v_dt_vencimento_certificado = $("#dt_vencimento_certificado").val();
    var v_ds_serie_nota = $("#ds_serie_nota").val();
    var v_ds_lote_nota = $("#ds_lote_nota").val();
    var v_ic_status = $("#ic_status").val();
    var dadosServico = fcFormatarDadosServico();


    formdata.append("pk",$("#pk").val());
    formdata.append("contas_lead_pk",v_contas_lead_pk);
    formdata.append("contas_config_pk",v_contas_pk);
    formdata.append("razaoSocial",v_ds_razao_social);
    formdata.append("nomeFantasia",v_ds_nome_fantasia);
    formdata.append("cpfCNPJ",v_ds_cnpj);
    formdata.append("inscricaoEstadual",v_ds_inscricao_estadual);
    formdata.append("inscricaoMunicipal",v_ds_inscricao_municipal);
    formdata.append("regimeTributario",v_ic_regime_tributario);
    formdata.append("incentivoFiscal",v_ic_incentivo_fiscal);
    formdata.append("incentivadorCultural",v_ic_incentivo_cultural);
    formdata.append("regimeTributarioEspecial",v_ic_regime_tributario_especial);
    formdata.append("ds_telefone",v_ds_tel);
    formdata.append("ddd",v_ds_ddd);
    formdata.append("email",v_ds_email);
    formdata.append("cep",v_ds_cep);
    formdata.append("logradouro",v_ds_endereco);
    formdata.append("estado",v_ds_uf);
    formdata.append("numero",v_ds_numero);
    formdata.append("complemento",v_ds_complemento);
    //formdata.append("tipoBairro",v_ds_tipo_logradouro);
    formdata.append("bairro",v_ds_bairro);
    formdata.append("certificado",v_arq_certificado);
    formdata.append("ds_id_certificado",v_n_certificado);
    formdata.append("ds_senha_certificado",v_ds_senha_certificado);
    formdata.append("dt_criacao_certificado",v_dt_criacao_certificado);
    formdata.append("dt_vencimento_certificado",v_dt_vencimento_certificado);
    formdata.append("loginPrefeitura",v_ds_login_prefeitura);
    formdata.append("senhaPrefeitura",v_ds_senha_prefeitura);
    formdata.append("numeroUltNota",v_ds_ult_numero_nota);
    formdata.append("serieNota",v_ds_serie_nota);
    formdata.append("loteNota",v_ds_lote_nota);
    formdata.append("ic_status",v_ic_status);
    formdata.append("dadosServico",dadosServico);

    $.ajax({
        type: 'POST',
        url: '/api/certificados_empresas/contaConfigSalvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true, log.message);
                }else{
                    utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
                }

            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}

function validarEmail(email) {
    // Expressão regular para verificar o formato do e-mail
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function fcValidarCampos(){

    if($("#ds_empresa").val()==""){
        $("#alert_ds_empresa").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_empresa").slideUp(500);
        });
        $('#ds_empresa').focus();
        return false;
    }
    if($("#ds_inscricao_municipal").val()==""){
        $("#alert_ds_inscricao_municipal").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_inscricao_municipal").slideUp(500);
        });
        $('#ds_inscricao_municipal').focus();
        return false;
    }
    if($("#ic_regime_tributario").val()==""){
        $("#alert_ic_regime_tributario").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ic_regime_tributario").slideUp(500);
        });
        $('#ic_regime_tributario').focus();
        return false;
    }

    if($("#ic_regime_tributario_especial").val()==""){
        $("#alert_ic_regime_tributario_especial").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ic_regime_tributario_especial").slideUp(500);
        });
        $('#ic_regime_tributario_especial').focus();
        return false;
    }

    if($("#ds_email").val()==""){
        $("#alert_ds_email").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_email").slideUp(500);
        });
        $('#alert_ds_email').focus();
        return false;
    }

    var email = validarEmail($("#ds_email").val())
    if(email == false){
        sweetMensagem('warning', 'Email inválido!');
        return false;
    }

    if($("#ds_cep").val()==""){
        $("#alert_ds_cep").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_cep").slideUp(500);
        });
        $('#alert_ds_cep').focus();
        return false;
    }

    if($("#ds_endereco").val()==""){
        $("#alert_ds_endereco").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_endereco").slideUp(500);
        });
        $('#alert_ds_endereco').focus();
        return false;
    }

    if($("#ds_numero").val()==""){
        $("#alert_ds_numero").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_numero").slideUp(500);
        });
        $('#alert_ds_numero').focus();
        return false;
    }

    if($("#ds_tipo_logradouro").val()==""){
        $("#alert_ds_tipo_logradouro").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_tipo_logradouro").slideUp(500);
        });
        $('#alert_ds_tipo_logradouro').focus();
        return false;
    }

    if($("#ds_bairro").val()==""){
        $("#alert_ds_bairro").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_bairro").slideUp(500);
        });
        $('#alert_ds_bairro').focus();
        return false;
    }

    if($("#n_certificado").val()==""){
        $("#alert_n_certificado").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_n_certificado").slideUp(500);
        });
        $('#n_certificado').focus();
        return false;
    }

    if($("#ds_senha_certificado").val()==""){
        $("#alert_ds_senha_certificado").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_senha_certificado").slideUp(500);
        });
        $('#ds_senha_certificado').focus();
        return false;
    }

    if($("#dt_criacao_certificado").val()==""){
        $("#alert_dt_criacao_certificado").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_criacao_certificado").slideUp(500);
        });
        $('#dt_criacao_certificado').focus();
        return false;
    }

    if($("#dt_vencimento_certificado").val()==""){
        $("#alert_dt_vencimento_certificado").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_vencimento_certificado").slideUp(500);
        });
        $('#dt_vencimento_certificado').focus();
        return false;
    }

    if($("#ds_login_prefeitura").val()==""){
        $("#alert_ds_login_prefeitura").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_login_prefeitura").slideUp(500);
        });
        $('#ds_login_prefeitura').focus();
        return false;
    }

    if($("#ds_senha_prefeitura").val()==""){
        $("#alert_ds_senha_prefeitura").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_senha_prefeitura").slideUp(500);
        });
        $('#ds_senha_prefeitura').focus();
        return false;
    }

    if($("#ds_ult_numero_nota").val()==""){
        $("#alert_ds_ult_numero_nota").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_ult_numero_nota").slideUp(500);
        });
        $('#ds_ult_numero_nota').focus();
        return false;
    }

    if($("#ds_serie_nota").val()==""){
        $("#alert_ds_serie_nota").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_serie_nota").slideUp(500);
        });
        $('#ds_serie_nota').focus();
        return false;
    }

    if($("#ds_lote_nota").val()==""){
        $("#alert_ds_lote_nota").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_lote_nota").slideUp(500);
        });
        $('#ds_lote_nota').focus();
        return false;
    }

}

function fcCarregarEmpresa() {
    var objParametros = {};
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);
    carregarComboAjax($("#ds_empresa"), arrCarregar, " ", "pk", "ds_razao_social");
}


function fcCarregarDadosEmpresa(){
    var objParametros = {
        'pk': $("#ds_empresa").val()
    };
    var arrCarregar = carregarController("conta", "listarPk", objParametros);
    $("#ds_cnpj").val(arrCarregar.data[0]['ds_cpf_cnpj'])
    $("#ds_nome_fantasia").val(arrCarregar.data[0]['ds_conta'])
    $("#ds_razao_social").val(arrCarregar.data[0]['ds_razao_social'])
}

function fcCarregarServicosPk(){
    var objParametros = {
        'servicos_pk': $("#servico_pk").val()
    };
    var arrCarregar = carregarController("certificados_empresas", "listarServicosPk", objParametros);
    let semHifens = arrCarregar.data[0]['codigo_tributacao'].replace(/-/g, "");
    $("#ds_codigo_tributacao").val(semHifens);
}

function fcCancelar(){
    var objParametros = {};
    sendPost('certificados_empresas','receptivo' ,objParametros);
}

function fcCarregar(){
    if($("#pk").val() > 0){
        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("certificados_empresas", "contaConfigConsultaPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_empresa").val(arrCarregar.data['contas_config_pk']);
            $("#contas_pk").val(arrCarregar.data['contas_config_pk']);
            $("#ds_razao_social").val(arrCarregar.data['razaoSocial']);
            $("#ds_nome_fantasia").val(arrCarregar.data['nomeFantasia']);
            $("#ds_cnpj").val(arrCarregar.data['cpfCNPJ']);
            $("#ds_inscricao_estadual").val(arrCarregar.data['inscricaoEstadual']);
            $("#ds_inscricao_municipal").val(arrCarregar.data['inscricaoMunicipal']);
            $("#ic_incentivo_cultural").val(arrCarregar.data['incentivadorCultural']==0?'':$("#ic_incentivo_cultural").attr('checked', true));
            $("#ic_incentivo_fiscal").val(arrCarregar.data['incentivoFiscal']==0?'':$("#ic_incentivo_fiscal").attr('checked', true));
            $("#ic_regime_tributario").val(arrCarregar.data['regimeTributario']);
            $("#ic_regime_tributario_especial").val(arrCarregar.data['regimeTributarioEspecial']);
            $("#ds_tipo_logradouro").val(arrCarregar.data['tipoLogradouro']);
            $("#ds_tel").val(arrCarregar.data['ds_telefone']);
            $("#ds_email").val(arrCarregar.data['email']);
            $("#ds_cep").val(arrCarregar.data['cep']);
            $("#ds_uf").val(arrCarregar.data['estado']);
            $("#ds_cidade").val(arrCarregar.data['cidade']);
            $("#ds_endereco").val(arrCarregar.data['logradouro']);
            $("#ds_numero").val(arrCarregar.data['numero']);
            $("#ds_complemento").val(arrCarregar.data['complemento']);
            $("#ds_bairro").val(arrCarregar.data['bairro']);
            $("#arq_certificado").val(arrCarregar.data['arq_certificado']);
            $("#n_certificado").val(arrCarregar.data['ds_id_certificado']);
            $("#ds_senha_certificado").val(arrCarregar.data['ds_senha_certificado']);
            $("#dt_criacao_certificado").val(arrCarregar.data['dt_criacao_certificado']);
            $("#dt_vencimento_certificado").val(arrCarregar.data['dt_vencimento_certificado']);
            $("#ds_login_prefeitura").val(arrCarregar.data['loginPrefeitura']);
            $("#ds_senha_prefeitura").val(arrCarregar.data['senhaPrefeitura']);
            $("#ds_ult_numero_nota").val(arrCarregar.data['numeroUltNota']);
            $("#ds_serie_nota").val(arrCarregar.data['serieNota']);
            $("#ds_lote_nota").val(arrCarregar.data['loteNota']);
            $("#ic_status").val(arrCarregar.data['ic_status']);
            if(arrCarregar.data['ds_nome_arquivo']!=null){
                fcCarregarGridDocumentosComPk(arrCarregar.data['ds_nome_arquivo']);
            }
            if(arrCarregar.data['servicos']!=null){
                for(var i=0; i<arrCarregar.data['servicos'].length;i++ ){
                    pk = arrCarregar.data['servicos'][i]['pk'] != null ? arrCarregar.data['servicos'][i]['pk'] : ''
                    ic_retencao = arrCarregar.data['servicos'][i]['ic_retencao'] != null ? arrCarregar.data['servicos'][i]['ic_retencao'] : ''
                    ds_retencao = arrCarregar.data['servicos'][i]['ds_retencao'] != null ? arrCarregar.data['servicos'][i]['ds_retencao'] : ''
                    vl_aliquota = arrCarregar.data['servicos'][i]['vl_aliquota'] != null ? arrCarregar.data['servicos'][i]['vl_aliquota'] : ''
                    codigo_tributacao = arrCarregar.data['servicos'][i]['codigo_tributacao'] != null ? arrCarregar.data['servicos'][i]['codigo_tributacao'] : ''
                   
                    tblServico.row.add(
                        [   
                            "<td><input type='hidden' id='item_pk[]' value ='"+pk+"'>"+ pk +"</td>",
                            "<td><input type='hidden' id='servico_nfe_pk[]' value ='"+arrCarregar.data['servicos'][i]['nfe_servicos_prefeitura_pk']+"'>"+arrCarregar.data['servicos'][i]['num_codigo_servico']+"</td>",
                            "<td><input type='hidden' id='codigo_tributacao[]' value ='"+codigo_tributacao+"'>"+codigo_tributacao+"</td>",
                            "<td><input type='hidden' id='ds_servico[]' value ='"+ arrCarregar.data['servicos'][i]['ds_servico']+"'>"+ arrCarregar.data['servicos'][i]['ds_servico'] +"</td>",
                            "<td><input type='hidden' id='iss_retido_nfe_pk[]' value ='"+ic_retencao+"'>"+ds_retencao+"</td>",
                            "<td><input type='hidden' id='ds_aliquota_nfe[]' value ='"+vl_aliquota+"'>"+float2moeda(vl_aliquota)+"</td>",
                            "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue' onclick='fcApagarServico("+pk+")'></i></span></a>"
                        ]
                    ).draw( false );
                }
            }



            
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
    
}

//DOCUMENTOS

function fcCarregarGridDocumentosComPk(nome_arquivo){

    tblDocumentos.row.add(
        [   
            nome_arquivo,
            "<i class='fa fa-download function_download' style='font-size:18px; color:blue'' title='DOWNLOAD DOCUMENTO'></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class='fa fa-trash function_delete' style='font-size:18px; color:blue'' title='EXCLUIR O DOCUMENTO'></i>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_download").on("click",fcDownloadDocumento);
    $(".function_delete").on("click",fcExcluirDocumento);
    return false;
        
    
}

function fcDownloadDocumento(){
    var arrCarregar = permissao("documento", "ins");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }

    //var url_documento = (window.location.protocol+"//"+window.location.host+"/app/src/docs/"+ds_documento)

    //DOWNLOAD
    var v_url = "/documento/downloadCertificado?pk="+$("#pk").val();

    window.open(v_url, '_blank');
}

function fcExcluirDocumento(){
    var arrCarregar = permissao("documento", "del");

    if (arrCarregar.status != true){
        sweetMensagem('warning','Você não tem permissão');
        return false;
    }
    if($("#pk").val() != ""){

        var objParametros = {
            "pk": $("#pk").val()
        };

        var arrExcluir = carregarController("certificados_empresas", "excluirDocs", objParametros);

        if (arrExcluir.status == true){

            //Exibe a mensagem
            utilsJS.toastNotify(true,arrExcluir.message);
            //fcExcluirArquivo(v_ds_documento);
            tblDocumentos.clear().destroy();
            fcCarregarGridDocsSemPk();
            fcCarregar();
        }
        else{
            utilsJS.toastNotify(false,'Falhou a requisição de exclusão.');
        }
    }
    else{
        sweetMensagem('warning','Código não encontrado');
    }
}
function fcCarregarGridDocsSemPk(){
    try {
            tblDocumentos = $("#tblDocumentos").DataTable(
                {
                    "searching": false,
                    "paging": false,
                    "scrollX": true,
                    "columnDefs" : [{
                        orderable: false,
                        targets: [0,1]
                    }]
                }
            );
            return false;
        
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}

function fcIncluirLinhaArquivo(nome_original){
    tblDocumentos.row.add(
        [   
            nome_original,
            "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue'></i></span></a>"
        ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarArquivo);
    return false;
}

function fcApagarArquivo(){

    tblDocumentos.row($(this).parents('tr')).remove().draw();
}

function fcCarregarListarServicos(){
    var objParametros = {
        "contas_pk": ""
    };
    
    var arrCarregar = carregarController("certificados_empresas", "listarNfeServico", objParametros);
    carregarComboAjax($("#servico_pk"), arrCarregar, " ", "pk", "ds_servico");

}

function fcCarregarGridServicosSemPk(){
    try {
            tblServico = $("#tblServico").DataTable(
                {
                    "searching": false,
                    "paging": false,
                    "scrollX": true,
                    "columnDefs" : [{
                        orderable: false,
                        targets: [0,1]
                    }]
                }
            );
            return false;
        
    } catch (error) {
        utilsJS.toastNotify(false,error);
    }
}

function fcAddLinhaServico(){
    if($("#servico_pk").val()==""){
        $("#alert_servico_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_servico_pk").slideUp(500);
        });
        $('#servico_pk').focus();
        return false;
    }

    tblServico.row.add(
        [   
            "<td><input type='hidden' id='item_pk[]' value =''>",
            "<td><input type='hidden' id='servico_nfe_pk[]' value ='"+$("#servico_pk option:selected").val()+"'>"+ $("#servico_pk option:selected").val() +"</td>",
            "<td><input type='hidden' id='codigo_tributacao[]' value ='"+$("#ds_codigo_tributacao").val()+"'>"+ $("#ds_codigo_tributacao").val() +"</td>",
            "<td><input type='hidden' id='ds_servico[]' value ='"+$("#servico_pk option:selected").text()+"'>"+ $("#servico_pk option:selected").text() +"</td>",
            "<td><input type='hidden' id='iss_retido_nfe_pk[]' value ='"+$("#iss_retido_pk option:selected").val()+"'>"+ $("#iss_retido_pk option:selected").text() +"</td>",
            "<td><input type='hidden' id='ds_aliquota_nfe[]' value ='"+$("#ds_aliquota").val()+"'>"+ $("#ds_aliquota").val() +"</td>",
            "<a class='function_delete'><span><i class='fa fa-trash' style='font-size:18px; color:blue'></i></span></a>"
        ]
    ).draw( false );

    $("#servico_pk").val("")
    $("#iss_retido_pk").val("")
    $("#ds_aliquota").val("")

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcApagarServicoSemPk);
    return false;
}

function fcApagarServicoSemPk(){
    tblServico.row($(this).parents('tr')).remove().draw();

}

function fcApagarServico(servico_pk){
   
    if(servico_pk != ''){
        var objParametros = {
            "pk": servico_pk
        };
    
        var arrExcluir = carregarController("certificados_empresas", "excluirServico", objParametros);
        if(arrExcluir.status == 1){
            utilsJS.toastNotify(true,'Item excluido com sucesso.');
            tblServico.clear().destroy();
            fcCarregarGridServicosSemPk();
            fcCarregar();
        }
    }
}

function fcFormatarDadosServico(){
    var servico_pk = "";
    var iss_retido_pk = "";
    var vl_aliquota = "";
    var contas_pk = $('#ds_empresa').val();

    var arrKeys = [];
    var arrDados = [];

    arrKeys[0] = "item_pk";
    arrKeys[1] = "servico_pk";
    arrKeys[2] = "iss_retido_pk";
    arrKeys[3] = "vl_aliquota";
    arrKeys[4] = "contas_pk";
    arrKeys[5] = "codigo_tributacao";

    var i = 0;
    var table = $('#tblServico').DataTable();
    var numRows = table.rows().count();
    if(numRows==0){
        return  arrayToJson(arrKeys, arrDados);
        
    }
    $("#tblServico").find('tbody tr').each(function () {
        if ($(this).find('td:nth-child(2) input').val() != "") {
            item_pk = $(this).find('td:nth-child(1) input').val();
            servico_pk = $(this).find('td:nth-child(2) input').val();
            codigo_tributacao = $(this).find('td:nth-child(3) input').val();
            ds_servico = $(this).find('td:nth-child(4) input').val();
            iss_retido_pk = $(this).find('td:nth-child(5) input').val();
            vl_aliquota = moeda2float($(this).find('td:nth-child(6) input').val());

            arrDados[i] = [item_pk, servico_pk, iss_retido_pk, vl_aliquota, contas_pk,codigo_tributacao];
            i++;
        }
    });
    return arrayToJson(arrKeys, arrDados);
}

function fcCarregarContaPrincipal(){
    
    var arrCarregar = carregarController("conta", "verificaContaPrincipal",  '');
    $('#contas_lead_pk').val(arrCarregar.data[0]['pk'])
}

$(document).ready(function(){
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdCancelar2', fcCancelar);
    $(document).on('click', '#cmdSalvar', fcEnviar);
    $(document).on('click', '#cmdSalvar2', fcEnviar);
    $(document).on('click', '#cmdFecharModal', fcFecharModalAddServico);
    $(document).on('click', '#cmdSalvarServico', fcSalvarServico);
    $(document).on('click', '#cmdAddLinhaServico', fcAddLinhaServico);
    fcCarregarEmpresa();
    fcCarregarContaPrincipal();

    $("#ds_empresa").change(function () {
        fcCarregarDadosEmpresa();
    });
    $("#servico_pk").change(function () {
        fcCarregarServicosPk();
    });
        
    
    $('#dt_criacao_certificado').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();

    $("#dt_criacao_certificado").keypress(function(){
        mascara(this,mdata);
    });


    $('#dt_vencimento_certificado').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();

    $("#dt_vencimento_certificado").keypress(function(){
        mascara(this,mdata);
    });

    $("#ds_tel").keypress(function(){
        mascara(this,mascaraTelefone);
    });
    $("#ds_cep").keypress(function(){
        mascara(this,cep);
    });
    $("#ds_numero").keypress(function(){
        mascara(this,soNumeros);
    });
    
    $("#ds_aliquota").keypress(function(){
        mascara(this,moeda);
    });
    
    $("#ds_cep").change(function(){
        fcCarregarCep($("#ds_cep").val());
    });

    fcCarregarGridServicosSemPk();
    fcCarregarGridDocsSemPk();
    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();
    
    $("#cmdAddServico").on('click',function(){
        if($('#ds_empresa').val()==null || $('#ds_empresa').val()==""){
            sweetMensagem('warning',"Informe a Razão Social antes de cadastrar um serviço!");
        }
        else{
            fcAbrirModalAddServico($('#ds_empresa').val());
        }
    });

    formdata = new FormData();
    fcCarregarListarServicos();
    


    //CARREGAR DOCUMENTOS

    if($("#pk").val()==""){

        $('#fileupload').off('change').on('change', function() {
            var arrCarregar = permissao("documento", "ins");
            if (arrCarregar.status != true) {
                sweetMensagem('warning', 'Você não tem permissão para cadastrar um documento');
                return false;
            }
            //on change event
            if($(this).prop('files').length > 0){
                $.each($(this).prop('files'), function (index, file) {
                    formdata.append(index, file);
                    
                    fcIncluirLinhaArquivo(file.name);
                });
            }
        });
    }
    else{
        $('#fileupload').off('change').on('change', function() {
            var arrCarregar = permissao("documento", "ins");
            if (arrCarregar.status != true) {
                sweetMensagem('warning', 'Você não tem permissão para cadastrar um documento');
                return false;
            }
            //on change event
            if($(this).prop('files').length > 0){
                $.each($(this).prop('files'), function (index, file) {
                    formdata.append(index, file);
                    
                    fcEnviarDocs();

                    tblDocumentos.clear().destroy();
                    
                    fcCarregarGridDocsSemPk();
                    fcCarregar();
                });
            }
        });
    }

});
