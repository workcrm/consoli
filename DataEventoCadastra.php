<?php 
###########
## Módulo para cadastro de horarios do evento
## Criado: 04/12/2008 - Maycon Edinger
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

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

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
													eve.grupo_id,
													eve.contato1,
													eve.contato_obs1,
													eve.contato_fone1,
													eve.contato2,
													eve.contato_obs2,
													eve.contato_fone2,
													eve.contato3,
													eve.contato_obs3,
													eve.contato_fone3,
													eve.contato4,
													eve.contato_obs4,
													eve.contato_fone4,
													eve.contato5,
													eve.contato_obs5,
													eve.contato_fone5,
													eve.contato6,
													eve.contato_obs6,
													eve.contato_fone6,
													eve.contato7,
													eve.contato_obs7,
													eve.contato_fone7,
													eve.contato8,
													eve.contato_obs8,
													eve.contato_fone8,
													eve.contato9,
													eve.contato_obs9,
													eve.contato_fone9,
													eve.contato10,
													eve.contato_obs10,
													eve.contato_fone10,
													eve.contato11,
													eve.contato_obs11,
													eve.contato_fone11,
													eve.contato12,
													eve.contato_obs12,
													eve.contato_fone12,
													eve.data_realizacao,
													eve.hora_realizacao,
													eve.duracao,
													eve.datas_timestamp,
													eve.datas_operador_id,
													concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
													LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.datas_operador_id
													WHERE eve.id = '$EventoId'");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
	
	case 0: $desc_status = "Em orçamento"; break;
	case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
	case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;

} 

switch ($dados_evento[grupo_id]) 
{
	
	case 1: $grupo_status = "Consoli Rio do Sul"; break;
	case 2: $grupo_status = "Consoli Joinville"; break;
	case 3: $grupo_status = "Gerri Adriani Consoli ME"; break;	

}
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	
	ID = document.getElementById(id);
	ID.style.display = "none";

}

