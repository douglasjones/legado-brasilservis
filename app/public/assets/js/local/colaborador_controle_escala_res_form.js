var tblEscala;
var strComboNova = "";

function fcCarregarGridEscala() {
        var objParametros = {
            "colaborador_pk": $("#colaborador_pk").val()
        };
        var v_url = routes_api("agenda_colaborador_padrao", "lisarEscalasResPadraoColaborador", objParametros);

        //NewWindow(v_last_url)

        //Trata a tabela
        tblEscala = $('#tblEscala').DataTable({
            searching: true,
            paging: true,
            scrollX: true,
            pageLength: 10,
            aLengthMenu: [10, 25, 50, 100],
            iDisplayLength: 10,
            processing: false,
            serverSide: false,
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
                        return full['t_ds_lead'];
                    },
                    'orderable': true,
                    'searchable': false,

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ds_identificacao_area'];
                    },
                    'orderable': true,
                    'searchable': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ds_produto_servico'];
                    },
                    'orderable': true,
                    'searchable': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_n_qtde_dias_semana'];
                    },
                    'orderable': true,
                    'searchable': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_dt_periodo_escala'];
                    },
                    'orderable': true,
                    'searchable': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_dt_cancelamento'];
                    },
                    'orderable': true,
                    'searchable': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ds_motivo_cancelamento'];
                    },
                    'orderable': true,
                    'searchable': false,

                }
                ,{
                    mRender: function (data, type, full) {
                        return full['t_ic_nao_repetir'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_preenchimento_automatico'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_sab'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_sex'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_qui'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_qua'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_ter'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_seg'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_saida_dom'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_sab'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_sex'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_qui'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_qua'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_ter'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_seg'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_intervalo_dom'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_sab_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_sex_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_qui_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_qua_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_ter_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_seg_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_dom_saida'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_sab'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_sex'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_qui'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_qua'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_ter'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_seg'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_turno_dom'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_sab_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_sex_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_qui_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_qua_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ter_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_seg_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_dom_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_sab_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_sex_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_qui_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_qua_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_ter_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_seg_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_dom_folga'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_sab'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_sex'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_qui'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_qua'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_ter'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_seg'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_dom'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_intrajornada'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_tipo_escala'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_ic_folga_inverter'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_retorno_intervalo'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_saida_intervalo'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_termino_expediente'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_hr_inicio_expediente'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_turnos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_contratos_itens_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_processos_etapas_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_produtos_servicos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_dt_fim_agenda'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_dt_inicio_agenda'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_contratos_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },{
                    mRender: function (data, type, full) {
                        return full['t_leads_pk'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_dias_escala_servico'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ic_ponto_fora_horario'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },
                {
                    mRender: function (data, type, full) {
                        return full['t_ic_tempo_antes_ponto'];
                    },
                    'orderable': true,
                    'searchable': false,
                    'visible': false,

                },
                {
                    mRender: function (data, type, full) {
                        var buttonPainel = '<a class="function_edit"><i class="bi bi-pencil-square" style="font-size:18px; color:blue" title="Editar"></i></a> ';
                        var buttonDelete = '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a> ';


                        return buttonPainel + buttonDelete;
                    },
                    'orderable': false,
                    'searchable': false,
                }
            ]
        });
        //Atribui os eventos na coluna ação.
        $('#tblEscala tbody').on('click', '.function_edit', function () {
            var data;
            rLinhaSelecionada = null;
            if (tblEscala.row($(this).parents('li')).data()) {
                data = tblEscala.row($(this).parents('li')).data();
                rLinhaSelecionada = $(this).parents('li');
            }
            else if (tblEscala.row($(this).parents('tr')).data()) {
                data = tblEscala.row($(this).parents('tr')).data();
                rLinhaSelecionada = $(this).parents('tr');
            }

            fcEditarAgenda(data);


        });

        $('#tblEscala tbody').on('click', '.function_delete', function () {
            var data;
            if (tblEscala.row($(this).parents('li')).data()) {
                data = tblEscala.row($(this).parents('li')).data();
            }
            else if (tblEscala.row($(this).parents('tr')).data()) {
                data = tblEscala.row($(this).parents('tr')).data();
            }

            if (data['t_pk'] != "") {
                fcExcluirAgenda(data['t_pk']);
            }
            tblEscala.row($(this).parents('tr')).remove().draw();
        });
        return false;



}

function recarregarGridEscala(){
    setTimeout(function(){
        tblEscala.ajax.reload();
    }, 100);
}

$(document).ready(function () {
    //carregar table escala
    fcCarregarGridEscala();


});