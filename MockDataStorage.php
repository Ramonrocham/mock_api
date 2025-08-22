<?php

class MockDataStorage{
    public static function newUser($username, $password, $fullName, $email, $number = null){
        $usersJson = file_get_contents("json/user.json");
        $users = json_decode($usersJson, true) ?? [];
        $users[count($users)] = array(
            'username' => $username,
            'password' => $password,
            'id' => "userID#0".(count($users) + 1),
            'status' => "active",
            'name' => $fullName,
            'created_at' => date("Y-m-d H:i:s"),
            'email' => $email,
            'number' => $number ?? ""
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
}