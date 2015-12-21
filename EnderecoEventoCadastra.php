<?php 
###########
## Módulo para cadastro de endereços de evento
## Criado: 21/05/2007 - Maycon Edinger
## Alterado: 23/09/2008
## Alterações: 
## 22/08/2007 - Implementado a exibição do grupo do evento
## 23/11/2007 - Incluído link para gerenciamento de serviços do evento
## 23/09/2008 - Alterado para pegar os dados direto da tabela de fornecedores
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//http://localhost/consoli/sistema.php?ModuloNome=EnderecoEventoCadastra&EventoId=1

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Recupera o id do evento
if($_POST) {
  $EventoId = $_POST["EventoId"]; 
} else {
  $EventoId = $_GET["EventoId"]; 
}

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

//Monitora o evento escolhido para o bactracking do usuário
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = '$EventoId' WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";

//Recupera dos dados do evento
$sql_evento = mysql_query("SELECT 
													eve.id,
													eve.nome,
													eve.descricao,
													eve.status,
													eve.cliente_id,
													eve.responsavel,
													eve.contato1,
													eve.contato_obs1,
													eve.contato_fone1,
													eve.contato2,
													eve.contato_obs2,
													eve.contato_fone2,
													eve.contato3,
													eve.contato_obs3,
													eve.contato_fone3,													
													eve.data_realizacao,
													eve.hora_realizacao,
													eve.duracao,
													eve.enderecos_timestamp,
													eve.enderecos_operador_id,
													concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
													LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.enderecos_operador_id
													WHERE eve.id = '$EventoId'");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) {
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;
} 

