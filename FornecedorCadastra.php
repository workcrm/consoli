<?php 
###########
## Módulo para cadastro de fornecedores
## Criado: 18/04/2007 - Maycon Edinger
## Alterado: 28/05/2007 - Maycon Edinger
## Alterações:
## 28/05/2007 - Implementado o campo ClienteID para a tabela 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup da tabela de cidades
//Monta o SQL
$lista_cidade = "SELECT * FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cidade = mysql_query($lista_cidade);
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() {
   var Form;
   Form = document.cadastro;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário Informar o Nome/Razão Social do Fornecedor !");
      Form.edtNome.focus();
      return false;
   }
    return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=FornecedorCadastra" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Fornecedores</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
		      <tr>
		        <td colspan="5">
			      <table id="1" style="display: none" width="100%" cellpadding="0" cellspacing="0" border="0">
		          <tr>
		            <td valign="midle"><img src="image/bt_ajuda.gif" width="13" height="16" /></td>
		          </tr>
			      </table>
			    </td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

          <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST["Submit"]){

					$chkAtivo = $_POST["chkAtivo"];
          $edtEmpresaId = $empresaId;
					$edtNome = $_POST["edtNome"];
          $edtTipoPessoa = $_POST["edtTipoPessoa"];
          $edtEndereco = $_POST["edtEndereco"];
          $edtComplemento = $_POST["edtComplemento"];
          $edtBairro = $_POST["edtBairro"];
          $cmbCidadeId = $_POST["cmbCidadeId"];
          $edtUf = $_POST["edtUf"];
          $edtCep = $_POST["edtCep"];
          $edtInscricao = $_POST["edtInscricao"];
          $edtCnpj = $_POST["edtCnpj"];
          $edtRg = $_POST["edtRg"];
          $edtCpf = $_POST["edtCpf"];
          $edtTelefone = $_POST["edtTelefone"];
          $edtFax = $_POST["edtFax"];
          $edtCelular = $_POST["edtCelular"];
          $edtEmail = $_POST["edtEmail"];
					$edtContato = $_POST["edtContato"];
					$edtOperadorId = $usuarioId;
          $edtObservacoes = $_POST["edtObservacoes"];

					//Monta o sql e executa a query de inserção dos fornecedores
    	    $sql = mysql_query("
                INSERT INTO fornecedores (
								ativo,
								empresa_id, 
								nome, 
								tipo_pessoa,
								endereco, 
								complemento,
								bairro, 
								cidade_id, 
								uf, 
								cep,
								inscricao,
								cnpj,
								rg,
								cpf, 
								telefone, 
								fax, 
								celular, 
								email,
								contato,
								observacoes,
								cadastro_timestamp,
								cadastro_operador_id
				
								) VALUES (
				
								'$chkAtivo',
								'$edtEmpresaId',
								'$edtNome',
								'$edtTipoPessoa',
								'$edtEndereco',
								'$edtComplemento',
								'$edtBairro',
								'$cmbCidadeId',
								'$edtUf',
								'$edtCep',
								'$edtInscricao',
								'$edtCnpj',
								'$edtRg',
								'$edtCpf',
								'$edtTelefone',
								'$edtFax',
								'$edtCelular',
								'$edtEmail',
								'$edtContato',
								'$edtObservacoes',
								now(),
								'$edtOperadorId'				
								);");
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Fornecedor cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>

        <table cellspacing="0" cellpadding="0" width="520" border="0">
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Submit" type="submit" class="button" id="Submit" title="Salva o fornecedor atual" value="Salvar Fornecedor">
            	<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
            </td>            
	       	</tr>
        </table>
           
         <TABLE class="tabDetailView" cellSpacing="0" cellPadding="0" width="100%" border="0">
         <TBODY>
           <TR>
             <TD class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="20">
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Fornecedor e clique em [Salvar Fornecedor] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
             <TD class="dataLabel" width="20%">
               <span class="dataLabel">Tipo de Fornecedor:</span>             </TD>
             <TD colspan="3" class=tabDetailViewDF>
                 <table width="100%" cellpadding="0" cellspacing="0">
                   <tr valign="middle">
                     <td width="129" height="20">
                       <label>
                       <input name="edtTipoPessoa" type="radio" value="1" checked>
                       <img src="image/bt_prospect.gif" alt="Pessoa Física" width="16" height="16" align="middle"> F&iacute;sica </label>
                     </td>
                     <td width="192" height="20">
                       <label>
                       <input type="radio" name="edtTipoPessoa" value="2">
                       <img src="image/bt_cliente.gif" alt="Pessoa Jurídica" width="16" height="16" align="middle"> Jur&iacute;dica </label>
                     </td>
                     <td width="176">
                       <div align="right">Cadastro Ativo
                         <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                       </div>
                     </td>
                   </tr>
                 </table>
              </TD>
          </TR>
           <TR>
             <TD class="dataLabel">Nome/Razão Social:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300;color: #6666CC; font-weight: bold" size="84" maxlength="50">
             </TD>
           </TR>
           <TR>
             <TD class="dataLabel">Endere&ccedil;o:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 300" size="84" maxlength="80">
             </TD>
             </TR>
          <TR>
            <TD class=dataLabel>
							<span class="dataLabel">Complemento:</span>						</TD>
            <TD colspan="3" class="tabDetailViewDF">
              <input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="84" maxlength="50">
            </TD>
          </TR>
          <TR>
            <TD class=dataLabel>Bairro:</TD>
            <TD colspan="3" class="tabDetailViewDF">
              <input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50">
            </TD>
          </TR>
          <TR>
            <TD class="dataLabel">Cidade:</TD>
            <TD colspan="3" class="tabDetailViewDF">
               <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha
									 while ($lookup_cidade = mysql_fetch_object($dados_cidade)) { 
								 ?>
                 <option value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?> </option>
                 <?php } ?>
               </select>
            </TD>
            </TR>
          <TR>
            <TD class="dataLabel">UF:</TD>
            <TD width="25%" class="tabDetailViewDF">
							<select class="datafield"name="edtUf" id="edtUf">
				        <option value="AC">AC</option>
				        <option value="AL">AL</option>
				        <option value="AM">AM</option>
				        <option value="BA">BA</option>
				        <option value="CE">CE</option>
				        <option value="DF">DF</option>
				        <option value="ES">ES</option>
				        <option value="GO">GO</option>
				        <option value="MA">MA</option>
				        <option value="MG">MG</option>
				        <option value="MS">MS</option>
				        <option value="MT">MT</option>
				        <option value="PA">PA</option>
				        <option value="PB">PB</option>
				        <option value="PE">PE</option>
				        <option value="PI">PI</option>
				        <option value="PR">PR</option>
				        <option value="RJ">RJ</option>
				        <option value="RN">RN</option>
				        <option value="RO">RO</option>
				        <option value="RR">RR</option>
				        <option value="RS">RS</option>
			    	    <option value="SC">SC</option>
				        <option value="SE">SE</option>
				        <option value="SP">SP</option>
				        <option value="TO">TO</option>
				      </select>
						</TD>
            <TD width="18%" class=dataLabel>Cep:</TD>
            <TD vAlign=top class=tabDetailViewDF>
							<input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onKeyPress="return FormataCampo(document.cadastro, 'edtCep', '99999-999', event);">
						</TD>
		   		</TR>
          <TR>
            <TD valign="top" class=dataLabel>Inscri&ccedil;&atilde;o Estadual:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtInscricao" type="text" class="datafield" id="edtInscricao" size="17" maxlength="15" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
            </TD>
            <TD class=dataLabel>CNPJ:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtCnpj" type="text" class="datafield" id="edtCnpj" size="20" maxlength="18" onKeyPress="return FormataCampo(document.cadastro, 'edtCnpj', '99.999.999/9999-99', event);">
            </TD>
          </TR>
           
          <TR>
            <TD valign="top" class=dataLabel>N&ordm; RG: </TD>
            <TD class=tabDetailViewDF>
              <input name="edtRg" type="text" class="datafield" id="edtRg" size="20" maxlength="18">
            </TD>
            <TD class=dataLabel>CPF:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtCpf" type="text" class="datafield" id="edtCpf" size="17" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtCpf', '999.999.999-99', event);">
            </TD>
          </TR>
          <TR>
            <TD valign="top" class=dataLabel>Telefone:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtTelefone', '(99) 9999-9999', event);">
            </TD>
            <TD class=dataLabel>Fax:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtFax" type="text" class="datafield" id="edtFax" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtFax', '(99) 9999-9999', event);">
            </td>
          </tr>
          <tr>
            <td valign="top" class=dataLabel>Celular:</TD>
            <td colspan="3" class=tabDetailViewDF>
              <input name="edtCelular" type="text" class="datafield" id="edtCelular" size="16" maxlength="14" onKeyPress="return FormataCampo(document.cadastro, 'edtCelular', '(99) 9999-9999', event);">
            </td>
          </tr>
          <tr>
            <td valign="top" class=dataLabel>E-mail: </TD>
            <td colspan="3" class=tabDetailViewDF>
              <input name="edtEmail" type="text" class="datafield" id="edtEmail" style="width: 300; text-transform:lowercase" size="52" maxlength="50">
            </td>
          </tr>
          <tr>
            <td valign="top" class="dataLabel">Contato:</td>
            <td colspan="3" class="tabDetailViewDF">
              <input name="edtContato" type="text" class="datafield" id="edtContato" style="width: 300" size="52" maxlength="50">
            </td>
          </tr>
          
           <tr>
             <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
             <td colspan="3" class="tabDetailViewDF">
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
						 </td>
          </tr>
	     </table>
     </td>
   </tr>
</table>
  	 
</form>

</tr>
</table>
