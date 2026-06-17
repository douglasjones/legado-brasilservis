function fcCarregarDisciplina() {
    //Carrega os grupos

    var objParametros = {
        "pk": ""
    };

    var arrCarregar = carregarController("agenda_colaborador_apontamento", "listarDisciplina", objParametros);
    carregarComboAjax($("#tipo_disciplina_pk"), arrCarregar, " ", "pk", "ds_tipo_dsciplna");

}
function fcLimparFormDisciplina(){
    $("#tipo_disciplina_pk").val("");
    $("#dt_disciplina").val("");
    $("#obs").val("");
}
function fcMascarasData(){
    $('#dt_disciplina').datepicker({
        defaultDate: "getDate()",
        dateFormat: 'dd/mm/yyyy',
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        todayBtn: "linked",
        minDate: 0
    });

    $("#dt_disciplina").keypress(function () {
        mascara(this, mdata);
    });


}

$(document).ready(function () {

    
    fcCarregarDisciplina();
    fcMascarasData();


    
});
