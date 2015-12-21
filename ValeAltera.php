<?php 
###########
## Módulo para alteraçao de vales
## Criado: 03/03/2010 - Maycon Edinger
## Alterado:
## Alterações:
###########

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Monta o lookup aa tabela de colaboradores
//Monta o SQL
$lista_colab = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colab = mysql_query($lista_colab); 

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitValeAltera() {
	 var Form;
   Form = document.frmValeAltera;
   if (Form.edtData.value.length == 0) {
      alert("É necessário informar a data do vale !");
      Form.edtData.focus();
      return false;
   }
   
   if (Form.cmbColaboradorId.value == 0) {
      alert("É necessário selecionar um Colaborador !");
      Form.cmbColaboradorId.focus();
      return false;
   }
   
   if (Form.edtValor.value.length == 0) {
      alert("É necessário informar o valor do vale !");
      Form.edtValor.focus();
      return false;
   }
      		
   return true;
}
</script>

<form name="frmValeAltera" action="sistema.php?ModuloNome=ValeAltera" method="post" onsubmit="return wdSubmitValeAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Vale para Colaborador </span></td>
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
							$id = $_POST["ValeId"];			
					  	//Recupera os valores vindo do formulário e atribui as variáveis
				  	  $edtEmpresaId = $empresaId;
  						$edtData = DataMySQLInserir($_POST["edtData"]);
  						$cmbColaboradorId = $_POST["cmbColaboradorId"];
  		        $edtValor = MoneyMySQLInserir($_POST["edtValor"]);
  		        $edtDevolucao = DataMySQLInserir($_POST["edtDevolucao"]);
  		        $edtObservacoes = $_POST["edtObservacoes"];	
              
              $LancamentoCaixaId = $_POST["LancamentoCaixaId"];						

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE vales SET 
																data = '$edtData',
																colaborador_id = '$cmbColaboradorId',
																valor = '$edtValor',
																observacoes = '$edtObservacoes',
                                data_devolucao = '$edtDevolucao',
                                alteracao_timestamp = now(),
																alteracao_operador_id = '$usuarioId'
																WHERE id = $id ");
                                
              //Altera também o lancçamento no caixa
              	/*$sql = mysql_query("
               									UPDATE caixa SET 
																data = '$edtData',
																valor = '$edtValor',
																observacoes = '$edtObservacoes',
                                alteracao_timestamp = now(),
																alteracao_operador_id = '$usuarioId'
																WHERE id = $LancamentoCaixaId");			 
							*/
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Vale alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            
            }

		//Recebe os valores passados do form anterior para edição do registro
		if($_POST) {
		  $ValeId = $_POST["ValeId"]; 
		} else {
		  $ValeId = $_GET["ValeId"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM vales WHERE id = $ValeId";
		//Executa a query
    $resultado = mysql_query($sql);
		//Monta o array dos dados
    $campos = mysql_fetch_array($resultado);

		?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="484"></td>
          </tr>
          <tr>
	          <td style="PADDING-BOTTOM: 2px">
	            <input name="ValeId" type="hidden" value="<?php echo $ValeId ?>" />
              <input name="LancamentoCaixaId" type="hidden" value="<?php echo $campos[lancamento_caixa_id] ?>" />
              <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Registro" >
              <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
             </td>
             <td align="right">
							 <input class="button" title="Retorna ao formulário de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $campos[colaborador_id] ?>','conteudo')" />						 
						 </td>
	         </tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                   <tr>
                     <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </td>
			             </tr>
		            </table>				 
             </td>
	         </tr>
           <tr>
	             <td class="dataLabel" width="130">
	               <span class="dataLabel">Data de Emissão:</span>             
							 </td>
	             <td class="tabDetailViewDF">
	               <?php
								    //Define a data do formul&aacute;rio
								    $objData->strFormulario = "frmValeAltera";  
								    //Nome do campo que deve ser criado
								    $objData->strNome = "edtData";
                    $objData->strRequerido = true;
								    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
								    $objData->strValor = DataMySQLRetornar($campos["data"]);
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
            <td width="140" class="dataLabel">Colaborador:</td>
            <td colspan="3" class="tabDetailViewDF">
               <select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
                 <?php while ($lookup_colab = mysql_fetch_object($dados_colab)) { ?>
                 <option <?php if ($lookup_colab->id == $campos[colaborador_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_colab->id ?>"><?php echo $lookup_colab->id . " - " . $lookup_colab->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>						
						</td>
           </tr>					           
           <tr>
             <td class="dataLabel">Valor:</td>
             <td class="tabDetailViewDF">
							<?php
								
                //Acerta a variável com o valor a alterar
								$valor_alterar = str_replace(".",",",$campos[valor]);
									
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValor";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "$valor_alterar";
								//Busca a descrição do XML para o componente
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
             <td class="dataLabel" valign="top">Observações:</td>
             <td colspan="3" class="tabDetailViewDF">
								<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"><?php echo $campos[observacoes] ?></textarea>
						 </td>
           </tr>
 					 <tr>
             <td class="dataLabel">Data de Desconto:</td>
             <td colspan="3" class="tabDetailViewDF">
					 			<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmValeAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDevolucao";
                  $objData->strRequerido = false;
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos["data_devolucao"]);
							    //Define o tamanho do campo 
							    //$objData->intTamanho = 15;
							    //Define o número maximo de caracteres
							    //$objData->intMaximoCaracter = 20;
							    //define o tamanho da tela do calendario
							    //$objData->intTamanhoCalendario = 200;
							    //Cria o componente com seu calendario para escolha da data
							    $objData->CriarData();
								?>
							</td>
           </tr>           
	   		 </table>
     	 </td>
   	 </tr>
	</table>  	 
	</form>
  </tr>
</table>
