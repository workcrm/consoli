<?php 
###########
## Módulo para alteração dos clientes
## Criado: 17/04/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
## 28/05/2007 - Implementado o campo ClienteID para a tabela
## 11/07/2007 - Removido validação para o campo email
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

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Monta o lookup da tabela de cidades
//Monta o SQL
$lista_cidade = "SELECT * FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cidade = mysql_query($lista_cidade);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitClienteAltera() {
   var Form;
   Form = document.frmClienteAltera;
   
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar o Nome/Razão Social do Cliente !");
      Form.edtNome.focus();
      return false;
   }

	 return true;
}
</script>

<form name="frmClienteAltera" action="sistema.php?ModuloNome=ClienteAltera" method="post" onsubmit="return wdSubmitClienteAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração do Cliente</span></td>
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
						$chkAtivo = $_POST["chkAtivo"];
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
            $edtTelefoneComl = $_POST["edtTelefoneComl"];
            $edtFax = $_POST["edtFax"];
            $edtCelular = $_POST["edtCelular"];
            $edtEmail = $_POST["edtEmail"];
						$edtContato = $_POST["edtContato"];
            $edtObservacoes = $_POST["edtObservacoes"];
            $edtDataAniversario = DataMySQLInserir($_POST["edtDataAniversario"]);
						$edtDataComemorativa = DataMySQLInserir($_POST["edtDataComemorativa"]);
						$edtDescricaoData = $_POST["edtDescricaoData"];
						$edtOperadorId = $usuarioId;

						//Executa a query de alteração da cliente
    	    	$sql = mysql_query("UPDATE clientes SET 
																ativo = '$chkAtivo',
																nome = '$edtNome', 
																tipo_pessoa = '$edtTipoPessoa', 
																endereco = '$edtEndereco', 
																complemento = '$edtComplemento', 
																bairro = '$edtBairro', 
																cidade_id = '$cmbCidadeId', 
																uf = '$edtUf', 
																cep = '$edtCep',
																inscricao = '$edtInscricao',
																cnpj = '$edtCnpj',
																rg = '$edtRg',
																cpf = '$edtCpf', 
																telefone = '$edtTelefone',
																telefone_comercial = '$edtTelefoneComl', 
																fax = '$edtFax', 
																celular = '$edtCelular', 
																email = '$edtEmail',
																contato = '$edtContato',
																observacoes = '$edtObservacoes',
																data_aniversario = '$edtDataAniversario',
																data_comemorativa = '$edtDataComemorativa',
																descricao_data = '$edtDescricaoData',
																alteracao_timestamp = now(),
																alteracao_operador_id = '$edtOperadorId'
																WHERE id = '$id' ");			 

				//Exibe a mensagem de alteração com sucesso
        echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cliente alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
        	}

        //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
				//Captura o id da cleinte a alterar
        if ($_GET["Id"]) {
					$ClienteId = $_GET["Id"];
				} else {
				  $ClienteId = $_POST["Id"];
				}
				
				//Monta o sql para busca da cliente
        $sql = "SELECT * FROM clientes WHERE id = $ClienteId";

        //Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
        $campos = mysql_fetch_array($resultado);
		
				//Efetua o switch para o check de ativo
				switch ($campos[ativo]) {
          case 00: $ativo_status = "value='1'";	  break;
          case 01: $ativo_status = "value='1' checked";  break;
				}
      
				//Efetua o switch para o campo de tipo de pessoa
				switch ($campos[tipo_pessoa]) {
          case 01: $pess_1 = "checked";	$pess_2 = ""; 		  break;
          case 02: $pess_1 = "";		$pess_2 = "checked";  break;
				}
        					
			?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="100%"> </td>
          </tr>
          <tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $ClienteId ?>" />
            	<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Cliente" />
            	<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
           	</td>
           	<td width="36" align="right">
							<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Cliente" onclick="wdCarregarFormulario('ClienteExibe.php?ClienteId=<?php echo $ClienteId ?>','conteudo')" />						
						</td>
	       	</tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
              <table cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do cliente e clique em [Salvar Cliente] <br />
                     <br />
                     <span class="style1">Aten&ccedil;&atilde;o:</span> Esta transa&ccedil;&atilde;o ser&aacute; monitorada pelo sistema e ser&aacute; gerado um log da atividade para fins de auditoria.</td>
			     			</tr>
		       		</table>						 
						</td>
	       	 </TR>
           <TR>
             <TD class="dataLabel" width="140">
						 	 <span class="dataLabel">Tipo de Cliente:</span>						 
						 </TD>
             <TD colspan="3" class=tabDetailViewDF>
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="116" height="20">
                     <input type="radio" name="edtTipoPessoa" value="1" <?php echo $pess_1 ?>>
                     <img src="image/bt_prospect.gif" alt="Física" width="16" height="16" align="middle"> Pessoa F&iacute;sica									 </td>
                   <td width="174" height="20">
                     <input type="radio" name="edtTipoPessoa" value="2" <?php echo $pess_2 ?>>
                     <img src="image/bt_cliente.gif" alt="Jurídica" width="16" height="16" align="middle"> Pessoa Jur&iacute;dica									 </td>
                   <td>
                     <div align="right"><input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>&nbsp;Cadastro Ativo                     </div>									 </td>
                 </tr>
               </table>             
						 </TD>
           </TR>
           <TR>
             <TD class="dataLabel">Nome/Razão:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 470px; color: #6666CC; font-weight: bold" maxlength="75" value="<?php echo $campos[nome] ?>">						 
						 </TD>
           </TR>
           <TR>
             <TD class="dataLabel">Endere&ccedil;o:</TD>
             <TD colspan="3" valign="middle" class=tabDetailViewDF>
               <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 470" maxlength="80" value="<?php echo $campos[endereco] ?>">						 
						 </TD>
           </TR>
           <TR>
             <TD class=dataLabel>Complemento:</TD>
             <TD colspan="3" class="tabDetailViewDF"><input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[complemento] ?>" /></TD>
           </TR>
           <TR>
            <TD class=dataLabel><span class="dataLabel">Bairro:</span></TD>
            <TD colspan="3" class="tabDetailViewDF">
							<input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[bairro] ?>">		    		</TD>
          </TR>
          <TR>
            <TD class="dataLabel">Cidade:</TD>
            <TD colspan="3" class="tabDetailViewDF">
               <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
                 <?php while ($lookup_cidade = mysql_fetch_object($dados_cidade)) { ?>
                 <option <?php if ($lookup_cidade->id == $campos[cidade_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?>				 
								 </option>
        	      <?php } ?>
               </select>
            </TD>
          </TR>
          <TR>
            <TD class="dataLabel">UF:</TD>
            <TD width="136" class="tabDetailViewDF">
							<select class="datafield"name="edtUf" id="edtUf">
	        			<option selected value="<?php echo $campos[uf] ?>"><?php echo $campos[uf] ?></option>
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
            <TD width="90" class=dataLabel>Cep:</TD>
            <TD width="404" vAlign=top class=tabDetailViewDF>
							<input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtCep', '99999-999', event);" value="<?php echo $campos[cep] ?>">						</TD>
		   		</TR>
          <TR>
            <TD valign="top" class=dataLabel>Inscr. Estadual: </TD>
            <TD class=tabDetailViewDF>
               <input name="edtInscricao" type="text" class="datafield" id="edtInscricao" size="17" maxlength="15" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[inscricao] ?>">						</TD>
            <TD class=dataLabel>CNPJ:</TD>
            <TD class=tabDetailViewDF>
               <input name="edtCnpj" type="text" class="datafield" id="edtCnpj" size="20" maxlength="18" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtCnpj', '99.999.999/9999-99', event);" value="<?php echo $campos[cnpj] ?>">						</TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>N&ordm; RG: </TD>
             <TD class=tabDetailViewDF>
						 		<input name="edtRg" type="text" class="datafield" id="edtRg" size="20" maxlength="14" value="<?php echo $campos[rg] ?>"></TD>
             <TD class=dataLabel>CPF:</TD>
             <TD class=tabDetailViewDF><input name="edtCpf" type="text" class="datafield" id="edtCpf" size="20" maxlength="18" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtCpf', '999.999.999-99', event);" value="<?php echo $campos[cpf] ?>" /></TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>Telefone Residencial:</TD>
             <TD class=tabDetailViewDF>
               <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtTelefone', '(99) 9999-9999', event);" value="<?php echo $campos[telefone] ?>">						 
						 </TD>
             <TD valign="top" class=dataLabel>Telefone Comercial:</TD>
             <TD class=tabDetailViewDF>
               <input name="edtTelefoneComl" type="text" class="datafield" id="edtTelefoneComl" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtTelefoneComl', '(99) 9999-9999', event);" value="<?php echo $campos[telefone_comercial] ?>">
						 </TD>
           </TR>
           <TR>
             <TD class=dataLabel>Fax:</TD>
             <TD class=tabDetailViewDF>
               <input name="edtFax" type="text" class="datafield" id="edtFax" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtFax', '(99) 9999-9999', event);" value="<?php echo $campos[fax] ?>">						 </TD>
             <TD valign="top" class=dataLabel>Celular:</TD>
             <TD class=tabDetailViewDF><input name="edtCelular" type="text" class="datafield" id="edtCelular" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmClienteAltera, 'edtCelular', '(99) 9999-9999', event);" value="<?php echo $campos[celular] ?>" /></TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>E-mail: </TD>
             <TD colspan="3" class=tabDetailViewDF><input name="edtEmail" type="text" class="datafield" id="edtEmail" style="width: 280; text-transform:lowercase" size="52" maxlength="50" value="<?php echo $campos[email] ?>" /></TD>
           </TR>
           <TR>
             <TD valign="top" class=dataLabel>Contato:</TD>
      		 <td colspan="3" class=tabDetailViewDF><input name="edtContato" type="text" class="datafield" id="edtContato" style="width: 280; text-transform:lowercase" size="52" maxlength="50" value="<?php echo $campos[contato] ?>" /></td>
           </TR>
          <TR>
            <TD valign="top" class="dataLabel">Data Aniversário:</TD>
            <TD colspan="3" class="tabDetailViewDF">
             	<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmClienteAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataAniversario";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_aniversario]);
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
          </TR>  
					<TR>
            <TD valign="top" class="dataLabel">Data do Evento:</TD>
            <TD class="tabDetailViewDF">
             	<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmClienteAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataComemorativa";
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_comemorativa]);
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
            <TD class=dataLabel>Descrição:</TD>
            <TD class=tabDetailViewDF>
              <input name="edtDescricaoData" type="text" class="datafield" id="edtDescricaoData" style="width: 300px" maxlength="75" value="<?php echo $campos[descricao_data] ?>" />
            </TD>
          </TR>  
           <TR>
             <TD valign="top" class=dataLabel>Observa&ccedil;&otilde;es:</TD>
             <TD colspan="3" class=tabDetailViewDF>
							 <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>						 </TD>
           </TR>
	   	 </TABLE>
     </td>
   </tr>
</table>  	 

</form>

</td>
</tr>
</table>
