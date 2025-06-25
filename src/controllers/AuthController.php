<?php
require_once BASE_PATH . '/src/models/Auth.php';
require_once BASE_PATH . '/src/models/Employee.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public static function register($input, $pdo)
    {
        $success = Register::registerUser($input, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudieron guardar los datos.'];
    }

    private static function getJwtSecret(): string
    {
        return $_ENV['JWT_SECRET'];
    }
    private const JWT_EXPIRE_SECONDS = 3600 * 24; // 24 horas

    public static function login(string $email, string $password, PDO $pdo): array
    {
        $user = Login::attemptLogin($email, $pdo);

        if (!$user || !password_verify($password, $user['PasswordHash'])) {
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }

        // Generar el token JWT
        $payload = [
            'iat' => time(),
            'exp' => time() + self::JWT_EXPIRE_SECONDS,
            'uid' => $user['UserID'],
            'region' => $user['Region'],
            'email' => $user['Email'],
            'role' => $user['RoleName'] ?? null
        ];

        $token = JWT::encode($payload, self::getJwtSecret(), 'HS256');

        return [
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user['UserID'],
                'name' => $user['FullName'],
                'email' => $user['Email'],
                'region' => $user['Region'],
                'role' => $user['RoleName'] ?? null
            ]
        ];
    }

    public static function verifyToken(string $token): array|null
    {
        try {
            $decoded = JWT::decode($token, new Key(self::getJwtSecret(), 'HS256'));
            return (array) $decoded;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function getUserInfoFromToken(string $token): array|null
    {
        $decoded = self::verifyToken($token);
        return $decoded ? [
            'id' => $decoded['uid'],
            'region' => $decoded['region'],
            'email' => $decoded['email'],
            'role' => $decoded['role'] ?? null
        ] : null;
    }

    //funcion para traer informacion adicioal con conexion al TRESS
    public static function getExtraData($email, $pdo)
    {
        $success = Employee::getData($email, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudieron guardar los datos.'];
    }
}
