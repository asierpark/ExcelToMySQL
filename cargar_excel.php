<?php
    use Box\Spout\Reader\ReaderFactory;
    use Box\Spout\Common\Type;

    require_once 'spout-2.4.3/src/Spout/Autoloader/autoload.php';

    // Comprueba si el nombre del archivo no está vacío
    if(!empty($_FILES['file']['name'])){
        $conn = mysqli_connect('HOST','BDNAME','BDPASS');
        mysqli_select_db($conn, 'BDNAME');
        mysqli_query($conn,"SET NAMES 'utf8'");
        // Recoge la extensión del archivo cargado
        $pathinfo = pathinfo($_FILES["file"]["name"]);
        // comprueba si el archivo tiene extensión .xsl
        // comprueba si el archivo no está vacío
       if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0 ) {
           // Nombre de archivo temporal
            $inputFileName = $_FILES['file']['tmp_name'];
            // Leemos el excel con el objeto ReaderFactory
            $reader = ReaderFactory::create(Type::XLSX);
            // Abrimos el archivo
            $reader->open($inputFileName);
            $count = 1;
            $incorrect = false;
            $sql = "INSERT INTO `TABLENAME` (`FIELD1`, `FIELD2`, `FIELD3`, `FIELD4`) VALUES ";
            // Número de hojas en el archivo Excel
            foreach($reader->getSheetIterator() as $sheet){
                // Número de filas en el archivo Excel
                foreach($sheet->getRowIterator() as $row){
                    // Lee a partir de la cabecera
                    if(($count > 0) and !($incorrect)){
                        $sql = $sql . "(
                        $row[0], $row[1], $row[2], $row[3]
                        ),<br />";
                    } //Aquí se pueden hacer las virguerías que queramos (valores absolutos, comprobaciones, etc.)
                }
                $count++;
            }
        }
        // Cierra el archivo Excel
        $reader->close();
        if(!$incorrect){
            $sql = substr($sql, 0, -1);
            echo $sql;
        }else{
            echo "Error al añadir a la base de datos.";
        }
    }else{
        echo "Selecciona un excel de verdad.";
    }
?>
<br />
<br />
