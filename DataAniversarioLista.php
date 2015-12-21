<?php
###########
## Módulo para listagem de datas de aniversário de cliente no menu principal
## Criado: 09/12/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";

}

//Processa a contagem inicial do total de recados do usuario
$sql = mysql_query("SELECT id,nome FROM clientes WHERE DAY(data_aniversario) = DAY(CURDATE()) AND MONTH(data_aniversario) = MONTH(CURDATE())");

$registros = mysql_num_rows($sql); 

//Consulta aniversários de colaboradores
$sql_colab = mysql_query("SELECT 
						col.id,
						col.tipo,
						col.nome,
						col.data_nascimento,
						day(col.data_nascimento) as dia_aniver,
						fun.nome as funcao_nome
						FROM colaboradores col
						LEFT OUTER JOIN funcoes fun ON fun.id = col.funcao_id
						WHERE col.ativo = 1 AND DAY(col.data_nascimento) = DAY(CURDATE()) AND MONTH(col.data_nascimento) = MONTH(CURDATE())
						ORDER BY col.nome");
			
//Conta o numero de compromissos que a query retornou
$registros_colab = mysql_num_rows($sql_colab);	

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Aniversariantes</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
				<tr height="12">
					<td height="12" colspan="5" class="listViewPaginationTdS1">
						<table width="100%"  border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="40" align="center">
									<img src="image/bt_aniversario_gd.gif" />
								</td>
								<td style="padding-top: 4px; padding-bottom: 4px;">	      	  		
									<?php 
										
										if ($registros == 0) 
										{
										
											$mensagem_regs = "Nenhum CLIENTE aniversariante para o dia de hoje";
										
										} 
                    
										else 
                    
										{
											
											$mensagem_regs = "<span style='color: #990000'>$registros</span> CLIENTE(S) aniversariante(s) para o dia de hoje";
                    
										} 
                    
										if ($registros_colab == 0) 
										{
										
											$mensagem_regs_colab = "Nenhum COLABORADOR aniversariante para o dia de hoje";
										
										} 
                    
										else 
                    
										{
											
											$mensagem_regs_colab = "<span style='color: #990000'>$registros_colab</span> COLABORADOR(ES) aniversariante(s) para o dia de hoje";
                    
										} 
									
									?>
									<span style="font-size: 12px; color: #444444"><b>
									<?php echo $mensagem_regs . '<br/>' . $mensagem_regs_colab ?>
								</b></span>
		  	  			</td>
		  	  		</tr>
		  	  	</table>
	    	  </td>
  			</tr>

  		<?php
        //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    	if ($registros > 0) 
		{ 
      	
		echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
				<td width='26'>&nbsp;</td>
				<td colspan='4'>Cliente</td> 
			</tr>";}

		//Cria o array e o percorre para montar a listagem dinamicamente
		while ($dados = mysql_fetch_array($sql))
		{
    
		?>
		<tr valign="middle">
			<td height="15" width="26" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-bottom: 1px solid; padding-bottom: 1px">
				<img src="image/bt_aniversario.gif" />
			</td>
			<td colspan="4" style="border-bottom: 1px solid">
				<span style="font-size: 12px; color: #CC3300">
						<a title="Clique para exibir este cliente" href="#" onclick="wdCarregarFormulario('ClienteExibe.php?ClienteId=<?php echo $dados[id] ?>','conteudo')"><?php echo $dados["nome"]; ?></a>
				</span>        
			</td>						
		</tr>

		<?php
		 
		//Fecha o WHILE
		}

    	if ($registros_colab > 0) 
		{ 
      	
			echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
					<td width='26'>&nbsp;</td>
					<td colspan='4'>Colaborador</td>";}

			//Cria o array e o percorre para montar a listagem dinamicamente
			while ($dados_colab = mysql_fetch_array($sql_colab))
			{
    
?>
			<tr valign="middle">
				<td height="15" width="26" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-bottom: 1px solid; padding-bottom: 1px">
					<img src="image/bt_aniversario.gif" />
				</td>
				<td colspan="4" style="border-bottom: 1px solid">
					<span style="font-size: 12px; color: #CC3300">
						<a title="Clique para exibir este cliente" href="#" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $dados_colab[id] ?>','conteudo')"><?php echo $dados_colab["nome"]; ?></a>
					</span>        
				</td>						
			</tr>

			<?php
		  
			//Fecha o WHILE
			}
			
			?>   
			<tr height="12">
				<td height="12" colspan="5" class="listViewPaginationTdS1" <?php echo $style_tabela ?>>
				<?php 
				
					$mes_referencia = date("m", mktime());			
				
					switch ($mes_referencia) 
					{
						case 1:  $month_name = "Janeiro";	break;
						case 2:  $month_name = "Fevereiro";	break;
						case 3:  $month_name = "Março";	break;
						case 4:  $month_name = "Abril";	break;
						case 5:  $month_name = "Maio";	break;
						case 6:  $month_name = "Junho";	break;
						case 7:  $month_name = "Julho";	break;
						case 8:  $month_name = "Agosto";	break;
						case 9:  $month_name = "Setembro";	break;
						case 10: $month_name = "Outubro";	break;
						case 11: $month_name = "Novembro";	break;
						case 12: $month_name = "Dezembro";	break;
					};
											
					//Monta e executa a query para buscar os eventos para os próximos 7 dias
					$sql = mysql_query("SELECT 
										col.id,
										col.tipo,
										col.nome,
										col.data_nascimento,
										day(col.data_nascimento) as dia_aniver,
										fun.nome as funcao_nome
										FROM colaboradores col
										LEFT OUTER JOIN funcoes fun ON fun.id = col.funcao_id
										WHERE month(col.data_nascimento) = $mes_referencia AND col.ativo = 1
										ORDER BY month(col.data_nascimento), day(col.data_nascimento), col.nome");

										//Conta o numero de compromissos que a query retornou
										$registros = mysql_num_rows($sql);			


					?>
					<span style="color: #444444"><b>Colaboradores com aniversário em <?php echo $month_name ?>: <span style="color: #990000"><?php echo $registros ?></span></b></span>
				</td>
			</tr>
			<?php
	
			//Cria o array e o percorre para montar a listagem dinamicamente
			while ($dados = mysql_fetch_array($sql))
			{
    	
			//Efetua o switch para o campo de tipo
			switch ($dados[tipo]) 
			{
				
				case 1: $desc_tipo = "Freelance"; break;
				case 2: $desc_tipo = "Funcionário"; break;
			
			}
    	
			?>
				<tr>
					<td height="15" width="22" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-bottom: 0px; padding-top: 0px">
						<img src="image/bt_aniversario.gif" />
					</td>
					<td valign="middle" width="450" style="padding-bottom: 0px; padding-top: 0px">
						<?php 
						
							$dia = date("d", mktime());
							
							$dia_aniver = $dados[dia_aniver];
							
							if ($dia_aniver < 10){
							
								$dia_aniver = "0" . $dia_aniver;
								
							}				
							
							if ($dia == $dia_aniver){
							
								echo "<a title='Clique para exibir os detalhes deste colaborador' href='#' onclick='wdCarregarFormulario(\"ColaboradorExibe.php?ColaboradorId=$dados[id]&headers=1\",\"conteudo\")' style='color: #990000'>$dados[nome]</a>&nbsp;&nbsp; <b><span style='color: #990000'>($dados[dia_aniver])</span></b>";	
								
							} 
							
							else 
							
							{
								
								echo "<a title='Clique para exibir os detalhes deste colaborador' href='#' onclick='wdCarregarFormulario(\"ColaboradorExibe.php?ColaboradorId=$dados[id]&headers=1\",\"conteudo\")'>$dados[nome]</a>&nbsp;&nbsp; <b><span style='color: #990000'>($dados[dia_aniver])</span></b>";	
													
							}
						
						?>			
					</td>
					<td valign="middle" width="100" style="padding-bottom: 0px; padding-top: 0px">
						<?php echo $desc_tipo ?>			
					</td>
					<td valign="middle" style="padding-bottom: 0px; padding-top: 0px">
						<?php echo $dados["funcao_nome"] ?>			
					</td>
				</tr>
				<?php
				
				//Fecha o while
				}
				
				?>
			</table>
		</td>
	</tr>
</table>
