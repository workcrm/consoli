<?php 
###########
## Módulo para Exibição dos dados dos colaboradores
## Criado: 20/04/2007 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 23/04/2007 - Acrescentado campo para valor_taxa_extra e renomeado campo valor_taxa
## 20/05/2007 - Adicionado novos campos
## 28/05/2007 - Implementado o campo ClienteID para a tabela
## 05/06/2007 - Implementado campo Dados complementares e redefinido rotinas de segurança
##							Implementado rotina de segurança para usuario nivel 1 só visualize
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
function TimestampMySQLRetornar($DATA)
{
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = "00:00:00";
  $data_array = split("[- ]",$DATA);
  if ($DATA <> "")
  {
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
		$HORA = $data_array[3];
    return $DIA."/".$MES."/".$ANO. " - " . $HORA;
  }
  else 
  {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA."/".$MES."/".$ANO;
  }
}

//Pega o valor da cliente a exibir
$ColaboradorId = $_GET["ColaboradorId"];

//Monta o SQL
$sql = "SELECT 
  		  con.id,
		  	con.ativo,
		  	con.empresa_id,
		  	con.nome,
		  	con.tipo,
		  	con.endereco,
		  	con.complemento,
		  	con.bairro,
		  	con.cidade_id,
		  	con.uf,
		  	con.cep,
		  	con.rg,
		  	con.titulo_eleitor,
		  	con.ctps,
		  	con.pis,
		  	con.nacionalidade,
		  	con.local_nascimento,
		  	con.data_nascimento,
		  	con.nome_pai,
		  	con.nome_mae,
		  	con.estado_civil,
		  	con.conjuge,
		  	con.cpf,
		  	con.telefone,
		  	con.fax,
		  	con.celular,
		  	con.email,
		  	con.data_admissao,
		  	con.data_desligamento,
		  	con.valor_salario,
		  	con.valor_taxa_normal,
		  	con.valor_taxa_extra,
		  	con.valor_hora,
		  	con.banco_horas,
		  	con.funcao_id,
		  	con.tipo_colaborador_id,
		  	con.chk_dirige,
		  	con.chk_fuma,
		  	con.chk_bebe,
		  	con.chk_brinco,
		  	con.chk_sem_fumar,
		  	con.chk_tirar_brinco,
		  	con.chk_tirar_barba,
				con.chk_tem_filho,
				con.chk_hora_extra,
				con.chk_trabalha_fds,
				con.chk_vale_transporte,		  	
		  	con.foto,
		  	con.contato,
		  	con.dados_complementares,
				con.observacoes,
		  	con.cadastro_timestamp,
		  	con.cadastro_operador_id,
		  	con.alteracao_timestamp,
		  	con.alteracao_operador_id,
		  	cid.nome as cidade_nome,
		  	usu_cad.nome as operador_cadastro_nome, 
		  	usu_cad.sobrenome as operador_cadastro_sobrenome,
		  	usu_alt.nome as operador_alteracao_nome, 
		  	usu_alt.sobrenome as operador_alteracao_sobrenome,
				fun.nome as funcao_nome

		  	FROM colaboradores con
		  	LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
		  	LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
				LEFT OUTER JOIN funcoes fun ON con.funcao_id = fun.id 
		  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
		  	
		  	WHERE con.id = '$ColaboradorId'";
  
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$campos = mysql_fetch_array($resultado);

  
//Efetua o switch para o campo de ativo
switch ($campos[ativo]) 
{
  case 0: $desc_ativo = "Cadastro Inativo"; break;
	case 1: $desc_ativo = "Cadastro Ativo"; break;
}    

//Efetua o switch para o campo de tipo
switch ($campos[tipo]) 
{
  case 1: $desc_tipo = "FREELANCE"; break;
	case 2: $desc_tipo = "FUNCIONÁRIO"; break;
  case 3: $desc_tipo = "EX-FUNCIONÁRIO"; break;
}

//Efetua o switch para o campo de banco de horas
switch ($campos[banco_horas]) 
{
  case 1: $desc_banco = "Sim"; break;
  case 0: $desc_banco = "Não"; break;
}	

