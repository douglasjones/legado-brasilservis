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
                                    v_html += '<tr><th width="20" style="text-align: center">SEMANA</th><th width="15" style="text-align: center">DATA</th><th colspan="4" width="500" style="text-align: center">REGISTROS</th><th align="center" style="text-align: center">SITUAÇÃO</th><th align="center" style="text-align: center">OBS</th></tr>';
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
                                                                                        v_html += "<font size='1,7'><span id='ds_situacao" + i + ""+colaborador_pk.trim()+"'>" + ds_situacao + "</span></font>";
                                                                                        v_html += "    </td>";
                                                                                        v_html += "    <td style=' text-align: center'>";
                                                                                        v_html +=       obs;
                                                                                        v_html += "    </td>";
                                            v_html += "</tr>";
                                            
                                        }
                                    }
                                    
                                    v_html += "<p>";
                                    v_html += "<p>";
                                    v_html += "                    <tr>";
                                    v_html += "             <td colspan='6'>";
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

                                    document.getElementById("areaImpressao").innerHTML = v_html;
                                    
                                    if(arrColaboradores.length==1){
                                       setTimeout(() => {
                                            //PreencherAutomatico(turnos_pk,colaborador_pk.trim());
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
            //PreencherAutomaticoAll(colaboradoresTurnos);
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

// Função principal que preenche os campos automaticamente
function PreencherAutomatico(turnos_pk, colaborador_pk) {
    try {
        
        var v_li = $("#totalLinhas").val(); 
    
        for (l = 0; l < v_li; l++) {
            var hr_ini_expediente = $("#hr_ini_expediente" + l + colaborador_pk).val() || "00:00";
            var hr_fim_expediente = $("#hr_fim_expediente" + l + colaborador_pk).val() || "00:00";
            var expediente_diario = $("#expediente_diario" + l + colaborador_pk).val() || "00:00";
           
            var hr_ini_intervalo = $("#hr_ini_intervalo" + l + colaborador_pk).val() || "0";
            var hr_fim_intervalo = $("#hr_fim_intervalo" + l+ colaborador_pk).val() || "0";
            var hr_excedentes = "00:00";
            var hr_faltantes = "00:00";
            var hr_adicional_noturno = "00:00";
    
            if (hr_ini_expediente != "00:00" && hr_fim_expediente != "00:00") {
    
                hr_ini_expediente = hmToMins(hr_ini_expediente);
                hr_fim_expediente = hmToMins(hr_fim_expediente);
                expediente_diario = hmToMins(expediente_diario);
    
                // Converter intervalo apenas se ambos forem preenchidos
                hr_ini_intervalo = hr_ini_intervalo != "00:00" ? hmToMins(hr_ini_intervalo) : hmToMins("00:01");
                hr_fim_intervalo = hr_fim_intervalo != "00:00" ? hmToMins(hr_fim_intervalo) : hmToMins("00:01");
    
                var hr_trabalhadas = 0;
    
                // Se ambos os horários do intervalo estiverem preenchidos, calcula normalmente
                if (hr_ini_intervalo > 0 && hr_fim_intervalo > 0) {
                    var hr_trabalhadas_manha = hr_ini_intervalo - hr_ini_expediente;
                    var hr_trabalhadas_tarde = hr_fim_expediente - hr_fim_intervalo;
    
                    hr_trabalhadas = hr_trabalhadas_manha + hr_trabalhadas_tarde;
                } else {
                    // Ignora o intervalo e calcula direto
                    hr_trabalhadas = hr_fim_expediente - hr_ini_expediente;
                }
    
                // Corrigir para expediente que ultrapassa meia-noite
                if (hr_trabalhadas < 0) {
                    hr_trabalhadas += 24 * 60;
                }
    
                // Margem de tolerância em minutos
                var margemTolerancia = 5;
    
                // Comparar com o expediente diário considerando a margem de tolerância
                if (expediente_diario > hr_trabalhadas + margemTolerancia) {
                    hr_faltantes = expediente_diario - hr_trabalhadas;
                    hr_faltantes = converHrs(hr_faltantes);
                } else if (expediente_diario < hr_trabalhadas - margemTolerancia) {
                    hr_excedentes = hr_trabalhadas - expediente_diario;
                    hr_excedentes = converHrs(hr_excedentes);
                } else {
                    hr_faltantes = "00:00";
                    hr_excedentes = "00:00";
                }
    
                // Adicional noturno para turnos específicos
                if (turnos_pk == 3) {
                    var adicionaisNoturnos = Math.floor(hr_trabalhadas / 60);
                    var minutosAdicionalNoturno = adicionaisNoturnos * 60;
                    hr_adicional_noturno = converHrs(minutosAdicionalNoturno);
                    $("#hr_adicional_noturno" + l+ colaborador_pk).text(hr_adicional_noturno);
                }
    
                hr_trabalhadas = converHrs(hr_trabalhadas);
                
                if($("#hr_faltantes" + l+ colaborador_pk).text()==""){
                    // Atualizar os valores no formulário
                    $("#hr_faltantes" + l+ colaborador_pk).text(hr_faltantes);
                }
                

                if($("#hr_excedentes" + l+ colaborador_pk).text()==""){
                    $("#hr_excedentes" + l+ colaborador_pk).text(hr_excedentes);
                }
               


                if($("#hr_trabalhadas" + l+ colaborador_pk).text()==""){
                    $("#hr_trabalhadas" + l+ colaborador_pk).text(hr_trabalhadas);
                }
                
                
            } else {
                $("#hr_excedentes" + l+ colaborador_pk).text("");
                $("#hr_faltantes" + l+ colaborador_pk).text("");
                $("#hr_trabalhadas" + l+ colaborador_pk).text("");
                $("#hr_adicional_noturno" + l+ colaborador_pk).text("");
            }
        }
     

    } catch (e) {
        alert(e);
    }
}




function PreencherAutomaticoAll(turnos_pk) {
    try {
        
        console.log(turnos_pk)
        // Captura o valor do input e converte para array, separando por vírgula
        let arrColaboradores = $('#colaborador_pk').val().split(',');
        // Percorre cada colaborador do array
        arrColaboradores.forEach(function(colaborador_pk) {
            for (l = 0; l < 31; l++) {
                
                if (typeof $("#hr_ini_expediente" + l + colaborador_pk.trim()).val() !== 'undefined') {
                    var hr_ini_expediente = $("#hr_ini_expediente" + l + colaborador_pk.trim()).val()|| "00:00";
                    var hr_fim_expediente = $("#hr_fim_expediente" + l + colaborador_pk.trim()).val()|| "00:00";
                    var expediente_diario = $("#expediente_diario" + l + colaborador_pk.trim()).val(); // Quantas horas a pessoa deveria trabalhar no dia
                    var hr_ini_intervalo = $("#hr_ini_intervalo" + l + colaborador_pk.trim()).val()|| "0";
                    var hr_fim_intervalo = $("#hr_fim_intervalo" + l + colaborador_pk.trim()).val()|| "0";
    
                    var hr_excedentes = "00:00";
                    var hr_faltantes = "00:00";
                    var hr_adicional_noturno = "00:00";
    
                    if (hr_ini_expediente != "00:00" && hr_fim_expediente != "00:00") {
    
                        hr_ini_expediente = hmToMins(hr_ini_expediente);
                        hr_fim_expediente = hmToMins(hr_fim_expediente);
                        hr_ini_intervalo = hr_ini_intervalo != '00:00' ? hmToMins(hr_ini_intervalo) : hmToMins("00:01");
                        hr_fim_intervalo = hr_fim_intervalo != '00:00' ? hmToMins(hr_fim_intervalo) : hmToMins("00:01");
                        expediente_diario = hmToMins(expediente_diario);
    
                        // Cálculo do intervalo
                        var hr_trabalhadas = 0;
    
                        // Se ambos os horários do intervalo estiverem preenchidos, calcula normalmente
                        if (hr_ini_intervalo > 0 && hr_fim_intervalo > 0) {
                            var hr_trabalhadas_manha = hr_ini_intervalo - hr_ini_expediente;
                            var hr_trabalhadas_tarde = hr_fim_expediente - hr_fim_intervalo;
            
                            hr_trabalhadas = hr_trabalhadas_manha + hr_trabalhadas_tarde;
                        } else {
                            // Ignora o intervalo e calcula direto
                            hr_trabalhadas = hr_fim_expediente - hr_ini_expediente;
                        }
            
                        // Corrigir para expediente que ultrapassa meia-noite
                        if (hr_trabalhadas < 0) {
                            hr_trabalhadas += 24 * 60;
                        }
            
                        // Margem de tolerância em minutos
                        var margemTolerancia = 5;
            
                        // Comparar com o expediente diário considerando a margem de tolerância
                        if (expediente_diario > hr_trabalhadas + margemTolerancia) {
                            hr_faltantes = expediente_diario - hr_trabalhadas;
                            hr_faltantes = converHrs(hr_faltantes);
                        } else if (expediente_diario < hr_trabalhadas - margemTolerancia) {
                            hr_excedentes = hr_trabalhadas - expediente_diario;
                            hr_excedentes = converHrs(hr_excedentes);
                        } else {
                            hr_faltantes = "00:00";
                            hr_excedentes = "00:00";
                        }
        
                
                        if (turnos_pk[colaborador_pk] == 3) {
                            // Calcular o adicional noturno baseado nas horas trabalhadas
                            var adicionaisNoturnos = Math.floor(hr_trabalhadas / 60); // Total de ciclos completos de 1 hora
                            var minutosAdicionalNoturno = adicionaisNoturnos * 60; // Total de minutos de adicional noturno acumulado
                            // Converter o total de adicional noturno para o formato HH:mm
                            hr_adicional_noturno = converHrs(minutosAdicionalNoturno);
    
                            // Atualizar o campo de adicional noturno no formulário
                            $("#hr_adicional_noturno" + l + colaborador_pk.trim()).text(hr_adicional_noturno);
    
                        
                            
    
                            // Atualizar o total de horas trabalhadas no formato HH:mm
                            hr_trabalhadas = converHrs(hr_trabalhadas);
                        
                            $("#hr_trabalhadas" + l + colaborador_pk.trim()).text(hr_trabalhadas);
                        
                        }
                        else{
                            hr_trabalhadas = converHrs(hr_trabalhadas);
                        }
    
                        if($("#hr_faltantes" + l+ colaborador_pk.trim()).text()==""){
                            // Atualizar os valores no formulário
                            $("#hr_faltantes" + l+ colaborador_pk.trim()).text(hr_faltantes);
                        }
                        if($("#hr_excedentes" + l+ colaborador_pk.trim()).text()==""){
                            $("#hr_excedentes" + l+ colaborador_pk.trim()).text(hr_excedentes);
                        }
                        if($("#hr_trabalhadas" + l+ colaborador_pk.trim()).text()==""){
                            $("#hr_trabalhadas" + l+ colaborador_pk.trim()).text(hr_trabalhadas);
                        }                        
                    } else {
                        $("#hr_excedentes" + l + colaborador_pk.trim()).text("");
                        $("#hr_faltantes" + l + colaborador_pk.trim()).text("");
                        $("#hr_trabalhadas" + l + colaborador_pk.trim()).text("");
                        $("#hr_adicional_noturno" + l + colaborador_pk.trim()).text("");
                    }
    
                }
                
            }

        });

        //calcTotalAll(); // Atualiza os totais
    } catch (e) {
        alert(e);
    }
}

function fcCancelar() {
    let v_pk = $('#pk').val()

    if($("#relatorio_banco_horas").val()==1){
        var objParametros = {};
        sendPost('relatorio','pesqAcompanhamentoBancoHoras' ,objParametros);
    }
    else if($("#reloginho").val()==1){
        var objParametros = {};
        sendPost('colaborador','receptivo' ,objParametros);
    }
    else{
        var objParametros = {
            "pk":v_pk  
        };
        sendPost('ponto_folha','colaboradoresCad',objParametros)
    }
    
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
