<?php 



//AUTENTICADOR
class Auth {
    function isLoggedIn(){
        //Verifica se método é POST e usuário está definido na seção
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usuario'])){   
            return true;
        }
    }
    
    function isAdmin(){
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['usuario'])){
            if ($_SESSION['role'] === 'admin');
            echo "Logado como admin";
        }
    }
}





?>