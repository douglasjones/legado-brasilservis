

function fcEnviar(){

    var v_auditoria_categorias_pk = $("#auditoria_categorias_pk").val();
    var v_ds_auditoria_categoria_tipo = $("#ds_auditoria_categoria_tipo").val();
    var v_ic_status = $("#ic_status").val();
    var v_leads_pk = $("#leads_pk").val();
    var v_produtos_pk = $("#produtos_pk").val();

    var objParametros = {
        "pk": $("#pk").val(),
        "auditoria_categorias_pk": v_auditoria_categorias_pk,
        "ds_auditoria_categoria_tipo": v_ds_auditoria_categoria_tipo,
        "leads_pk": v_leads_pk,
        "produtos_pk": v_produtos_pk,
        "ic_status": v_ic_status       
    };    

    var arrEnviar = carregarController("auditoria_categoria_tipos", "salvar", objParametros); 
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        CarregarGridForm(arrEnviar.data)
        $("#cmdEnviarTiposCategoria").hide();
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcIncluirNovoCampo(){
    var v_ds_categoria_item = $("#ds_categoria_item").val();
    var v_auditoria_categorias_pk = $("#auditoria_categorias_pk").val();
    var v_auditoria_categorias_tipos_pk = $("#auditorias_categorias_tipos_pk").val();
    var v_ic_obrigatorio = $("#ic_obrigatorio").prop('checked') == true ? v_ic_obrigatorio = 1 : v_ic_obrigatorio = 0;
    var v_tipo_item_pk = $("#tipo_item_pk").val();
    var v_ic_status = $("#ic_status").val();

    var objParametros = {
        "pk": "",
        "ds_categoria_item": v_ds_categoria_item,
        "auditorias_categorias_tipos_pk": v_auditoria_categorias_tipos_pk,
        "auditorias_categorias_pk": v_auditoria_categorias_pk,
        "ic_obrigatorio": v_ic_obrigatorio,
        "tipo_item_pk": v_tipo_item_pk,
        "ic_status": v_ic_status       
    };    

    var arrEnviar = carregarController("auditoria_categoria_tipos", "salvarItens", objParametros);
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#tblCampos").load(location.href + " #tblCampos");
        $("#campos_treeView").html("");
        $("#ds_categoria_item").val("");
        $("#tipo_item_pk").val("");
        $("#ic_obrigatorio").prop('checked', false);
        $("#container_campos_formulario").show();
        CarregarGridForm(v_auditoria_categorias_tipos_pk);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcSalvarItensCampos(){
    
    var strJSONDadosTabela = fcFormatarDadosItens();

    var objParametros = {
        "pk": "",
        "dadosItensCampo": strJSONDadosTabela
    };    

    var arrEnviar = carregarController("auditoria_categoria_tipos", "salvarItensCampos", objParametros);

    if (arrEnviar.status == true){
        utilsJS.toastNotify(true, arrEnviar.message);
        $("#campos_treeView").html("");
        CarregarGridForm($("#auditorias_categorias_tipos_pk").val());
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcFormatarDadosItens(){

    var ds_item = $("input[id='ds_item']");
    var itens_pk = $("input[id='itens_pk']");
    var tipo_item_pk = $("input[id='tipo_item_pk']");
    
    var arrKeys = [];
    arrKeys[0] = "ds_item";
    arrKeys[1] = "itens_pk";
    arrKeys[2] = "tipo_item_pk";
    
    var arrDados = []; 
    
    var pk_tens = "";
    var pk_tipo_item = "";

    
    for(i = 0; i < ds_item.length; i++){
        
        if(ds_item.get(i).value == ""){
            ds_item.get(i).focus();
            return false;
        }

        pk_tens = itens_pk.get(i).value;
        pk_tipo_item = tipo_item_pk.get(i).value;
        
        arrDados[i] = [ds_item.get(i).value, pk_tens, pk_tipo_item];
    }
    
    return arrayToJson(arrKeys, arrDados);
    
}

function fcAtualizarStatus(){
    
    var strJSONDadosStatus = fcFormatarDadosStatus();

    var objParametros = {
        "strJSONDadosStatus": strJSONDadosStatus
    };    

    carregarController("auditoria_categoria_tipos", "atualizarStatus", objParametros);
    sendPost('auditoria_categoria_tipos','receptivo' ,{});
}

function fcFormatarDadosStatus(){

    var status = $("select[id='ic_status_item']");
    var itens_pk = $("input[id='pk_ic_status']");
    
    var arrKeysStatus = [];
    arrKeysStatus[0] = "auditoria_categorias_itens_pk";
    arrKeysStatus[1] = "ic_status";
    
    var arrDadosStatus = []; 
    var ic_status = "";

    if(itens_pk.length > 0){
        for(i=0; i < itens_pk.length; i++){
            ic_status = status.get(i).value;
            arrDadosStatus[i] = [itens_pk.get(i).value, ic_status];
        }
    }
    
    
    return arrayToJson(arrKeysStatus, arrDadosStatus);
    
}

function fcAddItemCampo(item_pk, tipo_item_pk){
    table =  $("#tblCampos"+item_pk);
    table.append("<tr>\n\
                    <td colspan='2'>\n\
                        <input type='text' id='ds_item' name='ds_item' style='width:100%'>\n\
                        <input type='hidden' name='itens_pk' id='itens_pk' value='"+item_pk+"'>\n\
                        <input type='hidden' name='tipo_item_pk' id='tipo_item_pk' value='"+tipo_item_pk+"'>\n\
                    </td>\n\
                </tr>")

    $("#cmdSalvarItensCampos").show();
                
}

function fcCarregar(){
    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("auditoria_categoria_tipos", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
            $("#cmdEnviarTiposCategoria").hide();
            $("#ds_auditoria_categoria_tipo").val(arrCarregar.data[0]['ds_auditoria_categoria_tipo']);
            $("#auditoria_categorias_pk").val(arrCarregar.data[0]['auditoria_categorias_pk']);
            $("#ic_status").val(arrCarregar.data[0]['ic_status']);

            CarregarGridForm(arrCarregar.data[0]['pk']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhou ao carregar para salvar o registro');
        }
    }
}

function fcCarregarCategorias(){
    
    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("auditoria_categoria", "listarTodos", objParametros);
    carregarComboAjax($("#auditoria_categorias_pk"), arrCarregar, " ", "pk", "ds_categoria");
}

function CarregarGridForm(auditorias_categorias_tipos_pk){
    
    $("#container_campos_formulario").show();
    $("#auditorias_categorias_tipos_pk").val(auditorias_categorias_tipos_pk)

    var objParametros = {
        "auditorias_categorias_tipos_pk": auditorias_categorias_tipos_pk
    };  

    var arrCarregar = carregarController("auditoria_categoria_tipos", "listarPorCategoriasTiposPk", objParametros);

    if (arrCarregar.status == true) {
        if (arrCarregar.data.length != "") {
            if(arrCarregar.data[0]['pk'] != ""){
                vhtml = "";
                vhtml += "        <div>";

                for(var i=0; i < arrCarregar.data.length; i++){
                    vhtml += "          <ul>";
                    vhtml += "              <span class='caret'><b>";
                    vhtml +=                    arrCarregar.data[i]['ds_categoria_item'];
                    vhtml += "             </b> </span>";
                        vhtml += "              <ul class='nested' style='width:100%'>";
                        vhtml += "                  <br><b> Tipo Campo: </b>"+arrCarregar.data[i]['ds_tipo_item'];
                        vhtml += "                  <br><b> Obrigatório: </b>"+arrCarregar.data[i]['ds_ic_obrigatorio'];
                        vhtml += "                  <br><b> Status: </b>";
                        vhtml += "                      <select id='ic_status_item' name='ic_status_item'>";
                        vhtml += "                          <option></option>";
                        if(arrCarregar.data[i]['ic_status']==" " || arrCarregar.data[i]['ic_status']=='1'){
                            vhtml += "                          <option value='1' selected>Ativo</option>";
                            vhtml += "                          <option value='2'>Inativo</option>";
                        }else{
                            vhtml += "                          <option value='1'>Ativo</option>";
                            vhtml += "                          <option value='2' selected>Inativo</option>";
                        }
                        vhtml += "                      </select>";
                        vhtml += "                      <input type='hidden' id='pk_ic_status' name='pk_ic_status' value="+arrCarregar.data[i]['pk']+" style='width:100%'>";
                        if(arrCarregar.data[i]['tipo_item_pk'] == 1 || arrCarregar.data[i]['tipo_item_pk'] == 3){
                            vhtml += "                  <br><br><button title='Incluir um novo item' type='button' class='btn btn-link' id='cmdIncluirItem' onClick=fcAddItemCampo("+arrCarregar.data[i]['pk']+","+arrCarregar.data[i]['tipo_item_pk']+")>Adicionar item ao campo</button>";
                            vhtml += "                  <table class='table table-striped table-bordered nowrap' id='tblCampos"+arrCarregar.data[i]['pk']+"'>";
                            vhtml += "                      <thead>";
                            vhtml += "                          <tr>";
                            vhtml += "                              <th>Itens Campo</th>";
                            vhtml += "                              <th><button type='button' class='btn btn-primary btn-sm' id='cmdSalvarItensCampos' onClick=fcSalvarItensCampos()>Salvar Itens Adicionados</button></th>";
                            vhtml += "                          </tr>";
                            vhtml += "                      </thead>";
                            vhtml += "                      <tbody>";
                            for(var l=0; l < arrCarregar.data[i]['itensDados'].length; l++){
                                vhtml += "                           <tr>";
                                vhtml += "                              <td colspan='2'><label>" +arrCarregar.data[i]['itensDados'][l]['ds_item_dados']+"</label>";
                                vhtml += "                           </tr>";
                            }
                            vhtml += "                      </tbody>";
                            vhtml += "                </table>";
                        }
                        vhtml += "              </ul>";
                    vhtml += "              </ul>";
                }

                vhtml += "        </div>";
                $('#campos_treeView').append(vhtml); 
            }
        }
    }

    var toggler = document.getElementsByClassName("caret");
    var i;

    for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
            this.parentElement.querySelector(".nested").classList.toggle("active");
            this.classList.toggle("caret-down");
        });
        
    }
        
}

function fcCancelar(){
    sendPost('auditoria_categoria_tipos','receptivo' ,{});
}

function fcCarregarLeads() {
    //Carrega os grupos

    var objParametros = {
        
    };

    var arrCarregar = carregarController("lead", "listarTodos", objParametros);

    carregarComboAjax($("#leads_pk"), arrCarregar, " ", "pk", "ds_lead");

}

function fcComboPesqProdutosServicos() {
    var objParametros = {
        "pk": ""
    };
    var arrCarregar = carregarController("produto_servico", "listarTodos", objParametros);
    //NewWindow(v_last_url)
    carregarComboAjax($("#produtos_pk"), arrCarregar, " ", "pk", "ds_produto_servico");

}

$(document).ready(function(){
    fcCarregarCategorias();
    fcCarregarLeads();
    fcComboPesqProdutosServicos();
    
    if($("#pk").val() != ""){
        $("#container_campos_formulario").show();
    }else{
        $("#container_campos_formulario").hide();
    }

    //Atribui a validação do formulário dos campos obrigatórios
    //fcValidarForm();

    //Verifica se o registro é para alteracao e puxa os dados.
    fcCarregar();
    $(document).on('click', '#cmdEnviarTiposCategoria', fcEnviar);
    $(document).on('click', '#cmdIncluir', fcIncluirNovoCampo);
    $(document).on('click', '#cmdEnviar', fcAtualizarStatus);
    $(document).on('click', '#cmdEnviar2', fcAtualizarStatus);
    //Atribui os eventos
    $(document).on('click', '#cmdCancelar', fcCancelar);
    $(document).on('click', '#cmdCancelar2', fcCancelar);

    $(".chzn-select").chosen({ allow_single_deselect: true });
    
});
