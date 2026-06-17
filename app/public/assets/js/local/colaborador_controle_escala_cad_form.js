var tblEscala = $('#tabelaEscala').DataTable();
function fcFormatarDadosEscala(){
    var pk = "";
    var leads_pk = "";
    var ds_lead = "";
    var contratos_pk = "";
    var dt_inicio_agenda = "";
    var dt_fim_agenda = "";
    var produtos_servicos_pk = "";
    var ds_produto_servico = "";
    var colaboradores_pk = "";
    var processos_etapas_pk = "";
    var contratos_itens_pk = "";
    var turnos_pk = "";
    var hr_inicio_expediente = "";
    var hr_termino_expediente = "";
    var hr_saida_intervalo = "";
    var hr_retorno_intervalo = "";
    var ic_folga_inverter = "";
    var tipo_escala = "";
    var ic_intrajornada = "";
    var ic_dom = "";
    var ic_seg = "";
    var ic_ter = "";
    var ic_qua = "";
    var ic_qui = "";
    var ic_sex = "";
    var ic_sab = "";
    var ic_dom_folga = "";
    var ic_seg_folga = "";
    var ic_ter_folga = "";
    var ic_qua_folga = "";
    var ic_qui_folga = "";
    var ic_sex_folga = "";
    var ic_sab_folga = "";
    var dom_turnos_pk = "";
    var seg_turnos_pk = "";
    var ter_turnos_pk = "";
    var qua_turnos_pk = "";
    var qui_turnos_pk = "";
    var sex_turnos_pk = "";
    var sab_turnos_pk = "";
    var hr_turno_dom = "";
    var hr_turno_seg = "";
    var hr_turno_ter = "";
    var hr_turno_qua = "";
    var hr_turno_qui = "";
    var hr_turno_sex = "";
    var hr_turno_sab = "";
    var hr_turno_dom_saida = "";
    var hr_turno_seg_saida = "";
    var hr_turno_ter_saida = "";
    var hr_turno_qua_saida = "";
    var hr_turno_qui_saida = "";
    var hr_turno_sex_saida = "";
    var hr_turno_sab_saida = "";
    var hr_intervalo_dom = "";
    var hr_intervalo_seg = "";
    var hr_intervalo_ter = "";
    var hr_intervalo_qua = "";
    var hr_intervalo_qui = "";
    var hr_intervalo_sex = "";
    var hr_intervalo_sab = "";
    var hr_intervalo_saida_dom = "";
    var hr_intervalo_saida_seg = "";
    var hr_intervalo_saida_ter = "";
    var hr_intervalo_saida_qua = "";
    var hr_intervalo_saida_qui = "";
    var hr_intervalo_saida_sex = "";
    var hr_intervalo_saida_sab = "";
    var dt_cancelamento = "";
    var ds_motivo_cancelamento = "";
    var n_qtde_dias_semana = "";
    var ic_preenchimento_automatico = "";
    var ic_nao_repetir = "";
    var t_pk = "";
    var t_ds_lead = "";
    var t_ds_identificacao_area = "";///teste
    var t_ds_produto_servico = "";
    var t_n_qtde_dias_semana = "";
    var t_status = "";///teste
    var t_dt_periodo_escala = "";
    var t_dt_cancelamento = "";
    var t_ds_motivo_cancelamento = "";
    var t_ds_combo_contrato = "";
    var dias_escala_servico = "";
    var ic_ponto_fora_horario = "";
    var ic_tempo_antes_ponto = "";


    var arrKeys = [];
    var arrDados = [];
    arrKeys[0] = "pk";
    arrKeys[1] = "leads_pk";
    arrKeys[2] = "ds_lead";
    arrKeys[3] = "contratos_pk";
    arrKeys[4] = "dt_inicio_agenda";
    arrKeys[5] = "dt_fim_agenda";
    arrKeys[6] = "produtos_servicos_pk";
    arrKeys[7] = "ds_produto_servico";
    arrKeys[8] = "colaboradores_pk";
    arrKeys[9] = "processos_etapas_pk";
    arrKeys[10] = "contratos_itens_pk";
    arrKeys[11] = "turnos_pk";
    arrKeys[12] = "hr_inicio_expediente";
    arrKeys[13] = "hr_termino_expediente";
    arrKeys[14] = "hr_saida_intervalo";
    arrKeys[15] = "hr_retorno_intervalo";
    arrKeys[16] = "ic_folga_inverter";
    arrKeys[17] = "tipo_escala";
    arrKeys[18] = "ic_intrajornada";
    arrKeys[19] = "ic_dom";
    arrKeys[20] = "ic_seg";
    arrKeys[21] = "ic_ter";
    arrKeys[22] = "ic_qua";
    arrKeys[23] = "ic_qui";
    arrKeys[24] = "ic_sex";
    arrKeys[25] = "ic_sab";
    arrKeys[26] = "ic_dom_folga";
    arrKeys[27] = "ic_seg_folga";
    arrKeys[28] = "ic_ter_folga";
    arrKeys[29] = "ic_qua_folga";
    arrKeys[30] = "ic_qui_folga";
    arrKeys[31] = "ic_sex_folga";
    arrKeys[32] = "ic_sab_folga";
    arrKeys[33] = "dom_turnos_pk";
    arrKeys[34] = "seg_turnos_pk";
    arrKeys[35] = "ter_turnos_pk";
    arrKeys[36] = "qua_turnos_pk";
    arrKeys[37] = "qui_turnos_pk";
    arrKeys[38] = "sex_turnos_pk";
    arrKeys[39] = "sab_turnos_pk";
    arrKeys[40] = "hr_turno_dom";
    arrKeys[41] = "hr_turno_seg";
    arrKeys[42] = "hr_turno_ter";
    arrKeys[43] = "hr_turno_qua";
    arrKeys[44] = "hr_turno_qui";
    arrKeys[45] = "hr_turno_sex";
    arrKeys[46] = "hr_turno_sab";
    arrKeys[47] = "hr_turno_dom_saida";
    arrKeys[48] = "hr_turno_seg_saida";
    arrKeys[49] = "hr_turno_ter_saida";
    arrKeys[50] = "hr_turno_qua_saida";
    arrKeys[51] = "hr_turno_qui_saida";
    arrKeys[52] = "hr_turno_sex_saida";
    arrKeys[53] = "hr_turno_sab_saida";
    arrKeys[54] = "hr_intervalo_dom";
    arrKeys[55] = "hr_intervalo_seg";
    arrKeys[56] = "hr_intervalo_ter";
    arrKeys[57] = "hr_intervalo_qua";
    arrKeys[58] = "hr_intervalo_qui";
    arrKeys[59] = "hr_intervalo_sex";
    arrKeys[60] = "hr_intervalo_sab";
    arrKeys[61] = "hr_intervalo_saida_dom";
    arrKeys[62] = "hr_intervalo_saida_seg";
    arrKeys[63] = "hr_intervalo_saida_ter";
    arrKeys[64] = "hr_intervalo_saida_qua";
    arrKeys[65] = "hr_intervalo_saida_qui";
    arrKeys[66] = "hr_intervalo_saida_sex";
    arrKeys[67] = "hr_intervalo_saida_sab";
    arrKeys[68] = "dt_cancelamento";
    arrKeys[69] = "ds_motivo_cancelamento";
    arrKeys[70] = "n_qtde_dias_semana";
    arrKeys[71] = "ic_preenchimento_automatico";
    arrKeys[72] = "ic_nao_repetir";
    arrKeys[73] = "t_pk";
    arrKeys[74] = "t_ds_lead";
    arrKeys[75] = "t_ds_identificacao_area";
    arrKeys[76] = "t_ds_produto_servico";
    arrKeys[77] = "t_n_qtde_dias_semana";
    arrKeys[78] = "t_status";
    arrKeys[79] = "t_dt_periodo_escala";
    arrKeys[80] = "t_dt_cancelamento";
    arrKeys[81] = "t_ds_motivo_cancelamento";
    arrKeys[82] = "t_ds_combo_contrato";
    arrKeys[83] = "t_dias_escala_servico";
    arrKeys[84] = "t_ic_tempo_antes_ponto";
    arrKeys[85] = "t_ic_ponto_fora_horario";
    var  data = tblEscala.rows().data();


   

    for(i = 0; i< data.length; i++){
        var objParametros0 = {
            "contratos_pk":  data[i]['t_contratos_pk'],
            //"produtos_servicos_pk":data[i]['t_produtos_servicos_pk']
        }
        var arrCarregar0 = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros0);
        var v_contratos_itens_pk = arrCarregar0.data[0]['contratos_itens_pk'];
        var v_n_qtde_dias_semana = arrCarregar0.data[0]['n_qtde_dias_semana'];

        pk = data[i]['t_pk'];
        leads_pk = data[i]['t_leads_pk'];
        ds_lead = data[i]['t_ds_lead'];
        contratos_pk = data[i]['t_contratos_pk'];
        dt_inicio_agenda = data[i]['t_dt_inicio_agenda'];
        dt_fim_agenda = data[i]['t_dt_fim_agenda'];
        produtos_servicos_pk = data[i]['t_produtos_servicos_pk'];
        ds_produto_servico = data[i]['t_ds_produto_servico'];
        colaboradores_pk = data[i]['t_colaboradores_pk'];
        processos_etapas_pk = data[i]['t_processos_etapas_pk'];
        contratos_itens_pk = v_contratos_itens_pk;
        turnos_pk = data[i]['t_turnos_pk'];
        hr_inicio_expediente = data[i]['t_hr_inicio_expediente'];
        hr_termino_expediente = data[i]['t_hr_termino_expediente'];
        hr_saida_intervalo = data[i]['t_hr_saida_intervalo'];
        hr_retorno_intervalo = data[i]['t_hr_retorno_intervalo'];
        ic_folga_inverter = data[i]['t_ic_folga_inverter'];
        tipo_escala = data[i]['t_tipo_escala'];
        ic_intrajornada = data[i]['t_ic_intrajornada'];
        ic_dom = data[i]['t_ic_dom'];
        ic_seg = data[i]['t_ic_seg'];
        ic_ter = data[i]['t_ic_ter'];
        ic_qua = data[i]['t_ic_qua'];
        ic_qui = data[i]['t_ic_qui'];
        ic_sex = data[i]['t_ic_sex'];
        ic_sab = data[i]['t_ic_sab'];
        ic_dom_folga = data[i]['t_ic_dom_folga'];
        ic_seg_folga = data[i]['t_ic_seg_folga'];
        ic_ter_folga = data[i]['t_ic_ter_folga'];
        ic_qua_folga = data[i]['t_ic_qua_folga'];
        ic_qui_folga = data[i]['t_ic_qui_folga'];
        ic_sex_folga = data[i]['t_ic_sex_folga'];
        ic_sab_folga = data[i]['t_ic_sab_folga'];
        dom_turnos_pk = data[i]['t_dom_turnos_pk'];
        seg_turnos_pk = data[i]['t_seg_turnos_pk'];
        ter_turnos_pk = data[i]['t_ter_turnos_pk'];
        qua_turnos_pk = data[i]['t_qua_turnos_pk'];
        qui_turnos_pk = data[i]['t_qui_turnos_pk'];
        sex_turnos_pk = data[i]['t_sex_turnos_pk'];
        sab_turnos_pk = data[i]['t_sab_turnos_pk'];
        hr_turno_dom = data[i]['t_hr_turno_dom'];
        hr_turno_seg = data[i]['t_hr_turno_seg'];
        hr_turno_ter = data[i]['t_hr_turno_ter'];
        hr_turno_qua = data[i]['t_hr_turno_qua'];
        hr_turno_qui = data[i]['t_hr_turno_qui'];
        hr_turno_sex = data[i]['t_hr_turno_sex'];
        hr_turno_sab = data[i]['t_hr_turno_sab'];
        hr_turno_dom_saida = data[i]['t_hr_turno_dom_saida'];
        hr_turno_seg_saida = data[i]['t_hr_turno_seg_saida'];
        hr_turno_ter_saida = data[i]['t_hr_turno_ter_saida'];
        hr_turno_qua_saida = data[i]['t_hr_turno_qua_saida'];
        hr_turno_qui_saida = data[i]['t_hr_turno_qui_saida'];
        hr_turno_sex_saida = data[i]['t_hr_turno_sex_saida'];
        hr_turno_sab_saida = data[i]['t_hr_turno_sab_saida'];
        hr_intervalo_dom = data[i]['t_hr_intervalo_dom'];
        hr_intervalo_seg = data[i]['t_hr_intervalo_seg'];
        hr_intervalo_ter = data[i]['t_hr_intervalo_ter'];
        hr_intervalo_qua = data[i]['t_hr_intervalo_qua'];
        hr_intervalo_qui = data[i]['t_hr_intervalo_qui'];
        hr_intervalo_sex = data[i]['t_hr_intervalo_sex'];
        hr_intervalo_sab = data[i]['t_hr_intervalo_sab'];
        hr_intervalo_saida_dom = data[i]['t_hr_intervalo_saida_dom'];
        hr_intervalo_saida_seg = data[i]['t_hr_intervalo_saida_seg'];
        hr_intervalo_saida_ter = data[i]['t_hr_intervalo_saida_ter'];
        hr_intervalo_saida_qua = data[i]['t_hr_intervalo_saida_qua'];
        hr_intervalo_saida_qui = data[i]['t_hr_intervalo_saida_qui'];
        hr_intervalo_saida_sex = data[i]['t_hr_intervalo_saida_sex'];
        hr_intervalo_saida_sab = data[i]['t_hr_intervalo_saida_sab'];
        dt_cancelamento = data[i]['t_dt_cancelamento'];
        ds_motivo_cancelamento = data[i]['t_ds_motivo_cancelamento'];
        n_qtde_dias_semana = v_n_qtde_dias_semana;
        ic_preenchimento_automatico = data[i]['t_ic_preenchimento_automatico'];
        dias_escala_servico = data[i]['t_dias_escala_servico'];
        ic_nao_repetir = data[i]['t_ic_nao_repetir'];
        t_pk = data[i]['t_pk'];
        t_ds_lead = data[i]['t_ds_lead'];
        t_ds_identificacao_area = data[i]['t_ds_identificacao_area'];
        t_ds_produto_servico = data[i]['t_ds_produto_servico'];
        t_status = data[i]['t_status'];
        t_dt_periodo_escala = data[i]['t_dt_periodo_escala'];
        t_dt_cancelamento = data[i]['t_dt_cancelamento'];
        t_ds_motivo_cancelamento = data[i]['t_ds_motivo_cancelamento'];
        t_ds_combo_contrato = data[i]['t_ds_combo_contrato'];
        ic_ponto_fora_horario = data[i]['t_ic_ponto_fora_horario'];
        ic_tempo_antes_ponto = data[i]['t_ic_tempo_antes_ponto'];

        arrDados[i] = [
            pk,
            leads_pk,
            ds_lead,
            contratos_pk,
            dt_inicio_agenda,
            dt_fim_agenda,
            produtos_servicos_pk,
            ds_produto_servico,
            colaboradores_pk,
            processos_etapas_pk,
            contratos_itens_pk,
            turnos_pk,
            hr_inicio_expediente,
            hr_termino_expediente,
            hr_saida_intervalo,
            hr_retorno_intervalo,
            ic_folga_inverter,
            tipo_escala,
            ic_intrajornada,
            ic_dom,
            ic_seg,
            ic_ter,
            ic_qua,
            ic_qui,
            ic_sex,
            ic_sab,
            ic_dom_folga,
            ic_seg_folga,
            ic_ter_folga,
            ic_qua_folga,
            ic_qui_folga,
            ic_sex_folga,
            ic_sab_folga,
            dom_turnos_pk,
            seg_turnos_pk,
            ter_turnos_pk,
            qua_turnos_pk,
            qui_turnos_pk,
            sex_turnos_pk,
            sab_turnos_pk,
            hr_turno_dom,
            hr_turno_seg,
            hr_turno_ter,
            hr_turno_qua,
            hr_turno_qui,
            hr_turno_sex,
            hr_turno_sab,
            hr_turno_dom_saida,
            hr_turno_seg_saida,
            hr_turno_ter_saida,
            hr_turno_qua_saida,
            hr_turno_qui_saida,
            hr_turno_sex_saida,
            hr_turno_sab_saida,
            hr_intervalo_dom,
            hr_intervalo_seg,
            hr_intervalo_ter,
            hr_intervalo_qua,
            hr_intervalo_qui,
            hr_intervalo_sex,
            hr_intervalo_sab,
            hr_intervalo_saida_dom,
            hr_intervalo_saida_seg,
            hr_intervalo_saida_ter,
            hr_intervalo_saida_qua,
            hr_intervalo_saida_qui,
            hr_intervalo_saida_sex,
            hr_intervalo_saida_sab,
            dt_cancelamento,
            ds_motivo_cancelamento,
            n_qtde_dias_semana,
            ic_preenchimento_automatico,
            ic_nao_repetir,
            t_pk,
            t_ds_lead,
            t_ds_identificacao_area,
            t_ds_produto_servico,
            t_n_qtde_dias_semana,
            t_status,
            t_dt_periodo_escala,
            t_dt_cancelamento,
            t_ds_motivo_cancelamento,
            t_ds_combo_contrato,
            dias_escala_servico,
            ic_ponto_fora_horario,
            ic_tempo_antes_ponto
        ];
    }

    return arrayToJson(arrKeys, arrDados);
}


