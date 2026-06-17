
var tblArquivos;
function fcCarregarCategorias(){
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("auditoria_categoria", "listarTodos", objParametros);
    carregarComboAjax($("#auditoria_categorias_pk"), arrCarregar, " ", "pk", "ds_categoria");
}


function fcCarregarCategoriasTipos(){
    var objParametros = {
        "auditoria_categorias_pk": $("#auditoria_categorias_pk").val()
    };

    var arrCarregar = carregarController("auditoria_categoria_tipos", "listarPorAuditoriaCategoriasPk", objParametros);
    carregarComboAjax($("#auditoria_categoria_tipos_pk"), arrCarregar, " ", "pk", "ds_auditoria_categoria_tipo");
}

function fcCarregarLeads() {
    var objParametros = {};
    var arrCarregar = carregarController("lead", "listarTodos", objParametros);
    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");
}

function fcEnviar(){
    var pk = $('#pk').val();
    var auditoria_categorias_pk = $('#auditoria_categorias_pk').val();
    var auditoria_categoria_tipos_pk = $('#auditoria_categoria_tipos_pk').val();
    var leads_pk = $('#leads_pk').val();
    var ds_localizacao = $('#ds_localizacao').val();
    

    var objParametros = {
        "pk": pk,
        "auditorias_categorias_pk": auditoria_categorias_pk,
        "auditoria_categoria_tipos_pk": auditoria_categoria_tipos_pk,
        "leads_pk": leads_pk,   
        "ds_localizacao": ds_localizacao     
    };    

    var arrEnviar = carregarController("supervisao_auditoria_lead", "salvar", objParametros);
    
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        $( "#supervisao_auditoria_pk" ).val(arrEnviar.data);
        $( "#container_documentacao" ).show();
        $( "#cmdForm" ).hide();

        $("#leads_pk").attr("disabled","disabled");
        $("#auditoria_categorias_pk").attr("disabled","disabled");
        $("#auditoria_categoria_tipos_pk").attr("disabled","disabled");

        var auditoria_categoria_tipos_pk = $('#auditoria_categoria_tipos_pk').val();
        $( "#ds_form" ).append("Checklist - " + $('#auditoria_categoria_tipos_pk option').filter(":selected").text()); 
        fcCarregarEstruturaFormulario(auditoria_categoria_tipos_pk)

    }
    else{
        alert('Falhou a requisição para salvar o registro');
    }
    
}

