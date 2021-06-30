<?php


class Auth {

    protected $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function register($data)
    {
        if($data['password'] !== $data['password_confirmation']) {

            return false;
        }

        $this->database->query('INSERT INTO users (email, username, password, is_admin) VALUES (?,?,?,?)',
            $data['email'],
            $data['username'],
            md5($data['password']),
            0 // basic user role
        );

        $id = $this->database->lastInsertID();

        if(!$id) {
            return false;
        }

        return [
            'id'    => $id,
            'token' => $this->generateToken($id)
        ];
    }

    public function login($data)
    {
        $user = $this->database->query('SELECT id from users where username = ? AND password = ?', $data['username'], md5($data['password']))->fetchArray();

        if(!$user) {
            return false;
        }

        $token = $this->generateToken($user['id']);

        return $token;
    }

    public function authenticate()
    {
        $token = $this->getBearerToken();

        $user = $this->database->query('SELECT id, username, email, is_admin from users where token = ?', $token)->fetchArray();

        return $user;
    }

    public function generateToken($id = false)
    {
        $str = rand();
        $token = md5($str);

        if($id) {
            $this->storeTokenToUser($id, $token);
        }

        return $token;
    }

    public function storeTokenToUser($id, $token)
    {
        $this->database->query('UPDATE users SET token = ? WHERE id = ?', $token, $id);
    }

    public function getAuthorizationHeader(){
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();

            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }

    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();

        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}