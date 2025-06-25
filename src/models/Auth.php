<?php

class Login
{
    public static function attemptLogin(string $email, PDO $pdo): ?array
    {
        $sql = "
            SELECT TOP 1 U.UserID, U.Email, U.FullName, U.PasswordHash, U.Region, R.RoleName
            FROM Users U
            LEFT JOIN UserRoles UR ON U.UserID = UR.UserID
            LEFT JOIN Roles R ON UR.RoleID = R.RoleID
            WHERE U.Email = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}

class Register
{
    public static function registerUser($data,  $pdo)
    {
        try {
            $pdo->beginTransaction();

            $email = trim($data['email']);
            $password = $data['password'];
            $fullName = trim($data['fullName']);
            $region = strtoupper(trim($data['region'] ?? 'CENTRAL'));

            // Hashear la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar en tabla de usuarios
            $stmt = $pdo->prepare("
            INSERT INTO Users (Email, PasswordHash, FullName, Region, isActive, createdAt)
            VALUES (?, ?, ?, ?, 1, GETDATE())
            ");
            $stmt->execute([$email, $hashedPassword, $fullName, $region]);

            $userId = $pdo->lastInsertId();

            // Asignar rol predeterminado opcional
            $defaultRole = 'admin';
            $roleStmt = $pdo->prepare("SELECT RoleID FROM Roles WHERE RoleName = ?");
            $roleStmt->execute([$defaultRole]);
            $roleId = $roleStmt->fetchColumn();

            if ($roleId) {
                $assignStmt = $pdo->prepare("INSERT INTO UserRoles (UserID, RoleID) VALUES (?, ?)");
                $assignStmt->execute([$userId, $roleId]);
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }
}
