<?php
###########
## Módulo para visualizar os dados dos boletos vindos do arquivo de retorno
## Criado: 20/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

	
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

$dataProcessa = $_POST["edtDataProcessa"];

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="frmRetorno" action="sistema.php?ModuloNome=ModuloRetornoProcessa" method="post">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width='440'>
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Arquivos de Retorno</span>
		</td>
	</tr>
	<tr>
		<td>
			<img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	</tr>
</table>
      
<?php

function ParseFloat($floatString)
{ 
    $LocaleInfo = localeconv(); 
    $floatString = str_replace("." , "", $floatString); 
    $floatString = str_replace("," , ".", $floatString); 
    return floatval($floatString); 
} 


//Recupera os valores vindos do formulário e armazena nas variaveis
if($_POST["Submit"])
{
  
	//Caso tiver um nome de arquivo, armazena numa variável
	$arq = $_FILES['edtAnexo']['name'];
    
	//Caso nao tenha sido informado um arquivo a processar
	if(!$arq)
        exit("<b><span style='color: #990000'>É necessário selecionar um arquivo de retorno para processar !</span></b><br/><br/><input class='button' value='Voltar' type='button' name='btVoltar' onclick='wdCarregarFormulario(\"ModuloRetorno.php\",\"conteudo\")' style='width: 100px' />");
	
	$arquivo = $_FILES["edtAnexo"];
  
	//Diretório onde a imagem será salva
	$pasta = "documentos/". $arq;

	//Faz o upload da imagem
	move_uploaded_file($arquivo["tmp_name"], $pasta);
  
	//tenta abrir o arquivo de retorno do banco
	if(!$arrArquivo = file($pasta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
        exit("Não foi possível abrir o arquivo de retorno do banco!<br/><input class='button' value='Voltar' type='button' name='btVoltar' onclick='wdCarregarFormulario(\"ModuloRetorno.php\",\"conteudo\")' style='width: 100px' />");
          
  //linha atual
  $i = 0;
  $linha = 1;
  
?>
 <table width='100%' cellpadding='0' cellspacing='0' border='0'>		
	<tr>
		<td>
			<input name="Submit" type="submit" class="button" title="Processa o retorno" value="Processar Retorno" />
			<br/>
			<br/>
		</td>
	</tr>
	<tr>
		<td>
			<span style='font-size: 12px'>Confira atentamente os valores dos boletos listados. Após a conferência, clique em [Processar Boletos].</span><br/>
		</td>
	</tr>
</table>
<br/>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">  
							<tr height="20">
								<td width="40" class="listViewThS1">
									<div align="center">Proc.</div>
								</td>
								<td width="110" class="listViewThS1">
									&nbsp;&nbsp;Nro do Boleto
								</td>
								<td class="listViewThS1">
									Sacado
								</td>
								<td width="90" align="right" class="listViewThS1" style="padding-right: 4px">
									Valor Original
								</td>
								<td width="90" align="center" class="listViewThS1" style="padding-right: 4px">
									Valor Retorno
								</td>
							</tr>


<?php  
  
	//retira de um vez as quebras de linhas, retorno de carros e linhas vazias
	foreach($arrArquivo as &$linhas)
	{
          
		//retira \r
		$linhas = str_replace(chr(13), "", $linhas);
    
		//retira \n
		$linhas = str_replace(chr(10), "", $linhas);
    
		//apaga as linhas vazias
		if(empty($linhas)) unset($arrArquivo[$i]);
            
		//proxima linha
		$i++;
      
		if ($linha > 9)
		{
    
			$comeco = substr($linhas, 3, 5);
      
			//Veririfica se a linha é um boleto
			if ($comeco == "15209")
			{
          
				//Monta a string do boleto
				$numero_boleto = substr($linhas, 3, 17);                  
        
				//Monta a string do valor
				$valor_float = substr($linhas, 95, 11);
        
				//Converte a string para um decimal
				$valor_boleto = number_format(ParseFloat($valor_float), 2,",",".");
        
				//Monta a string do valor dos juros
				$valor_juros_float = substr($linhas, 115, 10);
        
				//Converte a string para um decimal
				$valor_juros_boleto = number_format(ParseFloat($valor_juros_float), 2,",",".");  

				//echo $valor_boleto . "<br/>";
				//echo $valor_juros_boleto . "<br/>";
				
				$valor_retorno = number_format(ParseFloat($valor_float) + ParseFloat($valor_juros_float), 2,",",".");  
				$valor_retorno_compara = number_format(ParseFloat($valor_float) + ParseFloat($valor_juros_float), 2,".",""); 
       
				$total_retorno = $total_retorno + $valor_retorno_compara;
				
				//Busca no banco de dados se existe o boleto
				//Monta a query para pegar os dados
				$sql_cli = "SELECT * FROM boleto WHERE nosso_numero = '$numero_boleto'";
        
				//Executa a query
				$query_cli = mysql_query($sql_cli);
          
				//Conta o numero de registros da query
				$registros_cli = mysql_num_rows($query_cli);
            
				//Caso não houver registros
				if ($registros_cli == 0) 
				{
          	
					$sacado = "<span style='color: #990000'><b>BOLETO NÃO ENCONTRADO !!!</b></span>";
					
					$cor_celula = "#F0D9D9";
					$marca_boleto = '';
					$desativa_chk = "disabled='disabled'";
					$valor_sacado = "R$ 0,00";
          
				} 
        
				else 
        
				{				
					
					$msg_registros = '';
					
					if ($registros_cli > 1)
					{
					
						$msg_registros = "<br/>Existem $registros_cli boletos com a mesma numeração. VERIFICAR...";
						
					}
					
					$marca_boleto = "checked='checked'";
					$desativa_chk = '';
					
					//efetua o loop na pesquisa
					while ($dados_rec = mysql_fetch_array($query_cli))
					{		
              
						$sacado = "<span style='color: #990000'><b>$dados_rec[sacado]</b></span>" . $msg_registros;
          				
						$valor_boleto_bd = $dados_rec["valor_boleto"];
						$total_boleto_bd = $total_boleto_bd + $valor_boleto_bd;
						
						$valor_sacado = "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
						
						$desc_situacao = $valor_boleto_bd . "<br/>" . $valor_retorno_compara;
						
						//Verifica se o valor do boleto no banco é igual ao valor do boleto do retorno
						if ($valor_boleto_bd == $valor_retorno_compara)
						{
						
							//Caso for igual, marca como verde
							$cor_celula = "#FFFFFF";
							
						}
						
						else
						
						{
						
							//Caso for igual, marca como amarelo para atencao
							$cor_celula = "#FFFFCD";
						
						
						
						}	
					}
                  
				}
          
			?>
			<tr height="24">
				<td valign="middle" bgcolor="<?php echo $cor_celula ?>" align="center" style="border-bottom: 1px dashed;">
					<input name="<?php echo ++$edtItemChk ?>" value="<?php echo $numero_boleto ?>" type="checkbox" <?php echo $desativa_chk ?> style="border: 0px" title="Clique para marcar ou desmarcar a compensação deste boleto" <?php echo $marca_boleto ?> />
				</td>
				<td valign="middle" bgcolor="<?php echo $cor_celula ?>" class="oddListRowS1" style="border-bottom: 1px dashed;">
					<b><span style="color: #6666CC"><?php echo substr($numero_boleto, 0,7) ?></span><span style="color: #990000"><?php echo substr($numero_boleto, 7,3) ?></span><span style="color: #59AA08"><?php echo substr($numero_boleto, 10,5) ?></span><?php echo substr($numero_boleto, 15,2) ?></b>	
				</td> 
				<td valign="middle" bgcolor="<?php echo $cor_celula ?>" class="currentTabList" style="border-bottom: 1px dashed;">
					<?php echo $sacado ?>
				</td> 
				<td align="right" bgcolor="<?php echo $cor_celula ?>" bgcolor="#fdfdfd" class="currentTabList" style="border-left: 1px dashed; border-bottom: 1px dashed; padding-right: 4px">
					<?php echo $valor_sacado ?>
				</td>
				<td align="center" bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-left: 1px dashed; border-bottom: 1px dashed; padding-right: 4px">
					<?php
										
						//Cria um objeto do tipo WDEdit 
						$objWDComponente = new WDEditReal();
						
						//Define nome do componente
						$objWDComponente->strNome = "edtValor$edtItemChk";
						//Define o tamanho do componente
						$objWDComponente->intSize = 10;
						//Busca valor definido no XML para o componente
						$objWDComponente->strValor = $valor_retorno;
						//Busca a descrição do XML para o componente
						$objWDComponente->strLabel = "";
						//Determina um ou mais eventos para o componente
						$objWDComponente->strEvento = "";
						//Define numero de caracteres no componente
						$objWDComponente->intMaxLength = 10;
						
						//Cria o componente edit
						$objWDComponente->Criar();  
					?>
				</td>
			</tr>
			<?php                     
		  
			}
      
		}
      
		//Pula a linha no txt
		$linha++;
  
	}
	
	?>
	<tr>
		<td height="24" colspan="3" align="right">
			<b>TOTAL:&nbsp;</b>
		</td>
		<td align="right" style="border-left: 1px dashed; padding-right: 4px">
			<?php echo "R$ " . number_format($total_boleto_bd,2,",",".") ?>
		</td>
		<td align="right" style="border-left: 1px dashed; padding-right: 4px">
			<?php echo "R$ " . number_format($total_retorno,2,",",".") ?>
		</td>
	</tr>
	<?php
	
	echo "</table></td></tr></table></td></tr></table>";

}
?>
<input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
</form>