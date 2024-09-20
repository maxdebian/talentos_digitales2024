<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Estamos en PHP</h1>
    <?php
        // definicio de variables
        $nombre = "Maximiliano"; 
        $apellido = "Tedesco"; 
        $nombreApellido = "";
        $edad=40;
        $bandera = true;
        $bandera2 = false;

        // Definicion de constantes
        define('MICONSTANTE','Valor de mi constante');
        
        echo "Archivo php con nombre: $nombre <br>";
        echo 'Archivo php con apellido: '.$apellido.' y su edad es: '.$edad.'<br>' ;
        echo MICONSTANTE;
        echo "<br><br><br>";
        echo $bandera." <----<br>";
        echo $bandera2." <----<br>";
        var_dump($nombre);
        echo "<br><br><br>";
        var_dump($edad);
        var_dump($bandera,$bandera2);

        date_default_timezone_set('America/Argentina/Buenos_Aires');
        echo "<br>".date('D m Y h:i:s');

        $pais = 'Argentina';
        $pais2 = $pais;
        $pais3 = &$pais;

        echo "<br> pais ".$pais."<br> ";
        echo "<br> pais2 ".$pais2."<br> ";
        echo "<br> pais3 ".$pais3."<br> ";
        echo "<br>---------<br>";
        
        $pais = 'Mexico';

        echo "<br> pais ".$pais."<br> ";
        echo "<br> pais2 ".$pais2."<br> ";
        echo "<br> pais3 ".$pais3."<br> ";

        // Operadores + - * / %
        // Incrementos y Decrementos ++ -- += -= ++Variable --Variable
        // and &&  or ||  Not ! 
        // === == !=  !==  > < >= <=

        $numero1 = 10;
        $numero2 = 15;

        // Incremento tradicional
        //   10   =   10    + 1
        
        //$numero1 = $numero1 + 5;
        //$numero1 +=5;   
        //$numero1++;
        
        //var_dump($numero1,$numero2);
        $suma = $numero1 + $numero2;
        $resta = $numero1 - $numero2; 
        echo "<br> $suma y la resta $resta";


        

        

    ?>
    
</body>
</html>




