function fcValidarForm(){
    var dt_ini_periodo = $("#data_periodo_ini").val();
    var dt_fim_periodo = $("#data_periodo_fim").val();

    if($("#dt_ini_periodo").val()==""){
        $("#alert_dt_ini_periodo").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_ini_periodo").slideUp(500);
        });
        $('#dt_ini_periodo').focus();
        return false;
    }     
    
    if($("#dt_fim_periodo").val()==""){
        $("#alert_dt_fim_periodo").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_fim_periodo").slideUp(500);
        });
        $('#dt_fim_periodo').focus();
        return false;
    } 

    if(DataYMD($("#dt_ini_periodo").val()) > DataYMD($("#dt_fim_periodo").val())){
        alert("A Dt. Ínicio Período não pode ser maior do que a Dt. Fim Período");
        return false;
    }else if(DataYMD($("#dt_fim_periodo").val()) > DataYMD(dt_fim_periodo)){
        alert("A Dt. Fim Período não pode ser maior do que a Dt. Período Folha Fim");
        return false;
    }else if(DataYMD($("#dt_ini_periodo").val()) < DataYMD(dt_ini_periodo)){
        //alert("O valor Dt. Ínicio Período não pode ser menor do que a Dt. Período Folha Ini");
        //return false;
    }

    fcEnviar();
}

function fcEnviar(){
    var objParametros = {
        "pk": $('#pk').val(),
        "dt_periodo_ini": $("#dt_ini_periodo").val(),
        "dt_periodo_fim": $("#dt_fim_periodo").val(),
        "arrColaborador": $("#colaboradores_pk").val()
    };

    var arrEnviar = carregarController("ponto_folha", "regerar", objParametros); 
    if (arrEnviar.status == true) {
        
        utilsJS.toastNotify(true, 'Registros regerados com sucesso');
        $("#janela_regerar").modal("hide")
        location.reload();
        //sendPost("ponto_folha_registros_res_form.php", { token: token, pk: pk});
    }
    else {
        alert('Falhou a requisição para salvar o registro');
    }
}

function fcAbrirRegerar(){
    
    var colaboradores_pk = [];

    // Seleciona todos os checkboxes que foram marcados
    var checkedRows = tblResultado.$('input[type="checkbox"].checks:checked');

    // Verifica se algum checkbox foi marcado
    if (checkedRows.length === 0) {
        sweetMensagem('warning', 'Nenhuma linha foi selecionada.');
        return false;
    }

    // Array para armazenar os valores de 'colaborador_pk' dos itens selecionados
    var colaboradores_pk = [];

    // Itera sobre os checkboxes marcados
    checkedRows.each(function(index, checkbox) {
        var rowData = tblResultado.row($(checkbox).closest('tr')).data();
        
        
        
        
        // Verifica se o status da linha é diferente de "Finalizada"
        if (rowData['ic_status'] != "Finalizada") {
            // Adiciona o 'colaborador_pk' ao array
            colaboradores_pk.push(rowData['colaborador_pk']);
        }
        
        
    });

 
    
    if(colaboradores_pk.length > 0){
        var json_colaboradores = JSON.stringify(colaboradores_pk);
        $("#colaboradores_pk").val(json_colaboradores);
        $("#dt_ini_periodo").val($("#dt_periodo_ini").html());
        $("#dt_fim_periodo").val($("#dt_periodo_fim").html());
        $("#data_periodo_ini").val($("#dt_periodo_ini").html());
        $("#data_periodo_fim").val($("#dt_periodo_fim").html());
    
        $("#janela_regerar").modal("show");
    }else{
        sweetMensagem('warning', 'Selecione ao menos um colaborador.');
    }

}

function fcFecharModalRegerar(){
    $("#janela_regerar").modal("hide")
}