function fcCadastrarEscala() {

    //alert($("#tipo_escala").val());

    var objParametros = {
        "agenda_colaborador_padrao_pk": $("#agenda_colaborador_padrao_pk").val(),
        "leads_pk": $("#leads_pk_agenda").val(),
        "colaboradores_pk": $("#colaborador_pk").val(),
        "dt_periodo_ini": $("#dt_inicio_agenda").val(),
        "dt_periodo_fim": $("#dt_fim_agenda").val(),
        "tipo_escala": $("#tipo_escala").val(),
        "n_qtde_dias_semana": $("#n_qtde_dias_semana").val(),
        "fl_escala_alternada": $("#fl_escala_alternada").val()
    }

    var arrEnviar = carregarController("agenda_colaborador_padrao", "escalaDadosColaborador", objParametros);
    //NewWindow(v_last_url)
    if (arrEnviar.status == true) {
        //alert(arrEnviar.message);
    } else {
        sweetMensagem('warning',arrEnviar.message || arrEnviar.result);
    }
}

function isEscalaAlternadaDetalhesObrigatorios() {
    return $("#fl_escala_alternada").val() == "1";
}

function toggleEscalaAlternadaFields() {
    var enabled = $("#fl_escala_alternada").val() == "1";
    $("#box_escala_alternada_detalhes, #box_tipo_escala_alternada").toggle(enabled);
    $("#dias_escala_alternada, #tipo_escala_alternada").prop("disabled", !enabled);
    if (!enabled) {
        $("#dias_escala_alternada").val("");
        $("#tipo_escala_alternada").val("");
        $("#alert_escala_alternada").hide();
    }
}

function validarCamposEscalaAlternada() {
    if (isEscalaAlternadaDetalhesObrigatorios() && ($("#dias_escala_alternada").val() == "" || $("#tipo_escala_alternada").val() == "")) {
        $("#alert_escala_alternada").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_escala_alternada").slideUp(500);
        });
        return false;
    }

    return true;
}

