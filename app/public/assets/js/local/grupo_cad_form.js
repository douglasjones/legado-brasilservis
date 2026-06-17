var tblPermissoes;
var strComboModulos = "";

function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_grupo:{
                required:true
            }
        },
        messages:{
            ds_grupo:{
                required:"Por favor, informe o Grupo do grupo"
            }
        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });
}
function fcEnviar(){

    var strJSONDadosTabela = fcFormatarDadosModulos();
    var v_ds_grupo = $("#ds_grupo").val();

    var objParametros = {
        "pk": $("#pk").val(),
        "ds_grupo": (v_ds_grupo),
        "modulos_grupos":strJSONDadosTabela
    };    

    var arrEnviar = carregarController("grupo", "salvar", objParametros);   
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true,arrEnviar.message);
        sendPost('grupo','receptivo' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }

    formdata.append("pk",$("#pk").val());
    formdata.append("ds_grupo",v_ds_grupo);
    formdata.append("modulos_grupos",strJSONDadosTabela);
    $.ajax({
        type: 'POST',
        url: '/api/grupo/salvar',
        data: formdata,
        processData: false,
        contentType: false,
        complete: function (response) {
            try {
                var log = JSON.parse(response.responseText);
                if(log.status==true){
                    utilsJS.toastNotify(true,arrEnviar.message);
                    sendPost('grupo','receptivo' ,objParametros);
                }
                else{
                    utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
                }

            } catch (e) {
                utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
            }
        }
    });
}

function fcCancelar(){
    var objParametros = {};
    sendPost('grupo','receptivo' ,objParametros);
}

function fcFormatarGrid(){
        
    tblPermissoes = $("#tblPermissoes").DataTable({
        searching: false,
        paging: false,
        scrollX: true,
        processing: false,
        serverSide: false,
        responsive: true,
            language: {
                emptyTable: "Não existem Dados cadastrados"
            },       
        });
        return false;
}

function carregarListaCombo(){

    var objParametros = {
        "pk": ""
    };        
    
    var arrCarregar = carregarController("modulo", "listarTodos", objParametros);
   
    if (arrCarregar.status == true){
    
        strComboModulos = "<select id='modulos_pk' name='modulos_pk'><option></option>";
        for(i = 0; i < arrCarregar.data.length; i++){
            if(arrCarregar.data[i]['ds_tipo_modulo']==null){
                strComboModulos = strComboModulos + "<option value='"+arrCarregar.data[i]['pk']+"'>"+arrCarregar.data[i]['ds_modulo']+"</option>";
            }
            else{
                strComboModulos = strComboModulos + "<option value='"+arrCarregar.data[i]['pk']+"'>"+arrCarregar.data[i]['ds_tipo_modulo']+" -> "+arrCarregar.data[i]['ds_dominio']+ "</option>";
            }
        }
        strComboModulos += "</select>";
    
        fcFormatarGrid();
        fcAtualizarDadosGrid();
      
    }
    else{
        utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
    }
}

function fcAtualizarDadosGrid(){
    if($("#pk").val()!=""){
        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("grupo", "listarPermissoesGrupo", objParametros);
       
        if (arrCarregar.status == true){
            for(i = 0; i < arrCarregar.data.length; i++){
                fcIncluirPermissao();
                var cboModulosPk = $("select[id='modulos_pk']");
                var chkIns = $("input[id='ic_ins']");
                var chkUpd = $("input[id='ic_upd']");
                var chkDel = $("input[id='ic_del']");
                var chkCons = $("input[id='ic_cons']");
                 
                cboModulosPk.get(i).value = arrCarregar.data[i]['t_modulos_pk'];
                if(arrCarregar.data[i]['t_ic_ins'] == 1)
                    chkIns.get(i).checked = true;
                if(arrCarregar.data[i]['t_ic_upd'] == 1)
                    chkUpd.get(i).checked = true;
                if(arrCarregar.data[i]['t_ic_del'] == 1)
                    chkDel.get(i).checked = true;
                if(arrCarregar.data[i]['t_ic_cons'] == 1)
                    chkCons.get(i).checked = true;
            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
    
}

function fcIncluirPermissao(){
    
    tblPermissoes.row.add(
            [strComboModulos,
             "<input type='checkbox' id='ic_cons' />",
             "<input type='checkbox' id='ic_ins' />",
             "<input type='checkbox' id='ic_upd' />",
             "<input type='checkbox' id='ic_del' />",
             "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"
            ]
    ).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinha);
    
    return false;
}

function fcExcluirLinha(){
    
    tblPermissoes.row($(this).parents('tr')).remove().draw();
    
    return false;
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("grupo", "listarPk", objParametros);
        if (arrCarregar.status == true){
        
            $("#ds_grupo").val(arrCarregar.data[0]['ds_grupo']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

function fcFormatarDadosModulos(){
    //Coloca em uma variavel os id das colunas das linhas do grid.
    var cboModulosPk = $("select[id='modulos_pk']");
    var chkIns = $("input[id='ic_ins']");
    var chkUpd = $("input[id='ic_upd']");
    var chkDel = $("input[id='ic_del']");
    var chkCons = $("input[id='ic_cons']");
    
    //Crio uma chave para o array
    var arrKeys = [];
    arrKeys[0] = "modulos_pk";
    arrKeys[1] = "ic_ins";
    arrKeys[2] = "ic_upd";
    arrKeys[3] = "ic_del";
    arrKeys[4] = "ic_cons";
    
    var arrDados = [];
    var v_ic_ins = 2;
    var v_ic_upd = 2;
    var v_ic_del = 2;
    var v_ic_cons = 2;    
    
    //AQUI ESTOU FAZENDO UM FOR PERCORRE AS LINHAS Q FORAM INSERIDAS NA GRID.
    for(i = 0; i < cboModulosPk.length; i++){
        
        if(cboModulosPk.get(i).value == ""){
            cboModulosPk.get(i).focus();
            return false;
        }
        
        v_ic_ins = 2;
        v_ic_upd = 2;
        v_ic_del = 2;
        v_ic_cons = 2;          
        
        if(chkIns.get(i).checked)
            v_ic_ins = 1;
        if(chkUpd.get(i).checked)
            v_ic_upd = 1;
        if(chkDel.get(i).checked)
            v_ic_del = 1;
        if(chkCons.get(i).checked)
            v_ic_cons = 1;
        
        arrDados[i] = [cboModulosPk.get(i).value, v_ic_ins, v_ic_upd, v_ic_del, v_ic_cons];
        
    }
    
    return arrayToJson(arrKeys, arrDados);
    
}
var formdata = null;
$(document).ready(function(){
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdIncluir', fcIncluirPermissao);
        $(document).on('click', '#cmdEnviar', fcEnviar);

        //Atribui a validação do formulário dos campos obrigatórios
        //fcValidarForm();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();

        //Monta a string com o combo dos módulos.
        carregarListaCombo();

        formdata = new FormData();
    
        //Carrega o grid com os módulos e suas atribuições.
});
