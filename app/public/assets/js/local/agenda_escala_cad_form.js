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

var confirmarNovaEscalaCallback = null;

function abrirModalConfirmacaoNovaEscala(message, onConfirm) {
    confirmarNovaEscalaCallback = onConfirm;
    $("#modal_confirmacao_nova_escala_mensagem").text(message || "");
    $("#modal_confirmacao_nova_escala").modal("show");
}

function fecharModalConfirmacaoNovaEscala() {
    confirmarNovaEscalaCallback = null;
    $("#modal_confirmacao_nova_escala").modal("hide");
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
            "produtos_servicos_pk": $('#produtos_servicos_pk option:selected').val()
        }
        var arrCarregar0 = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros0);

        var v_contratos_itens_pk = $("#contratos_itens_pk").val();
        var v_n_qtde_dias_semana = $("#n_qtde_dias_semana").val();

        if (arrCarregar0 && arrCarregar0.status == true && arrCarregar0.data.length > 0) {
            v_contratos_itens_pk = arrCarregar0.data[0]['contratos_itens_pk'];
            v_n_qtde_dias_semana = arrCarregar0.data[0]['n_qtde_dias_semana'];
        }


        if(v_n_qtde_dias_semana == '12x36' && $("#tipo_escala").val() == ""){
            $("#alert_tipo_escala").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_tipo_escala").slideUp(500);
            });
            $('#tipo_escala').focus();
            return false;
        }

        var processosEtapasPk = $('#processos_etapas_pk_2').val();
        if ((processosEtapasPk === "" || typeof processosEtapasPk === "undefined") && $("#agenda_colaborador_padrao_pk").val() != "") {
            var arrEscalaAtual = carregarController("agenda_colaborador_padrao", "lisarEscalaEditar", {
                "pk": $("#agenda_colaborador_padrao_pk").val()
            });
            if (arrEscalaAtual && arrEscalaAtual.status === true && arrEscalaAtual.data.length > 0) {
                processosEtapasPk = arrEscalaAtual.data[0]['processos_etapas_pk'] || "";
                $("#processos_etapas_pk_2").val(processosEtapasPk);
            }
        }

        var objParametros = {
            "pk": $("#agenda_colaborador_padrao_pk").val(),
            "leads_pk": $("#leads_pk_agenda").val(),
            "contratos_pk": $("#contratos_pk_combo").val(),
            "dt_inicio_agenda": $("#dt_inicio_agenda").val(),
            "dt_fim_agenda": $("#dt_fim_agenda").val(),
            "produtos_servicos_pk": $('#produtos_servicos_pk').val(),
            "colaboradores_pk": $("#agenda_colaboradores_pk").val(),
            "processos_etapas_pk": processosEtapasPk,
            "contratos_itens_pk": v_contratos_itens_pk,
            "turnos_pk": $('#turno_base_agenda_pk').val(),
            "hr_inicio_expediente": $('#hr_inicio_expediente').val(),
            "hr_termino_expediente": $('#hr_termino_expediente').val(),
            "hr_saida_intervalo": $('#hr_saida_intervalo').val(),
            "hr_retorno_intervalo": $('#hr_retorno_intervalo').val(),
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
            "dias_escala_servico": $("#dias_escala_servico").val(),
            "fl_escala_alternada": $("#fl_escala_alternada").val(),
            "dias_escala_alternada": $("#dias_escala_alternada").val(),
            "tipo_escala_alternada": $("#tipo_escala_alternada").val(),
            "hr_jornada_trabalho_intervalo": $("#hr_jornada_trabalho_intervalo").val(),
            "hr_total_expediente": $("#hr_total_expediente").val(),
            "ic_tempo_antes_ponto": $("#ic_tempo_antes_ponto").val(),
            "ic_ponto_fora_horario": $("#ic_ponto_fora_horario").val(),
            "n_qtde_dias_semana": v_n_qtde_dias_semana,
            "ic_preenchimento_automatico": ic_preenchimento_automatico,
            "ic_nao_repetir": ic_nao_repetir,
            "confirmar_nova_escala": confirmarNovaEscala ? 1 : "",
        };

        var arrEnviar = carregarController("agenda_colaborador_padrao", "salvar", objParametros);

        if (arrEnviar && arrEnviar.status == true) {
            $("#agenda_colaborador_padrao_pk").val(arrEnviar.data)
            $("#n_qtde_dias_semana").val(v_n_qtde_dias_semana)
            if ($("#dt_cancelamento_agenda_escala").val() != "") {
                utilsJS.toastNotify(true,"Registro cadastrado com sucesso!");
                setTimeout(function () {
                    fcVoltar();
                }, 800);
                return;
            }
            fcCadastrarEscala();
        } else if (arrEnviar && arrEnviar.requires_confirmation) {
            abrirModalConfirmacaoNovaEscala(arrEnviar.message, function () {
                fcSalvar(true);
            });
            return false;
        } else if (arrEnviar) {
            sweetMensagem('warning',arrEnviar.message || arrEnviar.result);
        }
    }catch(e){
        utilsJS.toastNotify(false,e)
    }

}