function fcSalvar(confirmarNovaEscala) {
    try{
        //variaveis
        var ic_folga_inverter = 2;
        var ic_dom = 2;
        var ic_seg = 2;
        var ic_ter = 2;
        var ic_qua = 2;
        var ic_qui = 2;
        var ic_sex = 2;
        var ic_sab = 2;
        var ic_dom_folga = 2;
        var ic_seg_folga = 2;
        var ic_ter_folga = 2;
        var ic_qua_folga = 2;
        var ic_qui_folga = 2;
        var ic_sex_folga = 2;
        var ic_sab_folga = 2;
        var ic_preenchimento_automatico = 2;
        var ic_nao_repetir = 2;

        //validações do grid de escala
        if ($('#ic_dom').is(":checked") && $('#dom_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#dom_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_seg').is(":checked") && $('#seg_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#seg_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_ter').is(":checked") && $('#ter_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#ter_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_qua').is(":checked") && $('#qua_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#qua_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_qui').is(":checked") && $('#qui_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#qui_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_sex').is(":checked") && $('#sex_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#sex_turnos_pk').focus();
            return false;
        }
        else if ($('#ic_sab').is(":checked") && $('#sab_turnos_pk').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#sab_turnos_pk').focus();
            return false;
        }
        //HORARIO
        else if ($('#ic_dom').is(":checked") && $('#hr_turno_dom').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_dom').focus();
            return false;
        }
        else if ($('#ic_seg').is(":checked") && $('#hr_turno_seg').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_seg').focus();
            return false;
        }
        else if ($('#ic_ter').is(":checked") && $('#hr_turno_ter').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_ter').focus();
            return false;
        }
        else if ($('#ic_qua').is(":checked") && $('#hr_turno_qua').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_qua').focus();
            return false;
        }
        else if ($('#ic_qui').is(":checked") && $('#hr_turno_qui').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_qui').focus();
            return false;
        }
        else if ($('#ic_sex').is(":checked") && $('#hr_turno_sex').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_sex').focus();
            return false;
        }
        else if ($('#ic_sab').is(":checked") && $('#hr_turno_sab').val() == "") {
            $("#alert").fadeTo(2000, 500).slideUp(500, function () {
                $("#alert").slideUp(500);
            });
            $('#hr_turno_sab').focus();
            return false;
        }

        if ($('#ic_dom').is(":checked")) {
            ic_dom = 1;
        }
        if ($('#ic_seg').is(":checked")) {
            ic_seg = 1;
        }
        if ($('#ic_ter').is(":checked")) {
            ic_ter = 1;
        }
        if ($('#ic_qua').is(":checked")) {
            ic_qua = 1;
        }
        if ($('#ic_qui').is(":checked")) {
            ic_qui = 1;
        }
        if ($('#ic_sex').is(":checked")) {
            ic_sex = 1;
        }
        if ($('#ic_sab').is(":checked")) {
            ic_sab = 1;
        }

        if ($('#ic_dom_folga').is(":checked")) {
            ic_dom_folga = 1;
        }
        if ($('#ic_seg_folga').is(":checked")) {
            ic_seg_folga = 1;
        }
        if ($('#ic_ter_folga').is(":checked")) {
            ic_ter_folga = 1;
        }
        if ($('#ic_qua_folga').is(":checked")) {
            ic_qua_folga = 1;
        }
        if ($('#ic_qui_folga').is(":checked")) {
            ic_qui_folga = 1;
        }
        if ($('#ic_sex_folga').is(":checked")) {
            ic_sex_folga = 1;
        }
        if ($('#ic_sab_folga').is(":checked")) {
            ic_sab_folga = 1;
        }

        if (!validarCamposEscalaAlternada()) {
            return false;
        }

        if ($('#ic_folga_inverter').is(":checked")) {
            ic_folga_inverter = 1;
        }

        if ($('#ic_preenchimento_automatico').is(":checked")) {
            ic_preenchimento_automatico = 1;
        }
        var  ic_intrajornada = ""
        if ($('#ic_intrajornada').is(":checked")) {
            ic_intrajornada = 1;
        }

        if ($('#ic_nao_repetir').is(":checked")) {
            ic_nao_repetir = 1;
        }



        //pega o contratos itens do produto serviço
        var objParametros0 = {
            "contratos_pk": $("#contratos_pk_combo option:selected").val(),
            //"produtos_servicos_pk": $('#produtos_servicos_pk option:selected').val()
        }
        var arrCarregar0 = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros0);
        var v_contratos_itens_pk = arrCarregar0.data[0]['contratos_itens_pk'];
        var v_n_qtde_dias_semana = arrCarregar0.data[0]['n_qtde_dias_semana'];

        if(v_n_qtde_dias_semana == '12x36' && $("#tipo_escala").val() == ""){
            $("#alert_tipo_escala").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_tipo_escala").slideUp(500);
            });
            $('#tipo_escala').focus();
            return false;
        }
        var objParametros = {
            "pk": $("#agenda_colaborador_padrao_pk").val(),
            "leads_pk": $("#leads_pk_agenda").val(),
            "contratos_pk": $("#contratos_pk_combo").val(),
            "dt_inicio_agenda": $("#dt_inicio_agenda").val(),
            "dt_fim_agenda": $("#dt_fim_agenda").val(),
            "produtos_servicos_pk": $('#produtos_servicos_pk').val(),
            "colaboradores_pk": $("#colaborador_pk").val(),
            "processos_etapas_pk": $('#processos_etapas_pk_2').val(),
            "contratos_itens_pk": v_contratos_itens_pk,
            "turnos_pk": $('#turno_base_agenda_pk').val(),
            "hr_inicio_expediente": $('#hr_inicio_expediente').val(),
            "hr_termino_expediente": $('#hr_termino_expediente').val(),
            "hr_saida_intervalo": $('#hr_saida_intervalo').val(),
            "hr_retorno_intervalo": $('#hr_retorno_intervalo').val(),
            "dias_escala_servico": $('#dias_escala_servico').val(),
            "ic_folga_inverter": ic_folga_inverter,
            "tipo_escala": $("#tipo_escala").val(),
            "ic_intrajornada": ic_intrajornada,
            "ic_dom": ic_dom,
            "ic_seg": ic_seg,
            "ic_ter": ic_ter,
            "ic_qua": ic_qua,
            "ic_qui": ic_qui,
            "ic_sex": ic_sex,
            "ic_sab": ic_sab,
            "ic_dom_folga": ic_dom_folga,
            "ic_seg_folga": ic_seg_folga,
            "ic_ter_folga": ic_ter_folga,
            "ic_qua_folga": ic_qua_folga,
            "ic_qui_folga": ic_qui_folga,
            "ic_sex_folga": ic_sex_folga,
            "ic_sab_folga": ic_sab_folga,
            "dom_turnos_pk": $('#dom_turnos_pk').val(),
            "seg_turnos_pk": $('#seg_turnos_pk').val(),
            "ter_turnos_pk": $('#ter_turnos_pk').val(),
            "qua_turnos_pk": $('#qua_turnos_pk').val(),
            "qui_turnos_pk": $('#qui_turnos_pk').val(),
            "sex_turnos_pk": $('#sex_turnos_pk').val(),
            "sab_turnos_pk": $('#sab_turnos_pk').val(),
            "hr_turno_dom": $("#hr_turno_dom").val(),
            "hr_turno_seg": $("#hr_turno_seg").val(),
            "hr_turno_ter": $("#hr_turno_ter").val(),
            "hr_turno_qua": $("#hr_turno_qua").val(),
            "hr_turno_qui": $("#hr_turno_qui").val(),
            "hr_turno_sex": $("#hr_turno_sex").val(),
            "hr_turno_sab": $("#hr_turno_sab").val(),
            "hr_turno_dom_saida": $("#hr_turno_dom_saida").val(),
            "hr_turno_seg_saida": $("#hr_turno_seg_saida").val(),
            "hr_turno_ter_saida": $("#hr_turno_ter_saida").val(),
            "hr_turno_qua_saida": $("#hr_turno_qua_saida").val(),
            "hr_turno_qui_saida": $("#hr_turno_qui_saida").val(),
            "hr_turno_sex_saida": $("#hr_turno_sex_saida").val(),
            "hr_turno_sab_saida": $("#hr_turno_sab_saida").val(),
            "hr_intervalo_dom": $("#hr_intervalo_dom").val(),
            "hr_intervalo_seg": $("#hr_intervalo_seg").val(),
            "hr_intervalo_ter": $("#hr_intervalo_ter").val(),
            "hr_intervalo_qua": $("#hr_intervalo_qua").val(),
            "hr_intervalo_qui": $("#hr_intervalo_qui").val(),
            "hr_intervalo_sex": $("#hr_intervalo_sex").val(),
            "hr_intervalo_sab": $("#hr_intervalo_sab").val(),
            "hr_intervalo_saida_dom": $("#hr_intervalo_saida_dom").val(),
            "hr_intervalo_saida_seg": $("#hr_intervalo_saida_seg").val(),
            "hr_intervalo_saida_ter": $("#hr_intervalo_saida_ter").val(),
            "hr_intervalo_saida_qua": $("#hr_intervalo_saida_qua").val(),
            "hr_intervalo_saida_qui": $("#hr_intervalo_saida_qui").val(),
            "hr_intervalo_saida_sex": $("#hr_intervalo_saida_sex").val(),
            "hr_intervalo_saida_sab": $("#hr_intervalo_saida_sab").val(),
            "dt_cancelamento": $("#dt_cancelamento_agenda_escala").val(),
            "ds_motivo_cancelamento": $("#ds_motivo_cancelamento").val(),
            "ic_tempo_antes_ponto": $("#ic_tempo_antes_ponto").val(),
            "ic_ponto_fora_horario": $("#ic_ponto_fora_horario").val(),
            "fl_escala_alternada": $("#fl_escala_alternada").val(),
            "dias_escala_alternada": $("#dias_escala_alternada").val(),
            "tipo_escala_alternada": $("#tipo_escala_alternada").val(),
            "n_qtde_dias_semana": v_n_qtde_dias_semana,
            "ic_preenchimento_automatico": ic_preenchimento_automatico,
            "ic_nao_repetir": ic_nao_repetir,
            "confirmar_nova_escala": confirmarNovaEscala ? 1 : "",
        };
        var arrEnviar = carregarController("agenda_colaborador_padrao", "salvar", objParametros);


        if (arrEnviar.status == true) {
            
            $("#agenda_colaborador_padrao_pk").val(arrEnviar.data)
            $("#n_qtde_dias_semana").val(v_n_qtde_dias_semana)

            if ($("#dt_cancelamento_agenda_escala").val() != "") {
                $("#janela_agendas").modal("hide");
                utilsJS.toastNotify(true,"Escala cancelada com sucesso !");
                recarregarGridEscala();
                return;
            }

            fcCadastrarEscala();

            $("#janela_agendas").modal("hide");

            utilsJS.toastNotify(true,"Escala salva com sucesso !");

        } else if (arrEnviar.requires_confirmation) {
            if (confirm(arrEnviar.message)) {
                return fcSalvar(true);
            }
        } else {
            utilsJS.toastNotify(false,arrEnviar.message || arrEnviar.result);
        }
    }catch(e){
        utilsJS.toastNotify(false,e)
    }

}

