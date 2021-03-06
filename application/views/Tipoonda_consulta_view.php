<div class="container-fluid">
    <div class="row-fluid">
        <div class="col-xs-10 col-xs-offset-1" id="centro">
            <ul class="abas">
                <li id="consulta" style="background-color: #A9A9A9;"><a href="<?php echo base_url('index.php/tipoonda') ?>" ><?php echo $this->lang->line('consult'); ?></a></li>
                <li id="cadastro"><a href="<?php echo base_url('index.php/tipoonda?link=cadastro') ?>" ><?php echo $this->lang->line('cadastre'); ?></a></li>
            </ul>
            <div id="aba1">
                <div class="row-fluid">
                    <div class="col-xs-10 col-xs-offset-1" id="formcentro">
                        <h2><?php echo $this->lang->line('table_title_kind_of_wave'); ?></h2>
                        <table id="myTable" class="table table-striped table-bordered sortable">
                            <thead>
                                <tr>
                                    <th  class="col1">
                                        <img id="mostrar1" src="<?php echo base_url('includes/imagens/lupa.png') ?>" />
                                        <?php echo $this->lang->line('actions'); ?>
                                        <img id="filtro1" src="<?php echo base_url('includes/imagens/filter.png') ?>" /><br/>
                                        <input type="text" id="txtColuna1" class="input-search" alt="sortable"/>
                                        <button id="fechar1" ><?php echo $this->lang->line('close'); ?></button>
                                    </th>
                                    <th  class="col2">
                                        <img id="mostrar2" src="<?php echo base_url('includes/imagens/lupa.png') ?>" />
                                        <?php echo $this->lang->line('code'); ?>
                                        <img id="filtro2" src="<?php echo base_url('includes/imagens/filter.png') ?>" /><br/>
                                        <input type="text" id="txtColuna2" class="2">
                                        <button id="fechar2" ><?php echo $this->lang->line('close'); ?></button>
                                    </th>
                                    <th  class="col3">
                                        <img id="mostrar3" src="<?php echo base_url('includes/imagens/lupa.png') ?>" />
                                        <?php echo $this->lang->line('description'); ?>
                                        <img id="filtro3" src="<?php echo base_url('includes/imagens/filter.png') ?>" /><br/>
                                        <input type="text" id="txtColuna3" class="3">
                                        <button id="fechar3" ><?php echo $this->lang->line('close'); ?></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($tipoonda as $dados) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo base_url('') ?>index.php/tipoonda/apagar_tipoonda/<?php echo $dados->codTipoOnda; ?>" onClick="return confirm('<?php echo $this->lang->line('msg_confirm_delete') . " " . $this->lang->line('kinds_of_wave') . " " . $this->lang->line('code') . ": " . $dados->codTipoOnda; ?>?')">
                                                <img src="<?php echo base_url('includes/imagens/delete.png') ?>"></a>
                                            <a href="<?php echo base_url('') ?>index.php/tipoonda/editar_tipoonda/<?php echo $dados->codTipoOnda; ?>">
                                                <img src="<?php echo base_url('includes/imagens/edit.png') ?>"></a>
                                        </td>
                                        <td><a href="<?php echo base_url('') ?>index.php/tipoonda/editar_tipoOnda/<?php echo $dados->codTipoOnda; ?>"><?php echo $dados->codTipoOnda; ?></a></td>
                                        <td><a href="<?php echo base_url('') ?>index.php/tipoonda/editar_tipoonda/<?php echo $dados->codTipoOnda; ?>"><?php echo $dados->desc; ?></a></td>
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        echo!empty($paginacao) ? $paginacao : '';
                        if ($this->session->flashdata('msg')) {
                            echo $this->session->flashdata('msg');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>