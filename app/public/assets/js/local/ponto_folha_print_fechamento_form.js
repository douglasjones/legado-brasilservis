function fcCarregar() {
    let pk = $('#pk').val();
    let colaboradoresTurnos = {}; // ou use um Map se quiser mais controle
    if (pk > 0) {


        var logo = "";
        $.ajax({
                type: 'GET',
                url: '/api/conta/carregarLogo',
                data:[],
                complete: function (response) {
            
                  
                    var log = JSON.parse(response.responseText);

                    if(log.data[0] == '[]'){
                        logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                    }
                    else{
                    if(log.data[0]['tipo_conta_pk'] == 1){
                        if(log.data[0]['ds_img_cliente'] != null){
                            logo = log.data[0]['ds_img_cliente'];
                        
                        }
                        else{
                                logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                        }
                    }
                    else{
                        logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                    }
                }

                // Captura o valor do input e converte para array, separando por vírgula
                let arrColaboradores = $('#colaborador_pk').val().split(',');


                // Exibe o array no console (opcional)
                console.log('Colaboradores:', arrColaboradores.length);
                var v_html = "";
                document.getElementById("areaImpressao").innerHTML = "";
                // Percorre cada colaborador do array
                arrColaboradores.forEach(function(colaborador_pk) {
                    // Define o objeto de parâmetros para cada colaborador
                    let objParametros = {
                        "pk": $('#pk').val(),
                        "colaborador_pk": colaborador_pk.trim(), // Remove espaços extras se houver
                        "leads_pk": $('#leads_pk').val()
                    };
                    let arrCarregar = carregarController("ponto_folha", "listarRegistros", objParametros);
                
                        if (arrCarregar.status == true) {
                            if (typeof arrCarregar.data !== 'undefined') {
                                var t_hr_ini_exp = "";
                                var v_contador = "";
                                var v_ht_total = "";
                                var v_he_total = "";
                                var v_hf_total = "";
                                var v_he1_total = "";
                                var v_he2_total = "";
                                var v_an_total = 0;
                                var v_expediente_diario = "";
                                var v_ds_turno = "";
                                var turnos_pk = "";
                                var dias_trabalhados = 0;
                                var v_ponto_batidos = 0;
                                var v_dias_folga = 0;
                                if (arrCarregar.data[0]['total_ht'] != null) {
                                    v_ht_total = arrCarregar.data[0]['total_ht'];
                                }
                                if (arrCarregar.data[0]['total_he'] != null) {
                                    v_he_total = arrCarregar.data[0]['total_he'];
                                }
                                if (arrCarregar.data[0]['total_hf'] != null) {
                                    v_hf_total = arrCarregar.data[0]['total_hf'];
                                }
                                if (arrCarregar.data[0]['total_he50'] != null) {
                                    v_he1_total = arrCarregar.data[0]['total_he50'];
                                }
                                if (arrCarregar.data[0]['total_he100'] != null) {
                                    v_he2_total = arrCarregar.data[0]['total_he100'];
                                }
                               

                               
                                if (arrCarregar.data[0]['expediente_diario'] != null) {
                                    v_expediente_diario = arrCarregar.data[0]['expediente_diario'];
                                    //console.log(v_expediente_diario);
                                }
                                if (arrCarregar.data[0]['ds_turno'] != null) {
                                    v_ds_turno = arrCarregar.data[0]['ds_turno'];
                                    turnos_pk = arrCarregar.data[0]['turnos_pk'];

                                    // Adiciona o valor de turnos_pk ao objeto colaboradoresTurnos, usando o colaborador_pk como chave
                                    if (!colaboradoresTurnos[colaborador_pk.trim()]) {
                                        colaboradoresTurnos[colaborador_pk.trim()] = []; // Se a chave ainda não existir, inicialize com um array
                                    }

                                    colaboradoresTurnos[colaborador_pk.trim()].push(turnos_pk);
                                }
                                if (arrCarregar.data[0]['ic_folha_finalizada'] == "1") {
                                    $("#ic_folha_finalizada").prop('checked', true);
                                }
                                
                                $("#totalLinhas").val(0);

                               
                                $("#totalLinhas").val(arrCarregar.data[0].registrosfolha.length);
                                    

                                    v_html += "<style type='text/css'>";
                                    v_html += "@media print {";
                                    v_html += "    body {";
                                    v_html += "        width: 100%;";
                                    v_html += "        margin: 0;";
                                    v_html += "        padding: 0;";
                                    v_html += "    }";
                                    v_html += "    input {";
                                    v_html += "        border: none;"; // Remove bordas dos inputs
                                    v_html += "        background: transparent;"; // Remove o fundo do input
                                    v_html += "        font-size: 10px;"; // Ajuste de fonte do input
                                    v_html += "    }";
                                    v_html += "    #container {";
                                    v_html += "        page-break-inside: avoid;";  // Garante que o conteúdo dentro do container não quebre no meio
                                    v_html += "        margin: 0;";
                                    v_html += "        max-width: 100%;";
                                    v_html += "        word-wrap: break-word;";
                                    v_html += "    }";
                                    v_html += "    .page {";
                                    v_html += "        page-break-before: always;"; // Adiciona quebra de página antes de cada novo conteúdo
                                    v_html += "        padding: 0;";
                                    v_html += "        margin: 0;";
                                    v_html += "    }";
                                    v_html += "    table {";
                                    v_html += "        width: 100%;";
                                    v_html += "        border-collapse: collapse;";  // Remove bordas entre as células
                                    v_html += "    }";
                                    v_html += "    th, td {";
                                    v_html += "        padding: 5px;";  // Ajuste de padding para mais espaço
                                    v_html += "        text-align: center;";
                                    v_html += "        border: none;"; // Remove todas as bordas
                                    v_html += "    }";
                                    v_html += "    th {";
                                    v_html += "        background: #1A0F6B;";  // Cor de fundo para cabeçalhos
                                    v_html += "        color: white;";  // Cor do texto para cabeçalhos
                                    v_html += "    }";
                                    v_html += "    td {";
                                    v_html += "        font-size: 10px;"; // Ajuste de fonte
                                    v_html += "    }";
                                    v_html += "}"; 
                                    v_html += "</style>";
                                    v_html += "<div class='page'>";
                                    v_html += "<div class='row' id='container' >";
                                    v_html += "    <div class='col-md-12' align='Left' style='font-size:10'>";
                                    v_html += "        <h5>Folha de ponto</h5>";
                                    v_html += "        Período " + arrCarregar.data[0]['ds_periodo'];
                                    v_html += "    </div>";
                                    v_html += "    <div class='col-md-12' align='center' >";
                                    v_html +='                    <img  style="width: 100px;" src="'+logo+'" >';
                                    v_html += "    </div>";
                                    v_html += "<div class='col-md-12' align='Left' >";
                                    v_html += "<table id='folha_ponto' style=' width: 100%; heigth: 50%'>";
                                    v_html += " <tr style='height:5px;background: #1A0F6B'>";
                                    v_html += "     <th colspan='2' >";
                                    v_html += "         <label style='color:white;font-size:10 '>DADOS DO COLABORADOR</label>";
                                    v_html += "     </th>";
                                    v_html += "     <th colspan='2'>";
                                    v_html += "         <label style=' color: white;font-size:10  '>DADOS DO EMPREGADOR</label>";
                                    v_html += "     </th>";
                                    v_html += " </tr>";
                                    v_html += " <tr style='font-size:10'>";
                                    v_html += "     <td width='20%'>";
                                    v_html += "         <b>Nome:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td width: 25%  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_colaborador'];
                                    v_html += "     </td>";
                                    v_html += "     <td width: 25%>";
                                    v_html += "         <b>Razão Social:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td width: 25%  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_empresa'];
                                    v_html += "     </td>";
                                    v_html += " </tr>";
                                    v_html += " <tr style='font-size:10'>";
                                    v_html += "     <td >";
                                    v_html += "         <b>CPF:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_cpf'];
                                    v_html += "     </td>";
                                    v_html += "     <td>";
                                    v_html += "         <b>Endereço:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_endereco'];
                                    v_html += "     </td>";
                                    v_html += "    </tr>";
                                    v_html += " <tr style='font-size:10'>";
                                    v_html += "     <td >";
                                    v_html += "         <b>Cargo:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_cargo'];
                                    v_html += "     </td>";
                                    v_html += "     <td>";
                                    v_html += "         <b>CNPJ:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td  align='Left'>";
                                    v_html +=           arrCarregar.data[0]['ds_cnpj'];
                                    v_html += "     </td>";
                                    v_html += "         </tr>";
                                    v_html += "             <tr style='font-size:10'>";
                                    v_html += "                  <td>";
                                    v_html += "                     <b>Posto de Trabalho:</b>";
                                    v_html += "                  </td>";
                                    v_html += "                  <td  align='Left'>";
                                    v_html +=                        arrCarregar.data[0]['ds_posto_trabalho']
                                    v_html += "                  </td>";
                                    v_html += "                  <td>";
                                    v_html += "         <b>DT Admissão:</b>";
                                    v_html += "     </td>";
                                    v_html += "     <td align='Left'>";
                                    v_html +=          arrCarregar.data[0]['dt_admissao']
                                    v_html += "                  </td>";
                                    v_html += "             </tr>";
                                    v_html += "             <tr style='font-size:10'>";
                                    v_html += "                 <td>";
                                    v_html += "                     <b>Turno:</b>";
                                    v_html += "                 </td>";
                                    v_html += "                 <td colspan='3' width: 100%  align='Left'>";
                                    v_html += "                     Turno: " + arrCarregar.data[0]['ds_turno'] + "  -  Escala: " + arrCarregar.data[0]['ds_escala'] + "  -  Expediente: " + arrCarregar.data[0]['ds_hr_expediente'];
                                    v_html += "                 </td>";
                                    v_html += "             </tr>";
                                    v_html += "             <tr>";
                                    v_html += "&nbsp;";
                                    v_html += "             </tr>";
                                    v_html += "             <tr style='height:5px;background: #1A0F6B'>";
                                    v_html += "                 <th colspan='4'>";
                                    v_html += "                     &nbsp;<label style=' color: white;font-size:10'>REGISTROS</label>";
                                    v_html += "                 </th>";
                                    v_html += "             </tr>";
                                    v_html += "<p>";
                                    v_html += "         </table>";
                                    v_html += "     </div>";
                                    v_html += "</div>";
                                    v_html+="<div class='row'>";
                                    v_html+="<div class='table-container'>";
                                    v_html+="<table  style='width:100%;overflow-y: scroll;height: 20px;' id='tblResultado1' >";
                                    v_html+="<thead >";
                                    v_html += '<tr><th width="20" style="text-align: center">SEMANA</th><th width="15" style="text-align: center">DATA</th><th colspan="4" width="500" style="text-align: center">REGISTROS</th><th align="center" style="text-align: center">H.T</th><th align="center" style="text-align: center">H.E</th><th align="center" style="text-align: center">H.F</th><th align="center" style="text-align: center">SITUAÇÃO</th><th align="center" style="text-align: center">H.E1</th><th align="center" style="text-align: center">H.E2</th>';
                                    v_html += '<th align="center" style="text-align: center">A.N</th>';
                                    v_html += '<th align="center" style="text-align: center">OBS</th></tr>';
                                    v_html+="                    </thead>";
                                    v_html+="                    <tbody >";
                                    
                                    if (typeof arrCarregar.data[0].registrosfolha[0].ponto_folha_registro_pk !== 'undefined') {
                                        for (i = 0; i < arrCarregar.data[0].registrosfolha.length; i++) {
                    
                                            var v_pk = "";
                                            var v_dt = "";
                                            var hr_ini_expediente = "";
                                            var hr_ini_intervalo = "";
                                            var hr_fim_intervalo = "";
                                            var hr_fim_expediente = "";
                                            var hr_extra50 = "";
                                            var hr_extra100 = "";
                                            var hr_adicional_noturno = "";
                                          
                                            var ds_situacao = "";
                                            var arrApontamento = "";
                                            var arrPontos = "";
                                            var tipo_apontamento = 0;
    
                                            v_pk = arrCarregar.data[0].registrosfolha[i].ponto_folha_registro_pk;
                                            v_dt = arrCarregar.data[0].registrosfolha[i].dt_registro_ponto;
                                            arrApontamento = arrCarregar.data[0].registrosfolha[i].arrApontamento;
                                            arrPontos = arrCarregar.data[0].registrosfolha[i].arrPontos;
    
                                            hr_ini_expediente = arrCarregar.data[0].registrosfolha[i].hr_ini_expediente;
                                            hr_ini_expediente = hr_ini_expediente == null ? '' : hr_ini_expediente;
    
                                            hr_ini_intervalo = arrCarregar.data[0].registrosfolha[i].hr_ini_intervalo;
                                            hr_ini_intervalo = hr_ini_intervalo == null ? '' : hr_ini_intervalo;
    
                                            hr_fim_intervalo = arrCarregar.data[0].registrosfolha[i].hr_fim_intervalo;
                                            hr_fim_intervalo = hr_fim_intervalo == null ? '' : hr_fim_intervalo;
    
                                            hr_fim_expediente = arrCarregar.data[0].registrosfolha[i].hr_fim_expediente;
                                            hr_fim_expediente = hr_fim_expediente == null ? '' : hr_fim_expediente;                
    
                                            hr_trabalhadas = arrCarregar.data[0].registrosfolha[i].hr_trabalhadas;
                                            hr_trabalhadas = hr_trabalhadas == null ? '' : hr_trabalhadas;
    
                                            hr_excedentes = arrCarregar.data[0].registrosfolha[i].hr_excedentes;
                                            hr_excedentes = hr_excedentes == null ? '' : hr_excedentes;
    
                                            hr_faltantes = arrCarregar.data[0].registrosfolha[i].hr_faltantes;
                                            hr_faltantes = hr_faltantes == null ? '' : hr_faltantes;
    
                                            hr_extra50 = arrCarregar.data[0].registrosfolha[i].hr_extra50;
                                            hr_extra50 = hr_extra50 == null ? '' : hr_extra50;
    
                                            hr_extra100 = arrCarregar.data[0].registrosfolha[i].hr_extra100;
                                            hr_extra100 = hr_extra100 == null ? '' : hr_extra100;
    
                                            hr_adicional_noturno = arrCarregar.data[0].registrosfolha[i].hr_adicional_noturno;
                                            hr_adicional_noturno = hr_adicional_noturno == null ? '' : hr_adicional_noturno;
    
                                            hr_adicional_noturno = arrCarregar.data[0].registrosfolha[i].hr_adicional_noturno;
                                            hr_adicional_noturno = hr_adicional_noturno == null ? '' : hr_adicional_noturno;
    
                                            v_dia_semana = arrCarregar.data[0].registrosfolha[i].dia_da_semana;
                                            v_dia_semana = v_dia_semana == null ? '' : v_dia_semana;
    
                                            if(hr_ini_expediente!=""){
                                                v_ponto_batidos++;
                                            }
    
                                            var  tipo_ponto_pk = arrCarregar.data[0].registrosfolha[i].tipo_ponto_pk;
                                            if (tipo_ponto_pk == 1) {
                                                ds_situacao = "Expediente";
                                            } else if (tipo_ponto_pk == 5) {
                                                v_dias_folga++;
                                                ds_situacao = "Folga";
                                            } else if (tipo_ponto_pk == 10) {
                                                ds_situacao = " ";//FALTA
                                            } else if (tipo_ponto_pk == 11) {
                                                ds_situacao = "Abonada";
                                            }else if (tipo_ponto_pk == 12) {
                                                ds_situacao = "Férias";
                                            }else if (tipo_ponto_pk == 15) {
                                                ds_situacao = "Afastamento";
                                            }else if (tipo_ponto_pk == 16) {
                                                ds_situacao = "Atestado";
                                            }else if (tipo_ponto_pk == 17) {
                                                ds_situacao = "Advertencia";
                                            }else if (tipo_ponto_pk == 18) {
                                                ds_situacao = "Declaração da defesa civil";
                                            }else if (tipo_ponto_pk == 19) {
                                                ds_situacao = "Demissão";
                                            }else if (tipo_ponto_pk == 20) {
                                                ds_situacao = "Folga compensatória";
                                            }else if (tipo_ponto_pk == 21) {
                                                ds_situacao = "Folga de feriado";
                                            }else if (tipo_ponto_pk == 22) {
                                                ds_situacao = "Justa causa";
                                            }else if (tipo_ponto_pk == 23) {
                                                ds_situacao = "Recisão indireta";
                                            }else if (tipo_ponto_pk == 24) {
                                                ds_situacao = "Suspensão";
                                            }else if (tipo_ponto_pk == 25) {
                                                ds_situacao = "Troca Folga";
                                            }
                                            else if (tipo_ponto_pk == 37) {
                                                ds_situacao = "Atestado de horas";
                                            }
                                            else if (tipo_ponto_pk == 33) {
                                                ds_situacao = "Declaração de horas abonar";
                                            }
                                            else if (tipo_ponto_pk == 36) {
                                                ds_situacao = "Audiência";
                                            }
                                            
                                            var obs = arrCarregar.data[0].registrosfolha[i].obs;
                                            obs = obs == null ? '' : obs;
                                            
                                            var ic_status = arrCarregar.data[0].registrosfolha[i].ic_status;
                                            ic_status = ic_status == null ? '' : ic_status;
                                            

                                            v_an_total += hmToMins(hr_adicional_noturno);
    
    
                                            
                                            
                                            
    
                                            
    
    
                                            
    
    
                                            //VERIFICA SE EXISTE ALGUM DADO EM FOLHA.CASO CONTRÁRIO PEGA AS INFORMAÇÕES DO PONTO
                                            if(hr_ini_expediente==""){
                                                hr_ini_expediente = arrPontos[0]['ponto_ini_expediente'].substring(0, 5)
                                                
                                            }
                                            if(hr_ini_intervalo==""){
                                                hr_ini_intervalo = arrPontos[0]['ponto_ini_intervalo'].substring(0, 5)
                                            }
                                            if(hr_fim_intervalo==""){
                                                hr_fim_intervalo = arrPontos[0]['ponto_term_intervalo'].substring(0, 5)
                                            }
                                            if(hr_fim_expediente==""){
                                                hr_fim_expediente = arrPontos[0]['ponto_term_expediente'].substring(0, 5)
                                            }
    
                                            
                                            if(arrApontamento[0]['arrApontamento'].length > 0){
                                                
                                                tipo_apontamento = arrApontamento[0]['tipo_apontamento_pk'];
                                                optionCombolist = 1;
                                                if(tipo_apontamento==1){
                                                    
                                                    for(a = 0;a<arrApontamento[0]['arrApontamento'].length;a++){
                                                        if(arrApontamento[0]['arrApontamento'][a]['tipo_ponto_pk']==1){
                                                            corEntrada = 1;
                                                            hr_ini_expediente = arrApontamento[0]['arrApontamento'][a]['hr_ponto'].substring(0, 5);
                                                        }
                                                        else if(arrApontamento[0]['arrApontamento'][a]['tipo_ponto_pk']==3){
                                                            corIniIntervalo=1;
                                                            hr_ini_intervalo = arrApontamento[0]['arrApontamento'][a]['hr_ponto'].substring(0, 5);
                                                        }
                                                        else if(arrApontamento[0]['arrApontamento'][a]['tipo_ponto_pk']==4){
                                                            corFimIntervalo =1;
                                                            hr_fim_intervalo = arrApontamento[0]['arrApontamento'][a]['hr_ponto'].substring(0, 5);
                                                        }
                                                        else if(arrApontamento[0]['arrApontamento'][a]['tipo_ponto_pk']==2){
                                                            corSaida = 1;
                                                            hr_fim_expediente = arrApontamento[0]['arrApontamento'][a]['hr_ponto'].substring(0, 5);
                                                        }
                                                    }
                                                }
    
                                                
                                                
    
                                                if (tipo_apontamento == 1) {
                                                    ds_situacao = "Expediente";
                                                } else if (tipo_apontamento == 5 || tipo_apontamento == 3) {
                                                    v_dias_folga++;
                                                    ds_situacao = "Folga";
                                                    hr_ini_expediente = "";
                                                    hr_ini_intervalo = "";
                                                    hr_fim_intervalo = "";
                                                    hr_fim_expediente = "";
                                                    
                                                } else if (tipo_apontamento == 10 || tipo_apontamento == 2) {
                                                    ds_situacao = "Falta";
                                                    hr_ini_expediente = "";
                                                    hr_ini_intervalo = "";
                                                    hr_fim_intervalo = "";
                                                    hr_fim_expediente = "";
                                                } else if (tipo_apontamento == 11) {
                                                    ds_situacao = "Abonada";
                                                }else if (tipo_apontamento == 12 || tipo_apontamento == 6) {
                                                    ds_situacao = "Férias";
                                                }else if (tipo_apontamento == 15) {
                                                    ds_situacao = "Afastamento";
                                                }else if (tipo_apontamento == 16) {
                                                    ds_situacao = "Atestado";
                                                }else if (tipo_apontamento == 17) {
                                                    ds_situacao = "Advertencia";
                                                }else if (tipo_apontamento == 18) {
                                                    ds_situacao = "Declaração da defesa civil";
                                                }else if (tipo_apontamento == 19) {
                                                    ds_situacao = "Demissão";
                                                }else if (tipo_apontamento == 20) {
                                                    ds_situacao = "Folga compensatória";
                                                }else if (tipo_apontamento == 21) {
                                                    ds_situacao = "Folga de feriado";
                                                    hr_ini_expediente = "";
                                                    hr_ini_intervalo = "";
                                                    hr_fim_intervalo = "";
                                                    hr_fim_expediente = "";
                                                }else if (tipo_apontamento == 22) {
                                                    ds_situacao = "Justa causa";
                                                }else if (tipo_apontamento == 23) {
                                                    ds_situacao = "Recisão indireta";
                                                }else if (tipo_apontamento == 24 || tipo_apontamento == 8) {
                                                    ds_situacao = "Suspensão";
                                                    
                                                }else if (tipo_apontamento == 25) {
                                                    ds_situacao = "Troca Folga";
                                                }
                                                else if (tipo_apontamento == 37) {
                                                    ds_situacao = "Atestado de horas";
                                                }
                                                else if (tipo_apontamento == 33) {
                                                    ds_situacao = "Declaração de horas abonar";
                                                }
                                                else if (tipo_apontamento == 36) {
                                                    ds_situacao = "Audiência";
                                                }
                                            }
    
                                            if(arrCarregar.data[0].registrosfolha[i].situacao!=null){
                                                ds_situacao = arrCarregar.data[0].registrosfolha[i].situacao;
                                                tipo_apontamento = arrCarregar.data[0].registrosfolha[i].tipo_apontamento_pk;
                                            }
    
                                            
                                            if(ds_situacao=="Atestado de horas"){
                                                hr_faltantes="00:00";
                                            }
                                            if(ds_situacao=="Abonada"){
                                                hr_faltantes="00:00";
                                            }
    
                                            if(hr_ini_expediente!=""){
                                                dias_trabalhados++;
                                            }                 
                                            
                                            
                                            v_html += "<tr>";
                                            v_html += "<input type='hidden' id='ponto_folha_registros_pk" + i + "' value='" + v_pk + "'>";
                                            v_html += "<input type='hidden' id='expediente_diario" + i + ""+colaborador_pk.trim()+"' value='" + v_expediente_diario + "'>";
                            
                                            v_html += "<input type='hidden' id='ds_turno" + i + "' value='" + v_ds_turno + "'>";
                                            v_html += "         <input type='hidden' id='dt_dia_semana" + i + "' size='3' value='" + v_dia_semana + "'>";
                            
                                        
                                            v_html += "   <td width='25'>";
                                            v_html +=           v_dia_semana;
                                            v_html += "   </td>";
                                            v_html += "   <td width='25'>";
                                            v_html += v_dt;
                                            v_html += "   <input type='hidden' id='dt_hora_ponto" + i + "' size='3' value='" + v_dt + "'>";
                                            v_html += "   </td>";
                                            v_html += "   <td width='25' >";
                                            v_html += "<input type='hidden' "+ (ic_status == 1 ? " " : "") +"  id='hr_ini_expediente" + i + ""+colaborador_pk.trim()+"' size='3' value='" + hr_ini_expediente + "' onkeypress='mascara(this,horamask)'>"+hr_ini_expediente;
                                            v_html += "   </td>";
                                            v_html += "   <td width='25' >";
                                            v_html += "<input type='hidden'  "+ (ic_status == 1 ? " " : "") +" id='hr_ini_intervalo" + i + ""+colaborador_pk.trim()+"' size='3' value='" + hr_ini_intervalo + "' onkeypress='mascara(this,horamask)'>"+hr_ini_intervalo;
                                            v_html += "    </td>";
                                            v_html += "    <td width='25' >";
                                            v_html += "<input type='hidden'  "+ (ic_status == 1 ? " " : "") +" id='hr_fim_intervalo" + i + ""+colaborador_pk.trim()+"' size='3' value='" + hr_fim_intervalo + "' onkeypress='mascara(this,horamask)'>"+hr_fim_intervalo;
                                            v_html += "    </td>";
                            
                                            v_html += "    <td width='25'>";
                                            v_html += "<input type='hidden' "+ (ic_status == 1 ? " " : "") +" id='hr_fim_expediente" + i + ""+colaborador_pk.trim()+"' size='3' value='" + hr_fim_expediente + "' onkeypress='mascara(this,horamask)'>"+hr_fim_expediente;
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_trabalhadas" + i + ""+colaborador_pk.trim()+"' size='3'  onkeypress='mascara(this,horamask)'>"+hr_trabalhadas+"</span>";
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_excedentes" + i + ""+colaborador_pk.trim()+"' size='3' onkeypress='mascara(this,horamask)'>"+hr_excedentes;
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_faltantes" + i + ""+colaborador_pk.trim()+"' size='3' onkeypress='mascara(this,horamask)'>"+hr_faltantes;
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<font size='1,7'><span id='ds_situacao" + i + ""+colaborador_pk.trim()+"'>" + ds_situacao + "</span></font>";
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_extra50" + i + "' size='3' value='" + hr_extra50 + "' onkeypress='mascara(this,horamask)'>";
                                            v_html += "    </td>";
                                            v_html += "    <th style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_extra100" + i + "' size='3' value='" + hr_extra100 + "'>";
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html += "<span "+ (ic_status == 1 ? " " : "") +" id='hr_adicional_noturno" + i + ""+colaborador_pk.trim()+"' size='3' onkeypress='mascara(this,horamask)'>"+hr_adicional_noturno;
                                            v_html += "    </td>";
                                            v_html += "    <td style=' text-align: center'>";
                                            v_html +=       obs;
                                            v_html += "    </td>";
                                            v_html += "</tr>";
                                            
                                        }
                                    }
                                    
                                    v_html += "<tr style='background: #1A0F6B'>";
                                    v_html += "    <td  width='15%'  colspan='16'>";
                                    v_html += "      <label style=' color: white '><b>TOTAL DE HORAS</b></label>";
                                    v_html += "    </td>";
                                    v_html += "</tr>";
                                    v_html += "<tr >";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center' colspan=4>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <b>D.T</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <b>H.T</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <b>H.E</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <b>H.F</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <b>H.E1</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td style=' text-align: center'>";
                                    v_html += "   <b>H.E2</b>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "   <b>A.N</b>";
                                    v_html += "    </td>";
                                    v_html += "</tr>";
                                    v_html += "<tr >";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center' colspan=4>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html +=           dias_trabalhados;
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <span id='ht_total"+colaborador_pk.trim()+"' name='ht_total' size='3' maxlength='8'  value='" + v_ht_total + "'>"+v_ht_total;
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <span id='he_total"+colaborador_pk.trim()+"' name='he_total' size='3' maxlength='6' value='" + v_he_total + "' onkeypress='mascara(this,horamask)'>"+v_he_total;
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <span id='hf_total"+colaborador_pk.trim()+"' name='hf_total' size='3' maxlength='6' value='" + v_hf_total + "'  onkeypress='mascara(this,horamask)'>"+v_hf_total;
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "      &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <span id='he1_total' name='he1_total' size='3' maxlength='6' onkeypress='mascara(this,horamask)'>";
                                    v_html += "    </td>";
                                    v_html += "    <td style=' text-align: center'>";
                                    v_html += "       <span id='he2_total' name='he2_total' size='3' maxlength='6' value='" + v_he2_total + "' onkeypress='mascara(this,horamask)'>";
                                    v_html += "    </td>";
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "       <span id='an_total"+colaborador_pk.trim()+"' name='an_total"+colaborador_pk.trim()+"'  maxlength='6' value='" + v_an_total + "' onkeypress='mascara(this,horamask)'>"+converHrs(v_an_total);
                                    v_html += "    </td>";
                                
                                    v_html += "    <td  style=' text-align: center'>";
                                    v_html += "   &nbsp;";
                                    v_html += "    </td>";
                                    v_html += "</tr>";
                                    v_html += "<p>";
                                    v_html += "            <tr style=' background: #f5f5f5'>";
                                    v_html += "                         <td colspan='14'>";
                                    v_html += "                             <table style=' width: 100%' >";
                                    v_html += "                                 <tr>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         H.T: Horas Trabalhasdas";
                                    v_html += "                                     </td>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         H.E: Horas Excedentes";
                                    v_html += "                                     </td>";
                                    v_html += "                                 </tr>";
                                    v_html += "                                 <tr>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         H.F: Horas Faltantes";
                                    v_html += "                                     </td>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         H.E1: Horas Extra Fase 1 (50%)";
                                    v_html += "                                     </td>";
                                    v_html += "                                 </tr>";
                                    v_html += "                                 <tr>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         H.E2: Horas Extra Fase 2 (100%)";
                                    v_html += "                                     </td>";
                                    v_html += "                                     <td width='50%' style='font-size:10'>";
                                    v_html += "                                         A.N:  Adicional Noturno";
                                    v_html += "                                     </td>";
                                    v_html += "                                 </tr>";
                                    v_html += "                             </table>";
                                    v_html += "                         </td>";
                                    v_html += "                    </tr>";
                                    v_html += "<p>";
                                    v_html += "                    <tr>";
                                    v_html += "                         <td colspan='13'>";
                                    v_html += "                             <table style=' width: 100%' >";
                                    v_html += "                             <tr>";
                                    v_html += "                             <td width='50%' style=' text-align: center'>";
                                    v_html += "                                 __________________________________<br>";
                                    v_html += "                                COLABORADOR";
                                    v_html += "                             </td>";
                                    v_html += "                             <td width='50%' style=' text-align: center'>";
                                    v_html += "                                 __________________________________<br>";
                                    v_html += "                               EMPREGADOR";
                                    v_html += "                          </td>";
                                    v_html += "                     </tr>";
                                    v_html += "           </table>";
                                    v_html += "<p>";
                                    v_html += "         </td>";
                                    v_html += "     </tr>";
                                    v_html += " </tfoot>";
                                    v_html += "</table>";
                                    v_html +='<div class="row">';
                                    v_html +='                <div class="col" >';
                                    v_html +='                    <img  src="/assets/img/rodapeFolhaPonto.png" >';
                                    v_html +='                </div>';
                                    v_html +='            </div>';
                                    v_html += "</div>";
                                    v_html += "</div> ";
                                    v_html += "</div> ";
                                    v_html += "</div> ";

                                    //$("#areaImpressao").html(v_html);
                                    let objParametros = {
                                        "dt_inicio": $('#dt_periodo_ini').val(),
                                        "dt_fim": $('#dt_periodo_fim').val(),
                                        "colaborador_pk": colaborador_pk.trim(),
                                        "leads_pk": $('#leads_pk').val()
                                    };

                                    let arrCarregarFechamento = carregarController("ponto", "pegarDadosFechamento", objParametros);
                                    console.log(arrCarregarFechamento);

                                    // ✅ garante que o retorno é um array válido
                                    let data = Array.isArray(arrCarregarFechamento.data)
                                        ? arrCarregarFechamento.data
                                        : (Array.isArray(arrCarregarFechamento) ? arrCarregarFechamento : []);

                                    v_html += '<div class="row">';
                                    v_html += '    <div class="col-md-12">';

                                    // CSS inline
                                    v_html += `
                                        <style>
                                            .lead-title { font-size: 1.3rem; font-weight: 600; margin-top: 2rem; color: #333; }
                                            .table-wrapper { width: 100%; overflow-x: auto; margin-bottom: 2rem; }
                                            .table-pontos { width: 1800px; border-collapse: collapse; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05); font-family: Arial, sans-serif; }
                                            .table-pontos th, .table-pontos td { padding: 0.75rem 1rem; text-align: center; border: 1px solid #ccc; white-space: nowrap; }
                                            .table-pontos thead { background-color: #f0f0f0; }
                                            .table-pontos tbody tr:nth-child(even) { background-color: #fafafa; }
                                            .table-pontos td:first-child { text-align: left; font-weight: 500; }
                                            .fora-tolerancia { color: #d9534f; font-weight: bold; }
                                            hr { border: none; border-top: 1px solid #ccc; margin: 2rem 0; }
                                        </style>
                                    `;

                                    v_html += '<div class="row">';
                                    v_html += '  <div class="col-md-12">';

                                    data.forEach(item => {
                                        let lead = item.lead;
                                        let colaboradores = item.colaboradores || [];

                                        // título do lead
                                        v_html += `
                                            <div class="table-wrapper">
                                                <table class="table-pontos">
                                                    <thead>
                                                        <tr>
                                                            <th>R.E</th>
                                                            <th>Colaborador</th>
                                                            <th>Data Admissão</th>
                                                            <th>Turno</th>
                                                            <th>Dias de Escala</th>
                                                            <th>Dias Trabalhados</th>
                                                            <th>Salário</th>
                                                            <th>VT Valor</th>
                                                            <th>VT Não Utilizados</th>
                                                            <th>Adic Noturno</th>
                                                            <th>Insal 20%</th>
                                                            <th>Insal 40%</th>
                                                            <th>H.E Total</th>
                                                            <th>Faltas Total</th>
                                                            <th>DSR</th>
                                                            <th>Atestado Total</th>
                                                            <th>Atestado Dias</th>
                                                            <th>Atrasos</th>
                                                            <th>Assuidade</th>
                                                            <th>Cesta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                        `;

                                        colaboradores.forEach(colab => {
                                            const f = colab.fechamento || {};
                                            const classeAtraso = f.atrasos > 0 ? 'fora-tolerancia' : '';

                                            v_html += `
                                                <tr>
                                                    <td>${colab.ds_re || ''}</td>
                                                    <td>${colab.ds_colaborador || ''}</td>
                                                    <td>${colab.dt_admissao || ''}</td>
                                                    <td>${colab.ds_turno || '--'}</td>
                                                    <td>${f.dias_escala || 0}</td>
                                                    <td>${f.dias_trabalhados || 0}</td>
                                                    <td>${colab.vl_salario?.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) || '0,00'}</td>
                                                    <td>${f.vt_valor || '0,00'}</td>
                                                    <td>${f.vt_nao_utilizados || '0,00'}</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                    <td>${v_he_total || '00:00'}</td>
                                                    <td>${f.faltas_total || 0}</td>
                                                    <td>--</td>
                                                    <td>${f.atestado_total || 0}</td>
                                                    <td>${f.dias_atestado || 0}</td>
                                                    <td class="${classeAtraso}">${f.atrasos || 0}</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                </tr>
                                            `;
                                        });

                                        v_html += `
                                                    </tbody>
                                                </table>
                                            </div>
                                        `;
                                    });

                                    v_html += '  </div>';
                                    v_html += '</div>';

                                    document.getElementById("areaImpressao").innerHTML = v_html;

                                    
                                    if(arrColaboradores.length==1){
                                       setTimeout(() => {
                                            utilsJS.loaded();
                                       }, 3000);
                                        
                                    }
                                    
                            }
                            
                    } else {
                        alert("Erro ao carregar os dados.");
                    }
                });              
                
            }
            
        });
        setTimeout(() => {
        }, 15000);
        utilsJS.loaded();
    }
    
}

