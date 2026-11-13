<?php

class LoginService
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function login(Login $login): array
    {
        try {

            // Buscar únicamente el usuario y su contraseña almacenada
            $query = "SELECT Username, Password
                      FROM usernames
                      WHERE Username = :Username";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ':Username' => $login->username
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar que el usuario exista y que la contraseña coincida
            if (
                !$user ||
                !password_verify($login->password, $user['Password'])
            ) {
                return [
                    'status' => 'error',
                    'message' => 'Usuario o contraseña incorrectos.'
                ];
            }

            // Iniciar la sesión si todavía no existe.
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // Renovar el identificador para evitar fijación de sesión.
            if (!session_regenerate_id(true)) {
                return [
                    'status' => 'error',
                    'message' => 'No fue posible iniciar la sesión.'
                ];
            }

            // Guardar la identidad autenticada.
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_username'] = $user['Username'];
            // Registrar el momento de la última actividad.
            $_SESSION['last_activity'] = time();

            return [
                'status' => 'success',
                'message' => 'Bienvenido.'
            ];
        } catch (PDOException $e) {

            error_log($e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar iniciar sesión.'
            ];
        }
    }
}
