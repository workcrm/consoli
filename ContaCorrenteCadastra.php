<?php 
###########
## Módulo para Cadastro de Contas Corrente
## Criado: 06/04/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
## Incluído o campo para o código do banco
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup aa tabela de bancos
//Monta o SQL
$lista_banco = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_banco = mysql_query($lista_banco); 

?>

<script language="JavaScript">

function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitContaCorrente() {
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

<form name="conta_corrente" action="sistema.php?ModuloNome=ContaCorrenteCadastra" method="post" onsubmit="return wdSubmitContaCorrente()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Conta Corrente</span>
			</td>
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
            if($_POST["Submit"]) {
				  	//Recupera os valores vindo do formulário e atribui as variáveis
				  	$edtEmpresaId = $empresaId;
				  	$edtCodigo = $_POST["edtCodigo"];
		        $edtNome = $_POST["edtNome"];
		        $cmbBancoId = $_POST["cmbBancoId"];
		        $edtAgencia = $_POST["edtAgencia"];
		        $edtConta = $_POST["edtConta"];
		        $chkAtivo = $_POST["chkAtivo"];
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO conta_corrente (
																empresa_id,
																codigo, 
																nome,
																banco_id,
																agencia,
																conta, 
																ativo
																) values (				
																'$edtEmpresaId',
																'$edtCodigo',
																'$edtNome',
																'$cmbBancoId',																
																'$edtAgencia',
																'$edtConta',
																'$chkAtivo'
																);");
	
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Conta Corrente cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="484">
						</td>
          </tr>
          <tr>
	        <td style="PADDING-BOTTOM: 2px">
	        	<input name="Submit" type="submit" class="button" accesskey="S" title="Salva o registro atual" value="Salvar Conta Corrente">
            <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
             </td>
             <td align="right">
						 		<input class="button" title="Emite o relatório das contas cadastradas" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/ContaCorrenteRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
						 </td>
	         </tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Conta Corrente e clique em [Salvar Conta Corrente] </td>
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
                     <input name="edtCodigo" type="text" class="requerido" id="edtCodigo" style="width: 50" size="6" maxlength="5">
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
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
               <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50">
             </td>
           </tr>
 					 <tr>
             <td class="dataLabel">Banco:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbBancoId" id="cmbBancoId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_banco = mysql_fetch_object($dados_banco)) { 
								 ?>
                 <option value="<?php echo $lookup_banco->id ?>"><?php echo $lookup_banco->nome ?> </option>
                 <?php } ?>
               </select>						 
							</td>
           </tr> 
					 <tr>
             <td class="dataLabel">
               <span class="dataLabel">Agência:</span>
             </td>
             <td width="180" class="tabDetailViewDF">
               <input name="edtAgencia" type="text" class="datafield" id="edtAgencia" size="10" maxlength="10">
             </td>
             <td class="dataLabel" width="50">
               <span class="dataLabel">Conta:</span>
             </td>
             <td width="220" class="tabDetailViewDF">
               <input name="edtConta" type="text" class="datafield" id="edtConta" size="10" maxlength="10">
             </td>
           </tr>           
	   	   </table>
       </td>
     </tr>
  </form>
</table>  	 
</td>
</tr>

<tr>
<td>
<br/>

<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td colspan="15" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Contas Cadastradas</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	  <tr height="20">
        <td width="42" class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
        <td width="42" class="listViewThS1">
          <div align="center">Código</div>
        </td>        
				<td width="305" class="listViewThS1" scope="col">
          &nbsp;&nbsp;Descrição da Conta
        </td>
        <td width="220" class="listViewThS1">
          Banco
        </td>
        <td width="50" class="listViewThS1">
          <div align="center">Agência</div>
        </td>
        <td width="50" class="listViewThS1">
          <div align="center">Conta</div>
        </td>
        <td nowrap="nowrap" class="listViewThS1" scope="col">
	        <div align="center">Ativo</div>
        </td>
	  </tr>

	<?php
	  //Monta a tabela de consulta das contas cadastrados
	  //Cria a SQL
	  $consulta = "SELECT
								con.id,
								con.codigo,
								con.nome,
								con.agencia,
								con.conta,
								con.ativo,
								ban.nome as banco_nome
								FROM conta_corrente con
								LEFT OUTER JOIN bancos ban ON ban.id = con.banco_id
								WHERE con.empresa_id = $empresaId ORDER BY con.nome";
								
		//Executa a query
	  $listagem = mysql_query($consulta);
	  //Monta e percorre o array com os dados da consulta
	  while ($dados = mysql_fetch_array($listagem)){
      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
  	  switch ($dados[ativo]) {
       	case 00: $ativo_figura = "";	break;
			  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
  	  }
  	//Fecha o php, mas o while continua
	?>

	  <tr height="16">
      <td width="42">
	  	  <div align="center">
          <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=conta_corrente&Retorno=ContaCorrenteCadastra','conteudo')}" style="cursor: pointer"></a>
											          
          <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('ContaCorrenteAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">          
        </div>
      </td>
      <td width="42">
	  	  <div align="center">
          <?php echo $dados[codigo] ?>
        </div>
      </td>      
	    <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" class="oddListRowS1" scope="row" onclick="wdCarregarFormulario('ContaCorrenteAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
      </td>
			<td>
   			<?php echo $dados[banco_nome] ?>
      </td>			
			<td>
	  	  <div align="center">
          <?php echo $dados[agencia] ?>
        </div>
      </td>
      <td>
	  	  <div align="center">
          <?php echo $dados[conta] ?>
        </div>
      </td>
      <td scope="row" valign="middle" bgcolor="#fdfdfd" class="currentTabList">
			  <div align="center"><?php echo $ativo_figura ?></div>
			</td>

	<?php
	//Fecha o while
	}
	?>
	</table>
	
</table>