// Função que converte horas no formato "HH:MM" para minutos totais
function hmToMins(str) {
    if (typeof str === 'string' && str.includes(':')) {
        const [hh, mm] = str.split(':').map(nr => parseInt(nr, 10) || 0);
        return hh * 60 + mm;
    }
    return 0;
}

// Função que converte minutos totais de volta para o formato "HH:MM"
function converHrs(minutos) {
    let horas = Math.floor(Math.abs(minutos) / 60);
    let min = Math.abs(minutos) % 60;

    // Formata horas e minutos para ter sempre 2 dígitos
    horas = horas < 10 ? '0' + horas : horas;
    min = min < 10 ? '0' + min : min;

    return `${horas}:${min}`;
}


function fcCancelar() {
    let v_pk = $('#pk').val()
    var objParametros = {
        "pk":v_pk  
    };
    sendPost('ponto_folha','colaboradoresCad',objParametros)
    
}
function printDiv() {

    var divToPrint = document.getElementById('areaImpressao');

    var newWin = window.open('', 'Print-Window');
    newWin.document.write('<title>Tela Impressão</title>');
    newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');
    newWin.document.close();
    setTimeout(function () { newWin.close(); }, 1000);
}

$(document).ready(function () {
    utilsJS.loading("Carregando as informações !");
    $(document).on('click', '#cmdVoltar', fcCancelar);
    $(document).on('click', '#cmdImprimirModal', printDiv);

    fcCarregar();
});
