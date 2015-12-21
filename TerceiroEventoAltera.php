<?php 
###########
## Módulo para alteração do terceiro do evento
## Criado: 12/02/2009 - Maycon Edinger
## Alterado:
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitTerceiroEventoAltera() {
   var Form;
   Form = document.cadastro;
   
	 if (Form.cmbFornecedorId.value == 0) {
      alert("É necessário selecionar um terceiro para o Evento !");
      Form.cmbFornecedorId.focus();
      return false;
   }
   if (Form.edtServicoContratado.value == 0) {
      alert("É necessário Informar o serviço contratado !");
      Form.edtServicoContratado.focus();
      return false;
   }
   
	 if (Form.edtCusto.value == 0) {
      alert("É necessário informar o valor do custo no evento !");
      Form.edtCusto.focus();
      return false;
     }   
   

  
   return true;
}
</script>

<form name="cadastro" action="sistema.php?ModuloNome=TerceiroEventoAltera" method="post" onsubmit="return wdSubmitTerceiroEventoAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Terceiro do Evento</span></td>
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
						//Verifica se a flag está vindo de uma postagem para liberar a alteração
            if($_POST["Submit"]){

						//Recupera os valores do formulario e alimenta as variáveis
						$id = $_POST["Id"];
						$ContaPagarId = $_POST["ContaPagarId"];
						$edtEmpresaId = $empresaId;
	          $edtEventoId = $_POST["EventoId"];
	          $cmbFornecedorId = $_POST["cmbFornecedorId"];					
	          $edtServicoContratado = $_POST["edtServicoContratado"];
	          $edtCusto = MoneyMySQLInserir($_POST["edtCusto"]);
	          $edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);	          
	          $edtObservacoes = $_POST["edtObservacoes"];
	          $edtOperadorId = $usuarioId;	          
	          $edtStatusContrato = $_POST["edtStatusContrato"];

						//Executa a query de alteração da conta
    	    	$sql = mysql_query("UPDATE eventos_terceiro SET 
																fornecedor_id = '$cmbFornecedorId', 
																servico_contratado = '$edtServicoContratado',
																custo = '$edtCusto',
																valor_venda = '$edtValorVenda',
																status_contrato = '$edtStatusContrato',
																observacoes = '$edtObservacoes'
																WHERE id = $id ");		
						
            //Verifica se há uma conta a pagar vinculada
						if ($ContaPagarId > 0){
							
							//Altera os dados da conta a pagar
							$sql_conta = mysql_query("UPDATE contas_pagar SET
																				pessoa_id = '$cmbFornecedorId',
																				valor = '$edtCusto'
																				WHERE id = $ContaPagarId");
							
						}																	
						
            //Verifica se há uma conta a pagar vinculada e o terceiro foi cancelado
						if ($edtStatusContrato == 3 AND $ContaPagarId > 0){
							
							//Altera os dados da conta a pagar
							$sql_conta = mysql_query("DELETE FROM contas_pagar 
																				WHERE id = $ContaPagarId");
							
						}

						//Configura a assinatura digital
    	    	$sql = mysql_query("UPDATE eventos SET terceiros_timestamp = now(), terceiros_operador_id = $usuarioId WHERE id = $edtEventoId");

						//Exibe a mensagem de alteração com sucesso
		        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Terceiro do Evento alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
        
					}	

        //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
				//Captura o id 
        if ($_GET["Id"]) {
					$TerceiroId = $_GET["Id"];
					$EventoId = $_GET["EventoId"];
				} else {
				  $TerceiroId = $_POST["Id"];
				  $EventoId = $_POST["EventoId"];
				}
				
				//Monta o sql para busca
        $sql = "SELECT * FROM eventos_terceiro WHERE id = $TerceiroId";

        //Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
        $campos = mysql_fetch_array($resultado);
        
        //Efetua o switch para o campo de grupo
				switch ($campos[status_contrato]) {
          case 1: 
						$status_1 = "checked"; 		  
						$status_2 = ""; 		   		  
					break;
					case 2: 
						$status_1 = ""; 		  
						$status_2 = "checked"; 		   		  
					break;
				}
					           					
			?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="100%"> </td>
          </tr>
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $TerceiroId ?>" />
	        		<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
	        		<input name="ContaPagarId" type="hidden" value="<?php echo $campos[conta_pagar_id] ?>" />
            	<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Terceiro">
            	<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
           	</td>
           	<td width="36" align="right">
							<input class="button" title="Retorna ao cadastro de terceiros do evento" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Terceiro do Evento" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
						</td>
	       	</tr>
        </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do terceiro do evento e clique em [Salvar Terceiro do Evento] </td>
			     </tr>
	       </table>             
			 </td>
	       </tr>
          		
					 <tr>
             <td class="dataLabel" width="20%">
               <span class="dataLabel">Fornecedor:</span>             </td>
             <td colspan="3" class="tabDetailViewDF">
               <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
                 	<?php while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { ?>
                 <option <?php if ($lookup_fornecedor->id == $campos[fornecedor_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>						 
						 </td>
          </tr>
         	<tr>
	         <td width="140" class="dataLabel">Serviço Contratado:</td>
	         <td colspan="3" valign="middle" class="tabDetailViewDF">
							<input name="edtServicoContratado" id="edtServicoContratado" type="text" class="requerido" style="width: 300px" maxlength="80" title="Informe o serviço contratado do terceiro" value="<?php echo $campos[servico_contratado] ?>">
					 </td>
	       </tr>
       <tr>
         <td width="140" class="dataLabel">Status:</td>
         <td colspan="3" valign="middle" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
	             <tr valign="middle">
	               <td width="120" height="20">
	                 <input name="edtStatusContrato" type="radio" value="1" <?php echo $status_1 ?> />&nbsp;A Contratar
	               </td>
	               <td width="120" height="20">
	                 <input name="edtStatusContrato" type="radio" value="2" <?php echo $status_2 ?> />&nbsp;Contratado
	               </td>
                 <td height="20">
	                 <input name="edtStatusContrato" type="radio" value="3" <?php echo $status_3 ?> />&nbsp;Cancelado <span style="color: #990000">(Irá excluir a conta a pagar, caso houver)</span>
	               </td>	                                  
	             </tr>
	           </table>	
				 </td>
       </tr>
   		 <tr>
          <td width="140" valign="top" class="dataLabel">Custo no Evento:</td>
          <td colspan="3" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
               <tr>
               	<td colspan="2">               	
									<?php
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										$valor_custo = str_replace(".",",",$campos[custo]);
										
										//Define nome do componente
										$objWDComponente->strNome = "edtCusto";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$valor_custo";
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
						</table>
					</td>
				</tr>						
				<tr>
          <td width="140" valign="top" class="dataLabel">Valor de Venda:</td>
          <td colspan="3" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
               <tr>
               	<td colspan="2">               	
									<?php
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										$valor_venda = str_replace(".",",",$campos[valor_venda]);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtValorVenda";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$valor_venda";
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
						</table>
					</td>
				</tr>          
         <tr>
           <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
           <td colspan="3" class="tabDetailViewDF">
					   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>
			  	</td>
        </tr>
	   	</table>
    </td>
  </tr>
  <tr>
  	<td>
			<br/>
  	  <?php
  	  
  	  	if ($campos[conta_pagar_id] > 0){
  	  		
  	  		echo "<b>Atenção:</b> Este terceiro possui uma conta a pagar vinculada. Quaisquer alterações efetuadas neste formulário afetará também sua conta a pagar.";
					
 	  		}
  	  
  	  ?>
  	</td>
  </tr>
	</form>
</table>  	 

</td>
</tr>
</table>
