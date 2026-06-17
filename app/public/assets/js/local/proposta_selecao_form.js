function fcGerarProposta(){
    if($("#ic_tipo_proposta").val() == 1){
        if($("#ic_abertura").val() == 1){
            var objParametros = {
                "pk": '',
                "ic_versao": '',
                "ic_abertura": 1,
                "leads_pk": "",
            };
        }else{
            var objParametros = {
                "pk": '',
                "ic_versao": '',
                "ic_abertura": 2,
                "leads_pk": $("#leads_pk").val(),
            };
        }
        sendPost('propostas_facilities','abrirPropostaDetalhada',objParametros);
        //sendPost('proposta_detalhada_cad_form.php',{token: token, pk: '', ic_versao: '', leads_pk: leads_pk});
    }else if($("#ic_tipo_proposta").val() == 2){
        //sendPost('proposta_basica_cad_form.php',{token: token, pk: ''});
    }else{
        $("#alert_ic_tipo_proposta").fadeTo(2000, 500).slideUp(500, function () {
            $("#alert_ic_tipo_proposta").slideUp(500);
        });
        $('#ic_tipo_proposta').focus();
        return false;
    }
}

function fcVoltar(){
    if($("#ic_abertura").val() == 1){
        var objParametros = {
            "ic_abertura":1,
            "pk":$("#leads_pk").val()
        };
        sendPost('lead','leadMainPainel' ,objParametros);

    }else{
        var objParametros = {
            "pk": '',
            "ic_versao": '',
            "ic_abertura": 2,
            "leads_pk": $("#leads_pk").val(),
        };
        sendPost('propostas_facilities','receptivo',objParametros);
    }

}

$(document).ready(function(){
    //Atribui os eventos dos demais controles
    $(document).on('click', '#cmdGerarProposta', fcGerarProposta);
    $(document).on('click', '#cmdVoltar', fcVoltar);


});