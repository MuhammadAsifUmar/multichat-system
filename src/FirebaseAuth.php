<?php
class FirebaseAuth {
    private $auth;

    public function __construct($auth) {
        $this->auth = $auth;
    }

    public function registerUser($phone, $password) {
        try {
            $this->auth->createUserWithPhoneNumberAndPassword($phone, $password);
            return "User registered successfully.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function loginUser($phone, $password) {
        try {
            $user = $this->auth->signInWithPhoneNumberAndPassword($phone, $password);
            return $user->idToken();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
