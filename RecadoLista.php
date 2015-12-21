<?php
###########
## Módulo para listagem de recados no menu principal
## Criado: 17/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
}

//Processa a contagem inicial do total de recados do usuario
$sql = mysql_query("SELECT id FROM recados WHERE destinatario_id = '$usuarioId' OR (global = 1 AND data >= '$usuarioDataCadastro')");
$registros = mysql_num_rows($sql); 

//Agora processa os 5 últimos recados para exibir
//Monta a query
$sql = mysql_query("SELECT 
					rec.id, 
					rec.empresa_id, 
					rec.remetente_id, 
					rec.destinatario_id, 
					rec.data, 
					rec.assunto, 
					rec.mensagem,
					rec.global,
					rec.lido, 
					usu.nome as remetente_nome, 
					usu.sobrenome as remetente_sobrenome 
					FROM recados rec 
					INNER JOIN usuarios usu ON rec.remetente_id = usu.usuario_id 
					WHERE rec.destinatario_id = '$usuarioId' 
					OR (rec.global = 1 AND rec.data >= '$usuarioDataCadastro')
					ORDER BY rec.data DESC
					LIMIT 0,5");

//Executa a query
$registros_exibindo = mysql_num_rows($sql);
?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
  <td>

	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Meus Recados</span></td>
	  </tr>
	</table>


  </td>
  </tr>
  <tr>
    <td>
      <table id="4" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
        <tr height="12">
	    	  <td height="12" colspan='3' class="listViewPaginationTdS1">
	      	  <table width='100%'  border='0' cellspacing='0' cellpadding='0'>
	      	  	<tr>
	      	  		<td width="40">
	      	  			<img src="image/bt_recado_menu.gif" />
	      	  		<td>
	      	  		<td>	      	  		
									<?php 
										
										if ($registros == 0) {
										
											$mensagem_regs = "Você não possui ";
										
										} else {
											
											$mensagem_regs = "Você possui <span style='color: #990000'>$registros</span> ";} 
									
									?>
									<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs ?>recados</b></span>
									<br>
									<span style="font-size: 10px">(Exibindo os <b><?php echo $registros_exibindo ?></b> últimos)</span>
		  	  			</td>
		  	  		</tr>
		  	  	</table>
	    	  </td>
		  		<td align="right" class="listViewPaginationTdS1">
						<span style="font-size: 12px"><b>[</b><a href='#' title='Exibe todos os recados' onclick="wdCarregarFormulario('ModuloRecados.php','conteudo')">Exibir todos os recados</a><b>]</b></span>
		  		</td>
  			</tr>

  		<?php
        //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    	if ($registros > 0) { 
      	echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='26'>&nbsp;</td>
	      	<td>Assunto</td>         
					<td width='65'><div align='left'>Data</div></td>
	      	<td width='220'>Remetente</td>
        </tr>
    ";}

	//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados = mysql_fetch_array($sql)){
    
    //Seta o texto e figura para o campo de recado lido
    switch ($dados[lido]) {
	  	//Se o recado estiver marcado como não lido
      case 0: $recado_figura = "<img src='image/bt_recado_novo.gif' alt='Recado não lido' />";	break;
      //Se o recado for lido
	  	case 1: $recado_figura = "<img src='image/bt_recado_lido.gif' alt='Recado lido' />";	break;
      //Se o recado for global
	  	case 2: $recado_figura = "<img src='image/bt_recado_global.gif' alt='Recado automático gerado pelo sistema' />";	break;	  
    }
?>
      <tr valign='middle'>
        <td height="15" width='26' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 1px">
          <?php echo $recado_figura ?>
				</td>
        <td>
          <span style="font-size: 12px; color: #CC3300">
						<a title="Clique para exibir este recado" href="#" onclick="wdCarregarFormulario('RecadoExibe.php?RecadoId=<?php echo $dados[id] ?>','conteudo')"><?php echo $dados['assunto']; ?></a>
					</span>        
				</td>
        <td width='65' valign='middle' bgcolor='#fdfdfd'>
          <?php echo DataMySQLRetornar($dados[data]) ?>
				</td>
        <td width='220' align='left' bgcolor='#fdfdfd'>
          <?php echo $dados[remetente_nome] . " " . $dados[remetente_sobrenome] ?>
				</td>						
  	  </tr>

  <?php
  //Fecha o WHILE
  }
  ?>
  </table>

</td>
</tr>
<tr>
	<td>
							<div id='recado' style="display: none"> 
				 			<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
							  <tr>
									<td height='20' align="center" width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
										<img src='./image/bt_informacao.gif' border='0' />
									</td>
									<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
										<span class="style1">Atenção:</span><br>Nos próximos dias o sistema work | eventos será atualizado, período no qual poderão ocorrer dificuldades técnicas.<br>Pedimos a colaboração e a compreensão de todos os usuários.
									</td>
								</tr>
							</table>
							</div>


	</td>
</tr>
</table>
