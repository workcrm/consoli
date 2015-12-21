<?php 
###########
## Módulo para alteração de endereço do evento
## Criado: 25/05/2007 - Maycon Edinger
## Alterado: 23/09/2008 - Maycon Edinger
## Alterações: 
## Alterado para ter o Fornecedor ID
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Monta o lookup da tabela de locais de evento
//Monta o SQL
$lista_local = "SELECT * FROM local_evento WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_local = mysql_query($lista_local);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitEnderecoEventoAltera() {
   var Form;
   Form = document.frmEnderecoEventoAltera;
   
	 if (Form.cmbLocalId.value == 0) {
      alert("É necessário selecionar um local para o Evento !");
      Form.cmbLocalId.focus();
      return false;
   }
	 if (Form.cmbFornecedorId.value == 0) {
      alert("É necessário selecionar um fornecedor para o Evento !");
      Form.cmbFornecedorId.focus();
      return false;
   } 
   return true;
}
</script>

<FORM name='frmEnderecoEventoAltera' action='sistema.php?ModuloNome=EnderecoEventoAltera' method='post' onSubmit='return wdSubmitEnderecoEventoAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Endereço de Evento</span></td>
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
						//Verifica se a flag está vindo de uma postagem para liberar a alteração
            if($_POST['Submit']){

						//Recupera os valores do formulario e alimenta as variáveis
						$id = $_POST["Id"];
						$edtEventoId = $_POST["EventoId"];
						$cmbLocalId = $_POST["cmbLocalId"];
						$cmbFornecedorId = $_POST["cmbFornecedorId"];
            $edtHoraInicio = $_POST["edtHoraInicio"];
						$edtHoraTermino = $_POST["edtHoraTermino"];
            $edtObservacoes = $_POST["edtObservacoes"];
	 					$edtOperadorId = $usuarioId;

						//Executa a query de alteração da conta
    	    	$sql = mysql_query("UPDATE eventos_endereco SET 
																local_id = '$cmbLocalId', 
																fornecedor_id = '$cmbFornecedorId', 
																hora_inicio = '$edtHoraInicio', 
																hora_termino = '$edtHoraTermino', 
																observacoes = '$edtObservacoes',
																alteracao_timestamp = now(),
																alteracao_operador_id = '$edtOperadorId'
																WHERE id = '$id' ");	
																
						//Configura a assinatura digital
						$sql = mysql_query("UPDATE eventos SET enderecos_timestamp = now(), enderecos_operador_id = $usuarioId WHERE id = $edtEventoId");		 

				//Exibe a mensagem de alteração com sucesso
        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Endereço do Evento alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
        	}

        //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
				//Captura o id 
        if ($_GET["Id"]) {
					$EnderecoId = $_GET["Id"];
					$EventoId = $_GET["EventoId"];
				} else {
				  $EnderecoId = $_POST["Id"];
				  $EventoId = $_POST["EventoId"];
				}
				
				//Monta o sql para busca da conta
        $sql = "SELECT * FROM eventos_endereco WHERE id = $EnderecoId";

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
	        		<input name="Id" type="hidden" value="<?php echo $EnderecoId ?>" />
	        		<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
            	<INPUT name='Submit' type='submit' class=button accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Endereço'>
            	<INPUT class=button title="Cancela as alterações efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Alterações'>
           	</TD>
           	<TD width="36" align=right>
							<input class="button" title="Retorna ao cadastro de endereços de eventos" name='btnVoltar' type='button' id='btnVoltar' value='Retornar aos Endereços do Evento' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
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
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do endereço do evento e clique em [Salvar Endereço] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='20%'>
               <span class="dataLabel">Local:</span>             </TD>
             <TD colspan='3' class=tabDetailViewDF>
               <select name="cmbLocalId" id="cmbLocalId" style="width:350px">
                 	<?php while ($lookup_local = mysql_fetch_object($dados_local)) { ?>
                 <option <?php if ($lookup_local->id == $campos[local_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_local->id ?>"><?php echo $lookup_local->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>						 
						 </TD>
          </TR>
           <TR>
             <TD class='dataLabel'>Nome:</TD>
        	   <TD colspan="3" valign="middle" class=tabDetailViewDF>
							 <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
                 	<?php while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { ?>
                 <option <?php if ($lookup_fornecedor->id == $campos[fornecedor_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>	
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Hora In&iacute;cio: </TD>
             <TD valign="middle" class=tabDetailViewDF>
               <input name="edtHoraInicio" type="text" class="datafield" id="edtHoraInicio" size="6" maxlength="5" onKeyPress="return FormataCampo(document.frmEnderecoEventoAltera, 'edtHoraInicio', '99:99', event);" value="<?php echo $campos[hora_inicio] ?>">
             </TD>
             <TD valign="middle" class=dataLabel>Hora T&eacute;rmino: </TD>
             <TD valign="middle" class=tabDetailViewDF>
               <input name="edtHoraTermino" type="text" class="datafield" id="edtHoraTermino" size="6" maxlength="5" onKeyPress="return FormataCampo(document.frmEnderecoEventoAltera, 'edtHoraTermino', '99:99', event);" value="<?php echo $campos[hora_termino] ?>">
             </TD>
           </tr>  
           <TR>
             <TD valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares :</TD>
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
