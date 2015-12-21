<?php 
###########
## Módulo para cadastro de Locacao
## Criado: 28/08/2007
## Alterado: 
## Alterações: 
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
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdExibir() {

	 //Captura o valor referente ao radio button selecionado
   var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
   
	 for (var i=0; i < edtTipoPessoaValor.length; i++) {
     if (edtTipoPessoaValor[i].checked == true) {
       edtTipoPessoaValor = edtTipoPessoaValor[i].value;
       break;
     }
   }

	if (edtTipoPessoaValor == 1) {
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
	  IDCol = document.getElementById(40);
	  IDFor.style.display = "none";
	  IDCol.style.display = "none";
	  IDCli.style.display = "inline";
	}
	
	if (edtTipoPessoaValor == 2) {
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
	  IDCol = document.getElementById(40);
	  IDFor.style.display = "inline";
	  IDCol.style.display = "none";
	  IDCli.style.display = "none";		
	}
	
	if (edtTipoPessoaValor == 3) {
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
	  IDCol = document.getElementById(40);
	  IDFor.style.display = "none";
	  IDCol.style.display = "inline";
	  IDCli.style.display = "none";		
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
   var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
   
	 for (var i=0; i < edtTipoPessoaValor.length; i++) {
     if (edtTipoPessoaValor[i].checked == true) {
       edtTipoPessoaValor = edtTipoPessoaValor[i].value;
       break;
     }
   }

	if (edtTipoPessoaValor == 1) {
	 if (Form.cmbClienteId.value == 0) {
      alert("É necessário selecionar um Cliente !");
      Form.cmbClienteId.focus();
      return false;
   }
	}
	
	if (edtTipoPessoaValor == 2) {
	 if (Form.cmbFornecedorId.value == 0) {
      alert("É necessário selecionar um Fornecedor !");
      Form.cmbFornecedorId.focus();
      return false;
   }
	}
	
	if (edtTipoPessoaValor == 3) {
	 if (Form.cmbColaboradorId.value == 0) {
      alert("É necessário selecionar um Colaborador !");
      Form.cmbColaboradorId.focus();
      return false;
   }
	}

   if (Form.edtDescricao.value.length == 0) {
      alert("É necessário Informar a Descrição !");
      Form.edtDescricao.focus();
      return false;
   }  
   if (Form.edtDataDevolucaoPrevista.value.length == 0) {
      alert("É necessário Informar a Data de Devolução Prevista !");
      Form.edtDataDevolucaoPrevista.focus();
      return false;
   }
   return true;
}

</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 

//Monta o lookup da tabela de clientes (para a pessoa_id)
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de colaboradores (para a pessoa_id)
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 
?>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=LocacaoCadastra" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Locação</span></td>
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
						$edtTipoPessoa = $_POST["edtTipoPessoa"];
						
						if ($edtTipoPessoa == 1) {
							$cmbPessoaId = 	$_POST["cmbClienteId"];
						}
	
						if ($edtTipoPessoa == 2) {
							$cmbPessoaId = 	$_POST["cmbFornecedorId"];
						}
	
						if ($edtTipoPessoa == 3) {
							$cmbPessoaId = 	$_POST["cmbColaboradorId"];
						}
											
	          $edtDescricao = $_POST["edtDescricao"];
						$edtDataDevolucaoPrevista = DataMySQLInserir($_POST["edtDataDevolucaoPrevista"]);
	          $edtSituacao = $_POST["edtSituacao"];
	          $edtDataDevolucaoRealizada = DataMySQLInserir($_POST["edtDataDevolucaoRealizada"]);
	          $edtRecebidoPor = $_POST["edtRecebidoPor"];
	          $edtObservacoes = $_POST["edtObservacoes"];
						$edtOperadorId = $usuarioId;	
            
            $edtObservacoesFinanceiro = $_POST["edtObservacoesFinanceiro"];
            $edtNotaFiscal = $_POST["edtNotaFiscal"];
					  $edtPosicaoFinanceira = $_POST["edtPosicaoFinanceira"];				 											
												
						//Monta o sql apra inserir a locacao
						$sql = "INSERT INTO locacao (
										empresa_id, 
										data,
										tipo_pessoa,
										pessoa_id,
										descricao,
										devolucao_prevista,
										situacao, 
										devolucao_realizada,
										recebido_por,
										observacoes,
										cadastro_timestamp,
										cadastro_operador_id,
                    obs_financeira,
                    posicao_financeira,
                    numero_nf
						
										) VALUES (
						
										'$edtEmpresaId',
										'$edtData',
										'$edtTipoPessoa',
										'$cmbPessoaId',
										'$edtDescricao',
										'$edtDataDevolucaoPrevista',
										'$edtSituacao',
										'$edtDataDevolucaoRealizada',
										'$edtRecebidoPor',
										'$edtObservacoes',
										now(),
										'$edtOperadorId',
                    '$edtObservacoesFinanceiro',                    
								    '$edtPosicaoFinanceira',
                		'$edtNotaFiscal'		
										);";
						
						//Executa a query de inserção da conta
						$query = mysql_query($sql);																								
			
						//Exibe a mensagem de inclusão com sucesso
		        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Locação cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        
        //Fecha o if de postagem
				}
        ?>

        <table cellspacing="0" cellpadding="0" width="520" border="0">
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Locação" />
            	<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
            </td>
	       	</tr>
        </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									 		<img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados da locação e clique em [Salvar Locação]									 
									 </td>
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
            <td width="140" valign="top" class="dataLabel">Tipo de Pessoa:<br/><br/>Locador:</td>
            <td colspan="4" class="tabDetailViewDF">
              <table cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="117" height="20">
                    <input type="radio" name="edtTipoPessoa" value="1" onclick="wdExibir()" />
                      Cliente
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoPessoa" value="2" checked onclick="wdExibir()" />
                      Fornecedor
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoPessoa" value="3" onclick="wdExibir()" />
                      Colaborador
                  </td>
                </tr>
              </table>
              
              
							<table id="20" cellpadding="0" cellspacing="0" style="display: none">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Cliente:<br/>
									 <select name="cmbClienteId" id="cmbClienteId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_cliente = mysql_fetch_object($dados_cliente)) { 
										 ?>
		                 <option value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>

              <table id="30" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Fornecedor:<br/>                  
		               <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
										 ?>
		                 <option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>

              <table id="40" cellpadding="0" cellspacing="0" style="display: none">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Colaborador:<br/>                  
		               <select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
										 ?>
		                 <option value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>              
				  	</td>
          </tr>

           <tr>
             <td width="140" class="dataLabel">Descrição:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 420px;color: #6666CC; font-weight: bold" size="84" maxlength="80" />             
						 </td>
          </tr>

           <tr>
             <td width="140" class="dataLabel">Devolução Prevista:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataDevolucaoPrevista";
							    $objData->strRequerido = true;
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
          
          <tr>
            <td width="140" valign="top" class="dataLabel">Situação:</td>
            <td width="250" class="tabDetailViewDF">
              <table width="250" cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="90" height="20">
                    <label>
                    <input name="edtSituacao" type="radio" value="1" checked="checked" />
                      Em aberto </label>
                  </td>
                  <td width="78" height="20">
                    <label>
                    <input type="radio" name="edtSituacao" value="2" />
                      Devolvido </label>
                  </td>
                </tr>
              </table>
				  	</td>
            <td width="146" class="dataLabel">Data de Devolução: </td>
            <td colspan="2" class="tabDetailViewDF">
              <?php
							    //Define a data do formul&aacute;rio
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataDevolucaoRealizada";
							    $objData->strRequerido = false;
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
             <td width="140" class="dataLabel">Recebido Por:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <input name="edtRecebidoPor" type="text" class="datafield" id="edtRecebidoPor" size="74" maxlength="70" />             
						 </td>
           </tr>					           
           <tr>
             <td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
             <td colspan="4" class="tabDetailViewDF">
             	 <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>    							
						 </td>
           </tr>
	   		</table>
        
        	   	  <br/>
				<span class="TituloModulo">Informações Financeiras:</span>
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
						<tr>
             <td valign="top" width="130" class="dataLabel">Posição Financeira:</td>
             <td colspan="5" class="tabDetailViewDF">						   
						   <table width="500" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="150" height="20">
                     <input name="edtPosicaoFinanceira" type="radio" value="1" checked="checked" />&nbsp;A Receber
                   </td>
                   <td width="150" height="20">
                     <input name="edtPosicaoFinanceira" type="radio" value="2" />&nbsp;Recebido
                   </td>
                   <td width="200" height="20">
                     <input name="edtPosicaoFinanceira" type="radio" value="3" />&nbsp;Cortesia
                   </td>
                 </tr>
               </table>	
				  	 </td>
           </tr>           
					 <tr>
             <td valign="top" class="dataLabel">Número da NF:</td>
             <td colspan="5" class="tabDetailViewDF">
						   <input name="edtNotaFiscal" type="text" class="datafield" id="edtNotaFiscal" style="width: 110px" maxlength="20">
				  	 </td>
           </tr>
					 <tr>
             <td valign="top" class="dataLabel">Obs. Financeiras:</td>
             <td colspan="5" class="tabDetailViewDF">
						   <textarea name="edtObservacoesFinanceiro" wrap="virtual" class="datafield" id="edtObservacoesFinanceiro" style="width: 100%; height: 80px"></textarea>
				  	 </td>
           </tr>
	   	  </table>   	  
        
     </td>
   </tr>
</table>  	 
</form>

</tr>
</table>
