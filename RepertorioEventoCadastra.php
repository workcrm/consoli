<?php 
###########
## Módulo para cadastro de repertório do evento
## Criado: 08/10/2007 - Maycon Edinger
## Alterado: 22/11/2007 - Maycon Edinger
## Alterações: 
## 22/11/2007 - Incluído link para gerenciamento de serviços do evento
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

//Recupera o id do evento
if($_POST) {
  $EventoId = $_POST["EventoId"]; 
} else {
  $EventoId = $_GET["EventoId"]; 
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
													eve.contato_obs2,
													eve.contato_fone2,
													eve.contato_obs1,
													eve.contato_fone1,
													eve.contato2,
													eve.contato3,
													eve.contato_obs3,
													eve.contato_fone3,													
													eve.data_realizacao,
													eve.hora_realizacao,
													eve.duracao,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
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

//Monta o lookup da tabela de categoria de repertorio
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_repertorio WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Monta o lookup da tabela de musicas
//Monta o SQL
$lista_musica = "SELECT * FROM musicas WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_musica = mysql_query($lista_musica);
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
	 if (Form.cmbCategoriaId.value == 0) {
      alert("É necessário selecionar um Momento de Repertório para o Evento !");
      Form.cmbCategoriaId.focus();
      return false;
   }
	 if (Form.cmbMusicaId.value == 0) {
      alert("É necessário selecionar uma Música para o Repertório do Evento !");
      Form.cmbMusicaId.focus();
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
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Repertório do Evento</span></td>
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
          $cmbCategoriaId = $_POST["cmbCategoriaId"];
          $cmbMusicaId = $_POST["cmbMusicaId"];
          $edtObservacoes = $_POST["edtObservacoes"];

					//Monta o sql e executa a query de inserção
    	    $sql = mysql_query("
                INSERT INTO eventos_repertorio (
								empresa_id, 
								evento_id,
								categoria_repertorio_id,								
								musica_id,
								observacoes
				
								) VALUES (
				
								'$edtEmpresaId',
								'$edtEventoId',
								'$cmbCategoriaId',
								'$cmbMusicaId',
								'$edtObservacoes'
								);");
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Repertório cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>
          <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
            <TBODY>
              <TR>
                <TD class='dataLabel' width='15%'> Nome do Evento : </TD>
                <TD colspan='5' class=tabDetailViewDF>
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["descricao"] ?>
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
	             </TD>
	           </TR>
							<TR>
                <TD colspan="6" valign="middle" class=tabDetailViewDF>
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
                    		 <a title='Clique para exibir o detalhamento deste evento' href='#' onClick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a> 
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_data_evento_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar as datas deste evento' href='#' onClick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_participante_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onClick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_endereco_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endereços deste evento' href='#' onClick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onClick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>                			
                			<td width='30'>
                    		 <img src='./image/bt_servico_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os serviços deste evento' href='#' onClick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>                			
                			<td width='30'>
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onClick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>                			
              			</tr>
              			
										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width='30'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onClick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onClick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>
											<?php 
											
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
											
											?>	
																 
                			<td width='30'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onClick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
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
          				</table>
				  			</TD>
              </TR>              
            </TBODY>
          </TABLE>

<?php 
//Monta um sql para pesquisar se há repertório para este evento
$sql_conta_rep = mysql_query("SELECT
														 rep.id,
														 rep.categoria_repertorio_id
														 FROM eventos_repertorio rep
														 WHERE rep.evento_id = '$EventoId'
														 ");
														 
$registros = mysql_num_rows($sql_conta_rep); 

//Verifica as categorias cadastradas para o evento
$sql_conta_categorias = mysql_query("SELECT 
																		rep.id, 
																		cat.id as categoria_id,
																		cat.nome as categoria_nome
																		FROM eventos_repertorio rep
																		INNER JOIN categoria_repertorio cat ON cat.id = rep.categoria_repertorio_id
																		WHERE rep.evento_id = '$EventoId'
																		GROUP BY rep.categoria_repertorio_id");														

?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'></br><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Repertório para o Evento:</span></td>
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
		      <td width='350' style='padding-left: 12px'>&nbsp;Música</td>
 		      <td width='350'>Intérprete</td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><b>Não há repertório cadastrado para este evento</b></font>
				</td>
		  </tr>	
		  ";	  
		  }     	

		//Cria o array e o percorre para montar a listagem das categorias
    while ($dados_conta_categoria = mysql_fetch_array($sql_conta_categorias)){

		//Exibe a descrição da categoria
		?>
      <tr valign='middle'>
        <td colspan="7" valign="bottom" style="padding-left: 6px">    				 	 
		  		<span style="font-size: 14px"><b>
					<?php echo $dados_conta_categoria['categoria_nome']; ?>
					</b></span>
				</td>
  	  </tr>
  	
		<?php

		//Monta a pesquisa das musicas listadas na categoria
		//verifica o repertório já cadastrado para este evento e exibe na tela
		$sql_consulta = mysql_query("SELECT
																 rep.id,
																 mus.nome as musica_nome,
																 mus.interprete as musica_interprete
																 FROM eventos_repertorio rep
																 LEFT OUTER JOIN musicas mus ON mus.id = rep.musica_id
																 WHERE rep.evento_id = '$EventoId' AND rep.categoria_repertorio_id = $dados_conta_categoria[categoria_id]
																 ");		
		

		//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
    
?>
      <tr valign='middle'>
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-bottom: 1px; padding-left: 12px">
          &nbsp;
          <font color='#CC3300' size='2' face="Tahoma"><a title="Clique para alterar este repertório" href="#" onclick="wdCarregarFormulario('RepertorioEventoAltera.php?Id=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta['musica_nome']; ?></a></font>        
				</td>
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd'>
          <?php echo $dados_consulta[musica_interprete] ?>
				</td>					
  	  </tr>

		  <?php
		  //Fecha o WHILE
		  }
		  
		  //Fecha o while das categorias
		  }
		  ?>
		</table>
	</td>
</tr>
</table>

														           
          </br>
          
          <TABLE cellSpacing='0' cellPadding='0' width='520' border='0'>
          <tr>
            <td width="484">
              <FORM id='form' name='cadastro' action='sistema.php?ModuloNome=RepertorioEventoCadastra' method='post' onSubmit='return valida_form()'>
			  </td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        	<INPUT name='Submit' type='submit' class=button id="Submit" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Repertório'>
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
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do repertório e clique em [Salvar Repertório] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='20%'>
               <span class="dataLabel">Momento do Repertório:</span>             </TD>
             <TD colspan='3' class=tabDetailViewDF>
               <select name="cmbCategoriaId" id="cmbCategoriaId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_categoria = mysql_fetch_object($dados_categoria)) { 
								 ?>
                 <option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?> </option>
                 <?php } ?>
               </select>						 
						 </TD>
          </TR>
          <TR>
            <TD class='dataLabel'>Música:</TD>
            <TD colspan="3" class='tabDetailViewDF'>
               <select name="cmbMusicaId" id="cmbMusicaId" style="width:350px">
                 <option value="0">Selecione uma Opção</option>
				 				 <?php 
									 //Monta o while para gerar o combo de escolha de funcao
									 while ($lookup_musica = mysql_fetch_object($dados_musica)) { 
								 ?>
                 <option value="<?php echo $lookup_musica->id ?>"><?php echo $lookup_musica->nome ?> </option>
                 <?php } ?>
               </select>
            </TD>
          </TR>          
           <TR>
             <TD valign="top" class=dataLabel>Observações:</TD>
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
