<?php
###########
## Módulo de pesquisa para BOLETOS
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

$chavePesquisa = $_GET['Id'];

//Monta a query para pegar os dados
$sql = "SELECT id, sacado, endereco1, endereco2 FROM boleto WHERE id = '$chavePesquisa'";

//Executa a query
$query = mysql_query($sql);

//Conta o numero de registros da query
$registros = mysql_num_rows($query);

//Caso não houver registros
if ($registros == 0) 
{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Boletos: </span><span class='style1'>Boleto nao encontrado !</span></td>
			</tr>
		</table>";

} 

else 

{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'>
					<span class='TituloModulo'>Informe os dados do sacado: </span>
					</br>
					<span style='color: #990000'>ATENCAO: Nao utilize os caracteres & (e comercial) ou ? (interrogacao).</span>
				</td>
			</tr>
			<tr>
				<td style='padding-top: 6px'>";		  
										

	//efetua o loop na pesquisa
	while ($dados_rec = mysql_fetch_array($query))
	{		
		
		?>
		<input name="edtBoletoId" type="hidden" id="edtBoletoId" value="<?php echo $dados_rec["id"] ?>" />
		<input name="edtSacado" type="text" class="requerido" id="edtSacado" style="width: 500px" maxlength="75" value="<?php echo $dados_rec["sacado"] ?>" />
		<br/>
		<input name="edtEndereco1" type="text" class="requerido" id="edtEndereco1" style="width: 500px" maxlength="75" value="<?php echo $dados_rec["endereco1"] ?>" /> 
		<br/>
		<input name="edtEndereco2" type="text" class="requerido" id="edtEndereco2" style="width: 500px" maxlength="75" value="<?php echo $dados_rec["endereco2"] ?>" /> 
		<br/>
		
	<?php 
		
		//Fecha o while
		}
	
	?>
	
		</td>
	</tr>
	<tr>
		<td>
			<br/>
			<input class="button" value="Salvar dados do sacado" name="btnSalvarBoleto" type="button" id="btnSalvarBoleto" onclick="wdCarregarFormulario('BoletoAlteraSacadoSalva.php?Sacado=' + document.getElementById('edtSacado').value + '&Endereco1=' + document.getElementById('edtEndereco1').value + '&Endereco2=' + document.getElementById('edtEndereco2').value + '&BoletoId=' + document.getElementById('edtBoletoId').value,'salva_boleto')" />		
		</td>
	</tr>
	<tr>
		<td>
			<div id="salva_boleto"></div>
		</td>
	</tr>
</table>

<?php			
		
	}

?> 