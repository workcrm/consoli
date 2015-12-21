<?php 
###########
## Módulo para exibição de recados
## Criado: 18/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo exibição dos recados
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Recebe o ID do recado a exibir               
$RecadoId = $_GET["RecadoId"];

//Monta a query de consulta do recado
$sql = "	SELECT 
		rec.id, 
		rec.empresa_id, 
		rec.remetente_id, 
		rec.destinatario_id, 
		rec.data, 
		rec.assunto, 
		rec.mensagem,
		rec.lido, 
		usu.nome as remetente_nome, 
		usu.sobrenome as remetente_sobrenome 
		FROM recados rec 
		LEFT OUTER JOIN usuarios usu ON rec.remetente_id = usu.usuario_id 
		WHERE rec.id = '$RecadoId'";

//Executa a query			
$resultado = mysql_query($sql);
//Monta o array associativo dos dados retornados
$campos = mysql_fetch_array($resultado);

//Processa a alteração do recado, marcando-o como LIDO
//Verifica se o recado não é global ou já está lido
if ($campos['lido'] == 0) {
  $recado_lido = mysql_query("UPDATE recados SET lido = '1' WHERE id = '$RecadoId'");
};
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Visualização do Recado </span></td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>
	
<table id="2" width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='750' class="text">
      <TABLE cellSpacing=0 cellPadding=0 width="400" border=0>
      <TBODY>
        <tr>
          <td width="140" style="PADDING-BOTTOM: 2px">
 	        	<input name='responde' type='button' class=button id="Submit" accessKey='N' title="Responde o Recado Exibido [Alt+R]" onClick='window.location="sistema.php?ModuloNome=RecadoResponde&RecadoId=<?php echo $campos[id] ?>"' value='Responder Recado'>
          </td>

	      	<td style="PADDING-BOTTOM: 2px">
	        	<input name='novo' type='button' class=button id="Submit" accessKey='N' title="Redige um novo recado [Alt+N]" onClick='window.location="sistema.php?ModuloNome=RecadoCadastra"' value='Novo Recado'>
					</TD>
	  		</TR>
    </TBODY>
    </TABLE>
           
    <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar redigir um novo recado, clique em [Novo Recado] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Remetente:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
			   			 <?php echo $campos[remetente_nome] . " " . $campos[remetente_sobrenome] ?>			   
             </TD>
          </TR>
           
           <TR>
             <TD class='dataLabel'>Data:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <?php echo DataMySQLRetornar($campos[data]) ?>             
			 			 </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Assunto:</TD>
             <TD colspan='3' valign="middle" class=tabDetailViewDF>
               <?php echo $campos[assunto] ?>             
			 			 </TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>Mensagem:</TD>
             <TD valign="top" colspan="3" class=tabDetailViewDF>
				  	   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                   <td valign="top">					  					  
					 				   <?php echo $campos[mensagem] ?>					  
					  			 </td>
                 </tr>
               </table>
			 			 </TD>
           </TR>
         </TBODY>
	   </TABLE>
	   <br />
	   <?php
	   //Monta a mensagem para informar o status do recado para o usuário
	   //Se for um recado global do sistema
	   if ($campos['lido'] == 2 ) {
	   $status_recado = "<img src='image/bt_recado_global.gif' alt='Recado automático gerado pelo sistema' /> Esta é uma mensagem originada automaticamente pelo Sistema. O status não será alterado"; 
	   } else {
	   //Caso seja um recado normal
	   $status_recado = "<img src='image/bt_recado_lido.gif' alt='Recado lido' /> Este recado foi assinalado como LIDO";
	   };
	   //Imprime o status na tela
	   echo $status_recado;
	   ?>
	  </td>
	</tr>
  </table>  	 
  </td>
</tr>
</table>

</td>
