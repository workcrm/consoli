<?php 
###########
## Módulo para Exibição dos dados dos clientes
## Criado: - 17/04/2007 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 28/05/2007 - Implementado o Cadastro do Cidade ID na tabela
## 05/06/2007 - Implementado rotina de segurança para usuario nivel 1 só visualize
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA){
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = "00:00:00";
  $data_array = split("[- ]",$DATA);
  if ($DATA <> ""){
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
		$HORA = $data_array[3];
    return $DIA."/".$MES."/".$ANO. " - " . $HORA;
  }else {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA."/".$MES."/".$ANO;
  }
}

//Pega o valor da cliente a exibir
$ClienteId = $_GET["ClienteId"];

//Monta o SQL
$sql = "SELECT 
  		  con.id,
		  	con.ativo,
		  	con.empresa_id,
		  	con.nome,
		  	con.tipo_pessoa,
		  	con.endereco,
		  	con.complemento,
		  	con.bairro,
		  	con.cidade_id,
		  	con.uf,
		  	con.cep,
		  	con.inscricao,
		  	con.cnpj,
		  	con.rg,
		  	con.cpf,
		  	con.telefone,
		  	con.telefone_comercial,
		  	con.fax,
		  	con.celular,
		  	con.email,
		  	con.contato,
		  	con.data_aniversario,
		  	con.data_comemorativa,
		  	con.descricao_data,
		  	con.observacoes,
		  	con.cadastro_timestamp,
		  	con.cadastro_operador_id,
		  	con.alteracao_timestamp,
		  	con.alteracao_operador_id,
				cid.nome as cidade_nome,
		  	usu_cad.nome as operador_cadastro_nome, 
		  	usu_cad.sobrenome as operador_cadastro_sobrenome,
		  	usu_alt.nome as operador_alteracao_nome, 
		  	usu_alt.sobrenome as operador_alteracao_sobrenome		  

		  	FROM clientes con
		  	LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
		  	LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
		  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
		  	WHERE con.id = '$ClienteId'";
  
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$campos = mysql_fetch_array($resultado);

//Efetua o switch para a figura do tipo de cliente
switch ($campos[tipo_pessoa]) 
{
  case 1: $conta_figura = "<img src='./image/bt_prospect.gif' alt='Pessoa Física' /> Pessoa Física";	break;
  case 2: $conta_figura = "<img src='./image/bt_cliente.gif' alt='Pessoa Jurídica' /> Pessoa Jurídica"; break;
}

  
//Efetua o switch para o campo de ativo
switch ($campos[ativo]) 
{
  case 0: $desc_ativo = "Cadastro Inativo"; break;
	case 1: $desc_ativo = "Cadastro Ativo"; break;
}    