function fcValidarFormAgendas() {
    $("#form_agenda").validate({
        rules: {
            leads_pk_agenda: {
                required: true
            },
            dias_escala_servico: {
                required: true
            },
            contratos_pk_combo: {
                required: true
            },
            dt_inicio_agenda: {
                required: true
            },
            dt_fim_agenda: {
                required: true
            },
            produtos_servicos_pk: {
                required: true
            }
        },
        messages: {
            leads_pk_agenda: {
                required: "Por favor, selecione um posto de trabalho!"
            },
            dias_escala_servico: {
                required: "Por favor, selecione um tipo escala!"
            },
            contratos_pk_combo: {
                required: "Por favor, selecione um contrato!"
            },
            dt_inicio_agenda: {
                required: "Por favor, informe a data de início da escala!"
            },
            dt_fim_agendak: {
                required: "Por favor, informe a data de termino da escala!"
            },
            produtos_servicos_pk: {
                required: "Por favor, selecione o serviço da escala!"
            }

        },
        submitHandler: function (form) {
            fcSalvarAgenda();
            return false;
        }
    });
}

function fcSalvarAgenda(){
    if($("#colaborador_pk").val() == '') {

        if($("#acao_agenda").val() == "upd"){
            fcEditarEscalaSemPk();
        }

        if($("#acao_agenda").val() == "ins"){
            fcIncluirNovaEscalaSemPk();
        }
    }
    else {
        fcSalvar(false);
        recarregarGridEscala();
    }
    $("#janela_agendas").modal("hide");
}
//EDITAR ESCALA
function fcEditarAgenda(objRegistro) {
    //alert(JSON.stringify(objRegistro))

    $("#janela_agendas").modal("show");
    $("#acao_agenda").val("upd");
    $("#grid_itens_contrato").html();
    fcLimparFormAgenda();
    fcComboLeads(objRegistro['t_leads_pk']);//COMBO LEADS
    fcComboContratos(objRegistro['t_leads_pk']);
    $("#leads_pk_agenda").val(objRegistro['t_leads_pk']);


    //alert(JSON.stringify(objRegistro['t_contratos_pk']))

    //CONSULTA DADOS ESCALA
    $("#contratos_pk_combo").val(objRegistro['t_contratos_pk']);//CONTRATOS PK
    //alert($("#leads_pk_agenda"))


    //$("#contratos_pk_combo").prop("disabled", true);//DESABILITA O COMBO DE CONTRATO

    fcHtmlItensContrato();//CARREGA HTML DE ITENS

    $("#dt_inicio_agenda").val(objRegistro['t_dt_inicio_agenda']);//DATA INICIO ESCALA
    $("#dt_fim_agenda").val(objRegistro['t_dt_fim_agenda']);//DATA INICIO ESCALA
    $("#dt_inicio_agenda").prop("disabled", true);
    $("#dt_fim_agenda").prop("disabled", true);
    fcProduto();//COMBO DE PRODUTOS
    $("#produtos_servicos_pk_combo").val(objRegistro['t_produtos_servicos_pk']);//CONTRATOS PK
    $("#produtos_servicos_pk_combo").prop("disabled", true);

    fcColaborador();// CARREGA COMBO DE COLABORADOR
    $("#colaboradores_pk_combo_agenda").val(objRegistro['t_colaborador_pk']);//CONTRATOS PK
    fcCarregarTurno();//CARREGA COMBO DE TURNOS*/
    $("#turno_base_agenda_pk").val(objRegistro['t_turnos_pk']);
    $("#hr_inicio_expediente").val(objRegistro['t_hr_inicio_expediente']);
    $("#hr_termino_expediente").val(objRegistro['t_hr_termino_expediente']);
    $("#hr_saida_intervalo").val(objRegistro['t_hr_saida_intervalo']);
    $("#hr_retorno_intervalo").val(objRegistro['t_hr_retorno_intervalo']);
    $("#dias_escala_servico").val(objRegistro['t_n_qtde_dias_semana']);
    if (objRegistro['t_ic_preenchimento_automatico'] == 1) {
        $("#ic_preenchimento_automatico").prop("checked", true);
    } else {
        $("#ic_preenchimento_automatico").prop("checked", false);
    }
    if (objRegistro['t_ic_intrajornada'] == 1) {
        $("#ic_intrajornada").prop("checked", true);
        fcIntrajornada();
    } else {
        $("#ic_intrajornada").prop("checked", false);
    }
    if (objRegistro['t_ic_folga_inverter'] == 1) {
        $("#ic_folga_inverter").prop("checked", true);
    } else {
        $("#ic_folga_inverter").prop("checked", false);
    }
    $("#tipo_escala").val(objRegistro['t_tipo_escala']);

    if (objRegistro['t_ic_dom_folga'] == 1) {
        $("#ic_dom_folga").prop("checked", true);
    } else {
        $("#ic_dom_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_seg_folga'] == 1) {
        $("#ic_seg_folga").prop("checked", true);
    } else {
        $("#ic_seg_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_ter_folga'] == 1) {
        $("#ic_ter_folga").prop("checked", true);
    } else {
        $("#ic_ter_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_qua_folga'] == 1) {
        $("#ic_qua_folga").prop("checked", true);
    } else {
        $("#ic_qua_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_qui_folga'] == 1) {
        $("#ic_qui_folga").prop("checked", true);
    } else {
        $("#ic_qui_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_sex_folga'] == 1) {
        $("#ic_sex_folga").prop("checked", true);
    } else {
        $("#ic_sex_folga").prop("checked", false);
    }
    if (objRegistro['t_ic_sab_folga'] == 1) {
        $("#ic_sab_folga").prop("checked", true);
    } else {
        $("#ic_sab_folga").prop("checked", false);
    }

    if (objRegistro['t_ic_dom'] == 1) {
        $("#ic_dom").prop("checked", true);
    } else {
        $("#ic_dom").prop("checked", false);
    }
    if (objRegistro['t_ic_seg'] == 1) {
        $("#ic_seg").prop("checked", true);
    } else {
        $("#ic_seg").prop("checked", false);
    }
    if (objRegistro['t_ic_ter'] == 1) {
        $("#ic_ter").prop("checked", true);
    } else {
        $("#ic_ter").prop("checked", false);
    }
    if (objRegistro['t_ic_qua'] == 1) {
        $("#ic_qua").prop("checked", true);
    } else {
        $("#ic_qua").prop("checked", false);
    }
    if (objRegistro['t_ic_qui'] == 1) {
        $("#ic_qui").prop("checked", true);
    } else {
        $("#ic_qui").prop("checked", false);
    }
    if (objRegistro['t_ic_sex'] == 1) {
        $("#ic_sex").prop("checked", true);
    } else {
        $("#ic_sex").prop("checked", false);
    }
    if (objRegistro['t_ic_sab'] == 1) {
        $("#ic_sab").prop("checked", true);
    } else {
        $("#ic_sab").prop("checked", false);
    }

    $("#dom_turnos_pk").val(objRegistro['t_dom_turnos_pk']);
    $("#seg_turnos_pk").val(objRegistro['t_seg_turnos_pk']);
    $("#ter_turnos_pk").val(objRegistro['t_ter_turnos_pk']);
    $("#qua_turnos_pk").val(objRegistro['t_qua_turnos_pk']);
    $("#qui_turnos_pk").val(objRegistro['t_qui_turnos_pk']);
    $("#sex_turnos_pk").val(objRegistro['t_sex_turnos_pk']);
    $("#sab_turnos_pk").val(objRegistro['t_sab_turnos_pk']);
    $("#hr_turno_dom").val(objRegistro['t_hr_turno_dom']);
    $("#hr_turno_seg").val(objRegistro['t_hr_turno_seg']);
    $("#hr_turno_ter").val(objRegistro['t_hr_turno_ter']);
    $("#hr_turno_qua").val(objRegistro['t_hr_turno_qua']);
    $("#hr_turno_qui").val(objRegistro['t_hr_turno_qui']);
    $("#hr_turno_sex").val(objRegistro['t_hr_turno_sex']);
    $("#hr_turno_sab").val(objRegistro['t_hr_turno_sab']);

    $("#hr_intervalo_dom").val(objRegistro['t_hr_intervalo_dom']);
    $("#hr_intervalo_seg").val(objRegistro['t_hr_intervalo_seg']);
    $("#hr_intervalo_ter").val(objRegistro['t_hr_intervalo_ter']);
    $("#hr_intervalo_qua").val(objRegistro['t_hr_intervalo_qua']);
    $("#hr_intervalo_qui").val(objRegistro['t_hr_intervalo_qui']);
    $("#hr_intervalo_sex").val(objRegistro['t_hr_intervalo_sex']);
    $("#hr_intervalo_sab").val(objRegistro['t_hr_intervalo_sab']);

    $("#hr_intervalo_saida_dom").val(objRegistro['t_hr_intervalo_saida_dom']);
    $("#hr_intervalo_saida_seg").val(objRegistro['t_hr_intervalo_saida_seg']);
    $("#hr_intervalo_saida_ter").val(objRegistro['t_hr_intervalo_saida_ter']);
    $("#hr_intervalo_saida_qua").val(objRegistro['t_hr_intervalo_saida_qua']);
    $("#hr_intervalo_saida_qui").val(objRegistro['t_hr_intervalo_saida_qui']);
    $("#hr_intervalo_saida_sex").val(objRegistro['t_hr_intervalo_saida_sex']);
    $("#hr_intervalo_saida_sab").val(objRegistro['t_hr_intervalo_saida_sab']);
    $("#hr_turno_dom_saida").val(objRegistro['t_hr_turno_dom_saida']);
    $("#hr_turno_seg_saida").val(objRegistro['t_hr_turno_seg_saida']);
    $("#hr_turno_ter_saida").val(objRegistro['t_hr_turno_ter_saida']);
    $("#hr_turno_qua_saida").val(objRegistro['t_hr_turno_qua_saida']);
    $("#hr_turno_qui_saida").val(objRegistro['t_hr_turno_qui_saida']);
    $("#hr_turno_sex_saida").val(objRegistro['t_hr_turno_sex_saida']);
    $("#hr_turno_sab_saida").val(objRegistro['t_hr_turno_sab_saida']);
    $("#fl_escala_alternada").val(objRegistro['t_fl_escala_alternada']);
    $("#dias_escala_alternada").val(objRegistro['t_dias_escala_alternada']);
    $("#tipo_escala_alternada").val(objRegistro['t_tipo_escala_alternada']);
    toggleEscalaAlternadaFields();
    $("#ic_tempo_antes_ponto").val(objRegistro['t_ic_tempo_antes_ponto']);
    $("#ic_ponto_fora_horario").val(objRegistro['t_ic_ponto_fora_horario']);
    $("#agenda_colaborador_padrao_pk").val(objRegistro['t_pk']);//PK AGENDA
    $("#exibir_data_cancelamento").show();//PERMITE O CANCELAMENTO DA ESCALA
    $('#dt_cancelamento_agenda_escala').datepicker({
        defaultDate: "",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    }).datepicker();
    $("#dt_cancelamento_agenda_escala").keypress(function () {
        mascara(this, mdata);
    });
    $("#dt_cancelamento_agenda_escala").val(objRegistro['t_dt_cancelamento']);//DT CANCELAMENTO
    $("#ds_motivo_cancelamento").val(objRegistro['t_ds_motivo_cancelamento']);

    if (objRegistro['t_dt_cancelamento'] != null  && objRegistro['t_dt_cancelamento'] !== "") {//desabilita a o salvar se a escala estiver cancelada
        $("#dt_cancelamento_agenda_escala").prop("disabled", true);
        $("#ds_motivo_cancelamento").prop("disabled", true);
        $("#cmdEnviarAgenda").hide();
    } else {
        $("#dt_cancelamento_agenda_escala").prop("disabled", false);
        $("#ds_motivo_cancelamento").prop("disabled", false);
        $("#cmdEnviarAgenda").show();
    }
    $("#colaboradores_pk_combo_agenda").val($("#colaborador_pk").val())

    $('#dt_fim_agenda').attr('disabled', true);
    $('#dt_inicio_agenda').attr('disabled', true);

}
//NOVO CADASTRO
function fcIncluirNovaEscalaSemPk(){


    var ic_folga_inverter = 2;
    var ic_dom = 2;
    var ic_seg = 2;
    var ic_ter = 2;
    var ic_qua = 2;
    var ic_qui = 2;
    var ic_sex = 2;
    var ic_sab = 2;
    var ic_dom_folga = 2;
    var ic_seg_folga = 2;
    var ic_ter_folga = 2;
    var ic_qua_folga = 2;
    var ic_qui_folga = 2;
    var ic_sex_folga = 2;
    var ic_sab_folga = 2;
    var ic_preenchimento_automatico = 2;
    var ic_nao_repetir = 2;


    if ($('#ic_dom').is(":checked")) {
        ic_dom = 1;
    }
    if ($('#ic_seg').is(":checked")) {
        ic_seg = 1;
    }
    if ($('#ic_ter').is(":checked")) {
        ic_ter = 1;
    }
    if ($('#ic_qua').is(":checked")) {
        ic_qua = 1;
    }
    if ($('#ic_qui').is(":checked")) {
        ic_qui = 1;
    }
    if ($('#ic_sex').is(":checked")) {
        ic_sex = 1;
    }
    if ($('#ic_sab').is(":checked")) {
        ic_sab = 1;
    }

    if ($('#ic_dom_folga').is(":checked")) {
        ic_dom_folga = 1;
    }
    if ($('#ic_seg_folga').is(":checked")) {
        ic_seg_folga = 1;
    }
    if ($('#ic_ter_folga').is(":checked")) {
        ic_ter_folga = 1;
    }
    if ($('#ic_qua_folga').is(":checked")) {
        ic_qua_folga = 1;
    }
    if ($('#ic_qui_folga').is(":checked")) {
        ic_qui_folga = 1;
    }
    if ($('#ic_sex_folga').is(":checked")) {
        ic_sex_folga = 1;
    }
    if ($('#ic_sab_folga').is(":checked")) {
        ic_sab_folga = 1;
    }

    if ($('#ic_folga_inverter').is(":checked")) {
        ic_folga_inverter = 1;
    }

    if ($('#ic_preenchimento_automatico').is(":checked")) {
        ic_preenchimento_automatico = 1;
    }
    var  ic_intrajornada = ""
    if ($('#ic_intrajornada').is(":checked")) {
        ic_intrajornada = 1;
    }

    if ($('#ic_nao_repetir').is(":checked")) {
        ic_nao_repetir = 1;
    }

    tblEscala.row.add(
        {
            "t_pk": "",
            "t_ds_lead": $("#leads_pk_agenda option:selected").text(),
            "t_ds_identificacao_area": "",///teste
            "t_ds_produto_servico": $("#produtos_servicos_pk_combo option:selected").text(),
            "t_n_qtde_dias_semana": $("#tipo_escala option:selected").text(),
            "t_status": "",///teste
            "t_dt_periodo_escala": $("#dt_inicio_agenda").val() + " Até " + $("#dt_fim_agenda").val(),
            "t_dt_cancelamento":  $("#dt_cancelamento_agenda_escala").val(),
            "t_ds_motivo_cancelamento":  $("#ds_motivo_cancelamento").val(),
            "t_contratos_pk": $("#contratos_pk_combo").val(),
            "t_ds_combo_contrato":$("#contratos_pk_combo option:selected").text(),
            "t_dt_inicio_agenda": $("#dt_inicio_agenda").val(),
            "t_dt_fim_agenda": $("#dt_fim_agenda").val(),
            "t_produtos_servicos_pk": $('#produtos_servicos_pk_combo').val(),
            "t_colaboradores_pk": $("#colaboradores_pk_combo_agenda").val(),
            "t_processos_etapas_pk": $('#processos_etapas_pk_2').val(),
            "t_tipo_escala": $('#tipo_escala').val(),
            "t_contratos_itens_pk": $("#agenda_contratos_itens_pk").val(),
            "t_turnos_pk": $('#turno_base_agenda_pk').val(),
            "t_hr_inicio_expediente": $('#hr_inicio_expediente').val(),
            "t_hr_termino_expediente": $('#hr_termino_expediente').val(),
            "t_hr_saida_intervalo": $('#hr_saida_intervalo').val(),
            "t_hr_retorno_intervalo": $('#hr_retorno_intervalo').val(),
            "t_ic_folga_inverter": ic_folga_inverter,
            "t_ic_intrajornada": ic_intrajornada ,
            "t_ic_dom": ic_dom,
            "t_ic_seg": ic_seg,
            "t_ic_ter": ic_ter,
            "t_ic_qua": ic_qua,
            "t_ic_qui": ic_qui,
            "t_ic_sex": ic_sex,
            "t_ic_sab": ic_sab,
            "t_ic_dom_folga": ic_dom_folga,
            "t_ic_seg_folga": ic_seg_folga,
            "t_ic_ter_folga": ic_ter_folga,
            "t_ic_qua_folga": ic_qua_folga,
            "t_ic_qui_folga": ic_qui_folga,
            "t_ic_sex_folga": ic_sex_folga,
            "t_ic_sab_folga": ic_sab_folga ,
            "t_dom_turnos_pk": $('#dom_turnos_pk').val(),
            "t_seg_turnos_pk": $('#seg_turnos_pk').val(),
            "t_ter_turnos_pk": $('#ter_turnos_pk').val(),
            "t_qua_turnos_pk": $('#qua_turnos_pk').val(),
            "t_qui_turnos_pk": $('#qui_turnos_pk').val(),
            "t_sex_turnos_pk": $('#sex_turnos_pk').val(),
            "t_sab_turnos_pk": $('#sab_turnos_pk').val(),
            "t_hr_turno_dom": $("#hr_turno_dom").val(),
            "t_hr_turno_seg": $("#hr_turno_seg").val(),
            "t_hr_turno_ter": $("#hr_turno_ter").val(),
            "t_hr_turno_qua": $("#hr_turno_qua").val(),
            "t_hr_turno_qui": $("#hr_turno_qui").val(),
            "t_hr_turno_sex": $("#hr_turno_sex").val(),
            "t_hr_turno_sab": $("#hr_turno_sab").val(),
            "t_hr_turno_dom_saida": $("#hr_turno_dom_saida").val(),
            "t_hr_turno_seg_saida": $("#hr_turno_seg_saida").val(),
            "t_hr_turno_ter_saida": $("#hr_turno_ter_saida").val(),
            "t_hr_turno_qua_saida": $("#hr_turno_qua_saida").val(),
            "t_hr_turno_qui_saida": $("#hr_turno_qui_saida").val(),
            "t_hr_turno_sex_saida": $("#hr_turno_sex_saida").val(),
            "t_hr_turno_sab_saida": $("#hr_turno_sab_saida").val(),
            "t_hr_intervalo_dom": $("#hr_intervalo_dom").val(),
            "t_hr_intervalo_seg": $("#hr_intervalo_seg").val(),
            "t_hr_intervalo_ter": $("#hr_intervalo_ter").val(),
            "t_hr_intervalo_qua": $("#hr_intervalo_qua").val(),
            "t_hr_intervalo_qui": $("#hr_intervalo_qui").val(),
            "t_hr_intervalo_sex": $("#hr_intervalo_sex").val(),
            "t_hr_intervalo_sab": $("#hr_intervalo_sab").val(),
            "t_hr_intervalo_saida_dom": $("#hr_intervalo_saida_dom").val(),
            "t_hr_intervalo_saida_seg": $("#hr_intervalo_saida_seg").val(),
            "t_hr_intervalo_saida_ter": $("#hr_intervalo_saida_ter").val(),
            "t_hr_intervalo_saida_qua": $("#hr_intervalo_saida_qua").val(),
            "t_hr_intervalo_saida_qui": $("#hr_intervalo_saida_qui").val(),
            "t_hr_intervalo_saida_sex": $("#hr_intervalo_saida_sex").val(),
            "t_hr_intervalo_saida_sab": $("#hr_intervalo_saida_sab").val(),
            "t_ic_preenchimento_automatico": ic_preenchimento_automatico,
            "t_ic_nao_repetir": ic_nao_repetir,
            "t_leads_pk":$("#leads_pk_agenda").val(),
            "t_dias_escala_servico":$("#dias_escala_servico").val(),
            "t_fl_escala_alternada":$("#fl_escala_alternada").val(),
            "t_dias_escala_alternada":$("#dias_escala_alternada").val(),
            "t_tipo_escala_alternada":$("#tipo_escala_alternada").val(),
            "t_ic_tempo_antes_ponto":$("#ic_tempo_antes_ponto").val(),
            "t_ic_ponto_fora_horario":$("#ic_ponto_fora_horario").val(),
            "t_functions":  ""
        }
    ).draw();

    $("#janela_agendas").modal("hide");
    return false;
}

function fcExcluirEscalaSemPk(){
    tblEscala.row($(this).parents('tr')).remove().draw();
    return false;
}



function fcEditarEscalaSemPk(){
    $("#janela_agendas").modal("show");
    $("#grid_itens_contrato").html();
    fcIncluirNovaEscalaSemPk();
    return false;
}

function fcAbrirFormNovaEscala() {
    $("#janela_agendas").modal("show");
    $("#grid_itens_contrato").html();
    $("#exibir_data_cancelamento").hide();
    //LIMPA FORM
    fcLimparFormAgenda();
    $("#acao_agenda").val("ins");



    $("#contratos_pk_combo").prop("disabled", false);
    $("#dt_inicio_agenda").prop("disabled", false);
    $("#dt_fim_agenda").prop("disabled", false);
    $("#produtos_servicos_pk_combo").prop("disabled", false);
    $("#colaboradores_pk_combo_agenda").prop("disabled", false);
    //COMBOS
    fcComboLeads("");//LEADS

    //CARREGAR HTML ITENS CONTRATO
    $("#contratos_pk_combo").change(function () {

        //VERIFICA SE PRODUTO SELECIONADO NÃO ESTA VAZIO
        if ($("#contratos_pk_combo").val() != "") {
            fcHtmlItensContrato();//CARREGA HTML ITENS CONTRATO
            fcContratoDatas();// CARREGA DATAS CONTRATO
            fcProduto() // CAREEGA COMBO PRODUTOS
            $("#print_escala_colaborador").html('');
        } else {
            $("#grid_itens_contrato").empty();
            $("#grid_itens_contrato").html('');
            $("#dt_inicio_agenda").val('');
            $("#dt_fim_agenda").val('');
            $("#produtos_servicos_pk_combo").val('');
            $("#colaboradores_pk_combo_agenda").html('');
            $("#print_escala_colaborador").html('');
        }
    });
    //CARREGA COMBO SERVIÇOS AO CLICAR EM CONTRATOS
    $("#produtos_servicos_pk_combo").change(function () {
        //VERIFICA SE PRODUTO SELECIONADO NÃO ESTA VAZIO
        if ($("#produtos_servicos_pk_combo").val() != "") {
            fcVerificaServidoQtdeEscala()//Verifica se o serviço selecionado a quantidade do contrato ja tem escalas definidas
            $("#print_escala_colaborador").html('');
        } else {
            $("#escala_colaborador").val('');
            $("#print_escala_colaborador").html('');
            $("#colaboradores_pk_combo_agenda").html('');
        }
    });
    //VERIFICA SE O COLABORADOR JA ESTA REGISTRADO EM OUTRA ESCALA
    $("#colaboradores_pk_combo_agenda").change(function () {
        //VERIFICA SE PRODUTO SELECIONADO NÃO ESTA VAZIO
        if ($("#colaboradores_pk_combo_agenda").val() != "") {
            fcVerificaOutraEscalaColaborador()//Verifica se o colaborador ja esta em outra escala
        }
    });

    fcCarregarTurno()//CARREGA TURNO

    //CLIQUE DO PREENCHIMENTO AUTOMATICO
    $('#ic_preenchimento_automatico').click(function () {
        fcPreenchimentoAutomatico();//FUNÇÃO DE PREENCHIMENTO AUTOMATICO
    });
    //valida formulário
    fcValidarFormAgendas();
}
//INICIA COMBOS
function fcComboLeads(leads_pk) {

    var v_leads_pk = "";

    if (leads_pk != '' && typeof leads_pk != 'undefined') {
        v_leads_pk = leads_pk;
    }

    var objParametros = {
        "leads_pk": v_leads_pk
    };
    var arrCarregar = carregarController("lead", "listarTodos", objParametros)
    if (v_leads_pk == "") {
        carregarComboAjax($("#leads_pk_agenda"), arrCarregar, " ", "pk", "ds_lead");
        //COMBO DE CONTRATOS
        $("#leads_pk_agenda").change(function () {
            $("#contratos_pk_combo").val('');
            $("#grid_itens_contrato").empty();
            $("#grid_itens_contrato").html('');
            $("#dt_inicio_agenda").val('');
            $("#dt_fim_agenda").val('');
            $("#produtos_servicos_pk_combo").val('');
            
            $('#produtos_servicos_pk_combo')
            .empty()
            .append('<option value="">Selecione...</option>');
            $("#colaboradores_pk_combo_agenda").html('');
            $("#dias_escala_servico").val('');
            $("#print_escala_colaborador").html('');
            fcComboContratos($("#leads_pk_agenda").val());// COMBO CONTRATOS

        });
        $("#leads_pk_agenda").prop("disabled", false);
    } else {
        // Chamada da pagina de processos
        carregarComboAjax($("#leads_pk_agenda"), arrCarregar, "", "pk", "ds_lead");
        fcComboContratos($("#leads_pk_agenda").val());//COMBO DE CONTRATOS
        $("#leads_pk_agenda").prop("disabled", true);
    }
}

function  fcVerificaColaborador(colaborador_pk){
    var objParametros = {
        "pk": colaborador_pk
    };

    var arrCarregar = carregarController("colaborador", "listarPk", objParametros);
    if (arrCarregar.result == 'success'){

        if(arrCarregar.data[0]['ic_status']!=1){
            sweetMensagem('warning','Colaborador não está com status de ativo em sua ficha, verifique!');
            $('#janela_agendas').modal('hide');
            return false;
        }
    }

}


function fcComboContratos(leads_pk) {

    var v_leads_pk = "";
    var v_processos_pk = "";
    var v_colaborador_pk = "";

    if (leads_pk != '' && typeof leads_pk != 'undefined') {
        v_leads_pk = leads_pk;
    }


    var objParametros = {
        "leads_pk": v_leads_pk
    };
    var arrCarregar = carregarController("contrato", "listarLeadsPk", objParametros);

    carregarComboAjax($("#contratos_pk_combo"), arrCarregar, " ", "pk", "ds_combo_contrato");
}

function fcProduto() {
    //var arrProdutoServicoColaborador(fcFormatarDadosQualificacao())
    if($("#contratos_pk_combo").val()!=""){
        var objParametros = {
            "contratos_pk": $("#contratos_pk_combo").val()
        };

        var arrCarregar = carregarController("produto_servico", "listarProdutosContrato", objParametros);
        carregarComboAjax($("#produtos_servicos_pk_combo"), arrCarregar, " ", "pk", "ds_produto_servico");
    }
    
}

function fcColaborador() {
    var objParametros = {
        "contratos_pk": $("#contratos_pk_combo").val(),
        "produtos_servicos_pk": $("#produtos_servicos_pk_combo").val(),
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradoresQualidicacaoContrato", objParametros);
    carregarComboAjax($("#colaboradores_pk_combo_agenda"), arrCarregar, " ", "pk", "ds_colaborador");
}

function fcCarregarTurno() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("agenda_colaborador_padrao", "listarTurno", objParametros);
    carregarComboAjax($("#turno_base_agenda_pk"), arrCarregar, " ", "pk", "ds_turno");

}
//CONSULTAS
function fcContratoDatas() {
    var objParametros = {
        "leads_pk": $("#leads_pk_agenda").val(),
        "pk": $("#contratos_pk_combo").val()
    };

    var arrCarregar = carregarController("contrato", "listarPk", objParametros);

    if (arrCarregar.status == true) {
        //$("#dt_inicio_agenda").val(arrCarregar.data[0]['dt_inicio_contrato']);

        var str = arrCarregar.data[0]['dt_fim_contrato'];
        //carraga processo etapa do contrato selecionado
        $("#processos_etapas_pk_2").val(arrCarregar.data[0]['processos_etapas_pk'])

        var date = new Date(str.split('/').reverse().join('/'));
        var novaData = new Date();

        if (date < novaData) {
            sweetMensagem('warning',"Período do contrato seleciona esta vencido! Selecione ou Cadastre um contrato!");
            $("#contratos_pk_combo").val(0);
        } else {
            //$("#dt_fim_agenda").val(arrCarregar.data[0]['dt_fim_contrato']);
        }
    }
}

function fcVerificaServidoQtdeEscala() {
    var objParametros = {
        "produtos_servicos_pk": $("#produtos_servicos_pk_combo").val(),
        "contratos_pk": $("#contratos_pk_combo option:selected").val()
    };
    var arrCarregar = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros);
    if (arrCarregar.result == 'success') {
        var v_qtde_escalas = (arrCarregar.data[0]['qtde_servico_escala']);
        if (v_qtde_escalas > arrCarregar.data[0]['qtde_servico_item_contrato']) {
            $("#dias_escala_servico").val("") // zera a quantidade de dias da escala
            $("#print_dias_por_servico").html('') //zera html dos dias de escala
            $("#colaboradores_pk_combo_agenda").html('') //zera o combo de colaboradores
            var resultado = confirm("O Serviço selecionado já tem a quantidade de Escalas do contrato cadastradas! Verifique o contrato ou selecione outro serviço !");
        } else {
            $("#print_dias_por_servico").html("Escala do serviço selecionado: " + arrCarregar.data[0]['dias_escala'])//PRINTA A ESCALA DO SERVIÇO SELECIONADO
            //$("#dias_escala_servico").val(arrCarregar.data[0]['dias_escala']);//ADICIONA O VALOR DA ESCALA PARA UTILIZAR NO PREENCHIMENTO AUTOMATICO

            fcColaborador();//CARREGA HColaborador

        }
    }
}
//verifica se o colaborador tem uma ou mais escalas ativas
function fcVerificaOutraEscalaColaborador() {
    var vhtml = "";
    var objParametros = {
        "colaboradores_pk": $("#colaboradores_pk_combo_agenda").val()
    };

    var arrCarregar = carregarController("agenda_colaborador_padrao", "verificaOutraEscalaColaborador", objParametros);
    //alert(v_last_url)
    if (arrCarregar.result == 'success') {

        if (arrCarregar.data[0]['pk'] != 0) {

            $("#cmdEnviarAgenda").hide();

            vhtml += "<div class='row'>";
            vhtml += "     <div class='col-md-12'>";
            vhtml += "         <h5>Escala(s) registradas para o Colaborador</h5>";
            vhtml += "     </div>";
            vhtml += " </div>";

            for (i = 0; i < arrCarregar.data.length; i++) {
                vstatus = "Escala Ativa";
                vhtml += "<div class='row'>";
                vhtml += "     <div class='col-md-12'>";
                vhtml += "        <table class='table' style='width:100%' >";
                vhtml += "            <tr align='left' style='font-size: 14px'>";
                vhtml += "                <td whidt='30%'>";
                vhtml += "                    Posto de Trabalho: ";
                vhtml += "                </td>";
                vhtml += "                <td>";
                vhtml += arrCarregar.data[i]['ds_lead'];
                vhtml += "                </td>";
                vhtml += "            </tr>";
                vhtml += "            <tr align='left' style='font-size: 14px'>";
                vhtml += "                <td whidt='30%' >";
                vhtml += "                    DT escala: ";
                vhtml += "                </td>";
                vhtml += "                <td >";
                vhtml += arrCarregar.data[i]['dt_inicio_agenda'] + " Até " + arrCarregar.data[i]['dt_fim_agenda'];
                vhtml += "                </td>";
                vhtml += "            </tr>";
                vhtml += "            <tr align='left' style='font-size: 14px'>";
                vhtml += "                <td whidt='30%' >";
                vhtml += "                    Status: ";
                vhtml += "                </td>";
                vhtml += "                <td style='color:red' >";
                vhtml += "Escala Ativa";
                vhtml += "                </td>";
                vhtml += "            </tr>";
                vhtml += "        </table>";
                vhtml += "      </div>";
                vhtml += "</div>";
            }
            vhtml += "<div class='row'>";
            vhtml += "     <div class='col-md-12'>";
            vhtml += "         <button type='button' id='btn_continuar_registro' class='btn btn-primary' onclick='liberaRegistroEscala()'  >Continuar o Registro</button>";
            vhtml += "     </div>";
            vhtml += " </div>";
            $("#print_escala_colaborador").html(vhtml)
        } else {
            $("#cmdEnviarAgenda").show();
            $("#print_escala_colaborador").html('')
        }
    }
}

//AÇÔES
function liberaRegistroEscala() {
    var resultado = confirm("O Colaborador " + $("#colaboradores_pk_combo_agenda").text() + " possui estala(s) ativas! Deseja continuar com o cadastro de uma nova escla para este colaboradro?");
    if (resultado == true) {
        $("#cmdEnviarAgenda").show();
        $("#print_escala_colaborador").html('')
    } else {
        $("#print_escala_colaborador").html('')
        $("#colaboradores_pk_combo_agenda").val('');
    }
}
function fcPreenchimentoAutomatico() {
    //VERIFICA SE O CHECKBOS ESTA MARCADO
    if ($("#produtos_servicos_pk option:selected").val() == "") {
        sweetMensagem('warning',"Por favor, selecione o serviço para o preenchimento da escala!");
        $("#ic_preenchimento_automatico").prop("checked", false);
        return false;
    }

    //VERIFICA SE AS INFORMAÇÕES NECESSÁRIAS ESTÃO MARCADAS
    if ($("#turno_base_agenda_pk").val() == "") {
        sweetMensagem('warning',"Por favor, selecione o turno para o preenchimento automático!");
        $("#ic_preenchimento_automatico").prop("checked", false);
        $("#turno_base_agenda_pk").focus();
        return false;
    }
    if ($("#hr_inicio_expediente").val() == "") {
        sweetMensagem('warning',"Por favor, informe horário de início do expediente!");
        $("#ic_preenchimento_automatico").prop("checked", false);
        $("#hr_inicio_expediente").focus();
        return false;
    }
    if ($("#hr_termino_expediente").val() == "") {
        sweetMensagem('warning',"Por favor, informe horário de termino do expediente!");
        $("#ic_preenchimento_automatico").prop("checked", false);
        $("#hr_termino_expediente").focus();
        return false;
    }
    //PRENCHIMENTO DE TURNO
    if ($("#dias_escala_servico").val() == '12x36') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", true);
        $("#ic_qua_folga").prop("checked", false);
        $("#ic_qui_folga").prop("checked", true);
        $("#ic_sex_folga").prop("checked", false);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", false);
        $("#ic_qua").prop("checked", true);
        $("#ic_qui").prop("checked", false);
        $("#ic_sex").prop("checked", true);
        $("#ic_sab").prop("checked", false);

    } else if ($("#dias_escala_servico").val() == '6x1') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", false);
        $("#ic_qua_folga").prop("checked", false);
        $("#ic_qui_folga").prop("checked", false);
        $("#ic_sex_folga").prop("checked", false);
        $("#ic_sab_folga").prop("checked", false);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", true);
        $("#ic_qua").prop("checked", true);
        $("#ic_qui").prop("checked", true);
        $("#ic_sex").prop("checked", true);
        $("#ic_sab").prop("checked", true);
    } else if ($("#dias_escala_servico").val() == '5D') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", false);
        $("#ic_qua_folga").prop("checked", false);
        $("#ic_qui_folga").prop("checked", false);
        $("#ic_sex_folga").prop("checked", false);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", true);
        $("#ic_qua").prop("checked", true);
        $("#ic_qui").prop("checked", true);
        $("#ic_sex").prop("checked", true);
        $("#ic_sab").prop("checked", false);
    } else if ($("#dias_escala_servico").val() == '4D') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", false);
        $("#ic_qua_folga").prop("checked", false);
        $("#ic_qui_folga").prop("checked", false);
        $("#ic_sex_folga").prop("checked", false);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", true);
        $("#ic_qua").prop("checked", true);
        $("#ic_qui").prop("checked", true);
        $("#ic_sex").prop("checked", true);
        $("#ic_sab").prop("checked", false);
    } else if ($("#dias_escala_servico").val() == '3D') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", false);
        $("#ic_qua_folga").prop("checked", false);
        $("#ic_qui_folga").prop("checked", true);
        $("#ic_sex_folga").prop("checked", true);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", true);
        $("#ic_qua").prop("checked", true);
        $("#ic_qui").prop("checked", false);
        $("#ic_sex").prop("checked", false);
        $("#ic_sab").prop("checked", false);
    } else if ($("#dias_escala_servico").val() == '2D') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", false);
        $("#ic_qua_folga").prop("checked", true);
        $("#ic_qui_folga").prop("checked", true);
        $("#ic_sex_folga").prop("checked", true);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", true);
        $("#ic_qua").prop("checked", false);
        $("#ic_qui").prop("checked", false);
        $("#ic_sex").prop("checked", false);
        $("#ic_sab").prop("checked", false);
    } else if ($("#dias_escala_servico").val() == '1D') {
        //FOLGAS
        $("#ic_dom_folga").prop("checked", true);
        $("#ic_seg_folga").prop("checked", false);
        $("#ic_ter_folga").prop("checked", true);
        $("#ic_qua_folga").prop("checked", true);
        $("#ic_qui_folga").prop("checked", true);
        $("#ic_sex_folga").prop("checked", true);
        $("#ic_sab_folga").prop("checked", true);
        //DIAS DE TRABALHO
        $("#ic_dom").prop("checked", false);
        $("#ic_seg").prop("checked", true);
        $("#ic_ter").prop("checked", false);
        $("#ic_qua").prop("checked", false);
        $("#ic_qui").prop("checked", false);
        $("#ic_sex").prop("checked", false);
        $("#ic_sab").prop("checked", false);
    }


    //TURNOS
    $("#dom_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#seg_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#ter_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#qua_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#qui_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#sex_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    $("#sab_turnos_pk").val($("#turno_base_agenda_pk option:selected").val());
    //HORARIO ENTRATDA EXPEDIENTE
    $("#hr_turno_dom").val($("#hr_inicio_expediente").val());
    $("#hr_turno_seg").val($("#hr_inicio_expediente").val());
    $("#hr_turno_ter").val($("#hr_inicio_expediente").val());
    $("#hr_turno_qua").val($("#hr_inicio_expediente").val());
    $("#hr_turno_qui").val($("#hr_inicio_expediente").val());
    $("#hr_turno_sex").val($("#hr_inicio_expediente").val());
    $("#hr_turno_sab").val($("#hr_inicio_expediente").val());
    //HORARIO SAIDA INTERVALO
    $("#hr_intervalo_dom").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_seg").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_ter").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_qua").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_qui").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_sex").val($("#hr_saida_intervalo").val());
    $("#hr_intervalo_sab").val($("#hr_saida_intervalo").val());
    //HORARIO RETORNO INTERVALO
    $("#hr_intervalo_saida_dom").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_seg").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_ter").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_qua").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_qui").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_sex").val($("#hr_retorno_intervalo").val());
    $("#hr_intervalo_saida_sab").val($("#hr_retorno_intervalo").val());
    //HORARIO SAIDA EXPEDIENTE
    $("#hr_turno_dom_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_seg_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_ter_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_qua_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_qui_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_sex_saida").val($("#hr_termino_expediente").val());
    $("#hr_turno_sab_saida").val($("#hr_termino_expediente").val());
}
function fecharModalEscala(){
    $("#janela_agendas").modal("hide");
}
//HTML
function fcHtmlItensContrato() {
    $("#grid_itens_contrato").empty();
    $("#grid_itens_contrato").html();
    var strRetorno = "";
    var strNenhumRegisto = "";
    var qtde_dias_semana = "";
    var ds_produto_servico = "";
    var ds_itens_contratador = "";
    var ds_profissionais_contratados = "";
    var ds_diferenca = "";
    var objParametros1 = {
        "leads_pk": $("#leads_pk_agenda").val(),
        "contratos_pk": $("#contratos_pk_combo").val()
    };
    var arrCarregar1 = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros1);

    $("#agenda_contratos_itens_pk").val(arrCarregar1.data[0]['t_pk'])
    if (arrCarregar1.status == true) {
        strRetorno += "<div class='row'>";
        strRetorno += "<div class='col-md-12'>";
        strRetorno += "<table class='table' style='width:100%' >";
        strRetorno += "<tbody>";

        strRetorno += "<thead>";
        strRetorno += "<tr align='center'>";
        strRetorno += "<th >Serviços Contratados</th><th >Qtde de<br>Colaborador</th><th >Escala</th>";
        strRetorno += "</tr>";
        strRetorno += "</thead>";
        for (i = 0; i < arrCarregar1.data.length; i++) {
            ds_produto_servico = arrCarregar1.data[i]['ds_produto_servico'];
            ds_itens_contratador = arrCarregar1.data[i]['n_qtde'];
            qtde_dias_semana = arrCarregar1.data[i]['n_qtde_dias_semana'];
            strRetorno += "<tbody>";
            strRetorno += "<tr align='center'>";
            strRetorno += "<td width='20%'>" + ds_produto_servico + "</td>";
            strRetorno += "<td width='20%'>" + ds_itens_contratador + "</td>";
            strRetorno += "<td width='20%'>" + qtde_dias_semana + "</td>";
            strRetorno += "</tr>";
            strRetorno += "</tbody>";
        }
        strRetorno += "</table>";
        strRetorno += "</div>";
        strRetorno += "</div>";
        $("#grid_itens_contrato").html(strRetorno);
    }
}
//LIMPEZA
function fcLimparFormAgenda() {
    $("#acao_agenda").val("");
    $("#grid_itens_contrato").html("");
    $("#dias_por_servico").html("");
    $("#agenda_colaborador_padrao_pk").val("");
    $("#grid").empty();
    $("#produtos_servicos_pk_combo").val("");
    $('#produtos_servicos_pk_combo')
    .empty()
    .append('<option value="">Selecione...</option>');
    $("#agenda_contratos_itens_pk").val("");
    $("#dt_inicio_agenda").val("");
    $("#dt_fim_agenda").val("");
    $("#colaboradores_pk_combo_agenda").val("");
    $("#contratos_pk_combo").val("");
    $("#dt_cancelamento_agenda_escala").val("");
    $("#ds_motivo_cancelamento").val("");
    $("#tipo_escala").val("");
    $("#colaboradores_pk_combo_agenda").val('');
    $("#ic_dom").prop("checked", false);
    $("#ic_seg").prop("checked", false);
    $("#ic_ter").prop("checked", false);
    $("#ic_qua").prop("checked", false);
    $("#ic_qui").prop("checked", false);
    $("#ic_sex").prop("checked", false);
    $("#ic_sab").prop("checked", false);
    $("#ic_dom_folga").prop("checked", false);
    $("#ic_seg_folga").prop("checked", false);
    $("#ic_ter_folga").prop("checked", false);
    $("#ic_qua_folga").prop("checked", false);
    $("#ic_qui_folga").prop("checked", false);
    $("#ic_sex_folga").prop("checked", false);
    $("#ic_sab_folga").prop("checked", false);
    $("#ic_folga_inverter").prop("checked", false);
    $("#ic_dom").prop("disabled", false);
    $("#ic_seg").prop("disabled", false);
    $("#ic_ter").prop("disabled", false);
    $("#ic_qua").prop("disabled", false);
    $("#ic_qui").prop("disabled", false);
    $("#ic_sex").prop("disabled", false);
    $("#ic_sab").prop("disabled", false);
    $("#ic_dom_folga").prop("disabled", false);
    $("#ic_seg_folga").prop("disabled", false);
    $("#ic_ter_folga").prop("disabled", false);
    $("#ic_qua_folga").prop("disabled", false);
    $("#ic_qui_folga").prop("disabled", false);
    $("#ic_sex_folga").prop("disabled", false);
    $("#ic_sab_folga").prop("disabled", false);
    $("#ic_folga_inverter").prop("disabled", false);
    $("#dom_turnos_pk").val("");
    $("#seg_turnos_pk").val("");
    $("#ter_turnos_pk").val("");
    $("#qua_turnos_pk").val("");
    $("#qui_turnos_pk").val("");
    $("#sex_turnos_pk").val("");
    $("#sab_turnos_pk").val("");
    $("#hr_turno_dom").val("");
    $("#hr_turno_seg").val("");
    $("#hr_turno_ter").val("");
    $("#hr_turno_qua").val("");
    $("#hr_turno_qui").val("");
    $("#hr_turno_sex").val("");
    $("#hr_turno_sab").val("");
    $("#hr_turno_dom_saida").val("");
    $("#hr_turno_seg_saida").val("");
    $("#hr_turno_ter_saida").val("");
    $("#hr_turno_qua_saida").val("");
    $("#hr_turno_qui_saida").val("");
    $("#hr_turno_sex_saida").val("");
    $("#hr_turno_sab_saida").val("");
    $("#hr_intervalo_dom").val("");
    $("#hr_intervalo_seg").val("");
    $("#hr_intervalo_ter").val("");
    $("#hr_intervalo_qua").val("");
    $("#hr_intervalo_qui").val("");
    $("#hr_intervalo_sex").val("");
    $("#hr_intervalo_sab").val("");
    $("#hr_intervalo_saida_dom").val("");
    $("#hr_intervalo_saida_seg").val("");
    $("#hr_intervalo_saida_ter").val("");
    $("#hr_intervalo_saida_qua").val("");
    $("#hr_intervalo_saida_qui").val("");
    $("#hr_intervalo_saida_sex").val("");
    $("#hr_intervalo_saida_sab").val("");
    $("#leads_pk_agenda").val("");
    $("#turno_base_agenda_pk").val("");
    $("#hr_inicio_expediente").val("");
    $("#hr_termino_expediente").val("");
    $("#hr_saida_intervalo").val("");
    $("#hr_retorno_intervalo").val("");
    $("#dias_escala_servico").val('');
    $("#fl_escala_alternada").val("");
    $("#dias_escala_alternada").val("");
    $("#tipo_escala_alternada").val("");
    
    $("#ic_tempo_antes_ponto").val("");
    $("#ic_ponto_fora_horario").val(1);
    $("#ic_preenchimento_automatico").prop("checked", false);
    toggleEscalaAlternadaFields();
}

