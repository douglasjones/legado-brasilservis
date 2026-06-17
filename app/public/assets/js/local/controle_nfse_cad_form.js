var tblDocumentos;
var tblServico;
var INSS = 0.7;


function fcInformacoesSevicos(){
    var objParametros = {
        'codigoServico': $('#codigoServico').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarDadosServico", objParametros);
    $("#descricaoServico").val(arrCarregar.data[0]['ds_servico'])
    $("#aliquota").val(arrCarregar.data[0]['vl_aliquota'])
    $("#codigo_tributacao").val(arrCarregar.data[0]['codigo_tributacao'])
    
    fcArrumarTextarea();
}

function listarPrestador(){
    
    var objParametros = {
        'contas_origem_pk': $('#contas_lead_pk').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "contaConfigListarEmpresas", objParametros);
    carregarComboAjax($("#prestador"), arrCarregar, " ", "pk", "razaoSocial");
}

function listarServico(){
    
    var objParametros = {
        'contas_pk': $('#prestador').val()
    };        
    var arrCarregar = carregarController("certificados_empresas", "listarNfeServico", objParametros);
    carregarComboAjax($("#listaServicoConsulta"), arrCarregar, " ", "num_codigo_servico", "ds_servico");
}

function listaDiscriminacaoServico(){    
    var objParametros = {
        'pk': ''
    };        
    var arrCarregar = carregarController("discriminacao_servicos", "listarDiscriminacao", objParametros);
    carregarComboAjax($("#listaDiscriminacaoServico"), arrCarregar, " ", "pk", "ds_discriminacao_servico");
}
function fcCarregarContaPrincipal(){
    
    var arrCarregar = carregarController("conta", "verificaContaPrincipal",  '');
    $('#contas_lead_pk').val(arrCarregar.data[0]['pk'])
}

function fcEnviar(){
  
    /*utilsJS.toastNotify(true, "Dados Salvos com sucesso!!");
    setTimeout(function() {
        sendPost('controle_nfse','receptivoFake' ,'');
    }, 2000);*/
    
    var tipoNotaFiscalEletronica = $("#tipoNotaFiscalEletronica").val();
    var prestador = $("#prestador").val();
    var naturezaOperacao = $("#naturezaOperacao").val();
    var razaoSocial = $("#razaoSocialParaEnviarNaNota").val();
    var cnpj = $("#cnpj").val();
    var cep = $("#cep").val();
    var v_estado = $("#ds_uf").val();
    var v_cidade = $("#ds_cidade").val();
    var v_bairro = $("#ds_bairro").val();
    var v_tipoLogradouro = $("#tipoLogradouro").val();
    var v_logradouro = $("#ds_endereco").val();
    var v_numero = $("#numero").val();
    var v_complemento = $("#complemento").val();
    var v_inscricaoMunicipal = $("#inscricaoMunicipal").val();
    var v_inscricaoEstadual = $("#inscricaoEstadual").val();
    var v_retidoTomador = $("#retidoTomador1").is(':checked')==true?1:2;
    
    var v_email = $("#email").val();
    var v_listaServicoConsulta = $("#listaServicoConsulta").val();
    var v_codigo = $("#codigoServico").val();
    var v_descricaoServico = $("#descricaoServico").val();
    var v_aliquota = $("#aliquota").val();
    var v_codigo_tributacao = $("#codigo_tributacao").val();
    var v_discriminacao = $("#discriminacao").val();
    var v_valorServico = $("#valorServico").val();
    var vl_liquido = $("#vl_liquido").val();
    var v_valorDeducao = $("#valorDeducao").val();
    
    var v_numeroRPS = $("#numeroRPS").val();
    var v_serieRPS = $("#serieRPS").val();
    
    var v_dataEmissaoRPS = $("#dataEmissaoRPS").val();
   
    var iss_aliquota = $("#iss_aliquota").val();
    var iss_valor = $("#iss_valor").val();
    var inss_aliquota = $("#inss_aliquota").val();
    var inss_valor = $("#inss_valor").val();
    var pis_aliquota= $("#pis_aliquota").val();
    var pis_valor= $("#pis_valor").val();
    var cofins_aliquota= $("#cofins_aliquota").val();
    var cofins_valor= $("#cofins_valor").val();
    var ir_aliquota= $("#ir_aliquota").val();
    
    var ir_valor= $("#ir_valor").val();
    var csll_aliquota= $("#csll_aliquota").val();
    var csll_valor= $("#csll_valor").val();
    var dt_vencimento= $("#dt_vencimento").val();
    var dt_competencia= $("#dt_competencia").val();
     
    formdata.append("pk",$("#pk").val());
    
    formdata.append("tipoNotaFiscalEletronica",tipoNotaFiscalEletronica);
    formdata.append("prestador",prestador);
    formdata.append("naturezaOperacao",naturezaOperacao);
    formdata.append("razaoSocial",razaoSocial);
    formdata.append("cnpj",cnpj);
    formdata.append("cep",cep);
    formdata.append("estado",v_estado);
    formdata.append("cidade",v_cidade);
    formdata.append("bairro",v_bairro);
    
    formdata.append("tipoLogradouro",v_tipoLogradouro);
    formdata.append("logradouro",v_logradouro);
    formdata.append("numero",v_numero);
    formdata.append("complemento",v_complemento);
    formdata.append("inscricaoMunicipal",v_inscricaoMunicipal);
    formdata.append("inscricaoEstadual",v_inscricaoEstadual);
    formdata.append("retidoTomador",v_retidoTomador);
    formdata.append("email",v_email);
    
    formdata.append("listaServicoConsulta",v_listaServicoConsulta);
    formdata.append("codigo",v_codigo);
    formdata.append("descricaoServico",v_descricaoServico);
    
    formdata.append("aliquota",v_aliquota);

    formdata.append("codigo_tributacao",v_codigo_tributacao);

    formdata.append("discriminacao",v_discriminacao);

    
    formdata.append("valorServico",moeda2float(v_valorServico));
    formdata.append("vl_liquido",(vl_liquido));
    formdata.append("valorDeducao",moeda2float(v_valorDeducao));
    formdata.append("numeroRPS",v_numeroRPS);
    formdata.append("serieRPS",v_serieRPS);
    formdata.append("dataEmissaoRPS",v_dataEmissaoRPS);
    
    formdata.append("iss_aliquota",moeda2float(iss_aliquota));
    formdata.append("iss_valor",moeda2float(iss_valor));
    formdata.append("inss_aliquota",moeda2float(inss_aliquota));
    formdata.append("inss_valor",moeda2float(inss_valor));
    formdata.append("pis_aliquota",moeda2float(pis_aliquota));
    formdata.append("pis_valor",moeda2float(pis_valor));
    formdata.append("cofins_aliquota",moeda2float(cofins_aliquota));
    formdata.append("cofins_valor",moeda2float(cofins_valor));
    formdata.append("ir_aliquota",moeda2float(ir_aliquota));
    formdata.append("ir_valor",moeda2float(ir_valor));
    formdata.append("csll_aliquota",moeda2float(csll_aliquota));
    formdata.append("csll_valor",moeda2float(csll_valor));
    formdata.append("dt_vencimento",dt_vencimento);
    formdata.append("dt_competencia",dt_competencia);
    
    
    $.ajax({
        type: 'POST',
        url: '/api/controle_nfse/salvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true, log.message);
                    sendPost('controle_nfse','receptivo' ,'');
                }else{
                    utilsJS.toastNotify(false,log.message);
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


function fcCarregarEmpresa() {
    var objParametros = {};
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#razaoSocial"), arrCarregar, " ", "pk", "ds_lead");
}

function fcCancelar(){
    var objParametros = {};
    sendPost('controle_nfse','receptivo' ,objParametros);
}

function fcCarregar(){
    if($("#pk").val() > 0){
        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("certificados_empresas", "contaConfigConsultaPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_empresa").val(arrCarregar.data['contas_config_pk']);
            
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
    
}
function fcCarregarAliquotaPorMunicipio(ds_uf,ds_cidade){
    if(ds_uf!="" && ds_cidade!=""){
            var objParametros = {
                "ds_uf": ds_uf,
                "ds_cidade":ds_cidade
            };        
            
            var arrCarregar = carregarController("iss_municipio", "pegarAliquotaPorMunicipio", objParametros);
            
            if (arrCarregar.status == true){
            
                $("#iss_aliquota").val(arrCarregar.data[0]['vl_aliquota_iss']);
                
            }
           
        }
    
}

function fcCarregarDadosEmpresa(){

    $("#cnpj").val("");
    $("#cep").val("");
    $("#ds_uf").val("");
    $("#ds_cidade").val("");
    $("#ds_bairro").val("");
    $("#ds_endereco").val("");
    $("#numero").val("");
    $("#complemento").val("");
    var objParametros = {
        'pk': $("#razaoSocial").val()
    };
    var arrCarregar = carregarController("lead", "listarPk", objParametros);
    $("#cnpj").val(arrCarregar.data[0]['ds_cpf_cnpj']);
    $("#cep").val(arrCarregar.data[0]['ds_cep']);
    $("#razaoSocialParaEnviarNaNota").val(arrCarregar.data[0]['pk']);
    $("#ds_uf").val(arrCarregar.data[0]['ds_uf']);
    $("#ds_cidade").val(arrCarregar.data[0]['ds_cidade']);
    $("#ds_bairro").val(arrCarregar.data[0]['ds_bairro']);
    $("#ds_endereco").val(arrCarregar.data[0]['ds_endereco']);
    $("#numero").val(arrCarregar.data[0]['ds_numero']);
    $("#complemento").val(arrCarregar.data[0]['ds_complemento']);
    $("#email").val(arrCarregar.data[0]['ds_email']);



    if(arrCarregar.data[0]['ic_iss_retido_tomador']==1){
        $('input[id="retidoTomador1"][value="' + arrCarregar.data[0]['ic_iss_retido_tomador'] + '"]').prop('checked', true);
    }
    else{
        $('input[id="retidoTomador2"][value="' + arrCarregar.data[0]['ic_iss_retido_tomador'] + '"]').prop('checked', true);
    }
    if(arrCarregar.data[0]['ic_inss_aplicacao']==2){
        INSS = 0.8;
    }
    if (arrCarregar.data[0]['dia_faturamento'] != null) {
        let diaFaturamento = parseInt(arrCarregar.data[0]['dia_faturamento'], 10);
        let hoje = new Date();
        let ano = hoje.getFullYear();
        let mes = hoje.getMonth() + 1;  // Janeiro = 0 → soma +1 para humano

        mes += 1;  // Próximo mês
        if (mes > 12) {
            mes = 1;
            ano += 1;
        }

        let ultimoDia = new Date(ano, mes, 0).getDate();
        let dia = Math.min(diaFaturamento, ultimoDia);

        let dataVencimento = new Date(ano, mes - 1, dia);

        let diaStr = String(dataVencimento.getDate()).padStart(2, '0');
        let mesStr = String(dataVencimento.getMonth() + 1).padStart(2, '0');  // Mês humano
        let anoStr = dataVencimento.getFullYear();

        let dataFormatada = `${diaStr}/${mesStr}/${anoStr}`;

        $("#dt_vencimento").val(dataFormatada);
    }

    

    if(arrCarregar.data[0]['ds_cep']==""){
        fcCarregarCep(arrCarregar.data[0]['ds_cep']);
    }

    setTimeout(function() {
        if( $("#ds_uf").val() !=""){
            if($("#ds_cidade").val() !=""){
                fcCarregarAliquotaPorMunicipio($("#ds_uf").val(),$("#ds_cidade").val());
            }
        }
    }, 2000);
    
    
    
    
}


function fcArrumarTextarea(){

    $("#descricao_nfse").val(" ");
    let valor_total = $('#valorServico').val() != '' ? moeda2float($('#valorServico').val()) : '';
    let vl_iss = $('#iss_valor').val();
    let vl_inss = $('#inss_valor').val();
    let vl_pis = $('#pis_valor').val();
    let vl_cofins = $('#cofins_valor').val();
    let vl_ir = $('#ir_valor').val();
    let vl_csll = $('#csll_valor').val();
    let dt_emissao = $('#dataEmissaoRPS').val();
    let dt_vencimento = $('#dt_vencimento').val();
    let dt_competencia = $('#dt_competencia').val();

    let texto = "";
    if($('#descricaoServico').val() != ''){
        //texto += $('#descricaoServico').val() + "\n\n\n";
        //texto += $('#descricaoServico').val() + "|||";
    }

    if($('#listaServicoConsulta').val() != ''){
        $("#listaServicoConsulta option:selected").text();
    }

	if($('#listaDiscriminacaoServico').val() != ''){
        texto += $("#listaDiscriminacaoServico option:selected").text() + "|||";
    }
    if(dt_emissao != ''){
        dt_emissao = DataYMD(dt_emissao);
        data = new Date(dt_emissao);
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

        /*texto += "COMPETENCIA  " + ds_mes + "\n";
        texto += "DATA DE VENCIMENTO " + $('#dataEmissaoRPS').val() + "\n";*/
        texto += "COMPETENCIA  " + ds_mes + "|\n";
        texto += "VENCIMENTO " + $('#dataEmissaoRPS').val() + "|";
    }

    if(valor_total != ''){
        //texto += "VALOR DA NOTA FISCAL " + valor_total + "\n";
        texto += "VALOR DA NOTA FISCAL R$ " + float2moeda(valor_total) + "|";
    }
    

    if(vl_iss != '' && vl_iss != '0,00'){
        //texto += "ISS RETIDO R$ " + vl_iss + "\n";
        texto += "ISS RETIDO R$ " + vl_iss + " |";
        valor_total = (valor_total) - moeda2float(vl_iss);
    }

    if(vl_inss != '' && vl_inss != '0,00'){
        //texto += "INSS RETIDO R$ " + vl_inss + "\n";
        texto += "INSS RETIDO R$ " + vl_inss + " |";
        valor_total = (valor_total) - moeda2float(vl_inss);
    }

    if(vl_pis != '' && vl_pis != '0,00'){
        //texto += "PIS R$ " + vl_pis + "\n";
        texto += "PIS R$ " + vl_pis + "|";
        valor_total = (valor_total) - moeda2float(vl_pis);
    }

    if(vl_cofins != '' && vl_cofins != '0,00'){
        //texto += "COFINS R$ " + vl_cofins + "\n";
        texto += "COFINS R$ " + vl_cofins + "|";
        valor_total = (valor_total) - moeda2float(vl_cofins);
    }

    if(vl_ir != '' && vl_ir != '0,00'){
        //texto += "IR R$ " + vl_ir + "\n";
        texto += "IR R$ " + vl_ir + "|";
        valor_total = (valor_total) - moeda2float(vl_ir);
    }

    if(vl_csll != '' && vl_csll != '0,00'){
        //texto += "CSLL R$ " + vl_csll + "\n";
        texto += "CSLL  R$ " + vl_csll + "|";
        valor_total = (valor_total) - moeda2float(vl_csll);
    }

    if(valor_total != ''){
        //texto += "VALOR LIQUIDO R$ " + float2moeda(valor_total) + "\n";
        texto += "VALOR LIQUIDO R$ " + float2moeda(valor_total) + "|";

        $("#vl_liquido").val(valor_total);
    }

    if(dt_vencimento != ''){
        //texto += "VALOR DA NOTA FISCAL " + valor_total + "\n";
        texto += "VENCIMENTO " + dt_vencimento + "|";
    }
    if(dt_competencia != ''){
        //texto += "VALOR DA NOTA FISCAL " + valor_total + "\n";
        texto += "COMPETENCIA " + dt_competencia + "|";
    }

    $("#discriminacao").val(texto);
}

var formdata = null;
$(document).ready(function(){

    $("#inss_aliquota").val("11,00");
    $("#pis_aliquota").val("0,65");
    $("#cofins_aliquota").val("3,00");
    $("#ir_aliquota").val("1,00");
    $("#csll_aliquota").val("1,00");
    
    $("#cep").keypress(function(){
        mascara(this,cep);
    });

    $("#ds_numero").keypress(function(){
        mascara(this,soNumeros);
    });
    
    $("#aliquota").keypress(function(){
        mascara(this,moeda);
    });
    
    $("#cep").change(function(){
        fcCarregarCep($("#cep").val());
    });

    $("#cnpj").keypress(function(){
        chama_mascara(this);
    }); 

    $("#valorServico").keypress(function(){
        mascara(this,moeda);
    });   

    $("#valorDeducao").keypress(function(){
        mascara(this,moeda);
    });   

    $("#numero").keypress(function(){
        mascara(this,soNumeros);
    });

    $("#valorServico").keypress(function(){
        mascara(this,moeda);
    });   

    $("#iss_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#iss_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#inss_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#inss_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#pis_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#pis_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#cofins_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#cofins_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#ir_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#ir_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#csll_aliquota").keypress(function(){
        mascara(this,moeda);
    });   

    $("#csll_valor").keypress(function(){
        mascara(this,moeda);
    });   

    $("#cmdInformacoesSevicos").click(function(){
        fcArrumarTextarea()
    })
      $("#listaDiscriminacaoServico").change(function(){
        fcArrumarTextarea()
    })

    $("#cmdCancelar").click(function(){
        fcCancelar()
    })
    $("#cmdCancelar1").click(function(){
        fcCancelar()
    })

    
    
    $("#listaServicoConsulta").change(function(){
        fcArrumarTextarea()
    });
    
    $("#textarea").change(function(){
        detalhamento_servico =  $("#textarea").val();
        fcArrumarTextarea()
    });
    
    $("#valorServico").change(function(){
        vl_iss = moeda2float($("#valorServico").val()) * (moeda2float($("#iss_aliquota").val()) / 100) ;
        $("#iss_valor").val(float2moeda(vl_iss));

        vl_inss = (moeda2float($("#valorServico").val()) * INSS) * (moeda2float($("#inss_aliquota").val()) / 100);
        $("#inss_valor").val(float2moeda(vl_inss));

        vl_pis = moeda2float($("#valorServico").val()) * (moeda2float($("#pis_aliquota").val()) / 100);
        $("#pis_valor").val(float2moeda(vl_pis));

        vl_cofins = moeda2float($("#valorServico").val()) * (moeda2float($("#cofins_aliquota").val()) / 100);
        $("#cofins_valor").val(float2moeda(vl_cofins));

        vl_ir = moeda2float($("#valorServico").val()) * (moeda2float($("#ir_aliquota").val()) / 100);
        $("#ir_valor").val(float2moeda(vl_ir));

        vl_csll = moeda2float($("#valorServico").val()) * (moeda2float($("#csll_aliquota").val()) / 100);
        $("#csll_valor").val(float2moeda(vl_csll));

        fcArrumarTextarea()
    });
    $("#iss_aliquota").change(function(){
        vl_iss = moeda2float($("#valorServico").val()) * (moeda2float($("#iss_aliquota").val()) / 100) ;
        $("#iss_valor").val(float2moeda(vl_iss));
        fcArrumarTextarea()
    });
    

    $("#iss_valor").change(function(){
        vl_iss = $("#iss_valor").val();
        fcArrumarTextarea()
    });

    $("#inss_aliquota").change(function(){
        vl_inss = (moeda2float($("#valorServico").val()) * INSS) * (moeda2float($("#inss_aliquota").val()) / 100);
        $("#inss_valor").val(float2moeda(vl_inss));
        fcArrumarTextarea()
    });

    $("#inss_valor").change(function(){
        vl_inss = $("#inss_valor").val();
        fcArrumarTextarea()
    });

    $("#pis_aliquota").change(function(){
        vl_pis = moeda2float($("#valorServico").val()) * (moeda2float($("#pis_aliquota").val()) / 100);
        $("#pis_valor").val(float2moeda(vl_pis));
        fcArrumarTextarea()
    });

    $("#pis_valor").change(function(){
        vl_pis = $("#pis_valor").val();
        fcArrumarTextarea()
    });

    $("#cofins_aliquota").change(function(){
        vl_cofins = moeda2float($("#valorServico").val()) * (moeda2float($("#cofins_aliquota").val()) / 100);
        $("#cofins_valor").val(float2moeda(vl_cofins));
        fcArrumarTextarea()
    });

    $("#cofins_valor").change(function(){
        vl_cofins = $("#cofins_valor").val();
        fcArrumarTextarea()
    });

    $("#ir_aliquota").change(function(){
        vl_ir = moeda2float($("#valorServico").val()) * (moeda2float($("#ir_aliquota").val()) / 100);
        $("#ir_valor").val(float2moeda(vl_ir));
        fcArrumarTextarea()
    });

    $("#ir_valor").change(function(){
        vl_ir = $("#ir_valor").val();
        fcArrumarTextarea()
    });

    $("#csll_aliquota").change(function(){
        vl_csll = moeda2float($("#valorServico").val()) * (moeda2float($("#csll_aliquota").val()) / 100);
        $("#csll_valor").val(float2moeda(vl_csll));
        fcArrumarTextarea()
    });

    $("#csll_valor").change(function(){
        vl_csll = $("#csll_valor").val();
        fcArrumarTextarea()
    });

    $("#dataEmissaoRPS").change(function(){
        fcArrumarTextarea()
    });
    $("#dt_vencimento").change(function(){
        fcArrumarTextarea()
    });
    $("#dt_competencia").change(function(){
        fcArrumarTextarea()
    });

    $('#dataEmissaoRPS').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();

    $("#dataEmissaoRPS").keypress(function(){
        mascara(this,mdata);
    });
    $('#dt_vencimento').datepicker({defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();

    $("#dt_vencimento").keypress(function(){
        mascara(this,mdata);
    });
    

    fcCarregarEmpresa();
    
    $("#razaoSocial").change(function () {
        fcCarregarDadosEmpresa();
    });
	listaDiscriminacaoServico();
    
    
    formdata = new FormData();
    listarPrestador();
    $("#prestador").change(function () {
        listarServico();
    });

    $("#listaServicoConsulta").change(function () {
        var listaServicoConsulta = $("#listaServicoConsulta").val();
        strValor = listaServicoConsulta.split("-")
        $('#codigoServico').val(strValor[0]);
        fcInformacoesSevicos();
    });

    $("#ds_uf").change(function () {
        fcCarregarAliquotaPorMunicipio($("#ds_uf").val(),$("#ds_cidade").val());
    });
    $("#ds_cidade").change(function () {
   
        fcCarregarAliquotaPorMunicipio($("#ds_uf").val(),$("#ds_cidade").val());
    });

    

    
    //Atribui os eventos
    $(document).on('click', '#cmdInformacoesSevicos', fcInformacoesSevicos);
    $(document).on('click', '#cmdSalvar', fcEnviar);
    $(document).on('click', '#cmdSalvar1', fcEnviar);
    
    fcCarregarContaPrincipal();


    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();

    
    $(".chzn-select").chosen({ allow_single_deselect: true });
    
    
});
