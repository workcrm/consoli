<?php 
###########
## Módulo para cadastro de lançamentos avulsos no caixa
## Criado: 17/02/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdExibir() {

	 //Captura o valor referente ao radio button selecionado
   var edtTipoLancamentoValor = document.getElementsByName('edtTipoLancamento');
   
	 for (var i=0; i < edtTipoLancamentoValor.length; i++) {
     if (edtTipoLancamentoValor[i].checked == true) {
       edtTipoLancamentoValor = edtTipoLancamentoValor[i].value;
       break;
     }
   }

	if (edtTipoLancamentoValor == 1) {
		ID_Credito = document.getElementById(91);
		ID_Debito = document.getElementById(92);
	  ID_Debito.style.display = "none";
	  ID_Credito.style.display = "inline";
	}
	
	if (edtTipoLancamentoValor == 2) {
		ID_Credito = document.getElementById(91);
		ID_Debito = document.getElementById(92);
	  ID_Credito.style.display = "none";
	  ID_Debito.style.display = "inline";		
	}

}

function valida_form() {
   var Form;
   Form = document.cadastro;   
	 
	 if (Form.edtData.value.length == 0) {
      alert("É necessário Informar a Data !");
      Form.edtData.focus();
      return false;
   }

	 //Captura o valor referente ao radio button selecionado
   var edtTipoLancamentoValor = document.getElementsByName('edtTipoLancamento');
   
	 for (var i=0; i < edtTipoLancamentoValor.length; i++) {
     if (edtTipoLancamentoValor[i].checked == true) {
       edtTipoLancamentoValor = edtTipoLancamentoValor[i].value;
       break;
     }
   }

	if (edtTipoLancamentoValor == 1) {
	 
    if (Form.cmbContaCaixaCreditoId.value == 0) {
      alert("É necessário selecionar uma Conta-caixa [Crédito] !");
      Form.cmbContaCaixaCreditoId.focus();
      return false;
    }
   
  }
  
  if (edtTipoLancamentoValor == 2) {
	 
    if (Form.cmbContaCaixaDebitoId.value == 0) {
      alert("É necessário selecionar uma Conta-caixa [Débito] !");
      Form.cmbContaCaixaDebitoId.focus();
      return false;
    }
   
  }

	 if (Form.cmbCentroCustoId.value == 0) {
      alert("É necessário selecionar um Centro de Custo !");
      Form.cmbCentroCustoId.focus();
      return false;
   }

   if (Form.edtHistorico.value.length == 0) {
      alert("É necessário Informar o Histórico do lançamento !");
      Form.edtDescricao.focus();
      return false;
   }
   if (Form.edtValor.value.length == 0) {
      alert("É necessário Informar o Valor do lançamento !");
      Form.edtValor.focus();
      return false;
   }   

   return true;
}

</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 
//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Monta o lookup da tabela de centro de custos
//Monta o SQL
$lista_centro_custo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_centro_custo = mysql_query($lista_centro_custo);

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_conta_caixa_debito = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_conta_caixa_debito = mysql_query($lista_conta_caixa_debito);


//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 1 que é entrada (crédito)
//Monta o SQL
$lista_conta_caixa_credito = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '1' ORDER BY nome";
//Executa a query
$dados_conta_caixa_credito = mysql_query($lista_conta_caixa_credito);