function fcIntrajornada(){
    if ($('#ic_intrajornada').is(":checked")) {
        $('#hr_saida_intervalo').val(" ")
        $('#hr_retorno_intervalo').val(" ")
        $("#hr_retorno_intervalo").attr('disabled','disabled');
        $("#hr_saida_intervalo").attr('disabled','disabled');

        $('#hr_intervalo_dom').val(" ")
        $('#hr_intervalo_seg').val(" ")
        $('#hr_intervalo_ter').val(" ")
        $('#hr_intervalo_qua').val(" ")
        $('#hr_intervalo_qui').val(" ")
        $('#hr_intervalo_sex').val(" ")
        $('#hr_intervalo_sab').val(" ")
        $("#hr_intervalo_dom").attr('disabled','disabled');
        $("#hr_intervalo_seg").attr('disabled','disabled');
        $("#hr_intervalo_ter").attr('disabled','disabled');
        $("#hr_intervalo_qua").attr('disabled','disabled');
        $("#hr_intervalo_qui").attr('disabled','disabled');
        $("#hr_intervalo_sex").attr('disabled','disabled');
        $("#hr_intervalo_sab").attr('disabled','disabled');

        $('#hr_intervalo_saida_dom').val(" ")
        $('#hr_intervalo_saida_seg').val(" ")
        $('#hr_intervalo_saida_ter').val(" ")
        $('#hr_intervalo_saida_qua').val(" ")
        $('#hr_intervalo_saida_qui').val(" ")
        $('#hr_intervalo_saida_sex').val(" ")
        $('#hr_intervalo_saida_sab').val(" ")
        $("#hr_intervalo_saida_dom").attr('disabled','disabled');
        $("#hr_intervalo_saida_seg").attr('disabled','disabled');
        $("#hr_intervalo_saida_ter").attr('disabled','disabled');
        $("#hr_intervalo_saida_qua").attr('disabled','disabled');
        $("#hr_intervalo_saida_qui").attr('disabled','disabled');
        $("#hr_intervalo_saida_sex").attr('disabled','disabled');
        $("#hr_intervalo_saida_sab").attr('disabled','disabled');
    }else{
        $("#hr_retorno_intervalo").removeAttr('disabled');
        $("#hr_saida_intervalo").removeAttr('disabled');

        $("#hr_intervalo_dom").removeAttr('disabled');
        $("#hr_intervalo_seg").removeAttr('disabled');
        $("#hr_intervalo_ter").removeAttr('disabled');
        $("#hr_intervalo_qua").removeAttr('disabled');
        $("#hr_intervalo_qui").removeAttr('disabled');
        $("#hr_intervalo_sex").removeAttr('disabled');
        $("#hr_intervalo_sab").removeAttr('disabled');

        $("#hr_intervalo_saida_dom").removeAttr('disabled');
        $("#hr_intervalo_saida_seg").removeAttr('disabled');
        $("#hr_intervalo_saida_ter").removeAttr('disabled');
        $("#hr_intervalo_saida_qua").removeAttr('disabled');
        $("#hr_intervalo_saida_qui").removeAttr('disabled');
        $("#hr_intervalo_saida_sex").removeAttr('disabled');
        $("#hr_intervalo_saida_sab").removeAttr('disabled');
    }
}