function fcSalvarForm(){
    var qtd_campos =  $( "#qtd_campos" ).val(); 
    var ds_resultado_dados = "";

    var arrKeys = [];
    arrKeys[0] = "pk";
    arrKeys[1] = "supervisao_auditoria_pk";
    arrKeys[2] = "ds_resultado_dados";
    arrKeys[3] = "ds_tipo_campo";
    arrKeys[4] = "auditorias_categorias_itens_pk";
    arrKeys[5] = "ds_obs_geral";
    arrKeys[6] = "arrResultadoDados";
    arrKeys[7] = "arrIcCheckbox";
    arrKeys[8] = "arrPkCheckbox";
    arrKeys[9] = "qtdCheckbox";


    for(var i=0; i <= qtd_campos; i++){
        var arrInformacoes = [];
        var arrDadosCheckbox = [];
        var ds_tipo_campo = $("#ds_tipo_campo_"+i).val();
        var pk = $("#pk_campo_"+i).val();
        var auditorias_categorias_itens_pk = $("#auditorias_categorias_itens_pk_"+i).val();
        var supervisao_auditoria_pk =  $( "#supervisao_auditoria_pk" ).val();
        var ds_obs_geral = $("#ds_obs_geral").val();

        if(ds_tipo_campo == 'checkbox'){
            var arrResultadoDados = [];
            var arrIcCheckbox = [];
            var arrPkCheckbox = [];
            for(var l=0; l<$("#qtd_checkbox_"+i).val(); l++){
                ds_resultado_dados = $("#campo_"+i+"_"+l).val(); 
                pk_checkbox = $("#pk_campo_"+i+"_"+l).val();
                if($("#campo_"+i+"_"+l).prop('checked') == true){
                    ic_checkbox = "1";
                }else{
                    ic_checkbox = "2";
                }
                arrResultadoDados[l] = [ds_resultado_dados];
                arrIcCheckbox[l] = [ic_checkbox];
                arrPkCheckbox[l] = [pk_checkbox];
            }
        }else{
            arrDadosCheckbox = ""
            ds_resultado_dados = $("#campo_"+i).val();
        }
        
        arrInformacoes[0] = [pk, supervisao_auditoria_pk, ds_resultado_dados, ds_tipo_campo, auditorias_categorias_itens_pk, ds_obs_geral, arrResultadoDados, arrIcCheckbox, arrPkCheckbox, $("#qtd_checkbox_"+i).val()];
        
        var JSONinfoSupervisao = arrayToJson(arrKeys, arrInformacoes);

        var objParametros = {
            "JSONinfoSupervisao": JSONinfoSupervisao
        };    

        var arrEnviar = carregarController("supervisao_auditoria_lead", "salvar", objParametros);
        //NewWindow(v_last_url)
    
    }
    
    if (arrEnviar.status == true){
        // Reload datable
        alert(arrEnviar.message);
        sendPost('auditoria_supervisao_postos_trabalho_res_form.php', {token: token});
    }
    else{
        alert('Falhou a requisição para salvar o registro');
    }

}

function fcCarregarTiposCategorias(auditoria_categorias_pk){
    var objParametros = {
        "auditoria_categorias_pk": auditoria_categorias_pk
    };

    var arrCarregar = carregarController("auditoria_categoria_tipos", "listarPorAuditoriaCategoriasPk", objParametros);
    carregarComboAjax($("#auditoria_categoria_tipos_pk"), arrCarregar, " ", "pk", "ds_auditoria_categoria_tipo");
}