//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 
?>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Lançamentos Avulsos no Caixa</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
					</td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

          <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST["Submit"]){

	          $edtEmpresaId = $empresaId;
						$edtData = DataMySQLInserir($_POST["edtData"]);
            
						$edtTipoLancamento = $_POST["edtTipoLancamento"];
						
						if ($edtTipoLancamento == 1) {
							$cmbContaCaixaId = 	$_POST["cmbContaCaixaCreditoId"];
						}
	
						if ($edtTipoLancamento == 2) {
							$cmbContaCaixaId = 	$_POST["cmbContaCaixaDebitoId"];
						}
											
						$cmbCentroCustoId = $_POST["cmbCentroCustoId"];
	          
            $cmbEventoId = $_POST["cmbEventoId"];
	          $edtHistorico = $_POST["edtHistorico"];
	          $edtNroDocumento = $_POST["edtNroDocumento"];
            $edtValor = MoneyMySQLInserir($_POST["edtValor"]);
	          $edtObservacoes = $_POST["edtObservacoes"];
						$edtOperadorId = $usuarioId;
            
            																				
							//Monta o sql e executa a query de inserção da conta sem desmembrar
		    	    $sql = mysql_query("
		                INSERT INTO caixa (
										empresa_id, 
										data,
                    evento_id,
                    conta_caixa_id,
                    centro_custo_id,
										tipo_lancamento,
										historico,
										documento,
										valor,
										observacoes,
										cadastro_timestamp,
										cadastro_operador_id
						
										) VALUES (
						
										'$edtEmpresaId',
										'$edtData',
                    '$cmbEventoId',
										'$cmbContaCaixaId',
										'$cmbCentroCustoId',
										'$edtTipoLancamento',
										'$edtHistorico',										
                    '$edtNroDocumento',
										'$edtValor',		
										'$edtObservacoes',
										now(),
										'$edtOperadorId'				
										);"); 
										
			
							?>
              
              
		        	<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
                <tr>
                  <td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px">
                    <img src="./image/bt_informacao.gif" border="0" />
                  </td>
                  <td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px">
                    <strong>Lançamento no caixa cadastrado com sucesso !</strong>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    &nbsp;
                  </td>
                </tr>
              </table>
              </td></tr><tr><td>
        
        <?php 
        
          //Fecha o if de postagem
  				}
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="484">
              <form id="form" name="cadastro" action="sistema.php?ModuloNome=CaixaCadastra" method="post" onsubmit="return valida_form()">
			  		</td>
          </tr>
          <tr>
		        <td style="PADDING-BOTTOM: 2px">
		        	<input name="Submit" type="submit" class="button" id="Submit" title="Salva o Lançamento no Caixa" value="Salvar Lançamento no Caixa" />
	            <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
	          </td>
	          <td width="36" align="right">	  </td>
	       	</tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									 		<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do lançamento no caixa e clique em [Salvar Lançamento no Caixa]									 </td>
			     			 </tr>
		       	 	  </table>			 
					 	  </td>
	         </tr>
           <tr>
             <td width="140" class="dataLabel">
               <span class="dataLabel">Data:</span>             
						</td>
             <td colspan="4" class="tabDetailViewDF">
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtData";
                  $objData->strRequerido = true;
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = Date("d/m/Y", mktime());
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
           
           <tr>
            <td width="140" valign="top" class="dataLabel">Tipo de Lançamento:</td>
            <td colspan="4" class="tabDetailViewDF">
              <table cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="117" height="20">
                    <input type="radio" name="edtTipoLancamento" value="2" checked="checked" onclick="wdExibir()" />
                      Débito
                  </td>
                  <td height="20">
                    <input type="radio" name="edtTipoLancamento" value="1"  onclick="wdExibir()" />
                      Crédito
                  </td>
                </tr>
              </table>            
				  	</td>
          </tr>
           
         	<tr>
            <td class="dataLabel" width="50">Evento:</td>
            <td colspan="4" width="490" class="tabDetailViewDF">
							<select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
							  <option value="0">Selecione uma Opção</option>
								<?php 
							    //Cria o componente de lookup de eventos
							    while ($lookup_eventos = mysql_fetch_object($dados_eventos)) { 
							  ?>
                 <option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
                <?php 
								  //Fecha o while
								  } 
								?>
              </select>
            </td>
          </tr>           
					<tr>
            <td width="140" class="dataLabel">Conta-caixa:</td>
            <td colspan="4" valign="middle" class="tabDetailViewDF">
             	<div id="91" style="display: none;">
                 <select name="cmbContaCaixaCreditoId" id="cmbContaCaixaCreditoId" style="width:350px">
                   	<option value="0">Selecione uma Opção</option>
    			 				 	<?php 
    								 	//Monta o while para gerar o combo de escolha
    								 	while ($lookup_conta_caixa_credito = mysql_fetch_object($dados_conta_caixa_credito)) { 
    							 	?>
                   	<option value="<?php echo $lookup_conta_caixa_credito->id ?>"><?php echo $lookup_conta_caixa_credito->id . " - " . $lookup_conta_caixa_credito->nome ?></option>
                   	<?php } ?>
               	</select>
             </div>
             <div id="92">
                <select name="cmbContaCaixaDebitoId" id="cmbContaCaixaDebitoId" style="width:350px">
                 	<option value="0">Selecione uma Opção</option>
  			 				 	<?php 
  								 	//Monta o while para gerar o combo de escolha
  								 	while ($lookup_conta_caixa_debito = mysql_fetch_object($dados_conta_caixa_debito)) { 
  							 	?>
                 	<option value="<?php echo $lookup_conta_caixa_debito->id ?>"><?php echo $lookup_conta_caixa_debito->id . " - " . $lookup_conta_caixa_debito->nome ?></option>
                 	<?php } ?>
             	</select>	
             </div> 				 						 
						</td>
          </tr>
					<tr>
          	<td width="140" class="dataLabel">Centro de Custo:</td>
            <td colspan="4" valign="middle" class="tabDetailViewDF">
               <select name="cmbCentroCustoId" id="cmbCentroCustoId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha
									 while ($lookup_centro_custo = mysql_fetch_object($dados_centro_custo)) { 
								 ?>
                 <option value="<?php echo $lookup_centro_custo->id ?>"><?php echo $lookup_centro_custo->id . " - " . $lookup_centro_custo->nome ?></option>
                 <?php } ?>
               </select>						 						 
						</td>
          </tr>
          <tr>
             <td width="140" class="dataLabel">Histórico:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <input name="edtHistorico" type="text" class="requerido" id="edtHistorico" style="width: 400px" size="84" maxlength="80" />             
						 </td>
          </tr>           

           <tr>
             <td width="140" class="dataLabel">Nº do Documento:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <input name="edtNroDocumento" type="text" class="datafield" id="edtNroDocumento" style="width: 140px" maxlength="20" />             
						 </td>
          </tr>
          

          <tr>
            <td width="140" valign="top" class="dataLabel">Valor Total: </td>
            <td colspan="4" class="tabDetailViewDF">
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
             <td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
             <td colspan="4" class="tabDetailViewDF">
             	 <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>    							
						 </td>
           	</tr>
	   		</table>
     </td>
   </tr>
</table>  	 

</tr>
</table>
</form>