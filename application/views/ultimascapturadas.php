<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title><?= $this->lang->line('last_captured'); ?></title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="<?= base_url() ?>includes/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="<?= base_url() ?>includes/css/abas.css">
        <link rel="stylesheet" href="<?= base_url() ?>includes/css/estilo.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="<?= base_url() ?>includes/js/jquery-2.1.1.js"></script> <!-- import jQuery -->
        <script src="<?= base_url() ?>includes/bootstrap/js/bootstrap.min.js"></script> <!-- import bootstrap js -->
        <script src="<?= base_url() ?>includes/js/highcharts.js"></script><!-- import Highcharts -->
        <script src="<?= base_url() ?>includes/js/exporting.js"></script><!-- import Export Highcharts -->
        <script src="<?= base_url() ?>includes/js/jquery.dataTables.min.js"></script><!-- import jQuery DataTables -->
        <script type="text/javascript" src="<?= base_url() ?>includes/js/graficosdetalhes.js"></script><!-- import gráficos linha e barra -->
        <script type="text/javascript">
            $(document).ready(function () {
                var cont = 0;
                var graficos = [];
                $('input[type="checkbox"]').click(function () {
                    var id = $(this).attr('id'); //pega o id do checkbox clicado
                    var nome = $(this).attr('name'); //pega o nome do checkbox clicado
                    // se fone comparar coluna comparar
                    if (nome === "comparar") {
                        var checkado = ($("#" + id).is(':checked')); //verifica se o checkbox foi clicado true == sim, false == não

                        if (checkado === true) { // se checkado == true monta gráfico na área de comparação
                            //ajax envia os dados p/ php e no php processa e retornar valores em dados.linha e dados.barra
                            if (cont < 5) {
                                graficos[cont] = id;
                                $.ajax({
                                    url: "<?= base_url(); ?>" + "index.php/ultimascapturadas/graficos",
                                    dataType: 'json',
                                    scriptCharset: 'UTF-8',
                                    type: "POST",
                                    data: {
                                        action: 'checkDados',
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
                                                data: dados.linha
                                            });
                                            cont = ++cont;
                                        } else
                                            alert("Erro Ajax");
                                    }
                                });
                            } else {
                                alert("Máximo de 5 Equipamentos Atingido");
                                $("#" + id).attr("checked", false);
                            }
                        } else { // senão oculta gráfico do equipamento

                            cont = --cont;
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
                    }
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#tabelaUltimasCap').dataTable({
                    //"aaSorting": [[1, 'desc']],
                    //"bSortCellsTop": false,
                    //"sDom": '<"top"f>rt<"bottom"ip><"clear">'

                });
            });
        </script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12" id="centro">
                    <div class="row-fluid menu">
                        <?php include ("menu.php"); ?>
                        <a href="<?= base_url() ?>index.php/login/logout"><img id="sair" src="<?= base_url() ?>includes/imagens/deslogar.png"/></a>
                    </div>
                    <div id="aba">
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="span5" style="overflow:auto;">
                                    <table id="tabelaUltimasCap" class="table table-striped table-bordered detalhes">
                                        <thead>
                                            <tr>
                                                <th><?= $this->lang->line('capture'); ?></th>
                                                <th><?= $this->lang->line('plug'); ?></th>
                                                <th><?= $this->lang->line('kinds_of_wave'); ?></th>
                                                <th><?= $this->lang->line('equipment'); ?></th>
                                                <th><?= $this->lang->line('events'); ?></th>
                                                <th><?= $this->lang->line('effective'); ?></th>
                                                <th><?= $this->lang->line('date'); ?></th>
                                                <th><?= $this->lang->line('compare'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>									
                                            <?php if (empty($uc)) { ?>
                                                <tr>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><?= $this->lang->line('empty'); ?></td>
                                                    <td><input type="checkbox"/></td>
                                                </tr>
                                                <?php
                                            } else {
                                                foreach ($uc as $dados) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $dados->codCaptura; ?></td>
                                                        <td><?= $dados->codTomada; ?></td>
                                                        <td><?= $dados->codTipoOnda; ?></td>
                                                        <td><?= $dados->codEquip; ?></td>
                                                        <td><?= $dados->codEvento; ?></td>
                                                        <td><?= substr($dados->eficaz, 0, 6); ?></td>
                                                        <td><?= date('d/m/Y H:m:s', strtotime($dados->dataAtual)); ?></td>
                                                        <?= '<td><input type="checkbox" id="' . $dados->codCaptura . '" name="comparar"></td>' ?>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="span7">
                                    <div id="linha"></div>
                                    <div id="barra"></div>
                                </div>
                            </div>	
                        </div>
                    </div>	
                    <div class="row-fluid">
                        <div class="span12" id="graficoslinha"></div>
                    </div>
                    <div class="row-fluid"><?php include("footer.php"); ?></div>
                </div>
            </div>
        </div>
    </body>
</html>