function fcCarregarEstruturaFormulario(auditoria_categoria_tipos_pk){
    
    try {
        var objParametros = {
            "auditorias_categorias_tipos_pk": auditoria_categoria_tipos_pk,
            "supervisao_auditorias_pk": pk
        };
        var arrCarregar = carregarController("supervisao_auditoria_lead", "listarPorCategoriasTiposSupervisao", objParametros);
        var html = "";
        for(var i=0; i < arrCarregar.data.length; i++){
            html += "<input type='hidden' id='auditorias_categorias_itens_pk_"+i+"' name='auditorias_categorias_itens_pk_"+i+"' value='"+arrCarregar.data[i]['pk']+"'>";
        
            if(arrCarregar.data[i]['tipo_item_pk'] == "1"){
                    html += "<div class='row'>";
                    html += "   <div class='col-md-4'>";
                    html += "       &nbsp;";
                    html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"''>";
                    html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='select'>";
                    html += "   </div>";
                    html += "   <div class='col-md-4'>";
                    html += "       <br>";
                    html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                    html += "       <select class='form-control form-control-sm' id='campo_"+i+"' name='campo_"+i+"'>";
                    html += "           <option></option>";
                    for(var l=0; l<arrCarregar.data[i]['itensDados'].length; l++){
                        html += "       <option value='"+arrCarregar.data[i]['itensDados'][l]['auditorias_categoria_itens_dados_pk']+"'>"+arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+"</option>"; 
                    }
                    html += "       </select>";
                    html += "   </div>";
                    html += "</div>";
            }else if(arrCarregar.data[i]['tipo_item_pk'] == "2"){
                    html += "<div class='row'>";
                    html += "   <div class='col-md-4'>";
                    html += "       &nbsp;";
                    html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"''>";
                    html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='text'>";
                    html += "   </div>";
                    html += "   <div class='col-md-4'>";
                    html += "       <br>";
                    html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                    html += "       <input class='form-control form-control-sm' type='text' id='campo_"+i+"' name='campo_"+i+"'>";
                    html += "   </div>";
                    html += "</div>";
            }else if(arrCarregar.data[i]['tipo_item_pk'] == "3"){
                    html += "<div class='row'>";
                    html += "   <div class='col-md-4'>";
                    html += "       &nbsp;";
                    html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"''>";
                    html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='checkbox'>";
                    html += "   </div>";
                    html += "   <div class='col-md-4'>";
                    html += "       <br>";
                    if(arrCarregar.data[i]['itensDados'].length > 0){
                        html += "       "+arrCarregar.data[i]['ds_categoria_item']+":";
                        for(var l=0; l<arrCarregar.data[i]['itensDados'].length; l++){
                            html += "   <label for='campo_"+i+"_"+l+"'>"+arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+":&nbsp;</label>";
                            html += "   <input type='checkbox' id='campo_"+i+"_"+l+"' name='campo"+i+"_"+l+"' value='"+arrCarregar.data[i]['itensDados'][l]['auditorias_categoria_itens_dados_pk']+"'><br>";
                        }
                    }else{
                        html += "   <label for='campo_"+i+"_"+l+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                        html += "   <input type='checkbox' id='campo_"+i+"0' name='campo"+i+"_0' value='"+arrCarregar.data[i]['pk']+"'><br>";
                    }
                    html += "       <input type='hidden' id='qtd_checkbox_"+i+"' name='qtd_checkbox_"+i+"'' value='"+l+"'>";
                    html += "   </div>";
                    html += "</div>";
            }else if(arrCarregar.data[i]['tipo_item_pk'] == "4"){
                    html += "<div class='row'>";
                    html += "   <div class='col-md-4'>";
                    html += "       &nbsp;";
                    html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"''>";
                    html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='textarea'>";
                    html += "   </div>";
                    html += "   <div class='col-md-4'>";
                    html += "       <br>";
                    html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                    html += "       <textarea class='form-control form-control-sm' id='campo_"+i+"' name='campo_"+i+"'></textarea>";
                    html += "   </div>";
                    html += "</div>";
            }

        }
        
        html += "<div class='row'>";
        html += "    <div class='col-md-4'>";
        html += "        &nbsp;";
        html += "    </div>";
        html += "    <div class='col-md-4'>";
        html += "       <br>";
        html += "        <label for='auditoria_categoria_tipos_pk'>Observação Checklist:&nbsp;</label>";
        html += "        <textarea class='form-control form-control-sm' id='auditoria_categoria_tipos_pk' name='auditoria_categoria_tipos_pk'></textarea>";
        html += "    </div>";
        html += "</div>";

        $( "#auditoria_categoria_form" ).append( html ); 
        $( "#qtd_campos" ).val(i); 

        
        
    } catch (error) {
        alert(error)
    }
}

function fcCarregar(){
    if(pk > 0){

        var objParametros = {
            "pk": pk
        };        
        
        var arrCarregar = carregarController("supervisao_auditoria_lead", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#auditoria_categorias_pk").val(arrCarregar.data[0]['auditorias_categorias_pk']);
            var auditoria_categorias_pk = $('#auditoria_categorias_pk').val();
            fcCarregarTiposCategorias(auditoria_categorias_pk);

            $("#auditoria_categoria_tipos_pk").val(arrCarregar.data[0]['auditorias_categorias_tipos_pk']);
            $("#leads_pk").val(arrCarregar.data[0]['leads_pk']);

            var auditoria_categoria_tipos_pk = $('#auditoria_categoria_tipos_pk').val();
            $( "#ds_form" ).append("Checklist - " + $('#auditoria_categoria_tipos_pk option').filter(':selected').text()); 
            fcCarregarForm(auditoria_categoria_tipos_pk);
            $("#leads_pk").prop('disabled', true);
            $("#auditoria_categorias_pk").prop('disabled', true);
            $("#auditoria_categoria_tipos_pk").prop('disabled', true);
        }
        else{
            alert('Falhar ao carregar o registro');
        }
    }
}

