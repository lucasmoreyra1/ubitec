<?php
        //todas las funciones siguientes son llamadas desde bd.php y son para la conexion con la base de datos

use LDAP\Result;

        function searchId($conn, $idUser){//busca el ultimo id de ruta del usuario
            $sql = "SELECT MAX(id_ruta) id_ruta FROM user_ruta WHERE id_user=:id_user";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $idUser);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        }

        function searchAllId($conn, $idUser){//busca todos los  id de ruta del usuario y los devuelve en un array
            $sql = "SELECT id_ruta FROM user_ruta WHERE id_user=:id_user";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_user', $idUser);
            $stmt->execute();
            $results = array();
            while($moment = $stmt->fetch(PDO::FETCH_ASSOC)){
                array_push($results, $moment['id_ruta']);
            }
            return $results;
        }

        function sendDireccion($conn, $direccion, $id_ruta){//envia las direcciones seguen el id de ruta
            $entregado = 0;
            $sql = "INSERT INTO ruta (id_ruta, direccion, entregado) VALUES (:id_ruta, :direccion, :entregado)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ruta', $id_ruta);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':entregado', $entregado);
            $stmt->execute();
            return;
        }

        //trae las direcciones desde la base de datos y devuelve un array 
        function searchDirections($id_ruta){
            $link = mysqli_connect("localhost", "root", "");
            mysqli_select_db($link, "ubitec");
            $resultFinal = array();
            try{
                $result = mysqli_query($link, "SELECT ruta.direccion FROM ruta INNER JOIN user_ruta ON user_ruta.id_ruta = ruta.id_ruta WHERE ruta.id_ruta=$id_ruta");
                mysqli_close($link);

            }catch(Exception $e){

            }
            if(!empty($result)){
                $lenght = $result->num_rows;//longitud de los datos
                //bucle para convertir el objeto en un array con las direcciones
                for($i = 0; $i<$lenght; $i++){
                    $resultFinal[$i]=mysqli_fetch_array($result)[0];
                }
            }
            return $resultFinal;

        }

        //elimina todas las direcciones que coincidan con el parametro
        function deleteAll($id_ruta){
            $link = mysqli_connect("localhost", "root", "");
            mysqli_select_db($link, "ubitec");

            $delete = mysqli_query($link, "DELETE FROM ruta WHERE id_ruta=$id_ruta");
            mysqli_close($link);
            return array();
        }

        //elimina la direccion que coincide con id_ruta y la direccion
        function deleteOne($direccion, $id_ruta){
            $link = mysqli_connect("localhost", "root", "");
            mysqli_select_db($link, "ubitec");

            $delete = mysqli_query($link, "DELETE FROM ruta WHERE id_ruta=$id_ruta AND direccion='$direccion'");
            mysqli_close($link);
            return;
        }

        //carga direcciones de inicio y final
        //$rutaIoF string que indica la tabla que se cargara la direccion ruta_inicio o ruta_final
        function loadExtremes($conn ,$id_ruta, $direccion, $rutaIoF){
            $load = "UPDATE user_ruta SET $rutaIoF=:direccion WHERE id_ruta=:id_ruta";
            $stmt = $conn->prepare($load);
            $stmt ->bindParam(':id_ruta', $id_ruta);
            $stmt ->bindParam(':direccion', $direccion);
            $result = $stmt->execute();
            return $result;
        }

        //recive el punto de inicio y final en un array clave valor con los nombres de las columnas
        function reciveExtremes($conn ,$id_ruta){
            $load = "SELECT ruta_inicio, ruta_final FROM user_ruta WHERE id_ruta=:id_ruta";
            $stmt = $conn->prepare($load);
            $stmt->bindParam(':id_ruta', $id_ruta);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            if($results){
                if($results['ruta_inicio'] && $results['ruta_final']){
                    return $results;
                }else{
                    return array();
                }
            }

        }

        //crea un nuevo id_ruta 
        function newId_ruta($conn, $userId){
            $sql = "INSERT INTO user_ruta (id_user) VALUES (:mostrar)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':mostrar', $userId);
            $stmt->execute();
        }

        function selectUser($conn, $email){
            $records = $conn->prepare('SELECT id FROM users WHERE email=:email');
            $records->bindParam(':email', $email);
            $records->execute();
            $results = $records->fetch(PDO::FETCH_ASSOC);
            if(is_countable($results) > 0){
                return $results['id'];
            }else return null;
        }

        function modifiEmail($conn, $email, $id_user){
            $load = "UPDATE users JOIN user_ruta ON users.id = user_ruta.id_user SET email=:email  WHERE id_user=:id_user";
            $stmt = $conn->prepare($load);
            $stmt ->bindParam(':id_user', $id_user);
            $stmt ->bindParam(':email', $email);
            $result = $stmt->execute();
            return $result;
        }

        function modifiPassword($conn, $password, $id_user){
            $load = "UPDATE users JOIN user_ruta ON users.id = user_ruta.id_user SET password=:password WHERE id_user=:id_user";
            $stmt = $conn->prepare($load);
            $stmt ->bindParam(':id_user', $id_user);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password);
            $result = $stmt->execute();
            return $result;
        }

        function selectEmail($conn, $id_user){
            $load = "SELECT email FROM users WHERE id=:id_user";
            $stmt = $conn->prepare($load);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        }
?>