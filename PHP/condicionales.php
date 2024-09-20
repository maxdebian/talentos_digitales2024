<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>condicionales</h1>

    <?php

        // Operadores + - * / %
        // Incrementos y Decrementos ++ -- += -= ++Variable --Variable
        // and &&  or ||  Not ! 
        // === == !=  !==  > < >= <=

        $edad = 40;
        $bandera = true;
        //var_dump(!($edad<40));
        
        //var_dump($edad>18 || $edad<40);
        /* if($edad>18){
            echo "Sos mayor de edad";
        }else{
            echo "Sos menor de edad";
        } */

        /* if($edad<18){
            echo "Sos Menor de edad";
        }else if($edad>18 && $edad<40){
            echo "Sos Mayor de edad";
        }else{
            echo "Sos adulto";
        } */

       /*  if($edad<18){
            echo "Sos Menor de edad";
        } 
        if($edad>18 && $edad<40){
            echo "Sos Mayor de edad";
        }
        if($edad>=40){
            echo "Sos adulto";
        } */

        // and &&  or ||  Not ! 

        /* && and Y 
        ($edad>18)  &&  ($edad<=40)
            V               V           =>   V TRUE
            F               V           =>   F FALSE
            V               F           =>   F FALSE
            F               F           =>   F FALSE
        if($edad>18 && $edad<40) [19-39]

        || or O 
        ($edad>18)  ||  ($edad<=40)
            V               V           =>  V TRUE  
            F               V           =>  V TRUE  
            V               F           =>  V TRUE     
            F               F           =>  F FALSE  */


        // === == !=  !==  > < >= <=
        $numero1 = 10;
        $numero2 = 10;
/*         var_dump($numero1,$numero2);
        if($numero1 !== $numero2){
            echo "<br>Son distintos";
        } */

        /* if($numero1>0){
            echo "Es positivo";
        }else{
            echo "Es negativo";
        } */
        // if ternarios
        /* $resultado = ($numero1>0) ? "Es positivo" : "Es negativo"; 
        echo $resultado; */

        echo ($numero1>0) ? "Es positivo" : "Es negativo";
    ?>  

</body>
</html>