//Monta o lookup da tabela de locais de evento
//Monta o SQL
$lista_local = "SELECT * FROM local_evento WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_local = mysql_query($lista_local);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);
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
	 if (Form.cmbLocalId.value == 0) {
      alert("É necessário selecionar um tipo de local para o Evento !");
      Form.cmbLocalId.focus();
      return false;
   }
	 if (Form.cmbFornecedorId.value == 0) {
      alert("É necessário selecionar um fornecedor para o Evento !");
      Form.cmbFornecedorId.focus();
      return false;
   }
   return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Endereços de Eventos</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

      <table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='100%' class='text'>

          <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST['Submit']){

          $edtEmpresaId = $empresaId;
          $edtEventoId = $_POST["EventoId"];
          $cmbLocalId = $_POST["cmbLocalId"];
          $cmbFornecedorId = $_POST["cmbFornecedorId"];
					$edtHoraInicio = $_POST["edtHoraInicio"];
					$edtHoraTermino = $_POST["edtHoraTermino"];
					$edtOperadorId = $usuarioId;
          $edtObservacoes = $_POST["edtObservacoes"];

					//Monta o sql e executa a query de inserção dos clientes
    	    $sql = mysql_query("
                INSERT INTO eventos_endereco (
								empresa_id, 
								evento_id,
								local_id,								
								fornecedor_id,
								hora_inicio,
								hora_termino,								 
								observacoes,
								cadastro_timestamp,
								cadastro_operador_id
				
								) VALUES (
				
								'$edtEmpresaId',
								'$edtEventoId',
								'$cmbLocalId',
								'$cmbFornecedorId',
								'$edtHoraInicio',
								'$edtHoraTermino',
								'$edtObservacoes',
								now(),
								'$edtOperadorId'				
								);");
	
					//Configura a assinatura digital
					$sql = mysql_query("UPDATE eventos SET enderecos_timestamp = now(), enderecos_operador_id = $usuarioId WHERE id = $edtEventoId"); 
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Endereço cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>
          <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
            <TBODY>
              <TR>
                <TD class='dataLabel' width='15%'>Nome do Evento:</TD>
                <TD colspan='5' class=tabDetailViewDF>
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo nl2br($dados_evento["descricao"]) ?>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Status:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
								  <?php echo $desc_status ?>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Data:</TD>
                <TD valign="middle" class=tabDetailViewDF>
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
				  			</TD>
                <TD valign="middle" class=dataLabel>Hora:</TD>
                <TD width="19%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["hora_realizacao"] ?>								
				  			</TD>
                <TD width="12%" valign="middle" class=dataLabel>Dura&ccedil;&atilde;o:</TD>
                <TD width="20%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["duracao"] ?>								
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Cliente:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["cliente_nome"] ?>
				  			</TD>
              </TR>
							<TR>
                <TD valign="top" class='dataLabel'>Grupo:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["grupo_nome"] ?>
				  			</TD>
              </TR>               
              <TR>
                <TD class='dataLabel'>Respons&aacute;vel:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["responsavel"] ?>								
				  			</TD>
              </TR>
	           <TR>
	             <TD valign="top" class='dataLabel'>Contatos:</TD>
	             <TD colspan="5" valign="middle" class=tabDetailViewDF>
	               <table width="100%" cellpadding="0" cellspacing="0">
								   <tr valign="middle">
	                   <td width="300" height='20'>
	                     Nome:
	                   </td>
	                   <td width="260" height='20'>
	                     Observações:
	                   </td>
	                   <td height="20">
	                     Telefone:
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
	                   </td>
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
										 </td>
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
	                   </td>
	                   <td height='20'>
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
	                   </td>
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
	                   </td>
	                 </tr>                 
	               </table>               							 
	             </td>
	           </tr>
							<tr>
                <td colspan="6" valign="middle" class="tabDetailViewDF">
									<table width='100%' cellpadding='0' cellspacing='0' border='0' >
              			<tr valign='middle'>
											<td colspan="8" style="padding-bottom: 4px">
                    		 <span style="font-size: 11px">Tarefas Adicionais:</span>
                			</td>
                		</tr>
              			<tr valign='middle'>
											<td width='30'>
                    		 <img src='./image/bt_evento_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para exibir o detalhamento deste evento' href='#' onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a> 
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_data_evento_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar as datas deste evento' href='#' onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_participante_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_servico_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os serviços deste evento' href='#' onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                		  </td>                			                			

										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width='30'>
                    		 <img src='./image/bt_repertorio_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o repertório deste evento' href='#' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>
                														<?php 
											
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
											
											?>	
																 
                			<td width='30'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                			</td>
                			<?php
                			
                			}else{
                				
                			?>
                			
											<td width='30'>
                    		<img src='./image/bt_fotovideo_gd_off.gif' title='Opção não habilitada para seu nível de acesso !'/>  
                			</td>
											<td>
                    		 &nbsp;
                			</td>
                			
                			<?php
                			
                			}
                			
                			?>
                      <td width="30">
                    		 <img src="./image/bt_documentos_gd.gif" /> 
                			</td>
											<td colspan="4">
                         <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                      </td> 										
              			</tr>                			
              			</tr>
          				</table>
				  			</td>
              </tr>              
          	</table>

<?php 
//verifica os endereços já cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT
														 end.id,
														 end.local_id,
														 end.fornecedor_id,
														 end.nome,
														 end.hora_inicio,
														 end.hora_termino,
														 end.telefone,
														 loc.nome as local_nome,
														 forn.nome as fornecedor_nome
														 FROM eventos_endereco end
														 INNER JOIN local_evento loc ON loc.id = end.local_id
														 LEFT OUTER JOIN fornecedores forn ON forn.id = end.fornecedor_id
														 WHERE end.evento_id = '$EventoId'
														 ");

$registros = mysql_num_rows($sql_consulta); 
?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Endereços Cadastrados para o Evento:</span></td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
      <table id="4" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">

  		<?php
      //Caso houverem registros
    	if ($registros > 0) { 

      	//Exibe o cabeçalho da tabela
				echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
		      <td width='280'>&nbsp;Tipo de Local</td>
 		      <td>Nome</td>
	        <td width='40'><div align='left'>Início</div></td>
		      <td width='60'>Término</td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><strong>Não há endereços cadastrados para este evento</strong></font>
				</td>
		  </tr>	
		  ";	  
		  }     	

		//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
    
?>
      <tr valign='middle'>
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-bottom: 1px">
          &nbsp;<?php echo $dados_consulta[local_nome] ?>
				</td>
        <td height="18">
          <font color='#CC3300' size='2' face="Tahoma">
						<a title="Clique para alterar este endereço" href="#" onclick="wdCarregarFormulario('EnderecoEventoAltera.php?Id=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')">
							<?php 
								
								//Caso seja antigo e não tem fornecedor ID cadastrado
								if ($dados_consulta['fornecedor_id'] == 0){
								
									//Imprime então o campo do nome do fornecedor
									echo $dados_consulta['nome']; 
								
								} else {
									
									//Imprime então o nome do fornecedor
									echo $dados_consulta['fornecedor_nome']; 
								}
									
							?>														
						</a>
					</font>        
				</td>
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd'>
          <?php echo $dados_consulta[hora_inicio] ?>
				</td>
        <td height="18" valign='middle' bgcolor='#fdfdfd'>
          <?php echo $dados_consulta[hora_termino] ?>
				</td>											
  	  </tr>

		  <?php
		  //Fecha o WHILE
		  }
		  ?>
		</table>
	</td>
