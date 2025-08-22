<?php

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

        if (self::isUsername($s_username)) {
            return array(
                "status" => "error",
                "message" => "Username already exists",
                "code" => 409
            );
        }
        
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true) ?? [];
        $users[count($users)] = array(
            'username' => $s_username,
            'password' => $s_password,
            'id' => "userID#0".(count($users) + 1),
            'status' => "active",
            'name' => $s_fullName,
            'created_at' => date("Y-m-d H:i:s"),
            'email' => $s_email,
            'number' => $s_number ?? ""
        );
        file_put_contents("json/user.json", json_encode($users, JSON_PRETTY_PRINT));
        return array(
            "status" => "success",
            "message" => "User created successfully",
            "code" => 201
        );
    }

    public static function getUsersByUsername($username){
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true);
        if (!$users) {
            return array(
                "status" => "error",
                "message" => "No user found",
                "code" => 404
            );
        }
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return array(
                    "status" => "success",
                    "message" => "User found",
                    "code" => 200,
                    "data" => $user
                );
            }
        }

        return array(
            "status" => "error",
            "message" => "User not found",
            "code" => 404
        );
    }

    public static function userLogin($username, $password){
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true);
        $logLoginJson = file_get_contents("json/logLogin.json");
        $logLogin = json_decode($logLoginJson, true) ?? [];
        if (!$users) {
            $logLogin[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'status' => "falied"
                );
            file_put_contents("json/logLogin.json", json_encode($logLogin, JSON_PRETTY_PRINT));
            return array(
                "status" => "error",
                "message" => "No user found",
                "code" => 404
            );
        }
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                
                $logLogin[] = array(
                    'userData' => array('userId' => $user['id'],
                        'username' => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'status' => "success"
                );
                file_put_contents("json/logLogin.json", json_encode($logLogin, JSON_PRETTY_PRINT));
                return array(
                    "status" => "success",
                    "message" => "Login successful",
                    "code" => 200,
                    "data" => $user
                );
            }
        }
        $logLogin[] = array(
                    'userData' => array(
                        "userId" => "null",
                        "username" => $username),
                    'timestamp' => date("Y-m-d H:i:s"),
                    'status' => "falied"
                );
        file_put_contents("json/logLogin.json", json_encode($logLogin, JSON_PRETTY_PRINT));
        return array(
            "status" => "error",
            "message" => "Invalid username or password",
            "code" => 401
        );
    }

    public static function setNewPassword($username, $password, $newPassword){
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
            if ($user['username'] === $username && $user['password'] === $password) {
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
        $response = self::getUsersByUsername($username);
        return $response['status'] === "success";
    }
}