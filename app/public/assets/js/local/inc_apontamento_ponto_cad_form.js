function fcHabilitarhr_manual(){
    if($('#hr_sistema').is(":checked") == true){
        $('#hr_manual').attr('disabled', true);
        $('#hr_manual').val('');
    }else if($('#hr_sistema').is(":checked") == false){
        $('#hr_manual').attr('disabled', false);
    }
}

function fcMascarasPonto(){
    $("#hr_manual").keypress(function () {
        mascara(this, horamask);
    });
}

function fcLimparFormPonto(){

    $("#tipo_ponto_pk").val("");
    $("#hr_sistema").prop('checked', false);
    $('#hr_manual').attr('disabled', false);
    $("#hr_manual").val("");
    $("#ds_obs_ponto").val("");
}

$(document).ready(function () {
    $("#hr_sistema").click(function () {
        fcHabilitarhr_manual();
    });
    fcMascarasPonto();

});

