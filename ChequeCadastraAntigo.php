<?php 
###########
## M�dulo para Cadastro de Cheques
## Criado: 05/07/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########


//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipula��o de valor monet�rio
include "./include/ManipulaMoney.php";

//Adiciona o acesso a entidade de cria��o do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que n�o deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Monta o lookup aa tabela de bancos
//Monta o SQL
$lista_banco = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_banco = mysql_query($lista_banco); 

//Monta o lookup aa tabela de conta corrente
//Monta o SQL
$lista_conta = "SELECT * FROM conta_corrente WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_conta = mysql_query($lista_conta); 
?>

<script language="JavaScript">

//Fun��o que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCheque() {
	 var Form;
   Form = document.Cheque;
   if (Form.edtNumero.value.length == 0) {
      alert("� necess�rio informar o numero do cheque !");
      Form.edtNumero.focus();
      return false;
   }
   if (Form.edtValor.value.length == 0) {
      alert("� necess�rio informar o valor do cheque !");
      Form.edtValor.focus();
      return false;
   }
      		
   return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="cheque" action="sistema.php?ModuloNome=ChequeCadastra" method="post" onsubmit="return wdSubmitCheque()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Manuten��o de Cheques Avulsos</span>
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
		    		//Verifica se a p�gina est� abrindo vindo de uma postagem
            if($_POST['Submit']) {
				  	
						//Recupera os valores vindo do formul�rio e atribui as vari�veis
				  	$edtEmpresaId = $empresaId;
		        $edtNumero = $_POST["edtNumero"];
		        $chkPredatado = $_POST["chkPredatado"];
						$edtBomPara = DataMySQLInserir($_POST["edtBomPara"]);
						$cmbBancoId = $_POST["cmbBancoId"];
		        $edtAgenciaTerceiro = $_POST["edtAgenciaTerceiro"];
		        $edtContaTerceiro = $_POST["edtContaTerceiro"];
		        $edtFavorecido = $_POST["edtFavorecido"];
		        $edtValor = MoneyMySQLInserir($_POST["edtValor"]);
		        $edtRecebimento = DataMySQLInserir($_POST["edtRecebimento"]);
						$edtPago = $_POST["edtPago"];		        		        		        
						$edtStatus = $_POST["edtStatus"];
		        $edtObservacoes = $_POST["edtObservacoes"];		      		        
		        
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO cheques (
																id,
																empresa_id, 
																pre_datado,
																banco_id,
																agencia,
																conta,																
																bom_para,																
																valor,
																favorecido,
																status,
																observacoes,
																data_recebimento,
																origem,
																) values (				
																'$edtNumero',
																'$edtEmpresaId',
																'$chkPredatado',
																'$cmbBancoId',
																'$edtAgenciaTerceiro',
																'$edtContaTerceiro',
																'$edtBomPara',
																'$edtValor',
																'$edtFavorecido',
																'$edtStatus',
																'$edtObservacoes',
																'$edtRecebimento',
																1
																);");
	
						//Exibe a mensagem de inclus�o com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cheque cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="484">
						</td>
          </tr>
          <tr>
	        <td style="PADDING-BOTTOM: 2px">
						<input name="Submit" type="submit" class="button" accesskey="S" title="Salva o registro atual [Alt+S]" value="Salvar Cheque" />
            <input class="button" title="Limpa o conte�do dos campos digitados [Alt+L]" accesskey="L" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
      		</td>
        	<td align="right">
						 		<input class="button" title="Emite o relat�rio dos cheques cadastrados" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relat�rio" style="width:100px" onclick="abreJanela('./relatorios/ChequeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
					 </td>
	       </tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
        	<tr>
         		<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
           		<table cellspacing="0" cellpadding="0" width="100%" border="0">
             		<tr>
               		<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Cheque e clique em [Salvar Cheque] </td>
			     			 </tr>
		       		 </table>								
						 </td>
	       	 </tr>
           <tr>
             <td class="dataLabel" width="19%">
               <span class="dataLabel">N�mero:</span>             
	 		       </td>
             <td colspan="3" class="tabDetailViewDF">              
                <input name="edtNumero" type="text" class="datafield" id="edtNumero" style="width: 60px" size="60" maxlength="10" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
						</td>
           </tr>
           	<tr>
	             <td class="dataLabel" width="20%">
	               <span class="dataLabel">Pr�-datado:</span>             
							 </td>
	             <td class="tabDetailViewDF">
	               <input name="chkPredatado" type="checkbox" id="chkPredatado" value="1"> Pr�-datado				 
							 </td>
							 <td class="dataLabel" width="100">
	               <span class="dataLabel">Bom Para:</span>             
							 </td>
	             <td class="tabDetailViewDF">
	               <?php
								    //Define a data do formul&aacute;rio
								    $objData->strFormulario = "cheque";  
								    //Nome do campo que deve ser criado
								    $objData->strNome = "edtBomPara";
								    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
								    $objData->strValor = "";
								    //Define o tamanho do campo 
								    //$objData->intTamanho = 15;
								    //Define o n&uacute;mero maximo de caracteres
								    //$objData->intMaximoCaracter = 20;
								    //define o tamanho da tela do calendario
								    //$objData->intTamanhoCalendario = 200;
								    //Cria o componente com seu calendario para escolha da data
								    $objData->CriarData();
								 ?>				 
							 </td>
	          </tr>	
					 <tr>
             <td class="dataLabel">Banco:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <select name="cmbBancoId" id="cmbBancoId" style="width:350px">
                 <option value="0">Selecione uma Op��o</option>
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
             <td class='dataLabel'>
               <span class="dataLabel">Ag�ncia:</span>             
						 </td>
             <td class="tabDetailViewDF">
               	<input name="edtAgenciaTerceiro" type="text" class='datafield' id="edtAgenciaTerceiro" size="10" maxlength="10">				 
						 </td>
						 <td class='dataLabel' width='100'>
               <span class="dataLabel">N� Conta:</span>             
						 </td>
             <td class="tabDetailViewDF">
               	<input name="edtContaTerceiro" type="text" class='datafield' id="edtContaTerceiro" size="10" maxlength="10">				 
						 </td>
          </tr>
					 <tr>
             <td class="dataLabel">Favorecido:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
               <input name="edtFavorecido" type="text" class="datafield" style="width: 350px" size="84" maxlength="80">						 				 
					 	</td>
          </tr>          
				  <tr>
             <td class="dataLabel">Valor:</td>
             <td colspan="3" class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValor";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descri��o do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>								
           		</td>
					 </tr>
					            
					 <tr>
             <td class="dataLabel">Data de Recebimento:</td>
             <td colspan="3" class="tabDetailViewDF">
					 			<?php
							    //Define a data do formul�rio
							    $objData->strFormulario = "cheque";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtRecebimento";
							    //Valor a constar dentro do campo (p/ altera��o)
							    $objData->strValor = "";
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o n�mero maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
								?>
							</td>
           </tr>
					 <tr>
             <td class="dataLabel">Status:</td>
             <td colspan="3" class="tabDetailViewDF">
			   				<table width="400" cellpadding="0" cellspacing="0">
               		<tr valign="middle">
                 		<td width="111" height="20">
				   						<input name="edtStatus" type="radio" value="1" checked> Em aberto
                 		</td>
                 		<td width="112">
				   						<input type="radio" name="edtStatus" value="2"> Compensado
									 	</td>
                 		<td>
                   		<input type="radio" name="edtStatus" value="3"> Voltou
										</td>
               		</tr>
               	</table>
					   </td>
           </tr>					           
           <tr>
             <td class="dataLabel" valign="top">Observa��es:</td>
             <td colspan="3" class="tabDetailViewDF">
								<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"></textarea>
						 </td>
           </tr>              
	   	   </table>
       </td>
     </tr>
  </form>
		</table>  	 
		</td>
	</tr>
</table>