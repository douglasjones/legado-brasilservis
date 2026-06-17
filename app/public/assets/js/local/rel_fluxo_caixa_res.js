var strRetorno;
var click_id = 0;




function fcCancelar(){
    objParametros = {}
    sendPost("relatorio", "pesqRelFluxoCaixa",objParametros);
}

function fcExport(){

    var htmlPlanilha = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    htmlPlanilha += '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
    htmlPlanilha += '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PlanilhaTeste</x:Name>';
    htmlPlanilha += '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml>';
    htmlPlanilha += '<![endif]--></head><body><table>' + $("#form").html() + '</table></body></html>';

// Converte a string em um blob
    var blob = new Blob([htmlPlanilha], { type: 'application/vnd.ms-excel;charset=utf-8' });

// Cria uma URL para o blob
    var url = URL.createObjectURL(blob);

// Cria um link para o download
    var link = document.createElement('a');
    link.download = 'export.xls';
    link.href = url;

// Adiciona o link ao corpo e simula o clique
    document.body.appendChild(link);
    link.click();

// Remove o link
    document.body.removeChild(link);
}

function AdicionarFiltroSelect(tabela, coluna) {
    var cols = $("#" + tabela + " thead tr:first-child th").length;
    if ($("#" + tabela + " thead tr").length == 1) {
        var linhaFiltro = "<tr>";
        for (var i = 0; i < cols; i++) {
            linhaFiltro += "<th></th>";
        }
        linhaFiltro += "</tr>";
 
        $("#" + tabela + " thead").append(linhaFiltro);
    }
 
    var colFiltrar = $("#" + tabela + " thead tr:nth-child(1) th:nth-child(" + coluna + ")");
 
    $(colFiltrar).html("<select id='filtroColuna_" + coluna.toString() + "'  class='filtroColuna'> </select>");
 
    var valores = new Array();
 
    $("#" + tabela + " tbody tr").each(function () {
        var txt = $(this).children("td:nth-child(" + coluna + ")").text();
        if (valores.indexOf(txt) < 0) {
            valores.push(txt);
        }
    });
    $("#filtroColuna_" + coluna.toString()).append("<option> </option>")
    for (elemento in valores) {
        $("#filtroColuna_" + coluna.toString()).append("<option>" + valores[elemento] + "</option>");
    }
 
    $("#filtroColuna_" + coluna.toString()).change(function () {
        var filtro = $(this).val();
        if($("#" + tabela + " tbody tr").is(':visible')){
            $(this).show();
        }
        if(filtro == ""){
            $("#" + tabela + " tbody tr").show();
        }
        if (filtro != "") {
            $("#" + tabela + " tbody tr").each(function () {
                var txt = $(this).children("td:nth-child(" + coluna + ")").text();
                if (txt != filtro) {
                    $(this).hide();
                }
            });
        }
        

        $("#valor_lancamento").html("")
        $("#valor_receita").html("")
        $("#valor_despesa").html("")

        //Calcula valor total Lançamento
        var valores_lancamento = new Array();
        var v_total_lancamento = new Number(0);
        $("#" + tabela + " tbody tr:visible").each(function () {
            var lancamento = $(this).children("td:nth-child(4)").text();
            if (valores_lancamento.indexOf(lancamento) < 0) {
                valores_lancamento.push(lancamento);
            }
        })
        for (elemento_lancamento in valores_lancamento) {
            if(valores_lancamento[elemento_lancamento] != ""){
                v_total_lancamento += moeda2float(valores_lancamento[elemento_lancamento].replace("-", "").replace(",", "."))
            }
        }
    
        //Calcula valor total Receita
        var v_total_receita = new Number(0);
        var valores_receita = new Array();
        $("#" + tabela + " tbody tr:visible").each(function () {
            var receita = $(this).children("td:nth-child(5)").text();
            if (valores_receita.indexOf(receita) < 0) {
                valores_receita.push(receita);
            }
        })
        for (elemento_receita in valores_receita) {
            if(valores_receita[elemento_receita] != ""){
                v_total_receita += moeda2float(valores_receita[elemento_receita].replace("-", "").replace(",", "."))
            }
        }
    
        //Calcula valor total Despesa
        var valores_despesa = new Array();
        var v_total_despesa = new Number(0);
        $("#" + tabela + " tbody tr:visible").each(function () {
            var despesa = $(this).children("td:nth-child(6)").text();
            if (valores_despesa.indexOf(despesa) < 0) {
                valores_despesa.push(despesa);
            }
        })
        for (elemento_despesa in valores_despesa) {
            if(valores_despesa[elemento_despesa] != ""){
                v_total_despesa += moeda2float(valores_despesa[elemento_despesa].replace("-", "").replace(",", "."))
            }
        }
    
        $("#valor_lancamento").html(formatReal(v_total_lancamento))
        $("#valor_receita").html(formatReal(v_total_receita))
        $("#valor_despesa").html(formatReal(v_total_despesa))
    
    });
};

$(document).ready(function(){  
    // Limpa todos os dados salvos em localStorage
    localStorage.clear();

    $(".loader").hide();
    $("#carregar").hide();
    $("#exibir").show();
   

    $(document).on('click', '#cmdExport', fcExport);
    $(document).on('click', '#cmdCancelar', fcCancelar);

    
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var min = today.getMinutes();
    var seg = today.getSeconds();
    //data
    if(dd<10) {
        dd = '0'+dd
    } 

    if(mm<10) {
        mm = '0'+mm
    } 
    //hora 
    if(hh<10) {
        hh = '0'+hh
    } 

    if(min<10) {
        min = '0'+min
    } 
    if(seg<10) {
        seg = '0'+seg
    } 

    today = dd + '/' + mm + '/' + yyyy + ' '+hh+':'+min+':'+seg;


    $("#dt_emissao").text(today);

    
    if($("#tipo_lancamento_pk").val()==0){
        $("#ds_tipo_lancamento").text("Receita e Despesa");
    }
    else if($("#tipo_lancamento_pk").val()==1){
        $("#ds_tipo_lancamento").text("Receita");
    }
    if($("#tipo_lancamento_pk").val()==2){
        $("#ds_tipo_lancamento").text("Despesa");
    }

    AdicionarFiltroSelect('tblResultado', 1)
    AdicionarFiltroSelect('tblResultado', 2)
    AdicionarFiltroSelect('tblResultado', 3);
    AdicionarFiltroSelect('tblResultado', 4)
    AdicionarFiltroSelect('tblResultado', 5)
    AdicionarFiltroSelect('tblResultado', 6)
    AdicionarFiltroSelect('tblResultado', 7);
    AdicionarFiltroSelect('tblResultado', 8);
    AdicionarFiltroSelect('tblResultado', 9);
    AdicionarFiltroSelect('tblResultado', 10);
    AdicionarFiltroSelect('tblResultado', 11);
    AdicionarFiltroSelect('tblResultado', 12);
    AdicionarFiltroSelect('tblResultado', 13);
    AdicionarFiltroSelect('tblResultado', 14);
    AdicionarFiltroSelect('tblResultado', 15)
    AdicionarFiltroSelect('tblResultado', 16);
    AdicionarFiltroSelect('tblResultado', 17);
    AdicionarFiltroSelect('tblResultado', 18);
    AdicionarFiltroSelect('tblResultado', 19);
    AdicionarFiltroSelect('tblResultado', 20);
    AdicionarFiltroSelect('tblResultado', 21)

    

});