</tr>
</table>
<br/>
				<span class="TituloModulo">Assinatura Digital:</span>
				<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
        	<tr>
          <td valign="top" width="120" class="dataLabel">Última Alteração:</td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($dados_evento[enderecos_timestamp]) 
						?>					
					</td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" width="200">
						<?php echo $dados_evento[operador_nome]	?>					
					</td>
        </tr>                 
  	  </table>
														           
          </br>
          
          <TABLE cellSpacing='0' cellPadding='0' width='520' border='0'>
          <tr>
            <td width="484">
              <FORM id='form' name='cadastro' action='sistema.php?ModuloNome=EnderecoEventoCadastra' method='post' onSubmit='return valida_form()'>
			  </td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        	<INPUT name='Submit' type='submit' class=button id="Submit" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Endereço'>
            <INPUT class=button title="Limpa o conteúdo dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
          </TD>
          <TD width="36" align=right>	  </TD>
	       </TR>
         </TBODY>
         </TABLE>
           
         <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do endereço do evento e clique em [Salvar Endereço] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='20%'>
               <span class="dataLabel">Tipo de Local:</span>             </TD>
             <TD colspan='3' class=tabDetailViewDF>
               <select name="cmbLocalId" id="cmbLocalId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_local = mysql_fetch_object($dados_local)) { 
								 ?>
                 <option value="<?php echo $lookup_local->id ?>"><?php echo $lookup_local->nome ?> </option>
                 <?php } ?>
               </select>						 
						 </TD>
          </TR>
           <TR>
             <TD class='dataLabel'>Nome:</TD>
        	   <TD colspan="3" valign="middle" class=tabDetailViewDF>
                   <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
										 ?>
		                 <option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?> </option>
		                 <?php } ?>
		               </select>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Hora In&iacute;cio: </TD>
             <TD valign="middle" class=tabDetailViewDF>
               <input name="edtHoraInicio" type="text" class="datafield" id="edtHoraInicio" size="6" maxlength="5" onKeyPress="return FormataCampo(document.cadastro, 'edtHoraInicio', '99:99', event);">
             </TD>
             <TD valign="middle" class=dataLabel>Hora T&eacute;rmino: </TD>
             <TD valign="middle" class=tabDetailViewDF>
               <input name="edtHoraTermino" type="text" class="datafield" id="edtHoraTermino" size="6" maxlength="5" onKeyPress="return FormataCampo(document.cadastro, 'edtHoraTermino', '99:99', event);">
             </TD>
           </TR>          
           <TR>
             <TD valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares :</TD>
             <TD colspan="3" class=tabDetailViewDF>
						   <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
				  </TD>
           </TR>
         </TBODY>
	   </TABLE>
     </td>
   </tr>
</FORM>
</table>  	 

</tr>
</table>
