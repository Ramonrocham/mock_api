<?php

require_once 'conexao.php';
include "email.php";

class MockDataStorage{
    public static function newUser($username, $password, $fullName, $email, $number = null){
        if(!$username || !$password || !$fullName || !$email){
            return array(
                "status" => "error",
                "message" => "Missing required fields",
                "code" => 400
            );
        }
        $s_username = strip_tags($username);
        $s_password = strip_tags($password);
        $s_fullName = strip_tags($fullName);
        $s_email = strip_tags($email);
        $Isemail = filter_var($s_email, FILTER_VALIDATE_EMAIL);
        $s_number = $number ? strip_tags($number) : null;
        if (!$Isemail) {
            return array(
                "status" => "error",
                "message" => "Invalid email format",
                "code" => 400
            );
        }
        $isUser = self::getUserByUsername($s_username);
        if($isUser["status"] == "success"){
            return array(
                "status" => "error",
                "message" => "Username already exists",
                "code" => 409
            );
        }
        $isUser = self::getUserByEmail($s_email);
        if($isUser["status"] == "success"){
            return array(
                "status" => "error",
                "message" => "Email already exists",
                "code" => 409
            );
        }
        try{
            $conn = getDbConnection();
            $numUser = $conn->query("SELECT COUNT(*) as count FROM users");
            $row = $numUser->fetch_assoc();
            $count = $row['count'];
            $id = "changeId#$count";


            $conn = getDbConnection();
            $numUser = $conn->query("SELECT COUNT(*) as count FROM users");
            $row = $numUser->fetch_assoc();
            $count = $row['count'];
            $id = "userID#$count";
            $stmt = $conn->prepare("INSERT INTO users (id, username, password, name, email, number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->bind_param("ssssss", $id, $s_username, $s_password, $s_fullName, $s_email, $s_number);
            $stmt->execute();
            $stmt->close();

            $conn->close();

            return array(
            "status" => "success",
            "message" => "User created successfully",
            "code" => 201
        );

        }catch(Exception $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );

        }
    }

