<?php 
###########
## Módulo para alteraçao de Conta Corrente
## Criado: 26/09/2009 - Maycon Edinger
## Alterado:
## Alterações: 
## Incluído o campo para o código do banco 
###########

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup aa tabela de bancos
//Monta o SQL
$lista_banco = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";
//Executa a query
$dados_banco = mysql_query($lista_banco); 

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitContaCorrenteAltera() {
	 var Form;
   Form = document.conta_corrente;
   if (Form.edtCodigo.value.length == 0) {
      alert("É necessário informar o código da Conta Corrente !");
      Form.edtCodigo.focus();
      return false;
   }

   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar a descrição da conta corrente !");
      Form.edtNome.focus();
      return false;
   }
   
 	 if (Form.cmbBancoId.value == 0) {
      alert("É necessário selecionar um Banco !");
      Form.cmbBancoId.focus();
      return false;
   }
	    
   //Verifica se o checkbox de ativo está marcado
   if (Form.chkAtivo.checked) {
   	 var chkAtivoValor = 1;
   } else {
   	 var chkAtivoValor = 0;
 	 }
   		
   return true;
}
</script>

<form name="conta_corrente" action="sistema.php?ModuloNome=ContaCorrenteAltera" method="post" onsubmit="return wdSubmitContaCorrenteAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Conta Corrente</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

          <?php
		    		//Verifica se a página está abrindo vindo de uma postagem
	          if($_POST["Alterar"]) {
							
							//Recupera os valores vindo do formulário e atribui as variáveis
							$id = $_POST["Id"];			
				  		$edtCodigo = $_POST["edtCodigo"];
		        	$edtNome = $_POST["edtNome"];
		        	$cmbBancoId = $_POST["cmbBancoId"];
		        	$edtAgencia = $_POST["edtAgencia"];
		        	$edtConta = $_POST["edtConta"];
		        	$chkAtivo = $_POST["chkAtivo"];
		        	
							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE conta_corrente SET 
																ativo = '$chkAtivo',
																codigo = '$edtCodigo',
																nome = '$edtNome',
																banco_id  = '$cmbBancoId',
																agencia = '$edtAgencia',
																conta = '$edtConta'
																WHERE id = $id ");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Conta Corrente alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edição do registro
		if($_POST) {
		  $ContaCorrenteId = $_POST["Id"]; 
		} else {
		  $ContaCorrenteId = $_GET["Id"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM conta_corrente WHERE id = $ContaCorrenteId";
		//Executa a query
    $resultado = mysql_query($sql);
		//Monta o array dos dados
    $campos = mysql_fetch_array($resultado);
		
		//Efetua o switch para a figura de status ativo
		switch ($campos[ativo]) {
          case 00: $ativo_status = "value='1'";	  break;
          case 01: $ativo_status = "value='1' checked";  break;
		}
		?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="484"></td>
          </tr>
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $ContaCorrenteId ?>" />
            	<input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Registro" >
            	<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
             </td>
             <td align="right">
							 <input class="button" title="Retorna ao formulário de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=ContaCorrenteCadastra';" />						 
						 </td>
	       	</tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os novos dados do registro e clique em [Salvar Registro] </td>
			     		 	 </tr>
		       	 	 </table>             
				 	 	 </td>
	       	 </tr>
         	 <tr>
             <td class="dataLabel" width="15%">
               <span class="dataLabel">Código:</span>
             </td>
             <td width="85%" colspan="3" class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="333" height="20">
                     <input name="edtCodigo" type="text" class="requerido" id="edtCodigo" style="width: 50" size="6" maxlength="5" value="<?php echo $campos[codigo] ?>" />
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" <?php echo $ativo_status ?> />
                     </div>
                   </td>
                 </tr>
               </table>
             </td>
           </tr>
           <tr>
             <td class="dataLabel" width="15%">
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
             </td>
             <td width="85%" colspan="3" class="tabDetailViewDF">
               <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50" value="<?php echo $campos[nome] ?>" />
             </td>
           </tr>
 					 <tr>
             <td class="dataLabel">Banco:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbBancoId" id="cmbBancoId" style="width:350px">
                 <?php while ($lookup_banco = mysql_fetch_object($dados_banco)) { ?>
                 <option <?php if ($lookup_banco->id == $campos[banco_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_banco->id ?>"><?php echo $lookup_banco->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>							 							 							 						 
						 </td>
           </tr> 
					 <tr>
             <td class="dataLabel">
               <span class="dataLabel">Agência:</span>
             </td>
             <td width="180" class="tabDetailViewDF">
               <input name="edtAgencia" type="text" class="datafield" id="edtAgencia" size="10" maxlength="10" value="<?php echo $campos[agencia] ?>" />
             </td>
             <td class="dataLabel" width="50">
               <span class="dataLabel">Conta:</span>
             </td>
             <td width="220" class="tabDetailViewDF">
               <input name="edtConta" type="text" class="datafield" id="edtConta" size="10" maxlength="10" value="<?php echo $campos[conta] ?>" />
             </td>
           </tr>           
	   	   </table>
     	 </td>
   	 </tr>
		</form>
	</table>  	 

  </tr>
</table>
