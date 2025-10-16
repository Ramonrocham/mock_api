<?php

require_once 'conexao.php';

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

    public static function setNewpasswordByEmail($username, $email, $newPassword){
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true);
        if (!$users) {
            $changes[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "password_change",
                    'status' => "falied"
                );
            file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));

            return array(
                "status" => "error",
                "message" => "No user found",
                "code" => 404
            );
        }
        foreach ($users as &$user) {
            if ($user['username'] === $username && $user['email'] === $email) {
                $user['password'] = $newPassword;
                file_put_contents("json/user.json", json_encode($users, JSON_PRETTY_PRINT));

                $changes[] = array(
                    'userData' => $user,
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "password_change",
                    'status' => "success"
                );
                file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));

                return array(
                    "status" => "success",
                    "message" => "Password updated successfully",
                    "code" => 200
                );
            }
        }

        $changes[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "password_change",
                    'status' => "falied"
                );
        file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));

        return array(
            "status" => "error",
            "message" => "Invalid username or email",
            "code" => 401
        );
    }

    public static function isUsername($username){
        $response = self::getUserByUsername($username);
        return $response['status'] === "success";
    }

    public static function setNewUsername($username, $newUsername){
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true);

        $changesJson = file_get_contents("json/logChanges.json");
        $changes = json_decode($changesJson, true) ?? [];

        if (!$users) {
            $changes[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "username_change",
                    'status' => "falied"
                );
            file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));

            return array(
                "status" => "error",
                "message" => "No user found",
                "code" => 404
            );
        }

        if (self::isUsername($newUsername)) {
            return array(
                "status" => "error",
                "message" => "Username already exists",
                "code" => 409
            );
        }

        foreach ($users as &$user) {
            if ($user['username'] === $username) {
                $oldUsername = $user['username'];
                $user['username'] = $newUsername;
                file_put_contents("json/user.json", json_encode($users, JSON_PRETTY_PRINT));

                $changes[] = array(
                    'userData' => $user,
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "username_change",
                    'status' => "success"
                );
                file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));
                return array(
                    "status" => "success",
                    "message" => "Username updated successfully from '$oldUsername' to '$newUsername'",
                    "code" => 200
                );
            }
        }

        $changes[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'action' => "username_change",
                    'status' => "falied"
                );
        file_put_contents("json/logChanges.json", json_encode($changes, JSON_PRETTY_PRINT));

        return array(
            "status" => "error",
            "message" => "Invalid username",
        );
    }
}