function valida_form() 
{
	
	var Form;
	Form = document.cadastro;
	
	if (Form.edtData.value == 0) 
	{
		alert("É necessário informar a data para o Evento !");
		Form.edtData.focus();
		return false;
	}
	
	if (Form.edtDescricao.value == 0) 
	{
		
		alert("É necessário informar a descricao da data do Evento !");
		Form.edtDescricao.focus();
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
					<td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Datas do Evento</span></td>
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
						if($_POST['Submit'])
						{

							$edtEmpresaId = $empresaId;
							$edtEventoId = $_POST["EventoId"];
							$edtData = DataMySQLInserir($_POST["edtData"]);
							$edtHora = $_POST["edtHora"];
							$edtDescricao = $_POST["edtDescricao"];
							$edtObservacoes = $_POST["edtObservacoes"];

							//Monta o sql e executa a query de inserção
							$sql = mysql_query("INSERT INTO eventos_data (
												empresa_id, 
												evento_id,
												data,								
												hora,
												descricao,
												observacoes,
												cadastro_timestamp,
												cadastro_operador_id

												) VALUES (

												'$edtEmpresaId',
												'$edtEventoId',
												'$edtData',
												'$edtHora',
												'$edtDescricao',
												'$edtObservacoes',
												now(),
												'$edtOperadorId'
												);");
								
							//Configura a assinatura digital
							$sql = mysql_query("UPDATE eventos SET datas_timestamp = now(), datas_operador_id = $usuarioId WHERE id = $edtEventoId"); 
	
							//Exibe a mensagem de inclusão com sucesso
							echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Data cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        
		}
        ?>
        <table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
            <tr>
                <td class='dataLabel' width='15%'>Nome do Evento: </td>
                <td colspan='5' class="tabDetailViewDF">
					<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
				</td>
            </tr>
            <tr>
                <td valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo nl2br($dados_evento["descricao"]) ?>
				  			</td>
              </tr>
              <tr>
                <td valign="top" class='dataLabel'>Status:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
								  <?php echo $desc_status ?>
				  			</td>
              </tr>
              <tr>
                <td valign="top" class='dataLabel'>Data:</td>
                <td valign="middle" class="tabDetailViewDF">
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
				  			</td>
                <td valign="middle" class="dataLabel">Hora:</td>
                <td width="19%" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["hora_realizacao"] ?>								
				  			</td>
                <td width="12%" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                <td width="20%" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["duracao"] ?>								
				  			</td>
              </tr>
              <tr>
                <td valign="top" class='dataLabel'>Cliente:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["cliente_nome"] ?>
				  			</td>
              </tr>
							<tr>
                <td valign="top" class='dataLabel'>Grupo:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $grupo_status ?>
				  			</td>
              </tr>               
              <tr>
                <td class='dataLabel'>Respons&aacute;vel:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["responsavel"] ?>								
				  			</td>
              </td>
	           <tr>
	             <td valign="top" class='dataLabel'>Contatos:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF">
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
                    		 <img src='./image/bt_participante_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_endereco_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endereços deste evento' href='#' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
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
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_repertorio_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o repertório deste evento' href='#' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
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
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>				 
                			<td width='30'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                			</td>
                      <td width="30">
                    		 <img src="./image/bt_documentos_gd.gif" /> 
                			</td>
											<td colspan="4">
                         <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                      </td> 									
              			</tr>              			
          				</table>
				  			</td>
              </tr>              
          </table>

<?php 
//Monta um sql para pesquisar se há repertório para este evento
$sql_datas = mysql_query("SELECT * FROM eventos_data WHERE evento_id = '$EventoId' ORDER BY data");
														 
$registros = mysql_num_rows($sql_datas); 														

?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Datas cadastradas para o Evento:</span></td>
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
		      <td width='110' style='padding-left: 8px'>Data/Hora</td>
 		      <td>Descrição</td>
 		      <td width='20'>&nbsp;</td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><b>Não há datas cadastradas para este evento</b></font>
				</td>
		  </tr>	
		  ";	  
		  }     	

		//Cria o array e o percorre para montar a listagem das categorias
    while ($dados_consulta = mysql_fetch_array($sql_datas)){

		//Exibe a descrição da categoria
		?>

      <tr valign='middle'>
        <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-left: 8px">
          <?php echo DataMySQLRetornar($dados_consulta[data]) . " - " . substr($dados_consulta[hora],0,5) ?>
				</td>
				<td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-bottom: 1px;">
          <font color='#CC3300' size='2' face="Tahoma">
						<a title="Clique para alterar esta data" href="#" onclick="wdCarregarFormulario('DataEventoAltera.php?Id=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')"><?php echo $dados_consulta['descricao']; ?></a>
					</font>        
				</td>
				<td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="padding-top: 3px">
					<img src="image/grid_exclui.gif" alt="Clique para excluir esta data" onclick="if(confirm('Confirma a exclusão desta Data ?')) {wdCarregarFormulario('DataEventoExclui.php?DataId=<?php echo $dados_consulta[id] ?>&EventoId=<?php echo $EventoId ?>','conteudo')}" style="cursor: pointer">
				</td>        					
  	  </tr>
  	  <tr>
  	  	<td colspan="3" style="padding-left: 118px">
  	  		<?php echo nl2br($dados_consulta['observacoes']) ?>
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
							echo TimestampMySQLRetornar($dados_evento[datas_timestamp]) 
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
              <FORM id='form' name='cadastro' action='sistema.php?ModuloNome=DataEventoCadastra' method='post' onsubmit='return valida_form()'>
			  </td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        	<INPUT name='Submit' type='submit' class=button id="Submit" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Data'>
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
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da data do evento e clique em [Salvar Data] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
			 </TD>
	       </TR>
           <TR>
						 <TD valign="middle" class=dataLabel>Data:</TD>
             <TD width="19%" valign="middle" class=tabDetailViewDF>
							 <?php
							    //Define a data do formulário
							    $objData->strFormulario = "cadastro";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtData";
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
             <TD width="60" valign="middle" class=dataLabel>Hora:</TD>
             <TD width="450" valign="middle" class=tabDetailViewDF>
						   <input name="edtHora" type="text" class="datafield" id="edtHora" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHora', '99:99', event);"/>								 
						 </TD>
          </TR>
          <TR>
            <TD class='dataLabel'>Descrição:</TD>
            <TD colspan="5" class='tabDetailViewDF'>
               <input name="edtDescricao" type="text" class='datafield' id="edtDescricao" style="width: 450px; color: #6666CC; font-weight: bold" maxlength="75">
            </TD>
          </TR>          
           <TR>
             <TD valign="top" class=dataLabel>Observações:</TD>
             <TD colspan="5" class=tabDetailViewDF>
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
