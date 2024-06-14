<?php
namespace Repositories\Classes;
use Firebase\JWT\JWT;

class AuthRepository
{
    private $connection;

    public function __construct()
    {
        $this->db = \Core\Classes\Db::getInstance();
        $this->connection = $this->db->getConnection();
    }
    public function login(string $email, string $password): array
    {
        $statement = $this->connection->prepare("SELECT id, email,role, password FROM users WHERE email=:email");
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$user) { //проверка есть ли такой пользователь
            throw new \Exception('Пользователь не найден', 404);
        }
        else {
            if (password_verify($password, $user['password'])) { //проверка пароля
                $header = [
                    'alg' => 'HS256',
                    'typ' => 'JWT',
                    'kid' => 'my_key'
                ];

                $payload = [
                    'role' => $user['role'],
                    'id' => $user['id'],
                ];

                $secret_key = 'my_secret_key';
                $jwt = JWT::encode($payload, $secret_key, 'HS256', null, $header);

                setcookie('access_token', $jwt, time() + 3600, "/", 'cloud-storage.local', false, false); //задать токен в куки

                return [
                    'data' => 'Authorization complite successfully',
                    'headers' => [
                        'Location: /',
                        'Content-Type: application/json',
                    ]
                ];
            }
            else {
                throw new \Exception('Wrong password', 403);
            }
        }
    }
    public function resetPassword(string $email, string $password): void
    {
        $statement = $this->connection->prepare("SELECT id, email password FROM users WHERE email=:email");
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            throw new \Exception('Your user not found', 404);
        }
        else {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = $this->connection->prepare("UPDATE users SET password = '$hashPassword' WHERE email = '$email'");
            $statement->execute(); //смена пароля на бд
        }
    }
}