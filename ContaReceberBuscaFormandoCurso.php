<?php 
###########
## Módulo para busca de formando de um evento para utilização numa conta a receber
## Criado: 10/12/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Captura o evento para filtragem dos formandos
$EventoId = $_GET["EventoId"];

//Efetua o lookup na tabela de cursos que existem neste evento
//Monta o sql de pesquisa
$lista_cursos = "SELECT 
					curso.id, 
					curso.nome as curso_nome
					FROM eventos_formando form 
					LEFT OUTER JOIN cursos curso ON curso.id = form.curso_id
					WHERE form.empresa_id = $empresaId AND form.evento_id = $EventoId 
					GROUP BY curso.id
					ORDER BY curso_nome";

//Executa a query
$dados_cursos = mysql_query($lista_cursos);

//Conta o total de formandos que existem no evento
$total_cursos = mysql_num_rows($dados_cursos);

//Caso o total de formandos for zero
if ($total_cursos == 0) 
{
 
  //Exibe a mensagem que não há formandos para este evento
  echo "<span style='color: #990000'><b>[ Não há cursos vinculados aos formandos deste evento ! ]</b></span>
        <input type='hidden' name='cmbCursoId' id='cmbCursoId' value='0'>
 "; 
  
} 

else 

{
 
?>

<select name="cmbCursoId" id="cmbCursoId" style="width:350px">
	<option value="0">Selecione uma Opção</option>
		<?php 
			//Monta o while para gerar o combo de escolha
			while ($lookup_curso = mysql_fetch_object($dados_cursos)) 
			{ 
		?>
    <option value="<?php echo $lookup_curso->id ?>" ><?php echo $lookup_curso->curso_nome ?> </option>
  <?php } ?>
</select>

<?php
  
}

?>