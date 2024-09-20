<?php

/*     $numero = null;
    if(is_null($numero)){
        echo "--->   ES nulo";
        
    }else{
        echo "--->   No es Nulo ".$numero;
    }
    echo "<br>"; */

    /* $numero2 = null;
    unset($numero2);
    if(isset($numero2)){
        echo "Esta definido ";
    }else{
        echo "No esta definido";
    } */
   /*  $palabra = "Hola";
    if(empty($palabra)){
        echo "VAriable vacia";
    }else{
        echo "Variable llena";
    } */

    $clave = "Hola";
    // md5
    echo md5($clave);
    echo "<br>";
    // sha1
   /*  echo sha1($clave);
    echo "<br>";
    foreach (hash_algos() as $key => $valueHash) {
        echo $valueHash.' - '.hash($valueHash,$clave)."<br>";
    }
 */
    echo "<br>";
    $clave2 = "Abc123";
    $clave_hash = password_hash($clave2,PASSWORD_BCRYPT,['cost'=>10]);
    $clave3 = "Abc123";
    if(password_verify($clave3,$clave_hash)){
        echo "Correcto";
    }else{
        echo "Incorrecto";
    }