function fcCadastrarEscala() {
    var objParametros = {
        "agenda_colaborador_padrao_pk": $("#agenda_colaborador_padrao_pk").val(),
        "leads_pk": $("#leads_pk_agenda").val(),
        "colaboradores_pk": $("#agenda_colaboradores_pk").val(),
        "dt_periodo_ini": $("#dt_inicio_agenda").val(),
        "dt_periodo_fim": $("#dt_fim_agenda").val(),
        "tipo_escala": $("#tipo_escala").val(),
        "dias_escala_servico": $("#dias_escala_servico").val(),
        "fl_escala_alternada": $("#fl_escala_alternada").val(),
        "n_qtde_dias_semana": $("#n_qtde_dias_semana").val()
    }

    var arrEnviar = carregarController("agenda_colaborador_padrao", "escalaDadosColaborador", objParametros);
    //NewWindow(v_last_url)
    if (arrEnviar && arrEnviar.status == true) {
        utilsJS.toastNotify(true,"Registro cadastrado com sucesso!");
        setTimeout(function () {
            fcVoltar();
        }, 800);
    } else if (arrEnviar) {
        sweetMensagem('warning',arrEnviar.message || arrEnviar.result);
    }
}


function fcValidarFormAgendas() {
    $("#form_agenda").validate({
        rules: {
            dias_escala_servico: {
                required: true
            },
            leads_pk_agenda: {
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
            },
            agenda_colaboradores_pk: {
                required: true
            }
        },
        messages: {
            dias_escala_servico: {
                required: "Por favor, selecione um Tipo de Escala!"
            },
            leads_pk_agenda: {
                required: "Por favor, selecione um posto de trabalho!"
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
            },
            agenda_colaboradores_pk: {
                required: "Por favor, selecione o colaborador da escala!"
            }

        },
        submitHandler: function (form) {
            fcSalvar(false);
            return false;
        }
    });
}
//EDITAR ESCALA
function fcCarregar() {
    if($("#pk").val()!=""){
        utilsJS.loading("Carregando dados da escala...");
        setTimeout(function () {
            fcCarregarDadosEscala();
        }, 50);
    }

}

