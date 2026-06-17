
function fcEnviarSolicitacaoCompras(){
    //Validações de campos
    if($("#ds_compra_solicitacao").val()==""){
        $("#alert_ds_compra_solicitacao").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_ds_compra_solicitacao").slideUp(500);
        });
        $('#ds_compra_solicitacao').focus();
        return false;
    }
    if($("#empresas_pk").val()==""){
        $("#alert_empresas_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_empresas_pk").slideUp(500);
        });
        $('#empresas_pk').focus();
        return false;
    }
    if($("#dt_solicitacao").val()==""){
        $("#alert_dt_solicitacao").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_solicitacao").slideUp(500);
        });
        $('#dt_solicitacao').focus();
        return false;
    }
    if($("#combo_usuario_aprovacao_pk").val()==""){
        $("#alert_usuario_aprovacao_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_usuario_aprovacao_pk").slideUp(500);
        });
        $('#combo_usuario_aprovacao_pk').focus();
        return false;
    }
    if($("#solicitante_pk").val()==""){
        $("#alert_solicitante_pk").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_solicitante_pk").slideUp(500);
        });
        $('#solicitante_pk').focus();
        return false;
    }
    var v_empresas_pk = $("#empresas_pk").val();
    var v_solicitante_pk = $("#solicitante_pk").val();
    var v_ds_compra_solicitacao = $("#ds_compra_solicitacao").val();
    var v_dt_solicitacao = $("#dt_solicitacao").val();
    var v_usuario_aprovacao_pk = $("#combo_usuario_aprovacao_pk").val();
    var v_obs_solicitacao = $("#obs_solicitacao").val();
    var v_tipo_grupo_centro_custo_pk = $("#tipo_grupo_centro_custo_pk").val();
    var v_grupo_lancamento_centrocusto_pk = $("#grupo_lancamento_centrocusto_pk").val();
    var v_dt_aprovacao = $("#dt_aprovacao").val();
    var v_obs_aprovacao = $("#obs_aprovacao").val();
    var objParametros = {
        "pk": $("#compra_solicitacao_pk").val(),
        "empresas_pk": (v_empresas_pk),
        "solicitante_pk": (v_solicitante_pk),
        "ds_compra_solicitacao": (v_ds_compra_solicitacao),
        "dt_solicitacao": (v_dt_solicitacao),
        "usuario_aprovacao_pk": (v_usuario_aprovacao_pk),
        "obs_solicitacao": (v_obs_solicitacao),
        "tipo_grupo_centro_custo_pk": (v_tipo_grupo_centro_custo_pk),
        "grupo_lancamento_centrocusto_pk": (v_grupo_lancamento_centrocusto_pk),
        "dt_aprovacao": (v_dt_aprovacao),
        "obs_aprovacao": (v_obs_aprovacao)
    };
    var arrEnviar = carregarController("compra_solicitacao", "salvar", objParametros);

    if (arrEnviar.status == true){
        utilsJS.toastNotify(true,arrEnviar.message);
        var v_compra_solicitacao_pk = arrEnviar.data
        sendPost('compra_solicitacao_orcamento',"cadForm",{ compra_solicitacao_pk: v_compra_solicitacao_pk, pk:'',usuario_aprovacao_pk:''});
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    sendPost("compra_solicitacao","receptivo", {});
}