//Efetua o switch para o campo de dirige
switch ($campos[chk_dirige]) 
{
  case 0: $figura_dirige = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_dirige = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de fuma
switch ($campos[chk_fuma]) 
{
  case 0: $figura_fuma = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_fuma = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de bebe
switch ($campos[chk_bebe]) 
{
  case 0: $figura_bebe = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_bebe = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de brinco
switch ($campos[chk_brinco]) 
{
  case 0: $figura_brinco = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_brinco = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de sem fumar
switch ($campos[chk_sem_fumar]) 
{
  case 0: $figura_sem_fumar = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_sem_fumar = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo tirar brinco
switch ($campos[chk_tirar_brinco]) 
{
  case 0: $figura_tirar_brinco = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_tirar_brinco = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de tirar barba
switch ($campos[chk_tirar_barba]) 
{
  case 0: $figura_tirar_barba = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_tirar_barba = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de filhos
switch ($campos[chk_tem_filho]) 
{
  case 0: $figura_filhos = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_filhos = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de hora extra
switch ($campos[chk_hora_extra]) 
{
  case 0: $figura_hora_extra = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_hora_extra = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo trablhar fds
switch ($campos[chk_trabalha_fds]) 
{
  case 0: $figura_trabalha_fds = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_trabalha_fds = "<img src='image/grid_ativo.gif'/>"; break;
}

//Efetua o switch para o campo de vale transporte
switch ($campos[chk_vale_transporte]) 
{
  case 0: $figura_vale_transporte = "<img src='image/grid_cancela.gif'/>"; break;
	case 1: $figura_vale_transporte = "<img src='image/grid_ativo.gif'/>"; break;
}
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Colaborador</span>
          </td>
			  </tr>
			  <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				  </td>
			  </tr>
				</table>
	
<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  class="text">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td width="130" style="PADDING-BOTTOM: 2px"> 						
						<form name="frmContaExibe" action="#">
 	        		<input name="btnEditarConta" type="button" class="button" title="Edita este Colaborador" value="Editar Colaborador" onclick="wdCarregarFormulario('ColaboradorAltera.php?Id=<?php echo $campos[id] ?>&headers=1','conteudo')" />
 	        	</form>
          </td>

	      	<td width="90" style="PADDING-BOTTOM: 2px">
					  <?php
					    //Verifica o nível de acesso do usuário
					    	//Exibe o botão de excluir
					    	echo "<form id='exclui' name='exclui' action='ProcessaExclusao.php' method='post'><input class=button title='Exclui este Colaborador' onclick='return confirm(\"Confirma a exclusão deste Colaborador ?\")' type='submit' value='Excluir Colaborador' name='Delete'><input name='Id' type='hidden' value=$campos[id] /><input name='Modulo' type='hidden' value='colaboradores' /></form>";
					  ?>
          </td>
          <td align="right" style="PADDING-BOTTOM: 2px">					
						<input class="button" title="Emite o relatório dos detalhes do colaborador" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/ColaboradorDetalheRelatorioPDF.php?ColaboradorId=<?php echo $campos[id] ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&Ta=<?php echo $nivelAcesso ?>&UsuarioId=<?php echo $usuarioId ?>')">
				 </td>
	  		</tr>
    </table>
           
    <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
	      <tr>
	        <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
	          <table cellspacing="0" cellpadding="0" width="100%" border="0">
	            <tr>
	              <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									<img src="image/bt_cadastro.gif" width="16" height="15"/> Caso desejar alterar este Colaborador, clique em [Editar Colaborador]. Para excluir, clique em [Excluir Colaborador]								
								</td>
		     			</tr>
		        </table>					
					</td>
	      </tr>

         <tr>
           <td width="140" class="dataLabel">
             <span class="dataLabel">Nome:</span>					 
					 </td>
           <td colspan="4" class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="462" height="20">
                     <span class="dataField"><strong><?php echo $campos[nome] ?></strong></span>					
									 </td>
                   <td width="119">
                     <div align="right">
                       <?php echo $desc_ativo ?>                     
										 </div>					    
									 </td>
                 </tr>
               </table>						
						</td>
         </tr>
         <tr>
           <td width="140" class="dataLabel">Tipo de Colaborador: </td>
           <td colspan="3" valign="middle" class="tabDetailViewDF">
							<span style="color: #990000"><b><?php echo $desc_tipo ?></b></span>					 
					 </td>
					 <td width="160" rowspan="7" align="center" valign="middle" class="tabDetailViewDF" style="border-left:1px solid; border-color:#dfdfdf; padding: 0px;">
							<?php
							//Verifica se existe um caminho de foto no banco							
							if ($campos[foto] != "") 
              {
							?>
							<div id="foto"><img src="imagem_colaborador/<?php echo $campos[foto] ?>" width="155" height="200" /></div>
							<?php
							//fecha o IF
							} 
              else 
              { 
								echo "Sem foto definida !"; 
							};
							?>					 
						</td>
         </tr>
         <tr>
           <td width="140" class="dataLabel">Fun&ccedil;&atilde;o:</td>
           <td colspan="3" valign="middle" class="tabDetailViewDF">
							<?php echo $campos[funcao_nome] ?>				  
					 </td>           
         </tr>
         <tr>
           <td width="140" class="dataLabel">Endere&ccedil;o:</td>
           <td colspan="3" valign="middle" class="tabDetailViewDF">
							<?php echo $campos[endereco] ?>					 
					 </td>
        </tr>
        <tr>
          <td width="140" class="dataLabel">
						<span class="dataLabel">Complemento:</span>					
					</td>
          <td colspan="3" class="tabDetailViewDF">
						<?php echo $campos[complemento] ?>					
					</td>
        </tr>
        <tr>
          <td width="140" class="dataLabel">Bairro:</td>
          <td colspan="3" class="tabDetailViewDF">
            <?php echo $campos[bairro] ?>					
					</td>
        </tr>
        <tr>
          <td width="140" class="dataLabel">Cidade:</td>
          <td colspan="3" class="tabDetailViewDF">
            <?php echo $campos[cidade_nome] ?>					
					</td>
        </tr>
        <tr>
          <td width="140" class="dataLabel">UF:</td>
          <td width="173" class="tabDetailViewDF">
						<?php echo $campos[uf] ?>				  
					</td>
          <td width="146" class="dataLabel">Cep:</td>
          <td width="129" valign="top" class="tabDetailViewDF">
						<?php echo $campos[cep] ?>					
					</td>
	   		</tr>
         
        <tr>
          <td width="140" valign="top" class="dataLabel">N&ordm; RG: </td>
          <td width="173" class="tabDetailViewDF">
            <?php echo $campos[rg] ?>					</td>
          <td width="146" class="dataLabel">CPF:</td>
          <td colspan="2" class="tabDetailViewDF">
            <?php echo $campos["cpf"] ?>					</td>
          </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">T&iacute;tulo de Eleitor: </td>
          <td width="173" class="tabDetailViewDF">
						<?php echo $campos[titulo_eleitor] ?>					
          </td>
          <td width="146" class="dataLabel">N&ordm; CTPS: </td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo $campos[ctps] ?>					
          </td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">N&ordm; PIS: </td>
          <td width="173" class="tabDetailViewDF">
						<?php echo $campos[pis] ?>					</td>
          <td width="146" class="dataLabel">Nacionalidade:</td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo $campos[nacionalidade] ?>					</td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Data Nascimento:</td>
          <td width="173" class="tabDetailViewDF">
						<?php echo DataMySQLRetornar($campos[data_nascimento]) ?>					</td>
          <td width="146" class="dataLabel">Local Nascimento: </td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo $campos[local_nascimento] ?>					</td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Nome do Pai: </td>
          <td colspan="4" class="tabDetailViewDF">
						<?php echo $campos[nome_pai] ?>					</td>
          </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Nome da M&atilde;e: </td>
          <td colspan="4" class="tabDetailViewDF">
						<?php echo $campos[nome_mae] ?>					</td>
          </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Estado Civil: </td>
          <td width="173" class="tabDetailViewDF">
            <?php 
							if ($campos[estado_civil] == '0') 
              {
								echo "Não informado";
							} 
              else 
              {
								echo $campos[estado_civil];
							}
						?>
					</td>
          <td width="146" class="dataLabel">C&ocirc;njuge:</td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo $campos[conjuge] ?>					
          </td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Telefone:</td>
          <td width="173" class="tabDetailViewDF">
            <?php echo $campos[telefone] ?>					
          </td>
          <td width="146" class="dataLabel">Fax:</td>
          <td colspan="2" class="tabDetailViewDF">
            <?php echo $campos[fax] ?>					
          </td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabe">Celular:</td>
          <td colspan="4" class="tabDetailViewDF">
            <?php echo $campos[celular] ?>					
          </td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">E-mail: </td>
          <td colspan="4" class="tabDetailViewDF">
            <a title="Clique para enviar um email para o colaborador" href="mailto://<?php echo $campos[email] ?>"><?php echo $campos[email] ?></a></TD>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Data Admiss&atilde;o: </td>
          <td width="173" class="tabDetailViewDF">
						<?php echo DataMySQLRetornar($campos[data_admissao]) ?>					
          </td>
          <td width="146" class="dataLabel">Data Desligamento: </td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo DataMySQLRetornar($campos[data_desligamento]) ?>
          </td>
        </tr>
            
				<tr>
          <td width="140" valign="top" class="dataLabel">Sal&aacute;rio:</td>
          <td width="173" class="tabDetailViewDF">						
						<?php	echo number_format($campos[valor_salario], 2, ",", ".") ?> 
					</td>
          <td width="146" class="dataLabel">Valor Hora: </td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo number_format($campos[valor_hora], 2, ",", ".") ?>
					</td>
        </tr>
        <tr>
          <td width="140" valign="top" class="dataLabel">Valor Taxa Normal:</td>
          <td class="tabDetailViewDF">
						<?php echo number_format($campos[valor_taxa_normal], 2, ",", ".") ?>
					</td>
          <td class="dataLabel">Valor Taxa Extra :</td>
          <td colspan="2" class="tabDetailViewDF">
						<?php echo number_format($campos[valor_taxa_extra], 2, ",", ".") ?>
					</td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Banco de Horas:</td>
          <td colspan="4" class="tabDetailViewDF">
					  <?php echo $desc_banco ?>
					</td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Contato:</td>
          <td colspan="4" class="tabDetailViewDF"><?php echo $campos[contato] ?></td>
        </tr>
         
         <tr>
           <td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
           <td colspan="4" class="tabDetailViewDF">
					 		<table width="100%" cellpadding="0" cellspacing="0">                      
                 <tr valign="middle">
                   <td height="20" colspan="4"><strong>Caracter&iacute;sticas e Particularidades:</strong>
									 </td>
                 </tr>
                 <tr valign="middle">
                   <td width="22" height="20"><?php echo $figura_dirige ?></td>
                   <td width="236">Dirige </td>
                   <td width="27"><?php echo $figura_sem_fumar ?></td>
                   <td width="296">Fica sem fumar durante o trabalho </td>
                 </tr>
                 <tr valign="middle">
                   <td height="20"><?php echo $figura_fuma ?></td>
                   <td>Fuma</td>
                   <td><?php echo $figura_tirar_brinco ?></td>
                   <td>Disposto a tirar o brinco </td>
                 </tr>
                 <tr valign="middle">
                   <td height="20"><?php echo $figura_bebe ?></td>
                   <td>Bebe</td>
                   <td><?php echo $figura_tirar_barba ?></td>
                   <td>Disposto a tirar a barba</td>
                 </tr>
                 <tr valign="middle">
                   <td height="20"><?php echo $figura_brinco ?></td>
                   <td> Usa Brinco</td>
                   <td><?php echo $figura_filhos ?></td>
                   <td>Possui filhos</td>
                 </tr>
                 <tr valign="middle">
                   <td height="20"><?php echo $figura_hora_extra ?></td>
                   <td>Pode fazer hora extra</td>
                   <td><?php echo $figura_trabalha_fds ?></td>
                   <td>Pode trabalhar nos finais de semana</td>
                 </tr>
                 <tr valign="middle">
                   <td height="20"><?php echo $figura_vale_transporte ?></td>
                   <td colspan="3">Precisa Vale-Transporte</td>
                 </tr>
                 <tr valign="middle">
                   <td height="14">&nbsp;</td>
                   <td colspan="3">&nbsp;</td>
                 </tr>
                 <tr valign="middle">
                   <td height="14" colspan="4"><strong>Dados Complementares:</strong></td>
                 </tr>
                 <tr valign="middle">
                   <td height="20" colspan="4">
                     <?php echo $campos[dados_complementares] ?>									 
									 </td>
                 </tr>
               </table>						 
						 </td>
         	 </tr>

           <tr>
             <td valign="top" class="dataLabel">Observações:</td>
             <td colspan="4" class="tabDetailViewDF">
						   <?php echo $campos[observacoes] ?>	
						 </td>
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
	          <td colspan="2" class="tabDetailViewDF">
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
					 			if ($campos[alteracao_operador_id] <> 0) 
                 {
									//Exibe o timestamp da alteração da conta
					   			echo TimestampMySQLRetornar($campos[alteracao_timestamp]);
					 			}
					 		?>			 		
						</td>
	          <td class="dataLabel">Operador:</td>
	          <td colspan="2" class="tabDetailViewDF">
					 		<?php 
					 			//Verifica se este registro já foi alterado
					 			if ($campos[alteracao_operador_id] <> 0) 
                 {
									//Exibe o nome do operador da alteração da conta
					   			echo $campos[operador_alteracao_nome] . " " . $campos[operador_alteracao_sobrenome];
					 			}
					 		?>			 		 
						</td>
					</tr>
	  		</table>
        <br/>
  
  
  
        <?php 
        
        //******** VALES DO COLABORADOR ************
        //verifica os vales deste colaborador e exibe na tela
			  $sql_consulta_permissao = mysql_query("SELECT novo_vale FROM usuarios	WHERE usuario_id = $usuarioId");
			
  			$dados_usuario = mysql_fetch_array($sql_consulta_permissao);
        
        //verifica se o usuário pode ver este menu
        if ($dados_usuario["novo_vale"] == 1)
        {
        ?>
  			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td height="30">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						  <tr>   
								<td><span class="TituloModulo">Vales Fornecidos ao Colaborador</span></td>
						  </tr>
              <tr>
			          <td>
				          <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
				        </td>
			        </tr>
						</table>
					</td>
				</tr>
			</table>

    	<?php
	         
			//verifica os vales deste colaborador e exibe na tela
			$sql_consulta = mysql_query("SELECT * FROM vales																	
																	 WHERE colaborador_id = $ColaboradorId
																	 ORDER by data
																	 ");
			
			//Verifica o numero de registros retornados
			$registros = mysql_num_rows($sql_consulta); 
		   
		  ?>
			<div id="88">   
			<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		
			<?php
		
			  if ($registros > 0) 
        { 
          
          //Caso houverem registros
        	//Exibe o cabeçalho da tabela
  				echo "
          <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
  					<td width='22'>&nbsp;&nbsp;S</td>
            <td width='80' align='center'>Data Emissão</td>
   		      <td width='120' align='right' style='padding-right: 10px' >Valor</td>
   		      <td width='550'>Observações</td>
            <td width='80'>Devolução</td>	      
          </tr>
  	    	";
        
        }
	    	
			  //Caso não houverem registros
			  if ($registros == 0) 
        { 
		
  			  //Exibe uma linha dizendo que nao registros
  			  echo "
  			  <tr height='24'>
  		      <td colspan='4' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
  				  	<font color='#33485C'><b>Não há vales fornecidos para este colaborador !</b></font>
  					</td>
  			  </tr>";	  
			  
        }     	
	
				//Cria a variável do total de vales
				$total_vales = 0;
				
				//Cria o array e o percorre para montar a listagem dinamicamente
		    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
		    	 
		    	
			?>
		
      <tr height="20" valign="middle">
				<td width="22" align="center">
          <img src="image/grid_exclui.gif" alt="Clique para excluir o lançamento deste vale ao colaborador" onclick="if(confirm('Confirma a exclusão deste vale fornecido e também o seu lançamento no fluxo de caixa ?')) {wdCarregarFormulario('ValeExclui.php?ValeId=<?php echo $dados_consulta[id] ?>&ColaboradorId=<?php echo $ColaboradorId ?>','conteudo')}" style="cursor: pointer" />
        </td>
        <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-bottom: 1px">
          &nbsp;<font color="#CC3300" size="2" face="Tahoma"><a title="Clique para alterar os dados deste vale" href="#" onclick="wdCarregarFormulario('ValeAltera.php?ValeId=<?php echo $dados_consulta[id] ?>&headers=1','conteudo')"><?php echo DataMySQLRetornar($dados_consulta["data"]); ?></a></font>        
				</td>
        <td align="right" style="padding-right: 10px">
          <?php echo "R$ " . number_format($dados_consulta[valor], 2, ",", ".") ?>
				</td>
        <td valign="middle" bgcolor="#fdfdfd">
          <?php echo $dados_consulta["observacoes"] ?>
				</td>
				<td valign="middle" bgcolor="#fdfdfd">
          <?php 
          
            if ($dados_consulta["data_devolucao"] != "0000-00-00")
            {
            
              echo $dados_consulta["data_devolucao"];
               
            }
              
          ?>
				</td>											
  	  </tr>
			
			<?php
      
      $total_vales = $total_vales + $dados_consulta[valor];
			
			//Fecha o while
			}
			?>
			</table>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
				<tr>
					<td height="26">
						<span style="font-size: 12px">
						<?php 

								echo "&nbsp;&nbsp;Valor total de vales:    <b>R$ " . number_format($total_vales, 2, ",", ".") . "</b>"; 
							
							?>
						</span>
					</td>
				</tr>
			</table>
			</div>
      <?php 
      
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