function fcCarregarForm(auditoria_categoria_tipos_pk){
    try {
        if(pk > 0){

            var objParametros = {
                "supervisao_auditorias_pk": pk,
                "auditoria_categoria_tipos_pk": auditoria_categoria_tipos_pk
            };
    
            var arrCarregar = carregarController("supervisao_auditoria_lead", "listarValoresCamposForm", objParametros);
            //NewWindow(v_last_url)
            if (arrCarregar.status == true){
                for(var i=0; i < arrCarregar.data.length; i++){
                    var html = "";
                    html += "<input type='hidden' id='auditorias_categorias_itens_pk_"+i+"' name='auditorias_categorias_itens_pk_"+i+"' value='"+arrCarregar.data[i]['pk']+"'>";
                
                    if(arrCarregar.data[i]['tipo_item_pk'] == "1"){
                        html += "<div class='row'>";
                        html += "   <div class='col-md-4'>";
                        html += "       &nbsp;";
                        html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"' value="+arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['pk']+">";
                        html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='select'>";
                        html += "   </div>";
                        html += "   <div class='col-md-4'>";
                        html += "       <br>";
                        html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                        html += "       <select class='form-control form-control-sm' id='campo_"+i+"' name='campo_"+i+"'>";
                        html += "           <option></option>";
                        for(var l=0; l<arrCarregar.data[i]['itensDados'].length; l++){
                            if(arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['ds_resultado_dados'] == arrCarregar.data[i]['itensDados'][l]['pk']){
                                html += "       <option value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"'selected>"+arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+"</option>"; 
                            }else{
                                html += "       <option value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"'>"+arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+"</option>"; 
                            }
                        }
                        html += "       </select>";
                        html += "   </div>";
                        html += "</div>";
                    }else if(arrCarregar.data[i]['tipo_item_pk'] == "2"){
                        var ds_resultado_dados = arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['ds_resultado_dados'];
                        if(ds_resultado_dados == null){
                            ds_resultado_dados = "";
                        }
                        html += "<div class='row'>";
                        html += "   <div class='col-md-4'>";
                        html += "       &nbsp;";
                        html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"' value="+arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['pk']+">";
                        html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='text'>";
                        html += "   </div>";
                        html += "   <div class='col-md-4'>";
                        html += "       <br>";
                        html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                        html += "       <input class='form-control form-control-sm' type='text' id='campo_"+i+"' name='campo_"+i+"' value="+ds_resultado_dados+">";
                        html += "   </div>";
                        html += "</div>";
                    }else if(arrCarregar.data[i]['tipo_item_pk'] == "3"){
                        html += "<div class='row'>";
                        html += "   <div class='col-md-4'>";
                        html += "       &nbsp;";
                        html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='checkbox'>";
                        html += "   </div>";
                        html += "   <div class='col-md-4'>";
                        html += "       <br>";
                        if(arrCarregar.data[i]['itensDados'].length > 0){
                            html += "       "+arrCarregar.data[i]['ds_categoria_item']+":";
                            for(var l=0; l<arrCarregar.data[i]['itensDados'].length; l++){
                                html += "       <input type='hidden' id='pk_campo_"+i+"_"+l+"' name='pk_campo_"+i+"_"+l+"'value="+arrCarregar.data[i]['supervisaoAuditoriasItens'][l]['pk']+">";
                                html += "   <label for='campo_"+i+"_"+l+"'>"+arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+":&nbsp;</label>";
                                if(arrCarregar.data[i]['supervisaoAuditoriasItens'][l]['ic_checkbox'] == "1"){
                                    html += "   <input type='checkbox' id='campo_"+i+"_"+l+"' name='campo"+i+"_"+l+"' value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"' checked><br>";
                                }else{
                                    html += "   <input type='checkbox' id='campo_"+i+"_"+l+"' name='campo"+i+"_"+l+"' value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"'><br>";
                                }
                            }
                        }else{
                            html += "   <label for='campo_"+i+"_"+l+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                            html += "       <input type='hidden' id='pk_campo_"+i+"_0' name='pk_campo_"+i+"_0' value="+arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['pk']+">";
                                if(arrCarregar.data[i]['supervisaoAuditoriasItens'][l]['ic_checkbox'] == "1"){
                                    html += "   <input type='checkbox' id='campo_"+i+"_0' name='campo"+i+"_0' value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"' checked><br>";
                                }else{
                                    html += "   <input type='checkbox' id='campo_"+i+"_0' name='campo"+i+"_0' value='"+arrCarregar.data[i]['itensDados'][l]['pk']+"'><br>";
                                }
                        }
                        html += "       <input type='hidden' id='qtd_checkbox_"+i+"' name='qtd_checkbox_"+i+"'' value='"+l+"'>";
                        html += "   </div>";
                        html += "</div>";
                    }else if(arrCarregar.data[i]['tipo_item_pk'] == "4"){
                        var ds_resultado_textarea = arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['ds_resultado_textarea'];
                        if(ds_resultado_textarea == null){
                            ds_resultado_textarea = "";
                        }
                        html += "<div class='row'>";
                        html += "   <div class='col-md-4'>";
                        html += "       &nbsp;";
                        html += "       <input type='hidden' id='pk_campo_"+i+"' name='pk_campo_"+i+"' value="+arrCarregar.data[i]['supervisaoAuditoriasItens'][0]['pk']+">";
                        html += "       <input type='hidden' id='ds_tipo_campo_"+i+"' name='ds_tipo_campo_"+i+"'' value='textarea'>";
                        html += "   </div>";
                        html += "   <div class='col-md-4'>";
                        html += "       <br>";
                        html += "       <label for='campo_"+i+"'>"+arrCarregar.data[i]['ds_categoria_item']+":</label>";
                        html += "       <textarea class='form-control form-control-sm' id='campo_"+i+"' name='campo_"+i+"'>"+ds_resultado_textarea+"</textarea>";
                        html += "   </div>";
                        html += "</div>";
                    }
                }
        
                html += "<div class='row'>";
                html += "    <div class='col-md-4'>";
                html += "        &nbsp;";
                html += "    </div>";
                html += "    <div class='col-md-4'>";
                html += "        <label for='auditoria_categoria_tipos_pk'>Observação Checklist:&nbsp;</label>";
                html += "        <textarea class='form-control form-control-sm' id='auditoria_categoria_tipos_pk' name='auditoria_categoria_tipos_pk'></textarea>";
                html += "    </div>";
                html += "</div>";
                $( "#qtd_campos" ).val(i); 
                $( "#auditoria_categoria_form" ).append( html ); 
            }else{
                alert('Falhar ao carregar o registro');
            }
        }
        
    } catch (error) {
        alert(error)
    }
}

function fcCancelar(){
    sendPost("auditoria_supervisao_postos_trabalho_res_form.php", {token: token});
}

$(document).ready(function(){
    let pk = $('#pk').val();

    $( "#supervisao_auditoria_pk" ).val(pk);
    fcCarregarLeads();
    fcCarregarCategorias();
    $('#auditoria_categorias_pk').change(function(){
        var auditoria_categorias_pk = $('#auditoria_categorias_pk').val();
        fcCarregarTiposCategorias(auditoria_categorias_pk);
    });

    fcCarregar();
    
    //limpa formulário
    $( "#ds_form" ).html(" "); 
    $( "#auditoria_categoria_form" ).html(" "); 

    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdEnviar', fcSalvarForm);
    $(document).on('click', '#cmdForm', fcEnviar);


    
});