function fcCarregarDadosEscala() {
    try {
        //CONSULTA DADOS ESCALA

        var objParametros0 = {
            "pk": $("#pk").val()
        };
        //CARREGA COMBOS
        var arrCarregar = carregarController("agenda_colaborador_padrao", "lisarEscalaEditar", objParametros0)

        if (arrCarregar.status == true) {
            $("#agenda_colaborador_padrao_pk").val(arrCarregar.data[0]['pk']);//PK AGENDA
            $("#contratos_itens_pk").val(arrCarregar.data[0]['contratos_itens_pk']);
            $("#processos_etapas_pk_2").val(arrCarregar.data[0]['processos_etapas_pk']);
            $("#n_qtde_dias_semana").val(arrCarregar.data[0]['n_qtde_dias_semana']);
            $("#exibir_data_cancelamento").show();//PERMITE O CANCELAMENTO DA ESCALA

            fcComboLeads(arrCarregar.data[0]['leads_pk']);//COMBO LEADS

            //fcComboContratos(arrCarregar.data[0]['leads_pk']);
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
            $("#dt_cancelamento_agenda_escala").val(arrCarregar.data[0]['dt_cancelamento']);//DT CANCELAMENTO
            $("#ds_motivo_cancelamento").val(arrCarregar.data[0]['ds_motivo_cancelamento']);
            if (arrCarregar.data[0]['dt_cancelamento'] != null) {//desabilita a o salvar se a escala estiver cancelada
                sweetMensagem('warning','A escala esta cancelada!')
                $("#dt_cancelamento_agenda_escala").prop("disabled", true);
                $("#ds_motivo_cancelamento").prop("disabled", true);
                $("#cmdEnviarAgenda").hide();
            } else {
                $("#dt_cancelamento_agenda_escala").prop("disabled", false);
                $("#ds_motivo_cancelamento").prop("disabled", false);
                $("#cmdEnviarAgenda").show();
            }

            $("#contratos_pk_combo").val(arrCarregar.data[0]['contratos_pk']);//CONTRATOS PK
            //$("#contratos_pk_combo").prop("disabled", true);//DESABILITA O COMBO DE CONTRATO
            fcHtmlItensContrato();//CARREGA HTML DE ITENS

            $("#dt_inicio_agenda").val(arrCarregar.data[0]['dt_inicio_agenda']);//DATA INICIO ESCALA
            $("#dt_fim_agenda").val(arrCarregar.data[0]['dt_fim_agenda']);//DATA INICIO ESCALA

            $("#dt_inicio_agenda").prop("disabled", true);
            $("#dt_fim_agenda").prop("disabled", true);
            fcProduto();//COMBO DE PRODUTOS
            fcGarantirOpcaoCombo(
                $("#produtos_servicos_pk"),
                arrCarregar.data[0]['produtos_servicos_pk'],
                arrCarregar.data[0]['ds_produto_servico']
            );
            $("#produtos_servicos_pk").val(arrCarregar.data[0]['produtos_servicos_pk']);//CONTRATOS PK
            $("#produtos_servicos_pk").prop("disabled", true);

            //Verifica status atual do Colaborador
            fcVerificaColaborador(arrCarregar.data[0]['colaborador_pk'])


            fcColaborador();//CARREGA HColaborador
            fcGarantirOpcaoCombo(
                $("#agenda_colaboradores_pk"),
                arrCarregar.data[0]['colaborador_pk'],
                arrCarregar.data[0]['ds_colaborador']
            );
            
            $("#agenda_colaboradores_pk").val(arrCarregar.data[0]['colaborador_pk']);//CONTRATOS PK

            
            fcCarregarTurno();//CARREGA COMBO DE TURNOS*/

            $("#turno_base_agenda_pk").val(arrCarregar.data[0]['turnos_pk']);


            $("#hr_inicio_expediente").val(arrCarregar.data[0]['hr_inicio_expediente']);
            $("#hr_termino_expediente").val(arrCarregar.data[0]['hr_termino_expediente']);
            $("#hr_saida_intervalo").val(arrCarregar.data[0]['hr_saida_intervalo']);
            $("#hr_retorno_intervalo").val(arrCarregar.data[0]['hr_retorno_intervalo']);
            if (arrCarregar.data[0]['ic_intrajornada'] == 1) {
                $("#ic_intrajornada").prop("checked", true);
                fcIntrajornada();
            } else {
                $("#ic_intrajornada").prop("checked", false);
            }

            $("#tipo_escala").val(arrCarregar.data[0]['tipo_escala']);

            if (arrCarregar.data[0]['ic_dom_folga'] == 1) {
                $("#ic_dom_folga").prop("checked", true);
            } else {
                $("#ic_dom_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_seg_folga'] == 1) {
                $("#ic_seg_folga").prop("checked", true);
            } else {
                $("#ic_seg_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_ter_folga'] == 1) {
                $("#ic_ter_folga").prop("checked", true);
            } else {
                $("#ic_ter_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_qua_folga'] == 1) {
                $("#ic_qua_folga").prop("checked", true);
            } else {
                $("#ic_qua_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_qui_folga'] == 1) {
                $("#ic_qui_folga").prop("checked", true);
            } else {
                $("#ic_qui_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_sex_folga'] == 1) {
                $("#ic_sex_folga").prop("checked", true);
            } else {
                $("#ic_sex_folga").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_sab_folga'] == 1) {
                $("#ic_sab_folga").prop("checked", true);
            } else {
                $("#ic_sab_folga").prop("checked", false);
            }

            if (arrCarregar.data[0]['ic_dom'] == 1) {
                $("#ic_dom").prop("checked", true);
            } else {
                $("#ic_dom").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_seg'] == 1) {
                $("#ic_seg").prop("checked", true);
            } else {
                $("#ic_seg").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_ter'] == 1) {
                $("#ic_ter").prop("checked", true);
            } else {
                $("#ic_ter").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_qua'] == 1) {
                $("#ic_qua").prop("checked", true);
            } else {
                $("#ic_qua").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_qui'] == 1) {
                $("#ic_qui").prop("checked", true);
            } else {
                $("#ic_qui").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_sex'] == 1) {
                $("#ic_sex").prop("checked", true);
            } else {
                $("#ic_sex").prop("checked", false);
            }
            if (arrCarregar.data[0]['ic_sab'] == 1) {
                $("#ic_sab").prop("checked", true);
            } else {
                $("#ic_sab").prop("checked", false);
            }
            $("#dias_escala_servico").val(arrCarregar.data[0]['n_qtde_dias_semana']);
            $("#dom_turnos_pk").val(arrCarregar.data[0]['dom_turnos_pk']);
            $("#seg_turnos_pk").val(arrCarregar.data[0]['seg_turnos_pk']);
            $("#ter_turnos_pk").val(arrCarregar.data[0]['ter_turnos_pk']);
            $("#qua_turnos_pk").val(arrCarregar.data[0]['qua_turnos_pk']);
            $("#qui_turnos_pk").val(arrCarregar.data[0]['qui_turnos_pk']);
            $("#sex_turnos_pk").val(arrCarregar.data[0]['sex_turnos_pk']);
            $("#sab_turnos_pk").val(arrCarregar.data[0]['sab_turnos_pk']);
            $("#hr_turno_dom").val(arrCarregar.data[0]['hr_turno_dom']);
            $("#hr_turno_seg").val(arrCarregar.data[0]['hr_turno_seg']);
            $("#hr_turno_ter").val(arrCarregar.data[0]['hr_turno_ter']);
            $("#hr_turno_qua").val(arrCarregar.data[0]['hr_turno_qua']);
            $("#hr_turno_qui").val(arrCarregar.data[0]['hr_turno_qui']);
            $("#hr_turno_sex").val(arrCarregar.data[0]['hr_turno_sex']);
            $("#hr_turno_sab").val(arrCarregar.data[0]['hr_turno_sab']);

            $("#hr_intervalo_dom").val(arrCarregar.data[0]['hr_intervalo_dom']);
            $("#hr_intervalo_seg").val(arrCarregar.data[0]['hr_intervalo_seg']);
            $("#hr_intervalo_ter").val(arrCarregar.data[0]['hr_intervalo_ter']);
            $("#hr_intervalo_qua").val(arrCarregar.data[0]['hr_intervalo_qua']);
            $("#hr_intervalo_qui").val(arrCarregar.data[0]['hr_intervalo_qui']);
            $("#hr_intervalo_sex").val(arrCarregar.data[0]['hr_intervalo_sex']);
            $("#hr_intervalo_sab").val(arrCarregar.data[0]['hr_intervalo_sab']);

            $("#hr_intervalo_saida_dom").val(arrCarregar.data[0]['hr_intervalo_saida_dom']);
            $("#hr_intervalo_saida_seg").val(arrCarregar.data[0]['hr_intervalo_saida_seg']);
            $("#hr_intervalo_saida_ter").val(arrCarregar.data[0]['hr_intervalo_saida_ter']);
            $("#hr_intervalo_saida_qua").val(arrCarregar.data[0]['hr_intervalo_saida_qua']);
            $("#hr_intervalo_saida_qui").val(arrCarregar.data[0]['hr_intervalo_saida_qui']);
            $("#hr_intervalo_saida_sex").val(arrCarregar.data[0]['hr_intervalo_saida_sex']);
            $("#hr_intervalo_saida_sab").val(arrCarregar.data[0]['hr_intervalo_saida_sab']);
            $("#hr_turno_dom_saida").val(arrCarregar.data[0]['hr_turno_dom_saida']);
            $("#hr_turno_seg_saida").val(arrCarregar.data[0]['hr_turno_seg_saida']);
            $("#hr_turno_ter_saida").val(arrCarregar.data[0]['hr_turno_ter_saida']);
            $("#hr_turno_qua_saida").val(arrCarregar.data[0]['hr_turno_qua_saida']);
            $("#hr_turno_qui_saida").val(arrCarregar.data[0]['hr_turno_qui_saida']);
            $("#hr_turno_sex_saida").val(arrCarregar.data[0]['hr_turno_sex_saida']);
            $("#hr_turno_sab_saida").val(arrCarregar.data[0]['hr_turno_sab_saida']);
            $("#hr_total_expediente").val(arrCarregar.data[0]['hr_total_expediente']);
            $("#hr_jornada_trabalho_intervalo").val(arrCarregar.data[0]['hr_jornada_trabalho_intervalo']);
            $("#ic_ponto_fora_horario").val(arrCarregar.data[0]['ic_ponto_fora_horario']);
            $("#ic_tempo_antes_ponto").val(arrCarregar.data[0]['ic_tempo_antes_ponto']);
            $("#fl_escala_alternada").val(arrCarregar.data[0]['fl_escala_alternada']);
            $("#dias_escala_alternada").val(arrCarregar.data[0]['dias_escala_alternada']);
            $("#tipo_escala_alternada").val(arrCarregar.data[0]['tipo_escala_alternada']);
            toggleEscalaAlternadaFields();
            if(arrCarregar.data[0]['hr_total_expediente']==null){
                calculoOnchange();
            }

             $('#dt_fim_agenda').attr('disabled', true);
            $('#dt_inicio_agenda').attr('disabled', true);
        
        }
    } catch(e) {
        utilsJS.toastNotify(false,e)
    } finally {
        $(".chzn-select").trigger("chosen:updated");
        utilsJS.loaded();
    }
}

function fcGarantirOpcaoCombo(objCombo, valor, texto) {
    if (valor != "" && typeof valor != "undefined" && objCombo.find("option[value='" + valor + "']").length == 0) {
        objCombo.append($('<option>', {
            value: valor,
            text: texto
        }));
    }
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
            $("#produtos_servicos_pk").html('');
            $("#agenda_colaboradores_pk").html('');
            $("#dias_escala_servico").val('');
            $("#print_escala_colaborador").html('');
            fcComboContratos($("#leads_pk_agenda").val());// COMBO CONTRATOS

        });
        $("#leads_pk_agenda").prop("disabled", false);
    } else {

        // Chamada da pagina de processos
        carregarComboAjax($("#leads_pk_agenda"), arrCarregar, "", "pk", "ds_lead");

        fcComboContratos(v_leads_pk);//COMBO DE CONTRATOS
        $("#leads_pk_agenda").val(v_leads_pk)
        $("#leads_pk_agenda").prop("disabled", true);
    }
}

function  fcVerificaColaborador(colaborador_pk){
    var objParametros = {
        "pk": colaborador_pk
    };
    var arrCarregar = carregarController("colaborador", "listarPk", objParametros);
    if (arrCarregar.status == true){
        /*if(arrCarregar.data[0]['ic_status']!=1){
            sweetMensagem('warning','Colaborador não está com status de ativo em sua ficha, verifique!');
            return false;
        }*/
    }
    
}

function fcComboContratos(leads_pk) {

    var v_leads_pk = "";

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

    var objParametros = {
        "contratos_pk": $("#contratos_pk_combo").val()
    };

    var arrCarregar = carregarController("produto_servico", "listarProdutosContrato", objParametros);

    carregarComboAjax($("#produtos_servicos_pk"), arrCarregar, " ", "pk", "ds_produto_servico");
}

function fcColaborador() {

    var objParametros = {
        "contratos_pk": $("#contratos_pk_combo").val(),
        "produtos_servicos_pk": $("#produtos_servicos_pk").val(),
    };

    var arrCarregar = carregarController("colaborador", "listarColaboradoresQualidicacaoContrato", objParametros);
    carregarComboAjax($("#agenda_colaboradores_pk"), arrCarregar, " ", "pk", "ds_colaborador");
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
            //$("#contratos_pk_combo").val(0);
        } else {
            //$("#dt_fim_agenda").val(arrCarregar.data[0]['dt_fim_contrato']);
        }
    }
}

