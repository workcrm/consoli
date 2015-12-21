<?php
###########
## Módulo de pesquisa para FORMANDOS
## Criado: - 03/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Monta a query para pegar os dados do formando
$sql_formando = "SELECT 
                form.id,
                form.evento_id,
                form.nome,
                form.status,
				form.situacao,
                form.cpf,
                form.contato, 
				form.operadora,
                form.email,
                form.senha, 
                eve.nome AS evento_nome 
                FROM eventos_formando form
                LEFT OUTER JOIN eventos eve ON form.evento_id = eve.id
                WHERE (form.nome LIKE '%$chavePesquisa%' AND form.empresa_id = '$empresaId') OR (form.id = '$chavePesquisa' AND form.empresa_id = '$empresaId') OR (form.cpf = '$chavePesquisa' AND form.empresa_id = '$empresaId')
                ORDER BY form.nome";

//Executa a query
$query_formando = mysql_query($sql_formando);

//Conta o numero de registros da query
$registros_formando = mysql_num_rows($query_formando);

//Caso não houver registros
if ($registros_formando == 0) 
{
	
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td valign='middle'><span class='TituloModulo'>Formandos: </span><span class='style1'>Não há formandos cadastrados que satisfaçam os critérios de pesquisa</span></td>
		</tr>";

} 

else 

{
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td valign='middle'><span class='TituloModulo'>Formandos: </span><span class='style1'>A pesquisa retornou $registros_formando resultado(s)</br>
			</td>
		</tr>
		<tr>
			<td>		  
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
					<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
						<td width='22'>&nbsp;&nbsp;S</td>
						<td>Formando</td>
						<td width='130'>Celular</td>
						<td width='250'>Email</td>   		      
						<td width='40'>Senha</td>
					</tr> 		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_formando = mysql_fetch_array($query_formando))
	{		
		    	
		//Efetua o switch para o campo de status
		switch ($dados_formando["status"]) 
		{
		
			case 1: $desc_status = "<img src='image/bt_a_formar.png' alt='A se formar'>"; break;
			case 2: $desc_status = "<img src='image/bt_formado.png' alt='Formado'>"; break;
			case 3: $desc_status = "<img src='image/bt_desistente.png' alt='Desistente'>"; break;
			case 4: $desc_status = "<img src='image/bt_pendencia.gif' alt='Aguardando Declaração Rescição'>"; break;
			
		} 
		
		$desc_operadora = '';
					
		//Efetua o switch para o campo de operadora
		switch ($dados_formando["operadora"]) 
		{
			case 1: $desc_operadora = " - <span style='color: #990000'>(VIVO)</span>"; break;
			case 2: $desc_operadora = " - <span style='color: #990000'>(TIM)</span>"; break;
			case 3: $desc_operadora = " - <span style='color: #990000'>(Claro)</span>"; break;
			case 4: $desc_operadora = " - <span style='color: #990000'>(Oi)</span>"; break;
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_formando["situacao"] == 2)
		{
		
			$cor_celula = "#F0D9D9";
			
		}
		
		else
		
		{
		
			$cor_celula = "#FFFFFF";
			
		}

	  ?>

		<tr height="16">
			<td bgcolor="<?php echo $cor_celula ?>" align="center">
				<?php echo $desc_status ?>
			</td>
			<td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
				<span style="font-size: 11px">(<?php echo $dados_formando['id']?>) - </span><a title="Clique para exibir os detalhes deste formando" href="#" onclick="wdCarregarFormulario('FormandoEventoAltera.php?FormandoId=<?php echo $dados_formando["id"] ?>&EventoId=<?php echo $dados_formando["evento_id"] ?>&headers=1','conteudo')"><?php echo $dados_formando["nome"]; ?></a><br/><span style="font-size: 10px;">Evento:&nbsp;<span style="color: #990000;"><?php echo $dados_formando["evento_id"] . ' - ' . $dados_formando["evento_nome"]; ?></span></span>
			</td>
			<td bgcolor="<?php echo $cor_celula ?>">
				<?php echo $dados_formando["contato"] . $desc_operadora ?>
			</td>
			<td bgcolor="<?php echo $cor_celula ?>">
				<a href="mailto:<?php echo $dados_formando["email"] ?>" title="Clique para enviar um email para o formando"><?php echo $dados_formando["email"] ?></a>
			</td>
			<td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd">
				<span style="color: #990000;"><?php echo $dados_formando["senha"] ?></span>
				<?php
					//verifica se o usuário pode ver este menu
					if ($dados_usuario["menu_financeiro"] == 1)
					{
				?>
				<img src="image/bt_boleto_avulso.png" alt="Clique para visualizar os boletos deste formando no site" onclick="abreJanela2('http://www.consolieventos.com.br/workeventos/WorkFinanceiro.php?user_login=<?php echo $dados_formando[cpf] ?>')" style="cursor: pointer">
				<?php

					}
					
				?>
			</td>	
		</tr>

		<?php 
			//Fecha o while
			}
			
			echo "</table><br />";
			}
		?>  
		</td>
	</tr>	 		
</table>