?>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Cliente</span></td>
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
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td width="95" style="PADDING-BOTTOM: 2px"> 
						<form name="frmContaExibe" action="#">
							<input name="btnEditarConta" type="button" class="button" title="Edita este Cliente" value="Editar Cliente" onclick="wdCarregarFormulario('ClienteAltera.php?Id=<?php echo $campos[id] ?>&headers=1','conteudo')">
						</form>
					</td>
					<?php
					
						if ($usuarioNome == "Maycon" OR $usuarioNome == "Valqueline" OR $usuarioNome == "Bruna")
						{
					
					?>
					<td width="90" style="PADDING-BOTTOM: 2px">
						<form id="exclui" name="exclui" action="ProcessaExclusao.php" method="post"><input class="button" title="Exclui este Cliente" onclick='return confirm(\"Confirma a exclusão deste Cliente ?\")' type='submit' value='Excluir' name='Delete'><input name='Id' type='hidden' value=<?php $campos[id] ?> /><input name='Modulo' type='hidden' value='clientes' /></form>
					</td>
					<?php
						}
					?>
					<td align="right" style="PADDING-BOTTOM: 2px">
						<input class="button" title="Emite o relatório dos detalhes do cliente" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/ClienteDetalheRelatorioPDF.php?ClienteId=<?php echo $campos[id] ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')">
					</td>
				</tr>
			</table>
           
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="20">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									<img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar alterar este Cliente, clique em [Editar Cliente]								
								</td>
							</tr>
						</table>					
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Código:</td>
						<td colspan="3" valign="middle" class="tabDetailViewDF">
            <span style="color: #990000;"><strong><?php echo $campos[id] ?></strong></span>				  
          </td>
        </tr>
        <tr>
          <td class="dataLabel" width="120">
              <span class="dataLabel">Tipo de Cliente:</span>          
          </td>
          <td colspan="3" class="tabDetailViewDF">          
             <table width="100%" cellpadding="0" cellspacing="0">
               <tr valign="middle">
                 <td width="300" height="20"><?php echo $conta_figura ?></td>
                 <td><div align="right"><?php echo $desc_ativo ?></div></td>
               </tr>
             </table>					
          </td>
        </tr>
        <tr>
          <td class="dataLabel">Nome/Razão Social:</td>
          <td colspan="3" valign="middle" class="tabDetailViewDF">
            <span><strong><?php echo $campos[nome] ?></strong></span>				  
          </td>
        </tr>
        <tr>
          <td class="dataLabel">Endere&ccedil;o:</td>
          <td colspan="3" valign="middle" class="tabDetailViewDF"><?php echo $campos[endereco] ?></td>
        </tr>
        <tr>
          <td class="dataLabel">Complemento:</td>
          <td colspan="3" class="tabDetailViewDF"><?php echo $campos[complemento] ?></td>
        </tr>
        <tr>
          <td class="dataLabel"><span class="dataLabel">Bairro:</span></td>
          <td colspan="3" class="tabDetailViewDF"><?php echo $campos[bairro] ?></td>
        </tr>
        <tr>
          <td class="dataLabel">Cidade:</td>
          <td colspan="3" class="tabDetailViewDF"><?php echo $campos[cidade_nome] ?></td>
        </tr>
        <tr>
          <td class="dataLabel">UF:</td>
          <td width="130" class="tabDetailViewDF"><?php echo $campos[uf] ?></td>
          <td width="50" class="dataLabel">Cep:</td>
          <td valign="top" class="tabDetailViewDF"><?php echo $campos[cep] ?></td>
		   	</tr>
        <tr>
          <td valign="top" class="dataLabel">Inscri&ccedil;&atilde;o Estadual: </td>
          <td class="tabDetailViewDF"><?php echo $campos[inscricao] ?></td>
          <td class="dataLabel">CNPJ:</TD>
          <td class="tabDetailViewDF"><?php echo $campos[cnpj] ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">N&ordm; RG: </td>
          <td class="tabDetailViewDF"><?php echo $campos[rg] ?></td>
          <td class="dataLabel">CPF:</td>
          <td class="tabDetailViewDF"><?php echo $campos[cpf] ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Telefone Residencial:</td>
          <td class="tabDetailViewDF"><?php echo $campos[telefone] ?></td>
          <td class="dataLabel">Telefone Comercial:</td>
          <td class="tabDetailViewDF"><?php echo $campos[telefone_comercial] ?></td>
        </tr>
        <tr>
          <td class="dataLabel">Fax:</td>
          <td class="tabDetailViewDF"><?php echo $campos[fax] ?></td>
          <td valign="top" class="dataLabel">Celular:</td>
          <td class="tabDetailViewDF"><?php echo $campos[celular] ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">E-mail: </td>
          <td colspan="3" class="tabDetailViewDF"><a title="Clique para enviar um email para o cliente" href="mailto://<?php echo $campos[email] ?>"><?php echo $campos[email] ?></a></TD>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Contato:</td>
      		<td colspan="3" class="tabDetailViewDF"><?php echo $campos[contato] ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Data Aniversário:</td>
      		<td colspan="3" class="tabDetailViewDF"><?php echo DataMySQLRetornar($campos[data_aniversario]) ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Data do Evento:</td>
          <td class="tabDetailViewDF"><?php echo DataMySQLRetornar($campos[data_comemorativa]) ?></td>
          <td class="dataLabel">Descrição:</td>
          <td class="tabDetailViewDF"><?php echo $campos[descricao_data] ?></td>
        </tr>          
        <tr>
          <td valign="top" class="dataLabel">Observa&ccedil;&otilde;es:</td>
          <td colspan="3" class="tabDetailViewDF"><?php echo $campos[observacoes] ?></td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Data de Cadastro: </td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($campos[cadastro_timestamp]) 
						?>					
          </td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o nome do operador do cadastro da conta
							echo $campos[operador_cadastro_nome] . " " . $campos[operador_cadastro_sobrenome] 
						?>					
          </td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o: </td>
          <td class="tabDetailViewDF">
		  	 		<?php 
				 			//Verifica se este registro já foi alterado
				 			if ($campos[alteracao_operador_id] <> 0) {
								//Exibe o timestamp da alteração da conta
				   			echo TimestampMySQLRetornar($campos[alteracao_timestamp]);
				 			}
				 		?>			 		
          </td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF">
				 		<?php 
				 			//Verifica se este registro já foi alterado
				 			if ($campos[alteracao_operador_id] <> 0) {
								//Exibe o nome do operador da alteração da conta
				   			echo $campos[operador_alteracao_nome] . " " . $campos[operador_alteracao_sobrenome];
				 			}
				 		?>			 		
          </td>
        </tr>
	  </table>
	</td>
</tr>
</table>  	 
</td>
</tr>

</table>
</td>
