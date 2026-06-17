var tblResultado;
function fcAbrirModalCopiar(pk) {
    $("#dt_ini_faturamento").val("");
    $("#dt_fim_faturamento").val("");
    $("#pk").val(pk);

    
    $('#dt_ini_faturamento').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_ini_faturamento").keypress(function(){
        mascara(this,mdata);
    });
    
    $('#dt_fim_faturamento').datepicker({defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });
    $("#dt_fim_faturamento").keypress(function(){
        mascara(this,mdata);
    });

    $('#event-modal').modal("show");

    $("#cmdEnviar").click(function(){
        fcCopiarFaturamento();
    });
    

    $("#cmdEnviar1").click(function(){
        fcCopiarFaturamento();
    });
    
    $("#cmdEnviar").click(function(){
        $('#event-modal').modal("hide");
    });

    $("#cmdFechar1").click(function(){
        $('#event-modal').modal("hide");
    });
}

function fcCopiarFaturamento(){
    if($("#dt_ini_faturamento").val()==""){
        $("#alert_dt_ini_faturamento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_ini_faturamento").slideUp(500);
        });
        $('#dt_ini_faturamento').focus();
        return false;
    }
    if($("#dt_fim_faturamento").val()== ""){
        $("#alert_dt_fim_faturamento").fadeTo(2000, 500).slideUp(500, function(){
            $("#alert_dt_fim_faturamento").slideUp(500);
        });
        $('#dt_fim_faturamento').focus();
        return false;
    }

    var objParametros = {
        "pk": $("#pk").val(),
        "dt_faturamento_ini": $("#dt_ini_faturamento").val(),
        "dt_faturamento_fim": $("#dt_fim_faturamento").val()
    };

    var arrCopiar = carregarController("faturamento", "faturamentoCopiar", objParametros);
    if (arrCopiar.status == true){
        //Exibe a mensagem
        utilsJS.toastNotify(true, arrCopiar.message);

        $("#event-modal").modal("hide");

        tblResultado.clear().destroy();
        fcCarregarGridFaturamento();
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição de exclusão.');
    }
}