var tblUsuario;
function fcValidarForm(){

    $("#form").validate({
        rules :{
            ds_equipe:{
                required:true,
                minlength:3
            }

        },
        messages:{
            ds_equipe:{
                required:"Por favor, informe Equipe",
                minlength:"Equipe deve ter pelo menos 3 caracteres"
            }

        },
        submitHandler: function(form){
            fcEnviar(); //Se a validação deu certo, faz o envio do formulario.
            return false;
        }
    });

}
function fcEnviar(){

    var v_ds_equipe = $("#ds_equipe").val();
    var strJSONDadosTabela = fcFormatarDadosUsuario();

    var objParametros = {
        "pk": $("#pk").val(),
        "ds_equipe": (v_ds_equipe),
        "equipes_usuarios":strJSONDadosTabela
    };    

    var arrEnviar = carregarController("equipe", "salvar", objParametros);   
           
    if (arrEnviar.status == true){
        // Reload datable
        utilsJS.toastNotify(true,arrEnviar.message);
        sendPost('equipe','receptivo' ,objParametros);
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
}

function fcCancelar(){
    var objParametros = {};
    sendPost('equipe','receptivo' ,objParametros);
}

function fcCarregar(){

    if($("#pk").val() > 0){

        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("equipe", "listarPk", objParametros);
        if (arrCarregar.status == true){
        
            $("#ds_equipe").val(arrCarregar.data[0]['ds_equipe']);

        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }
}


function carregarListaCombo(){
    
    var objParametros = {
            "pk": ""
        };        
        
        var arrCarregar = carregarController("usuario", "listarTodos", objParametros);
        
        
        if (arrCarregar.status == true){
        
            strComboUsuarios = "<select class='form-control form-control-sm' id='usuarios_pk' name='usuarios_pk'><option></option>";
            for(i = 0; i < arrCarregar.data.length; i++){
                strComboUsuarios = strComboUsuarios + "<option value='"+arrCarregar.data[i]['pk']+"'>"+arrCarregar.data[i]['ds_usuario']+"</option>";
            }
            strComboUsuarios += "</select>";
           
            fcFormatarGrid();
            
            fcAtualizarDadosGrid();
          
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
}

function fcFormatarGrid(){
    tblUsuario = $("#tblUsuario").DataTable({
        responsive: true,
        scrollX: true, 
        language: {
            emptyTable: "Não existem Dados cadastrados"
        },       
    });
    return false;
    
}

function fcAtualizarDadosGrid(){
    if($("#pk").val()!=""){
        var objParametros = {
            "pk": $("#pk").val()
        };        
        
        var arrCarregar = carregarController("equipe", "listarEquipesUsuarios", objParametros);
       
        if (arrCarregar.status == true){
            for(i = 0; i < arrCarregar.data.length; i++){
                fcIncluirUsuario();
                var cboUsuariosPk = $("select[id='usuarios_pk']");
                var chkBko = $("input[id='ic_bko']");
                var chkSupervisor = $("input[id='ic_supervisor']");
                  
                cboUsuariosPk.get(i).value = arrCarregar.data[i]['usuarios_pk'];
                if(arrCarregar.data[i]['ic_bko'] == 1)
                    chkBko.get(i).checked = true;
                if(arrCarregar.data[i]['ic_supervisor'] == 1)
                   chkSupervisor.get(i).checked = true;
            }
        }
        else{
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');
        }
    }     
}

function fcIncluirUsuario(){
    //ADICIONA UMA LINHA NO GRID (SEM A PARTE DE BANCO DE DADOS)
    tblUsuario.row.add(
            [strComboUsuarios, 
            "<input type='checkbox' id='ic_bko' />",
            "<input type='checkbox' id='ic_supervisor' />",
            '<a class="function_delete"><span><i class="bi bi-x-circle" style="font-size=18px;color:blue" title="excluir"></i></span></a>'
            ]
    ).draw( false );
    

    //Adiciona o evento click na linha que acabou de ser adicionada.
    $(".function_delete").on("click",fcExcluirLinha);
    
    return false;
}

function fcExcluirLinha(){
    //REMOVE A LINHA DA GRID(SEM A PARTE DO BANCO DE DADOS.)
    tblUsuario.row($(this).parents('tr')).remove().draw();
    
    return false;
}

function fcFormatarDadosUsuario(){
    //Coloca em uma variavel os id das colunas das linhas do grid.
    var cboUsuarioPk = $("select[id='usuarios_pk']");
    var chkBko = $("input[id='ic_bko']");
    var chkSupervisor = $("input[id='ic_supervisor']");
    //Crio uma chave para o array
    var arrKeys = [];
    arrKeys[0] = "usuarios_pk";
    arrKeys[1] = "ic_bko";
    arrKeys[2] = "ic_supervisor";
    
    var arrDados = []; 
    var v_ic_bko = 2;
    var v_ic_supervisor = 2;    
    //AQUI ESTOU FAZENDO UM FOR PERCORRE AS LINHAS Q FORAM INSERIDAS NA GRID.
    for(i = 0; i < cboUsuarioPk.length; i++){
        
        if(cboUsuarioPk.get(i).value == ""){
            cboUsuarioPk.get(i).focus();
            return false;
        }
        
        //SE NECESSÁRIO, TRATATIVA DE VALORES
        v_ic_bko = 2;
        v_ic_supervisor = 2;
        
        //SE O INPUT CHECKBOX COM O ID ic_bko FOR CHECADO = 1.
        if(chkBko.get(i).checked)
            v_ic_bko = 1;
        if(chkSupervisor.get(i).checked)
            v_ic_supervisor = 1;
        
        //FAZ A MONTAGEM DO ARRAY.
        arrDados[i] = [cboUsuarioPk.get(i).value,v_ic_bko,v_ic_supervisor];
        
    }
    //ESSE RETORNO CHAMA UMA FUNÇÃO QUE TRANSFORMA O ARRAY EM UM JSON.
    return arrayToJson(arrKeys, arrDados);
    
}

$(document).ready(function()
    {
        var arrCarregar = permissao("equipe", "ins");        

        if (arrCarregar.status != true){
            utilsJS.toastNotify(false, 'Falhar ao carregar o registro');            
            return false;
        }
        //Atribui os eventos
        fcValidarForm();
        $(document).on('click', '#cmdCancelar', fcCancelar);
        $(document).on('click', '#cmdIncluir', fcIncluirUsuario);
        //Atribui a validação do formulário dos campos obrigatórios
       
        carregarListaCombo();

        //Verifica se o registro é para alteracao e puxa os dados.
        fcCarregar();
    }
);
