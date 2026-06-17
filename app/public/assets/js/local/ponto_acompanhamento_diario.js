var tblResultado;
var click_id = 0;

function fcAtualizar(){

    $(".loader").show();
    $("#carregar").show();
    $("#exibir").hide();
    fcCarregarGrid();

    setTimeout(() => {
        $(".loader").hide();
        $("#carregar").hide();
        $("#exibir").show();
    }, 5000);
}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
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

function fcCarregarTurno() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("agenda_colaborador_padrao", "listarTurno", objParametros);
    carregarComboAjax($("#turnos_pk"), arrCarregar, " ", "pk", "ds_turno");

}

function fcCarregarGrid() {
    $("#datatable").html(""); // Limpa o conteúdo anterior

    let objParametros = {
        "leads_pk": $('#leads_pk').val(),
        "colaborador_pk": $('#colaborador_pk').val(),
        "turnos_pk": $('#turnos_pk').val(),
        "dt_pesquisa": $("#dia").val() + "/" + $("#mes").val() + "/" + $("#ano").val()
    };

    let arrCarregar = carregarController("ponto", "acompanhamentoPontoDiario", objParametros);
    let html = "";

    if (arrCarregar.status === true) {
        if (arrCarregar.data.length > 0) {
            html += `
                <style>
                    .lead-title {
                        font-size: 1.3rem;
                        font-weight: 600;
                        margin-top: 2rem;
                        color: #333;
                    }

                    .table-pontos {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 2rem;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
                        font-family: Arial, sans-serif;
                    }

                    .table-pontos th, .table-pontos td {
                        padding: 0.75rem 1rem;
                        text-align: center;
                        border: 1px solid #ccc;
                    }

                    .table-pontos thead {
                        background-color: #f0f0f0;
                    }

                    .table-pontos tbody tr:nth-child(even) {
                        background-color: #fafafa;
                    }

                    .table-pontos td:first-child {
                        text-align: left;
                        font-weight: 500;
                    }

                    .fora-tolerancia {
                        color: #d9534f;
                        font-weight: bold;
                    }

                    hr {
                        border: none;
                        border-top: 1px solid #ccc;
                        margin: 2rem 0;
                    }
                </style>
            `;

            // 🔹 Função auxiliar de cálculo de horas trabalhadas (com tolerância CLT + desconto almoço)
            function calcularHorasTrabalhadas(entrada, saida, inicioExpediente, fimExpediente, saidaIntervalo, retornoIntervalo) {
                if (!entrada || !saida) return '';

                function toMinutes(hora) {
                    const [h, m] = hora.split(':').map(Number);
                    return h * 60 + m;
                }

                let entradaMin = toMinutes(entrada);
                let saidaMin = toMinutes(saida);
                let inicioExp = toMinutes(inicioExpediente);
                let fimExp = toMinutes(fimExpediente);

                // Ajuste se expediente cruza a meia-noite (ex: 22:00 → 06:00)
                if (fimExp < inicioExp) fimExp += 24 * 60;
                if (saidaMin < entradaMin) saidaMin += 24 * 60;

                const tolerancia = 5;

                // Entrada dentro da tolerância
                if (entradaMin > inicioExp && entradaMin <= inicioExp + tolerancia) {
                    entradaMin = inicioExp;
                }

                // Saída dentro da tolerância
                if (saidaMin < fimExp && saidaMin >= fimExp - tolerancia) {
                    saidaMin = fimExp;
                }

                // Tempo total de trabalho
                let totalMinutos = saidaMin - entradaMin;

                // Desconta o intervalo de almoço, se houver
                if (saidaIntervalo && retornoIntervalo) {
                    let saidaIntMin = toMinutes(saidaIntervalo);
                    let retornoIntMin = toMinutes(retornoIntervalo);

                    // Ajuste se cruzar meia-noite
                    if (retornoIntMin < saidaIntMin) retornoIntMin += 24 * 60;

                    // Aplicar tolerância também ao intervalo
                    if (saidaIntMin < retornoIntMin) {
                        // Se saiu até 5 min antes, ajusta
                        if (saidaIntMin < inicioExp + tolerancia) saidaIntMin = inicioExp + tolerancia;
                        // Se voltou até 5 min depois, ajusta
                        if (retornoIntMin > fimExp - tolerancia) retornoIntMin = fimExp - tolerancia;
                    }

                    let duracaoIntervalo = retornoIntMin - saidaIntMin;
                    if (duracaoIntervalo > 0) totalMinutos -= duracaoIntervalo;
                }

                if (totalMinutos < 0) totalMinutos += 24 * 60;

                const horas = Math.floor(totalMinutos / 60);
                const minutos = totalMinutos % 60;

                return `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}`;
            }

            // 🔹 Percorre todos os leads e colaboradores
            arrCarregar.data.forEach(leadItem => {
                let lead = leadItem.lead;
                let colaboradores = leadItem.colaboradores;

                if (colaboradores.length > 0) {
                    html += `<h3 class="lead-title">Lead: ${lead.ds_lead}</h3>`;
                    html += `
                        <table class="table-pontos">
                            <thead>
                                <tr>
                                    <th>Colaborador</th>
                                    <th>Escala</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Horas Trabalhadas</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    colaboradores.forEach(colab => {
                        const nome = colab.ds_colaborador || "Sem Nome";
                        const entrada = colab.ponto?.hora_ponto_1 || '';
                        const saida = colab.ponto?.hora_ponto_2 || '';
                        const inicioExpediente = colab.hr_inicio_expediente;
                        const fimExpediente = colab.hr_termino_expediente;
                        const saidaIntervalo = colab.hr_saida_intervalo || '';
                        const retornoIntervalo = colab.hr_retorno_intervalo || '';

                        const horasTrabalhadas = calcularHorasTrabalhadas(
                            entrada,
                            saida,
                            inicioExpediente,
                            fimExpediente,
                            saidaIntervalo,
                            retornoIntervalo
                        );

                        let classeHoras = '';
                        if (entrada && inicioExpediente) {
                            const toMinutes = t => parseInt(t.split(':')[0]) * 60 + parseInt(t.split(':')[1]);
                            const atraso = Math.abs(toMinutes(entrada) - toMinutes(inicioExpediente));
                            const saidaDif = Math.abs(toMinutes(saida) - toMinutes(fimExpediente));
                            if (atraso > 5 || saidaDif > 5) {
                                classeHoras = 'fora-tolerancia';
                            }
                        }

                        html += `<tr>
                            <td>${nome}</td>
                            <td>${inicioExpediente} / ${fimExpediente}</td>
                            <td style="background-color:${getBackgroundColor(entrada, inicioExpediente)}">${entrada}</td>
                            <td style="background-color:${getBackgroundColor(saida, fimExpediente)}">${saida}</td>
                            <td class="${classeHoras}">${horasTrabalhadas}</td>
                        </tr>`;
                    });

                    html += `
                            </tbody>
                        </table>
                        <hr>
                    `;
                }
            });
        } else {
            html = "<p>Nenhum dado encontrado para os filtros selecionados.</p>";
        }
    } else {
        html = "<p>Erro ao carregar dados.</p>";
    }

    $("#datatable").html(html);
}





function getBackgroundColor(pontoHora, agendaHora) {
    if (!pontoHora) {
        // Ponto não veio, cor laranja
        return '#f99856';
    }
    if (!agendaHora) {
        // Horário da agenda não existe, deixa neutro (branco)
        return 'transparent';
    }

    // Compara strings "HH:mm", transformando em minutos para facilitar
    function toMinutes(t) {
        let parts = t.split(':');
        return parseInt(parts[0],10)*60 + parseInt(parts[1],10);
    }

    let pontoMin = toMinutes(pontoHora);
    let agendaMin = toMinutes(agendaHora);

    if (pontoMin <= agendaMin) {
        return '#92D050'; // verde
    } else {
        return '#FFFF00'; // amarelo
    }
}

$(document).ready(function(){    
    $(document).on('click', '#cmdAtualizar', fcAtualizar);

    var today = new Date();
    var dd = today.getDate();
    if(dd<10) {
        dd = '0'+dd
    } 

    // Obtém o mês atual (os meses são indexados a partir de 0, então adicionamos 1)
    const mes = today.getMonth() + 1;
    if(mes<=9){
        mesAtual = "0"+mes;
    }
    else{
        mesAtual = mes;
    }


    // Obtém o ano atual
    const anoAtual = today.getFullYear();
    
    $("#dia").val(dd);
    $("#mes").val(mesAtual);
    $("#ano").val(anoAtual);


    var html = "";
    for(i=parseInt(anoAtual);i >= parseInt(anoAtual-3);i--){
     
        html += "<option value='" + i + "'>" + i + "</option>";
    }
    $("select[name=ano]").html(html);
    
    $("#dt_emissao").text("Data : "+today);
    

      fcCarregarLeads();
      fcCarregarTurno();
      
      $("#leads_pk").change(function () {
        
        $(".chzn-select").chosen('destroy');
        fcCarregarColaborador();
        $(".chzn-select").chosen({ allow_single_deselect: true });

    });    
    fcCarregarGrid();

    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
    $(".chzn-select").chosen({ allow_single_deselect: true });
});