function fcVerificaServidoQtdeEscala() {
    var objParametros = {
        "produtos_servicos_pk": $("#produtos_servicos_pk").val(),
        "contratos_pk": $("#contratos_pk_combo option:selected").val()
    };
    var arrCarregar = carregarController("contrato_item", "verificaServidoQtdeEscala", objParametros);
    if (arrCarregar.status == true) {
        //var v_qtde_escalas = (arrCarregar.data[0]['qtde_servico_escala']);
        /*if (v_qtde_escalas > arrCarregar.data[0]['qtde_servico_item_contrato']) {
            $("#dias_escala_servico").val() // zera a quantidade de dias da escala
            $("#print_dias_por_servico").html('') //zera html dos dias de escala
            $("#agenda_colaboradores_pk").html('') //zera o combo de colaboradores
        } else {*/
            //$("#print_dias_por_servico").html("Escala do serviço selecionado: " + arrCarregar.data[0]['dias_escala'])//PRINTA A ESCALA DO SERVIÇO SELECIONADO
            //$("#dias_escala_servico").val(arrCarregar.data[0]['dias_escala']);//ADICIONA O VALOR DA ESCALA PARA UTILIZAR NO PREENCHIMENTO AUTOMATICO
            $(".chzn-select").chosen('destroy');
            fcColaborador();//CARREGA HColaborador
            //$(".chzn-select").chosen({allow_single_deselect: true});
        //}
    }
}
//verifica se o colaborador tem uma ou mais escalas ativas
function fcVerificaOutraEscalaColaborador() {
    var vhtml = "";
    var objParametros = {
        "colaboradores_pk": $("#agenda_colaboradores_pk").val()
    };

    var arrCarregar = carregarController("agenda_colaborador_padrao", "verificaOutraEscalaColaborador", objParametros);
    //alert(v_last_url)
    if (arrCarregar.status == true) {

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
    var resultado = confirm("O Colaborador " + $("#agenda_colaboradores_pk option:selected").text() + " possui estala(s) ativas! Deseja continuar com o cadastro de uma nova escla para este colaboradro?");
    if (resultado == true) {
        $("#cmdEnviarAgenda").show();
        $("#print_escala_colaborador").html('')
    } else {
        $("#print_escala_colaborador").html('')
        $("#agenda_colaboradores_pk option:selected").text('');
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
        }
        else if ($("#dias_escala_servico").val() == '5x2') {
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
        }
        else if ($("#dias_escala_servico").val() == '5D') {
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
    if(arrCarregar1.data.length>0){
        $("#agenda_contratos_itens_pk").val(arrCarregar1.data[0]['pk'])
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

}
//LIMPEZA
function fcLimparFormAgenda() {
    $("#grid_itens_contrato").html("");
    $("#dias_por_servico").html("");
    $("#agenda_colaborador_padrao_pk").val("");
    $("#grid").empty();
    $("#agenda_produtos_servicos_pk").val("");
    $("#agenda_contratos_itens_pk").val("");
    $("#dt_inicio_agenda").val("");
    $("#dt_fim_agenda").val("");
    $("#agenda_colaboradores_pk").val("");
    //$("#contratos_pk_combo").val("");
    $("#dt_cancelamento_agenda_escala").val("");
    $("#ds_motivo_cancelamento").val("");
    $("#tipo_escala").val("");
    $("#agenda_colaboradores_pk").html('');
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
    $("#ic_preenchimento_automatico").prop("checked", false);
}

function recarregarGridResEscala(){
    setTimeout(function(){
        if (typeof tblAgenda !== "undefined" && tblAgenda && tblAgenda.ajax) {
            tblAgenda.ajax.reload();
        }
    }, 100);
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
function fcVoltar(){
    sendPost('agenda_colaborador_padrao','receptivoEscala',{})
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

function calculoOnchange() {
    try {


        var hr_ini_expediente = $("#hr_inicio_expediente").val() || "00:00";
        var hr_fim_expediente = $("#hr_termino_expediente").val() || "00:00";

        var hr_ini_intervalo = $("#hr_saida_intervalo").val() != "00:00" ? $("#hr_saida_intervalo").val() : "00:01";
        var hr_fim_intervalo = $("#hr_retorno_intervalo").val() != "00:00" ? $("#hr_retorno_intervalo").val() : "00:01";

        if (hr_ini_expediente != "00:00" && hr_fim_expediente != "00:00") {

            hr_ini_expediente = hmToMins(hr_ini_expediente);
            hr_fim_expediente = hmToMins(hr_fim_expediente);

            // Converter intervalo apenas se ambos forem preenchidos
            hr_ini_intervalo = hr_ini_intervalo != "00:00" ? hmToMins(hr_ini_intervalo) : hmToMins("00:01");
            hr_fim_intervalo = hr_fim_intervalo != "00:00" ? hmToMins(hr_fim_intervalo) : hmToMins("00:01");
            
            var hr_trabalhadas = 0;
            var hr_jornada = 0;

            // Se ambos os horários do intervalo estiverem preenchidos, calcula normalmente
            if (hr_ini_intervalo > 0 && hr_fim_intervalo > 0) {
                var hr_trabalhadas_manha = hr_ini_intervalo - hr_ini_expediente;
                var hr_trabalhadas_tarde = hr_fim_expediente - hr_fim_intervalo;

                hr_jornada = hr_trabalhadas_manha + hr_trabalhadas_tarde;
                // Ignora o intervalo e calcula direto
                hr_trabalhadas = hr_fim_expediente - hr_ini_expediente;
                // Corrigir para expediente que ultrapassa meia-noite
                if (hr_trabalhadas < 0) {
                    hr_jornada += 24 * 60;
                }
            } else {
                // Ignora o intervalo e calcula direto
                hr_trabalhadas = hr_fim_expediente - hr_ini_expediente;
                hr_jornada = "";
            }

            // Corrigir para expediente que ultrapassa meia-noite
            if (hr_trabalhadas < 0) {
                hr_trabalhadas += 24 * 60;
            }


            hr_trabalhadas = converHrs(hr_trabalhadas);
            if(hr_jornada!=""){
                hr_jornada = converHrs(hr_jornada);
            }
            
            $("#hr_total_expediente").val(hr_trabalhadas);
            $("#hr_jornada_trabalho_intervalo").val(hr_jornada);
            
            
        }
     
    } catch (e) {
        alert("Erro: " + e.message);
    }
}




$(document).ready(function () {
    $("#grid_itens_contrato").html();
    $("#exibir_data_cancelamento").hide();

    //LIMPA FORM
    fcLimparFormAgenda();

    //COMBOS
    fcComboLeads($("#leads_pk").val());//LEADS
    fcCarregarTurno()//CARREGA TURNO

    fcCarregar();//CARREGA

    $("#contratos_pk_combo").prop("disabled", false);
    $("#dt_inicio_agenda").prop("disabled", false);
    $("#dt_fim_agenda").prop("disabled", false);
    $("#produtos_servicos_pk").prop("disabled", false);
    $("#agenda_colaboradores_pk").prop("disabled", false);


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
            $("#produtos_servicos_pk").html('');
            $("#agenda_colaboradores_pk").html('');
            $("#print_escala_colaborador").html('');
        }
    });
    //CARREGA COMBO SERVIÇOS AO CLICAR EM CONTRATOS
    $("#produtos_servicos_pk").change(function () {
        
        //VERIFICA SE PRODUTO SELECIONADO NÃO ESTA VAZIO
        if ($("#produtos_servicos_pk").val() != "") {
            fcVerificaServidoQtdeEscala()//Verifica se o serviço selecionado a quantidade do contrato ja tem escalas definidas
            $("#print_escala_colaborador").html('');
            $(".chzn-select").chosen({allow_single_deselect: true});
        } else {
            $("#escala_colaborador").val('');
            $("#print_escala_colaborador").html('');
            $("#agenda_colaboradores_pk").html('');
        }
    });
    //VERIFICA SE O COLABORADOR JA ESTA REGISTRADO EM OUTRA ESCALA
    $("#agenda_colaboradores_pk").change(function () {
        //VERIFICA SE PRODUTO SELECIONADO NÃO ESTA VAZIO
        if ($("#agenda_colaboradores_pk").val() != "") {
            fcVerificaOutraEscalaColaborador()//Verifica se o colaborador ja esta em outra escala
        }
    });

    //CLIQUE DO PREENCHIMENTO AUTOMATICO
    $('#ic_preenchimento_automatico').click(function () {
        fcPreenchimentoAutomatico();//FUNÇÃO DE PREENCHIMENTO AUTOMATICO
    });
    $('#fl_escala_alternada').change(toggleEscalaAlternadaFields);
    //valida formulário
    fcValidarFormAgendas();
    toggleEscalaAlternadaFields();

    $(document).on('click', '#ic_intrajornada', fcIntrajornada);
    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#btn_confirmar_nova_escala', function () {
        var callback = confirmarNovaEscalaCallback;
        fecharModalConfirmacaoNovaEscala();
        if (typeof callback === "function") {
            callback();
        }
    });
    $(document).on('click', '.btn_cancelar_confirmacao_nova_escala', fecharModalConfirmacaoNovaEscala);
    $('#modal_confirmacao_nova_escala').on('hidden.bs.modal', function () {
        confirmarNovaEscalaCallback = null;
    });
    if($("#dt_inicio_agenda").val()==""){
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
    }


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

    $(".chzn-select").chosen({allow_single_deselect: true});

    if($("#pk").val()!=""){
        $("#agenda_colaboradores_pk")
        .prop("disabled", true)
        .trigger("chosen:updated"); // <- força o chosen a atualizar
    }

});
