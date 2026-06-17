function printDiv() {
    var divToPrint = document.getElementById('colaboradordados'); 
    var divToPrint2 = document.getElementById('contratacaodados');
    var divToPrint3 = document.getElementById('contatodados'); 
    var divToPrint4 = document.getElementById('documentacaodados');
    var divToPrint5 = document.getElementById('dependendesdados');
    var divToPrint6 = document.getElementById('bancodados'); 
    var divToPrint7 = document.getElementById('enderecodados'); 
    var divToPrint8 = document.getElementById('vestuariodados');
    var divToPrint9 = document.getElementById('qualificacaodados');
    var divToPrint10 = document.getElementById('beneficiosdados');

    
   var print = '<html><body onload="window.print()">' + '<div class="row"><h5 style="margin-top: 1em; margin-bottom: 0;">Colaboradores</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div>' +
        divToPrint.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Dados de Contratação</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint2.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Dados de Contato do Colaborador</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint3.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Documentação do Colaborador</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint4.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Dados de Dependentes</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +  
        divToPrint5.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Dados Bancários</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint6.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Endereço do Colaborador</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint7.innerHTML + '<div class="row"><div class="col-md-10"><h5 style="margin-top: 1em; margin-bottom: 0;">Dados de Vestuário</h5><hr style="height:1px; border:none; color:#14074F; background-color:#14074F; margin-top: 0px; margin-bottom: 0px;"></div></div>' +
        divToPrint8.innerHTML +
        divToPrint9.innerHTML + 
        divToPrint10.innerHTML + 
        '</body></html>';
    var printContents = print;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;

        
 }



 function fcCancelar() {
    history.back();
}

$(document).ready(function () {
    
    $(document).on('click', '#cmdVoltar', fcCancelar);
    $(document).on('click', '#cmdImprimirModal', printDiv);


});




