<?php 
###########
## Módulo para alteraçao de preferências do sistema
## Criado: 18/12/2009 - Maycon Edinger
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

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

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
?>

<form name="frmPreferencias" action="sistema.php?ModuloNome=PreferenciasSistema" method="post">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Preferências do Sistema</span></td>
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
		    		//Verifica se a página está abrindo vindo de uma postagem
	          if($_POST["Alterar"]) {
							
              //Recupera os valores vindo do formulário e atribui as variáveis
							$edtBoletoAgencia = $_POST["edtBoletoAgencia"];			
							$edtBoletoConta = $_POST["edtBoletoConta"];
							$edtBoletoConvenio = $_POST["edtBoletoConvenio"];
              $edtBoletoContrato = $_POST["edtBoletoContrato"];
              $edtBoletoCarteira = $_POST["edtBoletoCarteira"];
              $edtBoletoVarCarteira = $_POST["edtBoletoVarCarteira"];
              $edtBoletoFormatoConvenio = $_POST["edtBoletoFormatoConvenio"];
              $edtBoletoFormatoNossoNumero = $_POST["edtBoletoFormatoNossoNumero"];
              $edtBoletoTaxa = MoneyMySQLInserir($_POST["edtBoletoTaxa"]);
              
              $edtValorMulta = MoneyMySQLInserir($_POST["edtValorMulta"]);
              $edtValorJuros = MoneyMySQLInserir($_POST["edtValorJuros"]);
              
              $edtInstrucoes1 = $_POST["edtInstrucoes1"];
              $edtInstrucoes2 = $_POST["edtInstrucoes2"];
              $edtInstrucoes3 = $_POST["edtInstrucoes3"];
              $edtInstrucoes4 = $_POST["edtInstrucoes4"]; 
              
              //Configurações para os vales de colaboradores
              $cmbValeContaCaixaId = $_POST["cmbValeContaCaixaId"];
              $cmbValeCentroCustoId = $_POST["cmbValeCentroCustoId"];        

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE parametros_sistema SET 
																boleto_agencia = '$edtBoletoAgencia',
																boleto_conta = '$edtBoletoConta',
                                boleto_convenio = '$edtBoletoConvenio',
                                boleto_contrato = '$edtBoletoContrato',
                                boleto_carteira = '$edtBoletoCarteira',
                                boleto_var_carteira = '$edtBoletoVarCarteira',
                                boleto_formato_convenio = '$edtBoletoFormatoConvenio',
                                boleto_formato_nosso_numero = '$edtBoletoFormatoNossoNumero',
                                boleto_taxa = '$edtBoletoTaxa',
                                instrucoes1 = '$edtInstrucoes1',
                                instrucoes2 = '$edtInstrucoes2',
                                instrucoes3 = '$edtInstrucoes3',
                                instrucoes4 = '$edtInstrucoes4',
                                
                                valor_multa = '$edtValorMulta',
                                valor_juros = '$edtValorJuros',                                                                                                
                                
                                vale_conta_caixa_id = '$cmbValeContaCaixaId',
                                vale_centro_custo_id = '$cmbValeCentroCustoId'
                                ");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Preferências alteradas com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
            }

		//Monta o sql
    $sql = "SELECT * FROM parametros_sistema";
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
              <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva as preferências" value="Salvar Preferências" />
              <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
            </td>
	         </tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="2">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados das preferências do sistema e clique em [Salvar Preferências]</td>
			           </tr>
		            </table>             
				      </td>
            </tr>
          </table>
          
          <br/>
          
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
    			  <tr>
    			    <td style="padding-bottom: 6px"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Configurações dos Boletos</span></td>
    			  </tr>
    			</table>  
          
          <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">  
            <tr>
             <td class="dataLabel" width="150">
               <span class="dataLabel">Agência:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoAgencia" type="text" class="datafield" id="edtBoletoAgencia" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_agencia] ?>"/>&nbsp; - Número da agência, sem dígito verificador.
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Conta:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoConta" type="text" class="datafield" id="edtBoletoConta" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_conta] ?>"/>&nbsp; - Número da conta, sem dígito verificador.
						 </td>
           </tr>  
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Convênio:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoConvenio" type="text" class="datafield" id="edtBoletoConvenio" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_convenio] ?>"/>&nbsp; - Número do convênio - REGRA: 6,7 ou 8 dígitos.
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Contrato:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoContrato" type="text" class="datafield" id="edtBoletoContrato" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_contrato] ?>"/>&nbsp; - Número do contrato com o banco.
						 </td>
           </tr>  
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Carteira:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoCarteira" type="text" class="datafield" id="edtBoletoCarteira" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_carteira] ?>"/>&nbsp; - Número da carteira (normalmente 18)
						 </td>
           </tr> 
           
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Variação Carteira:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoVarCarteira" type="text" class="datafield" id="edtBoletoVarCarteira" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_var_carteira] ?>"/>&nbsp; - Variação da Carteira, com traço (opcional)
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Formatação Convênio:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoFormatoConvenio" type="text" class="datafield" id="edtBoletoFormatoConvenio" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_formato_convenio] ?>"/>&nbsp; - REGRA: 8 p/ Convênio c/ 8 dígitos, 7 p/ Convênio c/ 7 dígitos, ou 6 se Convênio c/ 6 dígitos
						 </td>
           </tr>  
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Formatação Nosso Número:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtBoletoFormatoNossoNumero" type="text" class="datafield" id="edtBoletoFormatoNossoNumero" style="width: 80" maxlength="10" value="<?php echo $campos[boleto_formato_nosso_numero] ?>"/>&nbsp; - REGRA: Usado apenas p/ Convênio c/ 6 dígitos: informe 1 se for Nosso Número de até 5 dígitos ou 2 para opção de até 17 dígitos
						 </td>
           </tr> 
            <tr>
             <td class="dataLabel">
               <span class="dataLabel">Custo de Boleto:</span>
             </td>
             <td class="tabDetailViewDF">
               <?php
								//Acerta a variável com o valor a alterar
							  $valor_alterar = str_replace(".",",",$campos[boleto_taxa]);
              
                //Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtBoletoTaxa";
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
              &nbsp; - Taxa cobrada pelo banco para uso do boleto
						 </td>
           </tr>
            <tr>
             <td class="dataLabel">
               <span class="dataLabel">Linha Instruções 1:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtInstrucoes1" type="text" class="datafield" id="edtInstrucoes1" style="width: 500" maxlength="80" value="<?php echo $campos[instrucoes1] ?>"/>
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Linha Instruções 2:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtInstrucoes2" type="text" class="datafield" id="edtInstrucoes2" style="width: 500" maxlength="80" value="<?php echo $campos[instrucoes2] ?>"/>
						 </td>
           </tr> 
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Linha Instruções 3:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtInstrucoes3" type="text" class="datafield" id="edtInstrucoes3" style="width: 500" maxlength="80" value="<?php echo $campos[instrucoes3] ?>"/>
						 </td>
           </tr> 
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Linha Instruções 4:</span>
             </td>
             <td class="tabDetailViewDF">
               <input name="edtInstrucoes4" type="text" class="datafield" id="edtInstrucoes4" style="width: 500" maxlength="80" value="<?php echo $campos[instrucoes4] ?>"/>
						 </td>
           </tr>                                          
	   		 </table>
         
         <br/>
          
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
    			  <tr>
    			    <td style="padding-bottom: 6px"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Configurações dos Vales ao Colaborador</span></td>
    			  </tr>
    			</table>  
          
          
          <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">  
            <tr>
             <td class="dataLabel" width="110">
               <span class="dataLabel">Conta-Caixa:</span>
             </td>
             <td class="tabDetailViewDF">
               <span style="font-size: 9px; color: #000000"><strong>Selecione uma conta-caixa para o sistema utilizar sempre que lançar um vale ao colaborador no controle de caixa.</strong></span><br/>
               <select name="cmbValeContaCaixaId" id="cmbValeContaCaixaId" style="width:350px">
                 	<option value="0">Selecione uma Opção</option>
  			 				 	<?php 
  								 	//Monta o while para gerar o combo de escolha
  								 	while ($lookup_conta_caixa_debito = mysql_fetch_object($dados_conta_caixa_debito)) { 
  							 	?>
                 	<option <?php if ($lookup_conta_caixa_debito->id == $campos[vale_conta_caixa_id]) {
                        echo " selected ";
                      } ?>
                      value="<?php echo $lookup_conta_caixa_debito->id ?>"><?php echo $lookup_conta_caixa_debito->id . " - " . $lookup_conta_caixa_debito->nome ?></option>
                 	<?php } ?>
        	     </select>
               
               
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Centro de Custo:</span>
             </td>
             <td class="tabDetailViewDF">
               <span style="font-size: 9px; color: #000000"><strong>Selecione um centro de custo para o sistema utilizar sempre que lançar um vale ao colaborador no controle de caixa.</strong></span><br/>
               <select name="cmbValeCentroCustoId" id="cmbValeCentroCustoId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha
									 while ($lookup_centro_custo = mysql_fetch_object($dados_centro_custo)) { 
								 ?>
                 <option <?php if ($lookup_centro_custo->id == $campos[vale_centro_custo_id]) {
                        echo " selected ";
                      } ?>
                      value="<?php echo $lookup_centro_custo->id ?>"><?php echo $lookup_centro_custo->id . " - " . $lookup_centro_custo->nome ?></option>
                 <?php } ?>
               </select>
						 </td>
           </tr>
         </table>
         
         <br/>
          
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
    			  <tr>
    			    <td style="padding-bottom: 6px"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Configurações das Contas a Receber</span></td>
    			  </tr>
    			</table>  
          
          
          <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">  
            <tr>
             <td class="dataLabel" width="110">
               <span class="dataLabel">Taxa Multa:</span>
             </td>
             <td class="tabDetailViewDF">
               <?php
								//Acerta a variável com o valor a alterar
							  $valor_alterar = str_replace(".",",",$campos[valor_multa]);
              
                //Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorMulta";
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
              &nbsp;%
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">
               <span class="dataLabel">Taxa de Juros:</span>
             </td>
             <td class="tabDetailViewDF">
               <?php
								//Acerta a variável com o valor a alterar
							  $valor_alterar = str_replace(".",",",$campos[valor_juros]);
              
                //Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorJuros";
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
              &nbsp;%
						 </td>
           </tr>
         </table>
     	 </td>
   	 </tr>
		
	</table>  	 
	</form>

  </tr>
</table>
