<?php

$hoje = date("d/m/Y", mktime());

//Inclui o arquivo para manipulação de datas
include "include/ManipulaDatas.php";

$data_processa_1 = som_data($hoje, 1);

echo "Data processamento: " . $data_processa_1;

?>