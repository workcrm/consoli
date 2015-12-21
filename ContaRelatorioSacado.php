<?php 
###########
## Módulo para exibição da filtragem do relatório das contas - por categoria
## Criado: 14/05/2007- Maycon Edinger
## Alterado: 26/06/2007 - Maycon Edinger 
## Alterações:
## 26/06/2007 - Incluída opção de filtrar junto as datas 
###########
/**
* @package workeventos
* @abstract Módulo para exibição da filtragem do relatório das contas - por sacado
* @author Maycon Edinger
* @copyright 2007 - Work CRM
*/

if ($_GET['headers'] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");  
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

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

?>

<script language="JavaScript">
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
</script>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Sacado</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 

				<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
              <TR>
                <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='21'>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                      <TR>
                        <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Selecione o Sacado desejado para filtragem das contas:</TD>
                      </TR>
                  </TABLE>
                </TD>
              </TR>
			  			
							<tr>
			  				<td class='dataLabel' valign="top" width='105'>
			  				Tipo de Pessoa:</br></br>Sacado:
			  				</td>
			  				<td colspan="3" class=tabDetailViewDF>
									<table cellpadding="0" cellspacing="0">
		                <tr valign="middle">
		                  <td width="117" height='20'>
		                    <input type="radio" name="edtTipoPessoa" value="1" onClick="wdExibir()">
		                      Cliente
		                  </td>
		                  <td width="120" height="20">
		                    <input type="radio" name="edtTipoPessoa" value="2" checked onClick="wdExibir()">
		                      Fornecedor
		                  </td>
		                  <td width="120" height="20">
		                    <input type="radio" name="edtTipoPessoa" value="3" onClick="wdExibir()">
		                      Colaborador
		                  </td>
		                </tr>
	              	</table>
									
							<table id='20' cellpadding="0" cellspacing="0" style="display: none">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Cliente:</br>
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

              <table id='30' cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Fornecedor:</br>                  
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

              <table id='40' cellpadding="0" cellspacing="0" style="display: none">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Colaborador:</br>                  
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
							<TR>
                <TD class='dataLabel' width='65'>
                  <SLOT>In&iacute;cio:</SLOT>
                </TD>
                <TD width="107" class=tabDetailViewDF>
									<?php
									    //Define a data do formulário
									    $objData->strFormulario = "cadastro";  
									    //Nome do campo que deve ser criado
									    $objData->strNome = "edtDataIni";
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
                </TD>
                <TD width="61" class=dataLabel>T&eacute;rmino:</TD>
                <TD width="100" class=tabDetailViewDF>
									<?php
									    //Define a data do formulário
									    $objData->strFormulario = "cadastro";  
									    //Nome do campo que deve ser criado
									    $objData->strNome = "edtDataFim";
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
                </TD>                
			  		</tr>
						<tr>
		  				<td class='dataLabel'>
		  				Situação
		  				</td>
		  				<td colspan="3" class=tabDetailViewDF>
								<table width="530" border="0" cellspacing="0" cellpadding="0">
					    	  <tr>
					    	  	<td width="110">
					    	  		<input type="radio" name="edtSituacao" value="0" checked/> Todas
										</td>
										<td width="110">
					    	  		<input type="radio" name="edtSituacao" value="1" /> Em Aberto
										</td>
					    	  	<td width="110">
					    	  		<input type="radio" name="edtSituacao" value="2" /> Pagas
										</td>
					    	  	<td>
					    	  		<input type="radio" name="edtSituacao" value="3" /> Vencidas
										</td>						
					    	  </tr>
					    	</table>		  				
		  				</td>
		  			</tr>               		
          </TABLE>

		</td>
  </tr>
</table>