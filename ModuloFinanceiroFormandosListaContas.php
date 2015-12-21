<?php
###########
## Módulo para Listagem da posição financeira dos formandos de um evento
## Criado: 05/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
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

//Captura o evento informado
$edtEventoId = $_GET["EventoId"];
$edtTipoConsulta = $_GET["TipoConsulta"];
$edtContaCaixaId = $_GET["ContaCaixaId"];

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

if ($edtContaCaixaId > 0)
{

	$sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo WHERE id = $edtContaCaixaId");

	//Monta o array com os dados
	$dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);

	echo "Conta-caixa: ($edtContaCaixaId) - " . $dados_conta_caixa["nome"] . "<br/>";
	$where_conta_caixa = "AND crec.subgrupo_conta_id = $edtContaCaixaId";

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="padding-bottom: 4px;">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Evento: <span style="color: #990000;"><?php echo $dados_evento["nome"] ?></span></span>			  	
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 10px;">
						Consulta: <span style=1color: #990000;><strong>POR BOLETOS DO FORMANDO</strong></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
  
<?php	
  
//Busca os boletos emitidos para o evento
$sql_topo = mysql_query("SELECT 
						bol.id,
						bol.nosso_numero,
						bol.sacado,
						bol.demonstrativo2,
						bol.demonstrativo3,
						bol.valor_boleto,
						bol.data_documento,
						bol.data_vencimento,
						bol.boleto_recebido,
						form.nome as formando_nome
						FROM boleto bol 
						LEFT OUTER JOIN eventos_formando form ON form.id = bol.formando_id
						LEFT OUTER JOIN contas_receber crec ON crec.id = bol.conta_receber_id
						WHERE bol.evento_id = $edtEventoId
						$where_conta_caixa
						ORDER BY formando_nome
						LIMIT 0,1");
				  
//verifica o número total de registros
$registros = mysql_num_rows($sql_topo);                      
				  
//Busca os boletos emitidos para o evento
$sql = mysql_query("SELECT 
				  bol.id,
				  bol.nosso_numero,
				  bol.sacado,
				  bol.demonstrativo2,
				  bol.demonstrativo3,
				  bol.valor_boleto,
				  bol.data_documento,
				  bol.data_vencimento,
				  bol.boleto_recebido,
				  form.nome as formando_nome
				  FROM boleto bol 
				  LEFT OUTER JOIN eventos_formando form ON form.id = bol.formando_id
				  LEFT OUTER JOIN contas_receber crec ON crec.id = bol.conta_receber_id
				  WHERE bol.evento_id = $edtEventoId
				  $where_conta_caixa
				  ORDER BY formando_nome");                                            
  
?>
  
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <?php 
    
		//Verifica se há formandos cadastrados para o evento
		if ($registros == 0)
		{

	?>
    <tr>
		<td height="20" style="border: 1px #444444 solid; padding-left: 8px">
			<span style="font-size: 12px;"><b>Não há formandos para o evento selecionado !</b></span>
		</td>
    </tr>
    <?php 
    
		} 
		
		else 
		
		{
      
			$dados_topo = mysql_fetch_array($sql_topo);
			
			$formando = $dados_topo["formando_nome"];        
        
    ?>
      
    <tr>
        <td colspan="7">
            <span class="TituloModulo" style="color: #990000;"><?php echo $dados_topo["formando_nome"] ?></span>
		</td>
    </tr>
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
				<tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
					<td width="116" align="center">Nosso Número</td>
        	      	<td>Dados do Sacado/Evento/Formando</td>
					<td width="60" align="center">Emissão</td>
					<td width="60" align="center">Vencto</td>
					<td width="80" align="right">Valor</td>
					<td width="65" align="center">Situação</td>          
                </tr>
  
          
        <?php
        
        //Cria as variáveis zeradas
        $total_formando = 0;
        $total_geral = 0;
        
        while ($dados = mysql_fetch_array($sql))
		{
          
      		//Verifica a situação do boleto
      		switch ($dados["boleto_recebido"]) 
			{
      		  
				case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
				case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
            
			}
          
          
          if($formando != $dados["formando_nome"]){
          
         ?>
                <tr>
                  <td colspan="4" align="right">
                    <strong>Total do Formando:</strong>
                  </td>    
                  <td align="right">
                    <strong><?php echo "R$ " . number_format($total_formando, 2, ",", "."); ?></strong>
                  </td>
                  <td>
                    &nbsp;
                  </td>
                </tr>
              </table>
        	  </td>
          </tr>
         <tr>
            <td>
              <br/>
              <span class="TituloModulo" style="color: #990000;"><?php echo $dados["formando_nome"] ?></span>
        	  </td>
         </tr>
         <tr>
            <td>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
                <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
                  <td width="116" align="center">Nosso Número</td>
        	      	<td>Dados do Sacado/Evento/Formando</td>
                  <td width="60" align="center">Emissão</td>
                  <td width="60" align="center">Vencto</td>
                  <td width="80" align="right">Valor</td>
                  <td width="65" align="center">Situação</td>          
                </tr>
          
				<?php


					//Zera o totalizador do formando
					$total_formando = 0;          
					}

				?>
         
				<tr height="16">
          			<td style="border-bottom: 1px solid" align="center">
          				<span style="color: #6666CC"><?php echo substr($dados["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados["nosso_numero"], 8,3) ?></span><span style="color: #59AA08"><?php echo substr($dados["nosso_numero"], 11,4) ?></span><?php echo substr($dados["nosso_numero"], 15,2) ?>		
          			</td>      			
					<td style="border-bottom: 1px solid" height="20">
						<font color="#CC3300" size="2" face="Tahoma">
							<a title="Clique para exibir este boleto" href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')"><?php echo $dados["sacado"]; ?></a>
          				</font>
          				<br/>
          				<?php echo "<span style='color: #990000'><b>$dados[demonstrativo2]</b></span><br/>$dados[demonstrativo3]" ?>
          				</span>      
          			</td>
          			<td style="border-bottom: 1px solid" align="center">
          				<?php echo DataMySQLRetornar($dados["data_documento"]) ?>				
          			</td>
          			<td style="border-bottom: 1px solid" align="center">
          				<span style="color: #6666CC"><?php echo DataMySQLRetornar($dados["data_vencimento"]) ?></span>			
          			</td>			
					<td style="border-bottom: 1px solid" align="right">
						<?php 
							
							echo "R$ " . number_format($dados["valor_boleto"], 2, ",", ".");
							$total_formando = $total_formando + $dados["valor_boleto"]; 
							$total_geral = $total_geral + $dados["valor_boleto"];
						?>
          			</td>
          			<td style="border-bottom: 1px solid" align="center">
          				<?php echo $desc_situacao ?>				
          			</td>
				</tr>
			<?php
          
				$formando = $dados["formando_nome"];
          
        }
        
        ?>
                <tr>
                  <td colspan="4" align="right">
                    <strong>Total do Formando:</strong>
                  </td>    
                  <td align="right">
                    <strong><?php echo "R$ " . number_format($total_formando, 2, ",", "."); ?></strong>
                  </td>
                  <td>
                    &nbsp;
                  </td>
                </tr>
              </table>
              <br/>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">  
                <tr>
                  <td width="116" align="center">&nbsp;</td>
        	      	<td>&nbsp;</td>
                  <td width="60" align="center">&nbsp;</td>
                  <td width="60" align="center">&nbsp;</td>
                  <td width="80" align="right">&nbsp;</td>
                  <td width="65" align="center">&nbsp;</td> 
                </tr>
                <tr>  
                  <td colspan="4" align="right">
                    <span style="font-size: 13px;"><strong>Total Geral:</strong></span><br/>&nbsp;
                  </td>    
                  <td align="right">
                    <strong><?php echo "R$ " . number_format($total_geral, 2, ",", "."); ?></strong><br/>&nbsp;
                  </td>
                  <td>
                    &nbsp;
                  </td>
                </tr>
              </table>
        	  </td>
          </tr>
          
          <?php
        
      }
      
    ?>  
</table>