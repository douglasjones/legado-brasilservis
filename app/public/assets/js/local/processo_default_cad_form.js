var tblEtapas;
var tblModulo;

function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_processo_default:{
                required:true,
                minlength:3
            }

        },
        messages:{
            ds_processo_default:{
                required:"Por favor, informe Processo",
                minlength:"Processo deve ter pelo menos 3 caracteres"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_processo_default = $("#ds_processo_default").val();
    var v_ic_status = $("#ic_status").val();
    var strJSONDadosTabela = fcFormatarDadosEtapa();
    var strJSONDadosTabelaModulo = fcFormatarDadosModulo();


    var objParametros = {
        "pk": $("#pk").val(),
        "ds_processo_default": (v_ds_processo_default),
        "ic_status": (v_ic_status),
        "arrProcessoEtapa": (strJSONDadosTabela),
        "arrProcessoModulo": (strJSONDadosTabelaModulo)
        
    };    

    var arrEnviar = carregarController("processo_default", "salvar", objParametros);   

    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true,arrEnviar.message);
        sendPost('processo_default', 'receptivo' ,{});
    }
    else{
        utilsJS.toastNotify(false,'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {
    };
    sendPost('processo_default', 'receptivo' ,objParametros);
}

function fcCarregar(){
    
    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("processo_default", "listarPk", objParametros);
        
        if (arrCarregar.status == true){
        
            $("#ds_processo_default").val(arrCarregar.data['ds_processo_default']);
            $("#ic_status").val(arrCarregar.data['ic_status']);

        }
        else{
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
        }
        
        fcAtualizarDadosGrid();
        fcAtualizarDadosGridModulo();
    }
}

function fcFormatarGrid(){
        
    tblEtapas = $("#tblEtapas").DataTable({
        responsive: true,
            scrollX: true, 
            language: {
                emptyTable: "Não existem Dados cadastrados"
            },       
        });
        return false;
           
}

function fcIncluirEtapa(){
    
    tblEtapas.row.add([
            "<input type='text' class='form-control form-control-sm' onkeypress='mascara(this,soNumeros )' id='n_ordem_etapa' />",
            "<input type='text' id='ds_processo_default_etapa' class='form-control form-control-sm' />",
            "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"

    ]).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinha);
    
    return false;
}
function fcAtualizarDadosGrid(){
    if($("#pk").val()!=""){
       
        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("processo_default", "listarProcessoDefaultPk", objParametros);
        
        if (arrCarregar.status == true){
            for(i = 0; i < arrCarregar.data.length; i++){
                fcIncluirEtapa();
                var cboDsProcessoDefaultEtapa = $("input[id='ds_processo_default_etapa']");
                var intOrdem = $("input[id='n_ordem_etapa']");
                
                cboDsProcessoDefaultEtapa.get(i).value = arrCarregar.data[i]['ds_processo_default_etapa'];
                intOrdem.get(i).value = arrCarregar.data[i]['n_ordem_etapa'];
               }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }    
    }
}
function fcExcluirLinha(){
    
    tblEtapas.row($(this).parents('tr')).remove().draw();
    
    return false;
}

function fcFormatarDadosEtapa(){

    var StringDsProcessoDefaultEtapa = $("input[id='ds_processo_default_etapa']");
    var IntOrdemEtapa = $("input[id='n_ordem_etapa']");
    
    var arrKeys = [];
    arrKeys[0] = "ds_processo_default_etapa";
    arrKeys[1] = "n_ordem_etapa";
    
    var arrDados = [];  
    
    for(i = 0; i < StringDsProcessoDefaultEtapa.length; i++){
        
        if(StringDsProcessoDefaultEtapa.get(i).value == ""){
            StringDsProcessoDefaultEtapa.get(i).focus();
            return false;
        }
        
        arrDados[i] = [StringDsProcessoDefaultEtapa.get(i).value, IntOrdemEtapa.get(i).value];
        
    }
    
    return arrayToJson(arrKeys, arrDados);
    
}

function fcFormatarGridtblModulo(){
        
    tblModulo = $("#tblModulo").DataTable({
        responsive: true,
        scrollX: true, 
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },       
    });
    return false;
}

function fcIncluirModulo(){
    var arrCarregar = carregarController("modulo", "listarTodos", '');

    var html = "";
        html += "<option></option>";

    for(var i=0; i<arrCarregar.data.length; i++){
        html += "<option value="+arrCarregar.data[i]['pk']+">"+arrCarregar.data[i]['ds_modulo']+"</option>";
    }

    tblModulo.row.add([
            "<input type='text' onkeypress='mascara(this,soNumeros )' id='n_ordem_modulo' class='form-control form-control-sm' />",
            "<select id='modulo_pk' class='form-control form-control-sm'>"+html+"</select>",
             "<a class='function_delete'><span><i class='fa fa-trash' style='width: 13px'></i></span></a>"
    ]).draw( false );

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinhaModulo);
    
    return false;
}

function fcAtualizarDadosGridModulo(){
    if($("#pk").val()!=""){
        var objParametros = {
            "pk": $("#pk").val()
        };        
    
        var arrCarregar = carregarController("processo_default", "listarModulosProcessoDefaultPk", objParametros);
        
        if (arrCarregar.status == true){
            for(i = 0; i < arrCarregar.data.length; i++){
                fcIncluirModulo();
                var cboDsProcessoDefaultModulo = $("select[id='modulo_pk']");
                    var intOrdemodulo = $("input[id='n_ordem_modulo']");
                    
                    cboDsProcessoDefaultModulo.get(i).value = arrCarregar.data[i]['modulos_pk'];
                    intOrdemodulo.get(i).value = arrCarregar.data[i]['n_ordem'];
                }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}

function fcExcluirLinhaModulo(){
    
    tblModulo.row($(this).parents('tr')).remove().draw();
    
    return false;
}

function fcFormatarDadosModulo(){

    //var StringDsProcessoDefaultModulo = $("#modulo_pk option:selected");
    var StringDsProcessoDefaultModulo = $("select[id='modulo_pk']");
    var IntOrdemModulo = $("input[id='n_ordem_modulo']");
    
    var arrKeys = [];
    arrKeys[0] = "modulo_pk";
    arrKeys[1] = "n_ordem_modulo";
    
    var arrDados = [];  
    
    for(i = 0; i < StringDsProcessoDefaultModulo.length; i++){
        if(StringDsProcessoDefaultModulo.get(i).value == ""){
            StringDsProcessoDefaultModulo.get(i).focus();
            return false;
        }
        
        arrDados[i] = [StringDsProcessoDefaultModulo.get(i).value, IntOrdemModulo.get(i).value];
        
    }
    
    return arrayToJson(arrKeys, arrDados);
    
}

$(document).ready(function()
    {
        var arrCarregar = permissao("processo_default", "ins");        

        if (arrCarregar.status != true){            
            utilsJS.toastNotify(false,'Falhar ao carregar o registro');
            return false;
        }
        //Atribui os eventos
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdIncluir', fcIncluirEtapa);
        $(document).on('click', '#cmdIncluirModulo', fcIncluirModulo);

        //Atribui a validação do formulário dos campos obrigatórios
        fcValidarForm();

        //Carrega os dados no combo.
        fcFormatarGrid();
        fcFormatarGridtblModulo();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
        


    }
);
