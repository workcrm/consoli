<?php
###########
## Módulo para Gerenciamento de fornecedores do foto e video do evento
## Criado: 03/09/2013 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Gerenciamento de Fornecedores de Pedido de Foto e Vídeo</span>
          </td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td> 					
      <span style="color: #990000"><b>Informe o Código do Pedido:</b></span>
      <br/>
      <br/>
      <input name="edtPedidoId" id="edtPedidoId" type="text" class="datafield" style="width: 45px" maxlength="6" onkeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' />      	
    </td>
  </tr>	
  <tr>
    <td style="padding-top: 10px;">
      <input class="button" value="Iniciar Gerenciamento" name="btnGerencia" type="button" id="btnGerencia" onclick="if (document.getElementById('edtPedidoId').value == 0){alert('É necessário informar um pedido !')} else {wdCarregarFormulario('ModuloFotoVideoFornecedoresGerencia.php?PedidoId=' + document.getElementById('edtPedidoId').value,'gerencia')}" />
    </td>
  </tr>
  <tr>
    <td>
      <br/>
      <div id="gerencia"></div>
    </td>
  </tr>
</table>
<?php

  if ($PedidoId)
  {

    ?>
    <script>
      document.getElementById('btnGerencia').click();
    </script>
    <?php

  }

?>