$(document).ready(function () {
    //CHAMADA MODAL
    $(document).on('click', '#btn_modal_agenda', fcAbrirFormNovaEscala);
    $(document).on('click', '#ic_intrajornada', fcIntrajornada);
    $(document).on('change', '#fl_escala_alternada', toggleEscalaAlternadaFields);

        // Início
    $('#dt_inicio_agenda').datepicker({
            format: "dd/mm/yyyy",      // exibe como dia/mês/ano
            startView: "months",
            minViewMode: "months",
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            let year = e.date.getFullYear();
            let month = e.date.getMonth(); // 0 = Janeiro
            let firstDay = new Date(year, month, 1);

            // Define no campo o primeiro dia do mês
            $(this).datepicker('update', firstDay);

            // Calcula fim + 24 meses
            let endDate = new Date(firstDay);
            endDate.setMonth(endDate.getMonth() + 24);
            $('#dt_fim_agenda').datepicker('update', endDate);
        });

        // Fim
        $('#dt_fim_agenda').datepicker({
            format: "dd/mm/yyyy",      // agora garante formato BR
            language: "pt-BR",
            autoclose: true,
            todayHighlight: true
        });

        // Bloquear digitação        
        $('#dt_fim_agenda').attr('disabled', true);
        $('#dt_inicio_agenda').attr('readonly', true);
    

    $("#hr_inicio_expediente").keypress(function () {
        mascara(this, horamask);
    });
    $("#hr_termino_expediente").keypress(function () {
        mascara(this, horamask);
    });
    $("#hr_saida_intervalo").keypress(function () {
        mascara(this, horamask);
    });
    $("#hr_retorno_intervalo").keypress(function () {
        mascara(this, horamask);
    });

    toggleEscalaAlternadaFields();

});
