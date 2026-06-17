function fcVoltarLead(){

    var objParametros = {
        "local":$("#local").val()
    };
    sendPost('lead','receptivo' ,objParametros);
}
$(document).ready(function(){
    //Atribui os eventos
    $(document).on('click', '#cmdVoltarLead', fcVoltarLead);

    $("#dados_cadastrais_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link active');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');


        $("#dados_cadastrais_lead").addClass('tab-pane fade show active');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');


        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade');


    });
    $("#contatos_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link active');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');

        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade show active');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');

        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade');

        tblContatos.ajax.reload();
    })
    $("#agendas_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link ');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link active');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');

        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade ');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade show active');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');

        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade');

        tblAgenda.ajax.reload();
    });
    $("#comercial_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link ');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link active');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');


        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade ');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade  ');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade show active')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');

         $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade');


        tblResultadoComercial.ajax.reload();
    });
    $("#ocorrencias_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link ');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link ');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link active');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');


        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade ');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade  ');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade ')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade show active');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');

        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade');


        tblOcorrencia.ajax.reload();
    });
    $("#documentos_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link ');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link ');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link ');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link active');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link');


        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade ');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade  ');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade ')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade ');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade show active');

        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('pab-pane fade');

        tblDocumentos.ajax.reload();
    });
    $("#financeiro_lead-tab").click(function(){
        $("#dados_cadastrais_lead-tab").removeClass();
        $("#dados_cadastrais_lead-tab").addClass('nav-link ');

        $("#contatos_lead-tab").removeClass();
        $("#contatos_lead-tab").addClass('nav-link ');

        $("#agendas_lead-tab").removeClass();
        $("#agendas_lead-tab").addClass('nav-link');

        $("#comercial_lead-tab").removeClass();
        $("#comercial_lead-tab").addClass('nav-link ');

        $("#ocorrencias_lead-tab").removeClass();
        $("#ocorrencias_lead-tab").addClass('nav-link ');

        $("#documentos_lead-tab").removeClass();
        $("#documentos_lead-tab").addClass('nav-link');

        $("#financeiro_lead-tab").removeClass();
        $("#financeiro_lead-tab").addClass('nav-link active');


        $("#dados_cadastrais_lead").removeClass();
        $("#dados_cadastrais_lead").addClass('tab-pane fade');

        $("#contatos_lead").removeClass();
        $("#contatos_lead").addClass('tab-pane fade ');

        $("#agendas_lead").removeClass();
        $("#agendas_lead").addClass('tab-pane fade  ');

        $("#comercial_lead").removeClass();
        $("#comercial_lead").addClass('tab-pane fade ')

        $("#ocorrencias_lead").removeClass();
        $("#ocorrencias_lead").addClass('tab-pane fade ');

        $("#documentos_lead").removeClass();
        $("#documentos_lead").addClass('tab-pane fade');

        $("#financeiro_lead").removeClass();
        $("#financeiro_lead").addClass('tab-pane fade show active');

    });



});