    public static function getUserByUsername($username){
        try{
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT username FROM users WHERE username = ?");
            $SQL->bind_param("s", $username);
            $SQL->execute();
            $result = $SQL->get_result();
            $userDB = $result->fetch_assoc();
            $SQL->close();
            $user = $userDB ? $userDB['username'] : null;
            if($user === null){
                return array(
                    "status" => "error",
                    "message" => "User not found",
                    "code" => 404
                );
            }
            return array(
                "status" => "success",
                "message" => "User found",
                "code" => 200,
                "data" => $user
            );

        }catch(Exception $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
    }

    public static function getUserByEmail($email){
        try{
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT username FROM users WHERE email = ?");
            $SQL->bind_param("s", $email);
            $SQL->execute();
            $result = $SQL->get_result();
            $userDB = $result->fetch_assoc();
            $SQL->close();
            $user = $userDB ? $userDB['username'] : null;
            if($user === null){
                return array(
                    "status" => "error",
                    "message" => "User not found",
                    "code" => 404
                );
            }
            return array(
                "status" => "success",
                "message" => "User found",
                "code" => 200,
                "data" => $user
            );

        }catch(Exception $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
    }

    public static function userLoginWithUsername(String $username,String $password) : array{
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT name from users where username = ? and password = ?");
            $SQL->bind_param("ss", $username,$password);
            $SQL->execute();
            $result = $SQL->get_result();
            $userDB = $result->fetch_assoc();
            $user = $userDB ? $userDB['name'] : null;

            
            if($user){
                return array(
                    "status" => "success",
                    "message" => "user login successfully",
                    "name" => $user
                );
            }

        } catch (\Throwable $e) {
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }

     public static function userLoginWithEmail(String $email,String $password) : array{
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT name from users where email = ? and password = ?");
            $SQL->bind_param("ss", $email,$password);
            $SQL->execute();
            $result = $SQL->get_result();
            $userDB = $result->fetch_assoc();
            $user = $userDB ? $userDB['name'] : null;
            if($user){
                return array(
                    "status" => "success",
                    "message" => "user login successfully",
                    "name" => $user
                );
            }

        } catch (\Throwable $e) {
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }

    public static function setNewPassword($username, $password, $newPassword){
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT id from users where username = ? and password = ?");
            $SQL->bind_param("ss", $username, $password);
            $SQL->execute();
            $result = $SQL->get_result();
            $idDB = $result->fetch_assoc();
            $idUser = $idDB ? $idDB['id'] : null;
            if($idUser){
                $SQL = $conn->prepare("UPDATE users set password = ? where id = ?");
                $SQL->bind_param("ss", $newPassword, $idUser);
                if($SQL->execute()){
                    return array(
                        "status" => "success",
                        "message" => "Password change succefully"
                    );
                }else{
                    return array(
                        "status" => "error",
                        "message" => "error"
                    );
                }
            }

        }catch(\Throwable $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }

    public static function updateDataUser($column, $data, $username, $password){
    try {
        $conn = getDbConnection();
        $SQL = $conn->prepare("SELECT id from users where username = ? and password = ?");
        $SQL->bind_param("ss", $username, $password);
        $SQL->execute();
        $result = $SQL->get_result();
        $idDB = $result->fetch_assoc();
        $idUser = $idDB ? $idDB['id'] : null;
        if($idUser){
            $SQL = $conn->prepare("UPDATE users set ".$column." = ? where id = ?");
            $SQL->bind_param("ss", $data, $idUser);
            if($SQL->execute()){
                return array(
                    "status" => "success",
                    "message" => "$column change succefully"
                );
            }else{
                return array(
                    "status" => "error",
                    "message" => "change error"
                );
            }
        }
        }catch(\Throwable $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }

    public static function isUsername($username){
        $response = self::getUserByUsername($username);
        return $response['status'] === "success";
    }

    public static function recoveryPassword($email){
        $response = self::getUserByEmail($email);
        $isUser =  $response['status'] === "success";
        if($isUser){
            $mailer = new Mailer();
            $code = $mailer->getExpirationCode();
            $tz = new dateTimeZone('America/Sao_Paulo');
            $dateExpiration = new DateTime('now', $tz);
            $dateExpiration->modify('+5 minutes');

            $mailer->to($email, 'Recuperação de senha');
            $mailer->body('<h2>Recuperação de senha</h2><p>Esse é o codigo de recuperação da sua conta</p>
            <p style="text-aling: center;">'.$code.'</p><p>Codigo expira as '.$dateExpiration->format("Y/m/d H:m:s") .'</p>',  'Api login.');
            $mailer->send();

            self::saveOnDbRecoveryCode($email, $code);

            return array(
                "status" => "success",
                "message" => "recovery email sent",
                "code" => 200,
                "recovery_code" => $code,
                "expires_at" => $dateExpiration->format("Y-m-d H:i:s")
            );
        }

        return array(
                "status" => "error",
                "message" => "recovery email not sent",
                "code" => 500
        );
    }

    public static function validateRecoveryCode($newPassword, $recoveryCode){
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT user_id, timestamp FROM log_changes WHERE user_data = ? AND action = 'recovery_code' AND status = 'active' ORDER BY timestamp DESC LIMIT 1");
            $SQL->bind_param("s", $recoveryCode);
            $SQL->execute();
            $result = $SQL->get_result();
            $logDB = $result->fetch_assoc();
            $SQL->close();
            if(!$logDB){
                return array(
                    "status" => "error",
                    "message" => "Invalid recovery code",
                    "code" => 400
                );
            }
            
            $tz = new dateTimeZone('America/Sao_Paulo');
            $timestamp = new DateTime($logDB['timestamp'], $tz);
            $now = new DateTime('now', $tz);
            $interval = $now->getTimestamp() - $timestamp->getTimestamp();

            if($interval > 300){
                return array(
                    "status" => "error",
                    "message" => "Recovery code expired",
                    "code" => 400
                );
            }

            $userId = $logDB['user_id'];
            $SQL = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $SQL->bind_param("ss", $newPassword, $userId);
            $SQL->execute();
            $SQL->close();

            return array(
                "status" => "success",
                "message" => "Password updated successfully",
                "code" => 200
            );

        } catch (Exception $e) {
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
    }

    public static function saveOnDbRecoveryCode($email, $code){
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $SQL->bind_param("s", $email);
            $SQL->execute();
            $result = $SQL->get_result();
            $userDB = $result->fetch_assoc();
            $SQL->close();
            $id = $userDB ? $userDB['id'] : null;
            
            if($id === null){
                return array(
                    "status" => "error",
                    "message" => "User not found",
                    "code" => 404
                );
            }

            $stmt = $conn->prepare("INSERT INTO log_changes (user_id, user_data, timestamp, action, status) VALUES (?, ?, NOW(), 'recovery_code', 'active')");
            $stmt->bind_param("ss", $id, $code);
            $stmt->execute();
            $stmt->close();
            return array(
                "status" => "success",
                "message" => "recovery code saved",
                "code" => 201
            );
        } catch (Exception $e) {
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }

    }

    public static function deleteUser($username, $password){
        try {
            $conn = getDbConnection();
            $SQL = $conn->prepare("SELECT id from users where username = ? and password = ?");
            $SQL->bind_param("ss", $username, $password);
            $SQL->execute();
            $result = $SQL->get_result();
            $idDB = $result->fetch_assoc();
            $idUser = $idDB ? $idDB['id'] : null;
            if($idUser){
                $SQL = $conn->prepare("DELETE from users where id = ?");
                $SQL->bind_param("s", $idUser);
                if($SQL->execute()){
                    return array(
                        "status" => "success",
                        "message" => "User deleted succefully"
                    );
                }else{
                    return array(
                        "status" => "error",
                        "message" => "error"
                    );
                }
            }

        }catch(\Throwable $e){
            return array(
                "status" => "error",
                "message" => "Database connection error",
                "code" => 500
            );
        }
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }
}