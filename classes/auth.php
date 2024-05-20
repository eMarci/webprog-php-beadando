<?php
require_once "user.php";

class Auth
{
    private $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    public function register($user)
    {
        $user["password"] = password_hash($user['password'], PASSWORD_DEFAULT);
        return $this->userRepository->insert((object) $user);
    }
    public function user_exists($username)
    {
        $users = $this->userRepository->filter(function ($user) use ($username) {
            return ((array) $user)["username"] === $username;
        });
        return count($users) >= 1;
    }
    public function login($user)
    {
        $_SESSION["user"] = $user["username"];
    }
    public function check_credentials($username, $password)
    {
        $users = $this->userRepository->filter(function ($user) use ($username) {
            return ((array) $user)["username"] === $username;
        });
        if (count($users) === 1) {
            $user = (array) array_values($users)[0];
            return password_verify($password, $user["password"])
                ? $user
                : false;
        }
        return false;
    }
    private function get_user(): array
    {
        if (!$this->is_authenticated()) {
            return [];
        }
        $users = $this->userRepository->filter(function ($user) {
            return ((array) $user)["username"] === $_SESSION["user"];
        });
        if (count($users) === 1) {
            $user = (array) array_values($users)[0];
            return $user;
        }
        return [];
    }
    public function get(string $key): mixed
    {
        return $this->get_user()[$key];
    }
    public function change_balance_by(int $value)
    {
        $this->userRepository->update(function ($user) {
            return ((array) $user)["username"] === $_SESSION["user"];
        }, function ($user) use ($value) {
            $user->balance += $value;
        });
    }
    public function is_authenticated()
    {
        return isset($_SESSION["user"]);
    }
    public function logout()
    {
        unset($_SESSION["user"]);
    }
}