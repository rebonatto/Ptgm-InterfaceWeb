<!--
* 2016  View
* Desenvolvido por: Leonardo Francisco Rauber
* Email: leorauber@hotmail.com - 132789@upf.br
* Projeto de conclusão de curso
* UPF - Ciência da Computação
-->

<br>
<div class="container-fluid" >

    <div class="row">
        <div class="col-md-12 col-xs-12" id="centro">
            <div id="aba">
                <div class="row">   <!-- incluir botoes-->
                    <div class="col-xs-10 ">
                        <p>
                            <b>
                                <?php echo $this->lang->line('update'); ?>
                                <input id="atualiza" onclick="controleAtualizar()" type="checkbox" checked="true"> &emsp;

                                <?php echo $this->lang->line('phase'); ?>
                                <input id="fase" onclick="fase()" type="checkbox" checked="true">&emsp;

                                <?php echo $this->lang->line('leakage'); ?>
                                <input id="fuga" onclick="fuga()" type="checkbox" checked="true">&emsp;

                                <?php echo $this->lang->line('visualization'); ?>
                                <input id="visualization" onkeyup="visualization()" type="number" value="4" min="2" max="8" style="width:80px;">&emsp;

                                <?php echo $this->lang->line('fft'); ?>
                                <input id="fft" onclick="mostraFFT()" type="checkbox" >
                            </b>
                        </p>
                    </div>

                    <div class="col-xs-6">

                        <div style="overflow:auto; height:400px;">
                            <table id="tableUltimascapturas" class="table  table-condensed" style="font-size: 10pt; text-align: center; ">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('show'); ?></th>
                                    <th><?php echo $this->lang->line('capture'); ?></th>
                                    <th><?php echo $this->lang->line('plug'); ?></th>
                                    <th><?php echo $this->lang->line('equipment'); ?></th>
                                    <th><?php echo $this->lang->line('effective'); ?></th>
                                    <th><?php echo $this->lang->line('use'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php //echo $this->lang->line('dangerousness'); ?></th>
                                    <th><?php echo $this->lang->line('compare'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="tbodyUltimasCapturas"></tbody>
                            </table>
                        </div>
                        <div>
                            <div class="legenda">

                                <div class="line-fase"></div>
                                <span>Fase</span>

                                <div class="line-fuga"></div>
                                <span>Fuga</span>

                                <div class="green-circle"></div>
                                <span> Baixa periculosidade</span>

                                <div class="yellow-circle"></div>
                                <span>Média periculosidade</span>

                                <div class="red-circle"></div>
                                <span>Alta periculosidade</span>

                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div id="linha"></div>
                        <div id="barra"></div>
                        <div class="col-md-offset-1 col-md-10 col-xs-10">
                            <table class='table table-striped table-bordered' id="tabelaSimilaridade">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('capture_code'); ?></th>
                                    <th colspan="5"><?php echo $this->lang->line('similarity'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="tbodyTabelaSimilaridade"></tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>

            <div class="row-fluid col-md-12 col-xs-12" id="divMain" >

            </div>
        </div>
    </div>

</div>
<script> //pega a baseURL
    function getURL() {
        return location.origin + "/" + window.location.pathname.split('/')[1] + "/";
    }
</script>
<script type="text/javascript"> //pega os dados do banco e coloca na tabela a cada 1s
    var j = 0, cont = 0, checkClicados = 0, lock = 0, atualizando = 0;
    var firstCap = 0, lastCapture, ultimoCodCaptura, qntLines = 0, limitGraf = 4, contGraf = 1;
    var timeUpdate;
    var fasefuga = "fasefuga";
    var graficos = [];
    var baseUrl = getURL();
    window.onload = function () {
        ultimaCaptura();
    };

    function initFaseFuga(){
        $("#divMain").empty();

        if(graficos.length > 0){
            for (var x = checkClicados-1; x >= 0; x--) {
                var chart = $('#barra').highcharts();
                chart.series[x].remove();
                var chart = $('#linha').highcharts();
                chart.series[x].remove();
                graficos.splice(x, 1);
                document.getElementById("tabelaSimilaridade").deleteRow(x+1);
            }
            document.getElementById("tabelaSimilaridade").deleteRow(1);
            checkClicados = 0;
        }

        window.setTimeout(function(){
            qntLines = lock = 0;
            contGraf = 0;
            lastCapture = ultimoCodCaptura = firstCap;
            $('#tbodyUltimasCapturas').empty();
            atualizaTable();
            atualizando = 0;
            $("#fuga").prop("disabled","");
            $("#fase").prop("disabled","");
        }, 1010);

    }

    function atualizaTable() {

        $.ajax({
            type: 'post',
            dataType: 'json', //tipo de retorno
            url: baseUrl + "index.php/Ultimascapturadas/atualizaTable" + fasefuga, //arquivo onde serão buscados os dados
            data: {
                Captura: ultimoCodCaptura
            },
            success: function (dados) {
                if (dados) {
                    dados = dados.slice(0).reverse();
                    j = 0;
                    if (lock === 1 ){
                        var i = 0;
                        while(true){
                            if (lastCapture !== dados[i].codCaptura) j++;
                            else {
                                j++;
                                break;
                            }
                            i++;
                        }// alert(lastCapture + " valor lastCapture antes");
                    }// alert(j + " valor do j " + dados.length + " tamanho do dados");
                    while (j < dados.length) {
                        if (lock === 1){
                            insereLinha(dados[j]);
                            $(".w" + dados[j].codCaptura).prop("checked", true);
                            $(".w" + dados[j-limitGraf].codCaptura).prop("checked", false);
                            geraGraficoPrimeiraLinha(dados[j].codCaptura, 0);
                        }else {
                            insereLinha(dados[j]);
                        }
                        lastCapture = dados[j].codCaptura;
                        j++;
                    }// alert(lastCapture + " valor lastCapture depois");
                    if(lock === 0) {
                        geraGraficoInit();
                        lock = 1;
                    }
                    if(dados.length > 11){
                        ultimoCodCaptura = dados[j-11].codCaptura;
                    }
                    if(atualizando === 0){
                        timeUpdate = window.setTimeout(atualizaTable, 1000);
                    }
                }
                else {
                    alert("Erro Ajax.");
                }
            }
        });
    }

    function geraGraficoInit(){
        if (qntLines > limitGraf){
            for(var i = limitGraf-1; i >= 0; i--){
                $("#checkboxequip" + (qntLines - i)).prop("checked", true);
                var captura = $("#checkboxequip" + (qntLines - i)).attr("class");
                var cap = captura.substring(1 , captura.length);
                geraGraficoPrimeiraLinha(cap, 0);
            }
        } else {
            for(var i = qntLines-1; i >= 0; i--){
                $("#checkboxequip" + (qntLines - i)).prop("checked", true);
                var captura = $("#checkboxequip" + (qntLines - i)).attr("class");
                var cap = captura.substring(1 , captura.length);
                geraGraficoPrimeiraLinha(cap, 0);
            }
        }
    }

    function geraGraficoPrimeiraLinha(codCaptura, f) {
        var classe = codCaptura;

        if ((f === 0 || f === 1) && document.getElementById("divlinha"+classe) === null ){
            var divMain = document.getElementById("divMain");
            var div = document.createElement("div");
            div.id = "container" + classe;
            div.className = "col-md-3";
            var divlinha = document.createElement("div");
            divlinha.id = "divlinha" + classe;
            divlinha.className = "divlinha";
            divlinha.style = "height : 350px";
            div.appendChild(divlinha);
            if (f === 0){ divMain.insertBefore(div, divMain.firstChild); }
            if (f === 1){ divMain.appendChild(div); }
        }

        var $divCont = $('#divlinha' + classe);
        $.ajax({
            url: baseUrl + "index.php/Ultimascapturadas/linha", //requisita novo gráfico
            dataType: 'json',
            scriptCharset: 'UTF-8',
            type: "POST",
            data: {
                Captura: classe
            },
            success: function (dados) {
                if (dados) {
                    $divCont.highcharts({
                        chart: {
                            type: 'spline',
                            spacingBottom: 0,
                        },
                        legend: {
                            enabled: false
                        },
                        title: {
                            text: 'Cap ' + classe
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title:{
                                text: "<?php echo $this->lang->line('time'); ?>/100 (ms)"
                            },
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '8px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            },
                            tickPositions: [0,104,201, 305, 403,501,605,703,800,904,1002,1100,1204,1302,1406,1503,1601]
                        },
                        yAxis: {
                            title: {
                                text: 'Corrente (mA)'
                            }
                        },
                        series: [{data: dados.linha}]
                    });
                } else
                    alert("Erro Ajax.");
            }
        });

        setTimeout(function(){
            if ($("#divlinha"+classe).is(':empty')){
                recriaGraf(classe);
            }
        },1000);

        if($("#fft").prop("checked"))
            insereFFT(classe);
        if (f === 0){
            if(contGraf >= limitGraf) {
                $(divMain.lastChild).remove();
            }
            else contGraf++;
        }
    }

    function recriaGraf(classe){
        var $divCont = $('#divlinha' + classe);
        if(document.getElementById("divlinha" + classe)){
            $.ajax({
                url: baseUrl + "index.php/Ultimascapturadas/linha", //requisita novo gráfico
                dataType: 'json',
                scriptCharset: 'UTF-8',
                type: "POST",
                data: {
                    Captura: classe
                },
                success: function (dados) {
                    if (dados) {
                        $divCont.highcharts({
                            chart: {
                                type: 'spline',
                                spacingBottom: 0,
                            },
                            legend: {
                                enabled: false
                            },
                            title: {
                                text: 'Cap ' + classe
                            },
                            credits: {
                                enabled: false
                            },
                            xAxis: {
                                title:{
                                    text: 'Tempo/100 (ms)'
                                },
                                labels: {
                                    rotation: -45,
                                    style: {
                                        fontSize: '8px',
                                        fontFamily: 'Verdana, sans-serif'
                                    }
                                },
                                tickPositions: [0,104,201, 305, 403,501,605,703,800,904,1002,1100,1204,1302,1406,1503,1601]
                            },
                            yAxis: {
                                title: {
                                    text: 'Corrente (mA)'
                                }
                            },
                            series: [{data: dados.linha}]
                        });
                    } else
                        alert("Erro Ajax.");
                }
            });
        }
    }

    function criaGraficoEquipamento() {
        var classeI = $(this).attr('class'); //pega a classe do checkbox clicado, contendo o codigo da captura
        var classe = classeI.substring(1, classeI.length);
        var check = $(".w" + classe).prop("checked");

        if (check) {
            geraGraficoPrimeiraLinha(classe, 1);
        }
        else {
            $('#container'+classe).remove();
        }
    }

    function controleAtualizar(){
        if($("#atualiza").prop("checked")){
            atualizando = 0;
            for(var i = qntLines; i >= 0; i--){
                $('#checkboxequip'+i).prop("disabled",true);
                $('#checkboxequip'+i).prop("checked", false);
            }
            $("#divMain").empty();
            contGraf = 0;
            atualizaTable();
            geraGraficoInit();
        } else {
            for(var i = qntLines; i >= 0; i--){
                $('#checkboxequip'+i).prop("disabled",false);
            }
            atualizando = 1;
        }
    }

    function newTd(texto) {
        var tdElement = document.createElement("td");
        var textElement = document.createTextNode(texto);
        tdElement.appendChild(textElement);
        return tdElement;
    }

    function insereLinha(dados) {
        qntLines++;//acrescenta uma linha

        var tblBody = document.getElementById("tbodyUltimasCapturas");
        var row = document.createElement("tr");
        row.id = "linha" + dados.codCaptura;
        row.className = "linha"+qntLines;

        if (dados.codEvento === "1") {
            row.className = "fuga";
        }
        if (dados.codEvento === "4") {
            row.className = "fase";
        }
        if (dados.codEvento === "9") {
            row.className = "cExtFase";
        }
        if (dados.codEvento === "10") {
            row.className = "cExtFuga";
        }

        var mostrChk = document.createElement('input');
        mostrChk.type = "checkbox";
        mostrChk.className = "w" + dados.codCaptura;
        if ($("#atualiza").prop("checked")){
            mostrChk.disabled = "true";
        }
        mostrChk.id = "checkboxequip"+qntLines;
        mostrChk.onclick = criaGraficoEquipamento;
        var td = newTd('');
        td.appendChild(mostrChk);
        row.appendChild(td);

        row.appendChild(newTd(dados.codCaptura));
        row.appendChild(newTd(dados.CodTomada));
        row.appendChild(newTd(dados.CodEquip));
        row.appendChild(newTd(dados.eficaz));
        row.appendChild(newTd(dados.tempoUso));
        row.appendChild(newTd(dados.dataAtual));

        var cell = document.createElement("td");
        if (dados.codEvento === "1" || dados.codEvento === "10"){
            cell.id = "periculosidade" + dados.codCaptura;
            var div = document.createElement("div");
            periculosidade(dados, div);
            cell.appendChild(div);
        }
        row.appendChild(cell);

        var compararChk = document.createElement('input');
        compararChk.type = "checkbox";
        compararChk.name = "comparar";
        compararChk.className = dados.CodEquip;
        compararChk.id = dados.codCaptura;
        compararChk.onclick = criaGraficoBarraLinha;
        td = newTd('');
        td.appendChild(compararChk)
        row.appendChild(td);

        tblBody.insertBefore(row, tblBody.firstChild);
    }

    function ultimaCaptura() {

        $.ajax({
            type: 'post',
            dataType: 'json', //tipo de retorno
            url: baseUrl + "index.php/Ultimascapturadas/ultimaCaptura", //arquivo onde serão buscados os dados
            success: function (dados) {
                if (dados) {

                    dados = dados.slice(0).reverse();
                    ultimoCodCaptura = dados[0].codCaptura-1;

                    firstCap = ultimoCodCaptura;
                    atualizaTable();
                } else {
                    alert("Erro Ajax.");
                }
            }
        });
    }

    function mostraFFT(){
        $("#fft").prop("disabled","false");
        var checkFFT = $("#fft").prop("checked");
        if(checkFFT){
            atualizando = 1;
            window.setTimeout(iniciaFFT, 1000);
            if(!($('#atualiza').prop("checked"))){
                window.setTimeout(controleAtualizar, 2000);
            }
            if ($("#atualiza").prop("checked")){ atualizando = 0; }
        } else {
            $('.divbarra').remove();
            $("#fft").prop("disabled","");
        }
    }

    function iniciaFFT() {
        $.ajax({
            type: 'post',
            dataType: 'json', //tipo de retorno
            url: baseUrl + "index.php/Ultimascapturadas/ultimaCaptura", //arquivo onde serão buscados os dados
            data: {
                PrimeiraCaptura: firstCap
            },
            success: function (dados) {
                if (dados) {
                    for(var i = 0 ; i < dados.length; i++){
                        if($(".w"+dados[i].codCaptura).prop("checked")){
                            insereFFT(dados[i].codCaptura);
                        }
                    }
                    $("#fft").prop("disabled","");
                } else {
                    alert("Erro Ajax.");
                }
            }
        });
    }

    function insereFFT(codCaptura){
        var div = document.getElementById("container"+codCaptura);
        var divbarra = document.createElement("div");
        divbarra.id = "divbarra" + codCaptura;
        divbarra.name = "divbarra";
        divbarra.className = "divbarra";
        divbarra.style = "height : 350px";
        div.appendChild(divbarra);

        var $divCont = $('#divbarra' + codCaptura);

        $.ajax({
            url: baseUrl + "index.php/Ultimascapturadas/barra", //requisita novo gráfico
            dataType: 'json',
            scriptCharset: 'UTF-8',
            type: "POST",
            data: {
                Captura: codCaptura
            },
            success: function (dados) {
                if (dados) {
                    $divCont.highcharts({
                        chart: {
                            type: 'column',
                            spacingBottom: 0
                        },
                        title: {
                            text: 'Cap ' + codCaptura
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: ['DC', '60', '120', '180', '240', '300', '360', '420', '480', '540', '600', '660', '720'],
                            title: {
                                text: 'Frequencias (Hz)'
                            },
                            labels: {
                                rotation: -45,
                                style: {
                                    fontSize: '8px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Módulo Normalizado (mA)'
                            }
                        },
                        tooltip: {
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{data: dados.barra}]
                    });
                } else
                    alert("Erro Ajax.");
            }
        });
    }

    function criaGraficoBarraLinha() {
        var id = $(this).attr('id'); //pega o id do checkbox clicado, contendo o código de captura
        var checkadoID = document.getElementById(id).checked; //verifica se o checkbox foi clicado true == sim, false == não

        if (checkadoID === true) { // se checkado == true monta gráfico na área de comparação
            //ajax envia os dados p/ php e no php processa e retornar valores em dados.linha e dados.
            if (checkClicados < 5) {
                graficos[checkClicados] = id;
                $.ajax({
                    url: baseUrl + "index.php/Ultimascapturadas/graficos",
                    dataType: 'json',
                    scriptCharset: 'UTF-8',
                    type: "POST",
                    data: {
                        idCheckbox: id
                    },
                    success: function (dados) {
                        if (dados) {
                            var chart = $('#barra').highcharts();
                            chart.addSeries({
                                name: id,
                                data: dados.barra
                            });
                            var chart = $('#linha').highcharts();
                            chart.addSeries({
                                name: id,
                                data: dados.linha
                            });
                        } else
                            alert("Erro Ajax.");
                    }
                });
                checkClicados++;
            } else {
                alert("Máximo de 5 Equipamentos Atingido");
                $("#" + id).attr("checked", false);
            }

        } else { // senão oculta gráfico do equipamento

            for (var x = 0; x < graficos.length; x++) {
                if (graficos[x] === id) {
                    var chart = $('#barra').highcharts();
                    if (chart.series.length) {
                        chart.series[x].remove();
                    }
                    var chart = $('#linha').highcharts();
                    if (chart.series.length) {
                        chart.series[x].remove();
                    }
                    graficos.splice(x, 1);
                }
            }
        }

        //para construir a tabela de similaridade
        if (checkadoID === true) {
            mostraTabelaSimilaridade();
        } else {
            if (checkClicados === 0) {
                document.getElementById("tabelaSimilaridade").deleteRow(1);
            } else {
                mostraTabelaSimilaridade();
            }
        }
    } //OK

    function visualization(){
        var valVizu = $("#visualization").val();
        if (valVizu > 1 && valVizu < 9){
            atualizando = 1;
            window.clearTimeout(timeUpdate);
            window.setTimeout(function(){
                limitGraf = $("#visualization").val();
                for(var i = qntLines; i >= 0; i--){
                    $('#checkboxequip'+i).prop("checked", false);
                }
                $("#divMain").empty();
                contGraf = 0;
                geraGraficoInit();
                atualizando = 0;
                atualizaTable();
            }, 1010);
        } else {
            $("#visualization").val(limitGraf);
        }
    }

    function fase(){
        atualizando = 1;
        window.clearTimeout(timeUpdate);
        $("#fase").prop("disabled","false");
        $("#fuga").prop("disabled","false");

        if ($("#fase").prop("checked")){
            initFaseFuga();
            fasefuga = "fasefuga";
        }else {
            if ($("#fuga").prop("checked")){
                initFaseFuga();
                fasefuga = "fuga";
            } else {
                alert("Deve deixar 1 selecionado.");
                $("#fase").prop("checked", true);
                $("#fase").prop("disabled","");
                $("#fuga").prop("disabled","");
            }
        }
    }

    function fuga(){
        atualizando = 1;
        window.clearTimeout(timeUpdate);
        $("#fuga").prop("disabled","false");
        $("#fase").prop("disabled","false");

        if ($("#fuga").prop("checked")){
            initFaseFuga();
            fasefuga = "fasefuga";
        }else {
            if ($("#fase").prop("checked")){
                initFaseFuga();
                fasefuga = "fase";
            } else {
                alert("Deve deixar 1 selecionado.");
                $("#fuga").prop("checked", true);
                $("#fase").prop("disabled","");
                $("#fuga").prop("disabled","");
            }
        }
    }

    function periculosidade(dados, div) {
        div.className = "green-circle center-cicle";
        if (dados.eficaz >= 0.1 && dados.eficaz < 0.5) {
            //atenção
            div.className = "yellow-circle center-cicle";
        } else if (dados.eficaz >= 0.5) {
            //perigo
            div.className = "red-circle center-cicle";
        }
    }
</script>
<script>
    function mostraTabelaSimilaridade() {

        var baseUrl = getURL();
        //pega os checkboxes clicados em um array, a ordenação não é por clique e sim por leitura dos clicados, de cima para baixo
        var checkboxSelecionados = [];
        $('#aba input[name="comparar"]:checked').each(function () {
            checkboxSelecionados.push($(this).attr('id'));
        });
        //envia por post um json contendo os códigos de capturas dos checkboxes
        //recebendo o retorno da tabela preenchida
        $.ajax({
            url: baseUrl + "index.php/Ultimascapturadas/tabelaSimilaridade",
            dataType: 'json',
            scriptCharset: 'UTF-8',
            type: "POST",
            data: {
                Check: checkboxSelecionados
            },
            success: function (dados) {
                if (dados) {
                    //insere na tabela os dados calculados
                    document.getElementById("tbodyTabelaSimilaridade").innerHTML = dados;
                } else {
                    alert("Erro Ajax.");
                }
            }
        });
    }

</script>