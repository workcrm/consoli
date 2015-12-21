<?php 
###########
## Módulo para Cadastro de vales para colaboradores
## Criado: 03/03/2010 - Maycon Edinger
## Alterado: 02/07/2010- Maycon Edinger 
## Alterações: 
## Implementado opção de escolher se quer vale para funcionario ou para freelance
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

//Monta o lookup aa tabela de freelance
//Monta o SQL
$lista_free = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = 1 AND tipo = 1 ORDER BY nome";
//Executa a query
$dados_free = mysql_query($lista_free); 

//Monta o lookup aa tabela de colaboradores
//Monta o SQL
$lista_colab = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = 1 AND tipo = 2 ORDER BY nome";
//Executa a query
$dados_colab = mysql_query($lista_colab); 

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitVale() {
	 var Form;
   Form = document.Vale;
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

//Função para escolha do tipo de conta a receber
function wdTipoVale()
{
  
  var Form;
  Form = document.cadastro; 
  
  //Captura o valor referente ao radio button do tipo de vale
  var edtTipoValeValor = document.getElementsByName('edtTipoVale');
  
  for (var i=0; i < edtTipoValeValor.length; i++) {
   if (edtTipoValeValor[i].checked == true) {
     edtTipoValeValor = edtTipoValeValor[i].value;
     break;
   }
  }
  
  //Caso tenha escolhido funcionario
	if (edtTipoValeValor == 1) 
  {
	      
    IDCol = document.getElementById(20);
		IDFree = document.getElementById(30);
	  IDFree.style.display = "none";
	  IDCol.style.display = "inline";  
	
  }

  //Caso tenha escolhido de freelance
	if (edtTipoValeValor == 2) 
  {
	  
    IDCol = document.getElementById(20);
		IDFree = document.getElementById(30);
	  IDCol.style.display = "none";
    IDFree.style.display = "inline";
	  	
  }  
  
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="vale" action="sistema.php?ModuloNome=ValeCadastra" method="post" onsubmit="return wdSubmitVale()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Manutenção de Vales para Colaboradores</span>
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
            if($_POST['Submit']) {
				  	
						//Recupera os valores vindo do formulário e atribui as variáveis
				  	$edtEmpresaId = $empresaId;
						$edtData = DataMySQLInserir($_POST["edtData"]);
		        $edtValor = MoneyMySQLInserir($_POST["edtValor"]);
		        $edtDevolucao = DataMySQLInserir($_POST["edtDevolucao"]);
		        $edtObservacoes = $_POST["edtObservacoes"];
            
            $edtTipoVale = $_POST["edtTipoVale"];
                        
						if ($edtTipoVale == 1) 
            {
						
            	$cmbColaboradorId = 	$_POST["cmbFuncionarioId"];
						
            }
	
						//Se o tipo de pessoa for 2 é freelance
            if ($edtTipoVale == 2) 
            {
							
              //A pessoa ID é pega do combo de FORNECEDOR
              $cmbColaboradorId = 	$_POST["cmbFreelanceId"];
						
            }	      		        
		        
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO vales (
																empresa_id, 
																data,
																colaborador_id,
																valor,
																observacoes,
																data_devolucao,
                                cadastro_timestamp,
								                cadastro_operador_id
																) values (				
																'$edtEmpresaId',
																'$edtData',
																'$cmbColaboradorId',
																'$edtValor',
																'$edtObservacoes',
																'$edtDevolucao',
                                now(),
								                '$usuarioId'
																);");
            
            //Gera o id do lançamento o vale
            $ID_Vale = mysql_insert_id();
            
            /* ***** REMOVIDO POR ENQUANTO, OS VALES NÃO IRÃO LANÇAR NO CAIXA
                                                                
            //Busca os parametros de conta-caixa e centro de custo da tabela de parametros
            $lista_parametros = "SELECT vale_conta_caixa_id, vale_centro_custo_id FROM parametros_sistema";
            //Executa a query
            $dados_parametros = mysql_query($lista_parametros); 
            //Monta o array dos dados
            $campos = mysql_fetch_array($dados_parametros);
            
            //Cria as variáveis com os valores dos parametros
            $cmbValeContaCaixaId = $campos["vale_conta_caixa_id"];
            $cmbValeCentroCustoId = $campos["vale_centro_custo_id"];
            
            //Busca os parametros do colaborador selecionado
            $lista_colaborador = "SELECT nome FROM colaboradores WHERE id = $cmbColaboradorId";
            //Executa a query
            $dados_colaborador = mysql_query($lista_colaborador); 
            //Monta o array dos dados
            $campos_colaborador = mysql_fetch_array($dados_colaborador);
            
            $TituloLancamento = "Vale ao Colaborador " . $campos_colaborador["nome"];
            
            //Efetua o lançamento na tabela de caixa
            $sql_caixa = mysql_query("
                  		                INSERT INTO caixa (
                  										empresa_id, 
                  										data,
                                      conta_caixa_id,
                                      centro_custo_id,
                  										tipo_lancamento,
                  										historico,
                  										valor,
                                      observacoes,
                  										cadastro_timestamp,
                  										cadastro_operador_id
                  						
                  										) VALUES (
                  						
                  										'$edtEmpresaId',
                  										'$edtData',
                  										'$cmbValeContaCaixaId',
                  										'$cmbValeCentroCustoId',
                  										2,
                  										'$TituloLancamento',										
                  										'$edtValor',		
                  										'$edtObservacoes',
                  										now(),
                  										'$usuarioId'				
                  										);");
            
            //Gera o id do lançamento no caixa
            $ID_LancamentoCaixa = mysql_insert_id();
            
            //Insere o id do lançamento no caixa dentro do vale
            $sql_atualiza_vale = mysql_query("UPDATE vales SET lancamento_caixa_id = $ID_LancamentoCaixa WHERE id = $ID_Vale");
            
            */
             
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Vale ao colaborador cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
    		  }
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
	        <td style="PADDING-BOTTOM: 2px">
						<input name="Submit" type="submit" class="button" title="Salva o vale atual" value="Salvar Vale" />
            <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
      		</td>
	       </tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
        	<tr>
         		<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
           		<table cellspacing="0" cellpadding="0" width="100%" border="0">
             		<tr>
               		<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Vale e clique em [Salvar Vale] </td>
			     			 </tr>
		       		 </table>								
						 </td>
	       	 </tr>
           	<tr>
	             <td class="dataLabel" width="130">
	               <span class="dataLabel">Data Emissão:</span>             
							 </td>
	             <td class="tabDetailViewDF">
	               <?php
								    //Define a data do formul&aacute;rio
								    $objData->strFormulario = "vale";  
								    //Nome do campo que deve ser criado
								    $objData->strNome = "edtData";
                    $objData->strRequerido = true;
								    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
								    $objData->strValor = date("d/m/Y", mktime());
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
            <td valign="top" class="dataLabel">Tipo de Vale:</td>
            <td class="tabDetailViewDF">
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr valign="middle">
                  <td width="180" height="20">
                    <input type="radio" name="edtTipoVale" value="1" checked="checked" onclick="wdTipoVale()" />
                      Funcionário
                  </td>
                  <td height="20">
                    <input type="radio" name="edtTipoVale" value="2"  onclick="wdTipoVale()" />
                      Freelance
                  </td>                  
                </tr>
              </table>
              
              <table id="20" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Funcionário:<br/>
									 <select name="cmbFuncionarioId" id="cmbFuncionarioId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_colab = mysql_fetch_object($dados_colab)) { 
										 ?>
		                 <option value="<?php echo $lookup_colab->id ?>"><?php echo $lookup_colab->id . " - " . $lookup_colab->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>

              <table id="30" cellpadding="0" cellspacing="0" style="display: none">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Freelance:<br/>                  
		               <select name="cmbFreelanceId" id="cmbFreelanceId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_free = mysql_fetch_object($dados_free)) { 
										 ?>
		                 <option value="<?php echo $lookup_free->id ?>"><?php echo $lookup_free->id . " - " . $lookup_free->nome ?></option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>
            </td>
          </tr>                   
				  <tr>
             <td class="dataLabel">Valor:</td>
             <td class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValor";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
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
             <td class="tabDetailViewDF">
								<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"></textarea>
						 </td>
           </tr>
 					 <tr>
             <td class="dataLabel">Data de Desconto:</td>
             <td class="tabDetailViewDF">
					 			<?php
							    //Define a data do formulário
							    $objData->strFormulario = "vale";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDevolucao";
                  $objData->strRequerido = false;
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = "";
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
  </form>
		</table>  	 
		</td>
	</tr>
</table>