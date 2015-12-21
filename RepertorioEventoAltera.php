<?php 
###########
## M�dulo para altera��o de repertorio do evento
## Criado: 09/10/2007 - Maycon Edinger
## Alterado:
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup da tabela de categoria de repertorio
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_repertorio WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Monta o lookup da tabela de musicas
//Monta o SQL
$lista_musica = "SELECT * FROM musicas WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_musica = mysql_query($lista_musica);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
//Fun��o que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitRepertorioEventoAltera() {
   var Form;
   Form = document.frmRepertorioEventoAltera;
   
	 if (Form.cmbCategoriaId.value == 0) {
      alert("� necess�rio selecionar um Momento de Repert�rio para o Evento !");
      Form.cmbCategoriaId.focus();
      return false;
   }
	 if (Form.cmbMusicaId.value == 0) {
      alert("� necess�rio selecionar uma M�sica para o Repert�rio do Evento !");
      Form.cmbMusicaId.focus();
      return false;
   }  
   return true;
}
</script>

<FORM name='frmRepertorioEventoAltera' action='sistema.php?ModuloNome=RepertorioEventoAltera' method='post' onSubmit='return wdSubmitRepertorioEventoAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Altera��o de Repert�rio do Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

      <table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='100%' class='text'>

          <?php
						//Verifica se a flag est� vindo de uma postagem para liberar a altera��o
            if($_POST['Submit']){

						//Recupera os valores do formulario e alimenta as vari�veis
						$id = $_POST["Id"];
          	$cmbCategoriaId = $_POST["cmbCategoriaId"];
          	$cmbMusicaId = $_POST["cmbMusicaId"];
          	$edtObservacoes = $_POST["edtObservacoes"];

						//Executa a query de altera��o da conta
    	    	$sql = mysql_query("UPDATE eventos_repertorio SET 
																categoria_repertorio_id = '$cmbCategoriaId', 
																musica_id = '$cmbMusicaId',
																observacoes = '$edtObservacoes'
																WHERE id = '$id' ");			 

				//Exibe a mensagem de altera��o com sucesso
        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Repert�rio do Evento alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
        	}

        //RECEBENDO OS VALORES PARA A ALTERA�AO DO REGISTRO
				//Captura o id 
        if ($_GET["Id"]) {
					$RepertorioId = $_GET["Id"];
					$EventoId = $_GET["EventoId"];
				} else {
				  $RepertorioId = $_POST["Id"];
				  $EventoId = $_POST["EventoId"];
				}
				
				//Monta o sql para busca
        $sql = "SELECT * FROM eventos_repertorio WHERE id = $RepertorioId";

        //Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
        $campos = mysql_fetch_array($resultado);
					           					
			?>

        <TABLE cellSpacing='0' cellPadding='0' width='100%' border='0'>
          <tr>
            <td width="100%"> </td>
          </TR>
          <tr>
	        	<TD style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $RepertorioId ?>" />
	        		<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
            	<INPUT name='Submit' type='submit' class=button accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Repert�rio'>
            	<INPUT class=button title="Cancela as altera��es efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Altera��es'>
           	</TD>
           	<TD width="36" align=right>
							<input class="button" title="Retorna ao cadastro de repert�rio do evento" name='btnVoltar' type='button' id='btnVoltar' value='Retornar ao Repert�rio do Evento' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
						</TD>
	       	</TR>
        </TABLE>
           
         <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Repert�rio do evento e clique em [Salvar Repert�rio] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='20%'>
               <span class="dataLabel">Momento do Repert�rio:</span>             </TD>
             <TD colspan='3' class=tabDetailViewDF>
               <select name="cmbCategoriaId" id="cmbCategoriaId" style="width:350px">
                 	<?php while ($lookup_categoria = mysql_fetch_object($dados_categoria)) { ?>
                 <option <?php if ($lookup_categoria->id == $campos[categoria_repertorio_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>						 
						 </TD>
          </TR>
          <TR>
            <TD class='dataLabel'>M�sica:</TD>
            <TD colspan="3" class='tabDetailViewDF'>
               <select name="cmbMusicaId" id="cmbMusicaId" style="width:350px">
                 <?php while ($lookup_musica = mysql_fetch_object($dados_musica)) { ?>
                 <option <?php if ($lookup_musica->id == $campos[musica_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_musica->id ?>"><?php echo $lookup_musica->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>	
            </TD>
          </TR>          
           <TR>
             <TD valign="top" class=dataLabel>Observa&ccedil;&otilde;es:</TD>
             <TD colspan="3" class=tabDetailViewDF>
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>
				  </TD>
           </TR>
         </TBODY>
	   </TABLE>
     </td>
   </tr>
</FORM>
</table>  	 

</td>
</tr>
</table>
