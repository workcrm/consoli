<?php 
###########
## Módulo para Resposta de recados POSTAGEM
## Criado: 17/06/2007 - Maycon Edinger
## Alterado:
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para Resposta de recados POSTAGEM
* @author Maycon Edinger
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
//header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

// Processa as diretivas de segurança 
require("Diretivas.php");

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";
?>

<body>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Envio de Recados - Responder Recado</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

      <table id='2' width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='750' class='text'>

          <?php
            if($_POST['Submit']){

            $edtEmpresaId = $empresaId;
						$edtRemetenteId = $usuarioId;
            $edtDestinatarioId = $_POST["cmbDestinatario"];
            $edtData = DataMySQLInserir($_POST["edtData"]);
            $edtAssunto = $_POST["edtAssunto"];
            $edtMensagem = $_POST["edtMensagem"];

    	    	$sql = mysql_query("
                INSERT INTO recados (
								empresa_id, 
								remetente_id, 
								destinatario_id, 
								data, 
								assunto, 
								mensagem
								
								) VALUES (
				
								'$edtEmpresaId',
								'$edtRemetenteId',
								'$edtDestinatarioId',
								'$edtData',
								'$edtAssunto',
								'$edtMensagem'
								);");
								
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Recado respondido com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
					}
        ?>
				<input class="button" title="Retorna ao Portal" name='btnVoltar' type='button' id='btnVoltar' value='Retornar ao Meu Portal' onclick="wdCarregarFormulario('MeuPortal.php','conteudo')" />
     </td>
   </tr>
</table>  	 

</tr>
</table>
