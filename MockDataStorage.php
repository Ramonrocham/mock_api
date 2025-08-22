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
}