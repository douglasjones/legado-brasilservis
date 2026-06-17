function fcCarregarQRCode(pk){

        var logo = "";
        $.ajax({
      
            type: 'GET',
            url: '/api/conta/carregarLogo',
            data:[],
            complete: function (response) {
        
                try {
                    var log = JSON.parse(response.responseText);
    
                    if(log.data[0] == '[]'){
                        logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                    }
                    else{
                       if(log.data[0]['tipo_conta_pk'] == 1){
                           if(log.data[0]['ds_img_cliente'] != null){
                               logo = log.data[0]['ds_img_cliente'];
                           }
                           else{
                                logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                           }
                       }
                       else{
                           logo = 'https://server.gpros.com.br/comercial/logos/logoOficial.png';
                       }
                    }


                    try{
                        $('#qrcode-container').empty();
                        var objParametros = {
                            "leads_pk": pk
                        };
                        var arrCarregar = carregarController("lead", "listarQRCode", objParametros);

                        var html = "";
                        for (var i = 0; i < arrCarregar.data.length; i++) {
                            fcIncluirLinhaComPk(arrCarregar.data[i]['ds_ponto']);
                            
                            html = "";
                            html += '<div class="row">';
                            html += '<br>';
                            html += '<br>';
                            html += '<br>';
                            html += '   <div class="col-md-2">';
                            html += ' </div>';
                            html += '   <div class="col-md-8">';
                            html += '       <div class="card shadow" style="border-color:black">';
                            html += '           <div style="text-align: center;" >';
                            html += '           <div class="row" style="text-align: center;">';
                            html += '               <div class="col-md-12">';
                            html += '                      <img src="' + logo + '" style="width: 150px" >';
                            html += '               </div>';
                            html += '           </div><br><br>';
                            html += '           <div class="row" style="text-align: center;">';
                            html += '               <div class="col-md-12">';
                            html += '                   <span><b>Local: ' + arrCarregar.data[i]['ds_ponto'] + '</b></span>';
                            html += '               </div>';
                            html += '           </div><hr>';
                            html += '           <div class="card-body" style="text-align: center;">';
                            html += '               <div class="row">';
                            html += '                   <div class="col-md-12">';
                            html += '                       <div id="qrCode' + arrCarregar.data[i]['pk'] + '" ></div>';
                            html += '                   </div>';
                            html += '               </div>';
                            html += '           </div>';
                            html += '       </div>';
                            html += '       </div>';
                            html += '   </div>';
                            html += '</div>';
                            // Adiciona o HTML gerado
                            $('#qrcode-container').append(html);
                            
                            // Gera o QR Code
                            var id = "qrCode" + arrCarregar.data[i]['pk'];
                            gerarQrCode(id, arrCarregar.data[i]['ds_ponto']);

                            // Verifica se é o último item do par e adiciona a quebra de página depois
                            if ((i + 1) % 2 === 0 && i !== arrCarregar.data.length - 1) {
                                $('#qrcode-container').append('<div style="page-break-before: always;"></div>');
                            }
                        }

                    } catch (error) {
                        utilsJS.toastNotify(false, error);
                    }
                    
                } catch (e) {
                    utilsJS.toastNotify(false, 'Ocorreu um erro para efetuar o Login !.<br />');
                }
            }
            
        });
}

function fcSalvar(){

    var strJson = fcFormatarDados();

    var objParametros = {
        "leads_pk": $("#leads_pk").val(),
        "arrLocalPonto": (strJson)
    };

    var arrEnviar = carregarController("lead", "salvarQrCode", objParametros);

    if (arrEnviar.status == true){
        // Reload datable
        tblGrid.clear().destroy();
        fcFormatarGrid();
        fcCarregarQRCode($("#leads_pk").val());
    }
    else{
        utilsJS.toastNotify(false, 'Falhou a requisição para salvar o registro');
    }
    
}

function fcVoltar(){
    var objParametros = {
        "ic_abertura":1
    };
    sendPost('lead','receptivo' ,objParametros);
}

function fcFormatarGrid(){
    tblGrid = $("#tblGrid").DataTable(
        {
            "searching": false,
            "paging": false,
            "columnDefs" : [{
                orderable: false,
                targets: [0,1]
            }]
        }
    );
    return false;

}
function fcIncluirLinha(){

    tblGrid.row.add(
           ["<input type='text' class='form-control form-control-sm' onchange='' id='local_ponto' name='local_ponto' />",
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>"
        ]
    ).draw( false );

    $(".function_delete").on("click",fcExcluirLinha);



    return false;
}

function fcIncluirLinhaComPk(ds_local){

    tblGrid.row.add(
           ["<input type='text' class='form-control form-control-sm' onchange='' id='local_ponto' name='local_ponto' value='"+ds_local+"' />",
            "<a class='function_delete'><span><i class='bi bi-x-circle' style='font-size:18px; color:blue' title='EXCLUIR'></i></span></a>"
        ]
    ).draw( false );

    $(".function_delete").on("click",fcExcluirLinha);



    return false;
}

function fcExcluirLinha(){

    tblGrid.row($(this).parents('tr')).remove().draw();
    return false;
}

function fcFormatarDados(){
    try{

        var local_ponto = $("input[id='local_ponto']");

        var arrKeys = [];
        arrKeys[0] = "local_ponto";

        var arrDados = [];


        for(i = 0; i < local_ponto.length; i++){
            

            arrDados[i] = [local_ponto.get(i).value];

        }

        return arrayToJson(arrKeys, arrDados);
    }
    catch(err) {
        utilsJS.toastNotify(false,err);
    }
}



function gerarQrCode(id,local){
    var urlComEspacos = "https://gepros1.com.br/crm/facilities/gtronda/view/ronda_cad_form.php?posto="+$("#ds_lead").val()+"&local="+local;
    // Criar um elemento de âncora
    var linkElement = document.createElement("a");
    linkElement.href = urlComEspacos; // Definir o atributo href com a URL
    var url = linkElement.href;

    // Instanciar um objeto QR Code
    var qr = qrcode(0, 'M'); // Modo 0 (correspondente ao "Numeric"), nível de correção "M"
    qr.addData(url); // Adicionar os dados (URL)
    qr.make(); // Gerar o QR Code

    // Renderizar o QR Code em um elemento HTML
    var qrElement = document.getElementById(id);
    qrElement.innerHTML = qr.createImgTag();
    qrElement.innerHTML = qr.createImgTag(4, 0); // Aumenta o tamanho do QR Code
}

function fcImprimir(){
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;

    // Oculta o conteúdo da página exceto a parte imprimível
    document.body.innerHTML = "<div id='printableArea'>" + printContents + "</div>";
    document.getElementById("printableArea").style.display = "block";
    document.getElementById("printableArea").style.visibility = "visible";

    // Inicia a impressão
    window.print();

    // Restaura o conteúdo original da página
    document.body.innerHTML = originalContents;
}
$(document).ready(function(){
    fcFormatarGrid();

    fcCarregarQRCode($("#leads_pk").val())

    $(document).on('click', '#cmdVoltar', fcVoltar);
    $(document).on('click', '#cmdIncluirLinha', fcIncluirLinha);
    $(document).on('click', '#cmdImprimir', fcImprimir);
    $(document).on('click', '#cmdSalvar', fcSalvar);
    
});