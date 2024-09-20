<?php

    function saludar(){
        //echo "Salimos de la funcion";
        return "Buenas noches";
    }

    echo saludar().'<br>';

    $numero1 = 5;
    $numero2 = 10;
    echo ($numero1+$numero2)*2;
    echo $numero1-$numero2;
    $numero1=20;
    $numero2=30;
    echo ($numero1+$numero2)*2;
    echo $numero1-$numero2;

    function calcular($numero1Function,$numero2Function,$operador){
       /*  $a = 5;
        $b = 10; */
        switch ($operador) {
            case '+':
                $result = $numero1Function + $numero2Function;
                break;
            case '-':
                $result = $numero1Function - $numero2Function;
                break;
            case '*':
                $result = $numero1Function * $numero2Function;
                break;
            case '/':
                $result = $numero1Function / $numero2Function;
                break;
        }
        return $result;
    }

    echo calcular($numero1,$numero2,'+').'<br>';
    echo calcular($numero1,$numero2,'-').'<br>';
    echo calcular($numero1,$numero2,'*').'<br>';
    echo calcular($numero1,$numero2,'/').'<br>';
