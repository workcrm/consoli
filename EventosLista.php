<?php 
###########
## Módulo para Listagem dos eventos no form principal
## Criado: 03/12/2007 - Maycon Edinger
## Alterado:
## Alterações: 
###########

//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Alimenta as variáveis com a data atual
$id = "0";
$data_hoje = date("Y-m-d",mktime());

//Monta e executa a query para buscar os compromissos da data atual do usuario
$sql = mysql_query("SELECT 
                    id,
                    nome,
                    data_realizacao,
                    hora_realizacao,
                    status, 
                    duracao,
                    data_certame,
                    hora_certame,
                    data_foto_convite,
                    hora_foto_convite,
                    data_ensaio,
                    data_culto,
                    data_colacao,
                    data_baile 
                    FROM eventos 
                    WHERE data_realizacao = '$data_hoje'
                    OR data_certame = '$data_hoje'
                    OR data_foto_convite = '$data_hoje' 
                    OR data_ensaio = '$data_hoje'
                    OR data_culto = '$data_hoje'
                    OR data_colacao = '$data_hoje'
                    OR data_baile = '$data_hoje'
                    AND empresa_id = '$empresaId' 
                    ORDER BY nome");

//Conta o numero de compromissos que a query retornou
$registros = mysql_num_rows($sql);

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
  
  //Inclui o arquivo para manipulação de datas
  include "./include/ManipulaDatas.php";
  
}

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Próximos Eventos</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
    <?php
    
      //Armazena o mês atual na variável
      $mes = date("m",mktime());

      //Efetua o switch para determinar o nome do mes atual
      switch ($mes) 
      {
        case 1: $mes_nome = "Janeiro";	break;
        case 2: $mes_nome = "Fevereiro";	break;
        case 3: $mes_nome = "Março";	break;
        case 4: $mes_nome = "Abril";	break;
        case 5: $mes_nome = "Maio";	break;
        case 6: $mes_nome = "Junho";	break;
        case 7: $mes_nome = "Julho";	break;
        case 8: $mes_nome = "Agosto";	break;
        case 9: $mes_nome = "Setembro";	break;
        case 10: $mes_nome = "Outubro";	break;
        case 11: $mes_nome = "Novembro";	break;
        case 12: $mes_nome = "Dezembro";	break;
      }
        
    ?>

  <table width="100%" align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
    <tr height="12">
      <td height="12" colspan='5' class="listViewPaginationTdS1">
      	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width="40">
              <img src="image/bt_eventos_gd.gif" />
            </td>
            <td>
							<?php 
							
								if ($registros == 0) {
									
									$str_titulo = "eventos";	
									$mensagem_regs = "Não há ";
								
								} else {
									
									if ($registros > 1) {
										$str_titulo = "eventos";
									} else {
										$str_titulo = "evento";
									}
									
									$mensagem_regs = "Há <span style='color: #990000'>$registros </span>";
								} 
							
							?>
	  					<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs . " " . $str_titulo ?> para <span style='color: #990000'><?php echo date("d",mktime()); ?> de <?php echo $mes_nome; ?> de <?php echo date("Y",mktime()); ?></b></span></span>
	  				</td>
	  			</tr>
	  		</table>
    	</td>
  	</tr>

  <?php
  
    //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    if ($registros > 0) 
    { 
      
      //Define o style para fechar com a parte dos eventos para 7 dias
      $style_tabela = "style='border-top: 1px #9E9E9E solid'";
			
      echo "
          <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
            <td width='16' class='listViewThS1'>&nbsp;&nbsp;&nbsp;S</td>
            <td width='480' class='listViewThS1'>&nbsp;&nbsp;Evento</td>
            <td width='62' class='listViewThS1'>Data</td>
            <td width='40' class='listViewThS1'>Hora</td>
            <td class='listViewThS1' align='center'>Ações</td>
          </tr>";
      
    }
		
    //Monta e percorre o array dos dados
    while ($dados = mysql_fetch_array($sql))
    {
    	
      //Efetua o switch para o campo de status
      switch ($dados[status]) 
      {
        case 0: $status_fig = "<img src='./image/bt_evento_orcamento.png' title='Em Orçamento'>"; break;
        case 1: $status_fig = "<img src='./image/bt_evento_aberto.png' title='Em Aberto'>"; break;
        case 2: $status_fig = "<img src='./image/bt_evento_realiz.png' title='Realizado'>"; break;
        case 3: $status_fig = "<img src='./image/bt_evento_nao_realiz.png' title='Não Realizado'>"; break;
      } 

      ?>
      <tr height='16'>	
        <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
          <?php echo $status_fig ?>
        </td>
        <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
          <?php 
						
            if ($dados[data_certame] == $data_hoje)
            {
						
              $mostra_data_certame = DataMySQLRetornar($dados[data_certame]);
              $mostra_hora_certame = $dados[hora_certame];
              
              echo "<span style='color: #990000; cursor: pointer' title='Data do CERTAME: $mostra_data_certame - Hora: $mostra_hora_certame'><b>[CERTAME]</b> </span>";

            }
            
            if ($dados[data_foto_convite] == $data_hoje)
            {
						
              $mostra_data_foto_convite = DataMySQLRetornar($dados[data_foto_convite]);
              $mostra_hora_foto_convite = $dados[hora_foto_convite];
              
              echo "<span style='color: #990000; cursor: pointer' title='Data do Foto-Convite: $mostra_data_foto_convite - Hora: $mostra_hora_foto_convite'><b>[FOTO-CONVITE]</b> </span>";

            }
            
            if ($dados[data_realizacao]	== $data_hoje)
            {
						
              $mostra_data = DataMySQLRetornar($dados[data_realizacao]);
              
              echo "<span style='color: #990000; cursor: pointer' title='Data do Evento: $mostra_data'><b>[EVENTO]</b> </span>";

            }

            if ($dados[data_ensaio] == $data_hoje)
            {	
						  
              $mostra_ensaio = DataMySQLRetornar($dados[data_ensaio]);
							
              echo "<span style='color: #990000; cursor: pointer' title='Data do Ensaio: $mostra_ensaio'><b>[ENSAIO]</b> </span>";

            }

            if ($dados[data_culto] == $data_hoje)
            {
						  
              $mostra_culto = DataMySQLRetornar($dados[data_culto]);
							
              echo "<span style='color: #990000; cursor: pointer' title='Data do Culto: $mostra_culto'><b>[CULTO]<b> </span>";	

            }

            if ($dados[data_colacao] == $data_hoje)
            {
							
              $mostra_colacao = DataMySQLRetornar($dados[data_colacao]);

              echo "<span style='color: #990000; cursor: pointer' title='Data da Colação: $mostra_colacao'><b>[COLAÇÃO]</b> </span>";		

            }

            if ($dados[data_baile] == $data_hoje)
            {
						
              $mostra_baile = DataMySQLRetornar($dados[data_baile]);

              echo "<span style='color: #990000; cursor: pointer' title='Data do Baile: $mostra_baile'><b>[BAILE]</b> </span>";	

            }
            
            echo "<span style='color: #990000'><b>:&nbsp;</b></span>";

          ?>
          
          <a title="Clique para exibir os detalhes deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')">
          
          <?php 
                  						
            //Verifica se o evento está como nao realizado
            if ($dados[status] == 3)
            {

              echo "<span style='color: #990000; text-decoration: line-through'>$dados[nome]</span>";

            } 

            else 
            {

              echo $dados[nome];

            }						

          ?>
        </a>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <?php echo DataMySQLRetornar($dados[data_realizacao]) ?>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <?php echo $dados[hora_realizacao] ?>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <div align="center">
          <img src='./image/bt_data_evento.gif' title='Clique para gerenciar as datas deste evento' onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_participante.gif' title='Clique para gerenciar os participantes deste evento' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_endereco.gif' title='Clique para gerenciar os endereços deste evento' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_item.gif' title='Clique para gerenciar os produtos deste evento' onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_terceiro.gif' title='Clique para gerenciar os terceiros deste evento' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">					
          <img src='./image/bt_brinde.gif' title='Clique para gerenciar os brindes deste evento' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">	
          <img src='./image/bt_repertorio.gif' title='Clique para gerenciar o repertório musical deste evento' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_formando.gif' title='Clique para gerenciar os formandos deste evento' onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">	
          <img src='./image/bt_fotovideo.gif' title='Clique para gerenciar o foto e vídeo deste evento' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
        </div>
      </td>
    </tr>
    <?php
  
  //Fecha o while
  }
  
  ?>
  <tr height="12">
    <td height="12" colspan='6' class="listViewPaginationTdS1" <?php echo $style_tabela ?>>
      <?php 

        $amanha = som_data(date("d/m/Y", mktime()),1);
        $data_sete_dias = som_data(date("d/m/Y", mktime()),7);

        $data_inicio = DataMySQLInserir($amanha);
        $data_termino = DataMySQLInserir($data_sete_dias);


        //Monta e executa a query para buscar os eventos para os próximos 7 dias
        $sql = mysql_query("SELECT 
                            id,
                            nome,
                            data_realizacao, 
                            status, 
                            hora_realizacao,													
                            data_ensaio,
                            data_culto,
                            data_colacao,
                            data_baile, 
                            duracao 
                            FROM eventos 
                            WHERE data_realizacao BETWEEN '$data_inicio' AND '$data_termino' 
                            OR data_ensaio BETWEEN '$data_inicio' AND '$data_termino'
                            OR data_culto BETWEEN '$data_inicio' AND '$data_termino'
                            OR data_colacao BETWEEN '$data_inicio' AND '$data_termino'
                            OR data_baile BETWEEN '$data_inicio' AND '$data_termino'  													
                            AND empresa_id = '$empresaId' 
                            ORDER BY data_realizacao, nome");
													
			
        //Conta o numero de compromissos que a query retornou
        $registros = mysql_num_rows($sql);			


        ?>
        <span style="color: #444444"><b>Eventos para os próximos 7 dias </b><?php echo "(" . $amanha . " a " . $data_sete_dias . ")" ?><b>:&nbsp;<span style='color: #990000'><?php echo $registros ?></span></b></span>
      </td>
    </tr>	
    <?php
    
      //Caso não tenha compromissos então não exibe a linha de cabeçalho.
      if ($registros > 0) 
      { 
          
      	echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='16' class='listViewThS1'>&nbsp;&nbsp;&nbsp;S</td>
          <td width='450' class='listViewThS1'>&nbsp;&nbsp;Evento</td>
          <td width='62' class='listViewThS1'>Data</td>
          <td width='40' class='listViewThS1'>Hora</td>
          <td class='listViewThS1' align='center'>Ações</td>
        </tr>";
        
      }

      //Cria o array e o percorre para montar a listagem dinamicamente
      while ($dados = mysql_fetch_array($sql))
      {
    	
        //Efetua o switch para o campo de status
        switch ($dados[status]) 
        {
          case 0: $status_fig = "<img src='./image/bt_evento_orcamento.png' title='Em Orçamento'>"; break;
          case 1: $status_fig = "<img src='./image/bt_evento_aberto.png' title='Em Aberto'>"; break;
          case 2: $status_fig = "<img src='./image/bt_evento_realiz.png' title='Realizado'>"; break;
          case 3: $status_fig = "<img src='./image/bt_evento_nao_realiz.png' title='Não Realizado'>"; break;
        } 
    
        ?>
        <tr height="16">	
          <td width="18" valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
            <?php echo $status_fig ?>
          </td>	
          <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">		    
            <?php 

              if ($dados[data_realizacao]	>= $data_inicio AND $dados[data_realizacao] <= $data_termino )
              {

                $mostra_data = DataMySQLRetornar($dados[data_realizacao]);

                echo "<span style='color: #990000; cursor: pointer' title='Data do Evento: $mostra_data'><b>[EVENTO]</b> </span>";

              }

              if ($dados[data_ensaio]	>= $data_inicio AND $dados[data_realizacao] <= $data_termino )
              {

                $mostra_ensaio = DataMySQLRetornar($dados[data_ensaio]);

                echo "<span style='color: #990000; cursor: pointer' title='Data do Ensaio: $mostra_ensaio'><b>[ENSAIO]</b> </span>";

              }
						
              if ($dados[data_culto] >= $data_inicio AND $dados[data_realizacao] <= $data_termino )
              {

                $mostra_culto = DataMySQLRetornar($dados[data_culto]);

                echo "<span style='color: #990000; cursor: pointer' title='Data do Culto: $mostra_culto'><b>[CULTO]<b> </span>";

              }
						
              if ($dados[data_colacao] >= $data_inicio AND $dados[data_realizacao] <= $data_termino )
              {

                $mostra_colacao = DataMySQLRetornar($dados[data_colacao]);

                echo "<span style='color: #990000; cursor: pointer' title='Data da Colação: $mostra_colacao'><b>[COLAÇÃO]</b> </span>";			

              }
						
              if ($dados[data_baile] >= $data_inicio AND $dados[data_realizacao] <= $data_termino )
              {

                $mostra_baile = DataMySQLRetornar($dados[data_baile]);

                echo "<span style='color: #990000; cursor: pointer' title='Data do Baile: $mostra_baile'><b>[BAILE]</b> </span>";	

              }
            
            echo "<span style='color: #990000'><b>:&nbsp;</b></span>";
						
          ?>
      
          <a title="Clique para exibir os detalhes deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')">
      
          <?php 
      
            //Verifica se o evento está como nao realizado
            if ($dados[status] == 3)
            {

              echo "<span style='color: #990000; text-decoration: line-through'>$dados[nome]</span>";

            } 

            else 
            {

              echo $dados[nome];

            } 
					
          ?>
        </a>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <?php echo DataMySQLRetornar($dados[data_realizacao]) ?>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <?php echo $dados[hora_realizacao] ?>
      </td>
      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
        <div align="center">
          <img src='./image/bt_data_evento.gif' title='Clique para gerenciar as datas deste evento' onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_participante.gif' title='Clique para gerenciar os participantes deste evento' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_endereco.gif' title='Clique para gerenciar os endereços deste evento' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_item.gif' title='Clique para gerenciar os produtos deste evento' onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
          <img src='./image/bt_terceiro.gif' title='Clique para gerenciar os terceiros deste evento' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">					
          <img src='./image/bt_brinde.gif' title='Clique para gerenciar os brindes deste evento' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">						
          <img src='./image/bt_repertorio.gif' title='Clique para gerenciar o repertório musical deste evento' onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">	
          <img src='./image/bt_formando.gif' title='Clique para gerenciar os formandos deste evento' onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">	
          <?php		
          
            //Verifica o nível de acesso do usuário
            if ($nivelAcesso >= 4) 
            {
              
              ?>
              <img src='./image/bt_fotovideo.gif' title='Clique para gerenciar o foto e vídeo deste evento' onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
              <?php

            } 

            else 
            {

              ?>
              <img src='./image/bt_fotovideo_off.gif' title='Opção não habilitada para seu nível de acesso !' />
              <?php	

            }
            
          ?>									
        </div>
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