function fcCarregarSolicitacao(){
    if($("#compra_solicitacao_pk").val() > 0){
        var objParametros = {
            "pk": $("#compra_solicitacao_pk").val()
        };
        var arrCarregar = carregarController("compra_solicitacao", "listarPk", objParametros);
        if (arrCarregar.status == true){
            $("#empresas_pk").val(arrCarregar.data[0]['empresas_pk']);
            $("#solicitante_pk").val(arrCarregar.data[0]['solicitante_pk']);
            fcComboAprovador();//carrega combo de aprovadores conforme o solicitante
            $("#ds_compra_solicitacao").val(arrCarregar.data[0]['ds_compra_solicitacao']);
            $("#dt_solicitacao").val(arrCarregar.data[0]['dt_solicitacao']);
            $("#obs_solicitacao").val(arrCarregar.data[0]['obs_solicitacao']);
            $("#combo_usuario_aprovacao_pk").val(arrCarregar.data[0]['usuario_aprovacao_pk']);
            $("#dt_aprovacao").val(arrCarregar.data[0]['dt_aprovacao']);
            $("#obs_aprovacao").val(arrCarregar.data[0]['obs_aprovacao']);
            $("#tipo_grupo_centro_custo_pk").val(arrCarregar.data[0]['tipo_grupo_centro_custo_pk']);
            fcComboGruposCentroCusto();
            $("#grupo_lancamento_centrocusto_pk").val(arrCarregar.data[0]['grupo_lancamento_centrocusto_pk']);
            //fcCarregarGrid();            
            if(arrCarregar.data[0]['dt_aprovacao']!=null){
                $("#ds_compra_solicitacao").prop("disabled", true);
                $("#dt_solicitacao").prop("disabled", true);
                $("#empresas_pk").prop("disabled", true);
                $("#solicitacao_pk").prop("disabled", true);
                $("#combo_usuario_aprovacao_pk").prop("disabled", true);
                $("#tipo_grupo_centro_custo_pk").prop("disabled", true);
                $("#grupo_lancamento_centrocusto_pk").prop("disabled", true);
                $("#obs_solicitacao").prop("disabled", true);
            }
        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
    }
}

function fcLimpaFormSolicitacao(){
    $("#solicitante_pk").val('');
    $("#empresas_pk").val('');
    $("#solicitante_pk").val('');
    $("#combo_usuario_aprovacao_pk").val('');
    $("#tipo_grupo_centro_custo_pk").val('');
    $("#grupo_lancamento_centrocusto_pk").val('');
    $("#ds_compra_solicitacao").val('');
    $("#dt_solicitacao").val('');
    $("#obs_solicitacao").val('');
    $("#combo_usuario_aprovacao_pk").val('');
    $("#dt_aprovacao").val('');
    $("#obs_aprovacao").val('');
    $("#tipo_grupo_centro_custo_pk").val('');
    $("#grupo_lancamento_centrocusto_pk").val('');
}

//combos
function fcComboEmpresas(){
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("conta", "listarTodos", objParametros);

    carregarComboAjax($("#empresas_pk"), arrCarregar, " ", "pk", "ds_conta");
    //carregarComboAjax($("#empresa_pk), arrCarregar, " ", "pk", "ds_conta");        
}

function fcComboSolicitante(){
    var objParametros = {

    };
    var arrCarregar = carregarController("usuario", "listarTodosSemAdm", objParametros)

    carregarComboAjax($("#solicitante_pk"), arrCarregar, " ", "pk", "ds_usuario");
}

function fcComboGruposCentroCusto(){
    var objParametros = {
        "tipo_grupo_pk": ""
    };
    if($("#tipo_grupo_centro_custo_pk").val()==1){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoLeads", objParametros);
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_lead");
    }else if($("#tipo_grupo_centro_custo_pk").val()==2){
        var arrCarregar = carregarController("lancamento", "listaItensGrupoColaboradores", objParametros);
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_colaborador");
    }else if($("#tipo_grupo_centro_custo_pk").val()==4){

        var arrCarregar = carregarController("equipe", "listarTodos", objParametros);
        carregarComboAjax($("#grupo_lancamento_centrocusto_pk"), arrCarregar, " ", "pk", "ds_equipe");
    }
}

function fcComboAprovador(){
    var objParametros = {
        "solicitante_pk":$('#solicitante_pk').val()
    };

    var arrCarregar = carregarController("equipe", "listarResponsavelEquipe", objParametros)

    if(arrCarregar.data[0]['usuario_aprovacao_pk']==0){//Se o usuario não estiver em nenhuma equipe lista os ADM dos sistema para a aprovação
        var arrCarregarADM = carregarController("usuario", "listarAdmSistema", objParametros)
        //alert('Solicitante selecionado não participa de equipes! Aprovadores listados ADM do sistema!')
        carregarComboAjax($("#combo_usuario_aprovacao_pk"), arrCarregarADM, " ", "usuario_aprovacao_pk", "ds_usuaario_aprovacao");

    }else{
        carregarComboAjax($("#combo_usuario_aprovacao_pk"), arrCarregar, " ", "usuario_aprovacao_pk", "ds_usuaario_aprovacao");
    }
}

function fcVerificarOrcamento(){
    try {
        var qtdItens = $('#tblResultado tbody tr').length;
        if(qtdItens > 0){
            sendPost('compra_solicitacao',"receptivo",{});
        }else{
            sweetMensagem('warning','Adicione ao menos um orçamento!')
        }
    } catch (error) {
        utilsJS.toastNotify(false,error)
    }
}

$(document).ready(function(){
    //limpa formulario    
    fcLimpaFormSolicitacao();

    //combos
    fcComboSolicitante();//combo de solicitante  
    $("#solicitante_pk").change(function(){
        $(".chzn-select").chosen('destroy');
        fcComboAprovador();
        $(".chzn-select").chosen({allow_single_deselect: true});
    });

    fcComboEmpresas();
    $("#tipo_grupo_centro_custo_pk").change(function(){
        $(".chzn-select").chosen('destroy');
        fcComboGruposCentroCusto()//combo de centros de custo  
        $(".chzn-select").chosen({allow_single_deselect: true});
    });

    $(".chzn-select").chosen({allow_single_deselect: true});

    //mascaras de campos
    $('#dt_solicitacao').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker("setDate",  );

    $("#dt_solicitacao").keypress(function(){
        mascara(this,mdata);
    });

    $("#vl_frete").on('keyup', function () {
        mascara(this,moeda);
    });

    $("#vl_item_produto").on('keyup', function () {
        mascara(this,moeda);
    });
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdCancelar2', fcCancelar);

    //Atribui a validação do formulário dos campos obrigatórios
    //fcValidarForm();

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregarSolicitacao();

    $(document).on('click', '#cmdEnviarSolicitacaoCompra', fcVerificarOrcamento);
    $(document).on('click', '#cmdEnviarSolicitacaoCompra2', fcVerificarOrcamento);

    if($("#usuario_aprovacao_pk").val() >0){
        $("#div_aprovacao").show();
    }else{
        $("#div_aprovacao").hide();
    }
});
