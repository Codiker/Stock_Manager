<?php

/** 
 * Modelo de Usuario
 *  Representa un usuario en el sistema.
 * @author Elder Urzola  ingelderurzola@gmail.com.
 * @version 1.0
 */
require_once __DIR__ . '/../config/Conection.php';
class Usuario
{
    private $nombre;
    private $email;
    private $password;
    private $rol_id;
    private $estado;

    public function __construct($nombre, $email, $password, $rol_id, $estado)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol_id = $rol_id;
        $this->estado = $estado;
    }


    public function getNombre()
    {
        return $this->nombre;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getRolId()
    {
        return $this->rol_id;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    public static function buscarPorCorreo($email)
    {
        $db = conectarBD();
        if (!$db) {
            throw new Exception("Error de conexión a la BD");
        }
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Formato de correo inválido");
            }


            $db = conectarBD();


            /** @var \SQLiteCloud\SQLiteCloudRowset $rs */
            $rs = $db->execute(
                "SELECT id, nombre, email, password, rol_id, estado
           FROM usuarios
          WHERE email = ?
          LIMIT 1",
                [$email]
            );

            // 3. Comprueba si trajo al menos una fila
            if ($rs && $rs->nrows > 0) {
                // 4. Reconstruye la fila 0 en un array asociativo
                $fila = [];
                for ($column = 0; $column < $rs->ncols; $column++) {
                    $colName       = $rs->name($column);
                    $fila[$colName] = $rs->value(0, $column);
                }
                error_log("Resultado de la consulta: " . print_r($fila, true));
                // 5. Devuelve el objeto Usuario con los datos correctos
                return new Usuario(
                    $fila['id'],
                    $fila['nombre'],
                    $fila['email'],
                    $fila['password'],
                    $fila['rol_id'],
                    $fila['estado']
                );
            }
            return null;
        } catch (Exception $e) {
            error_log("Error en buscarPorCorreo: " . $e->getMessage());
            return null;
        }
    }

    public static function crear($nombre, $email, $password, $rol_id, $estado)
    {
        try {
            $db = conectarBD();
            $hashedClave = password_hash($password, PASSWORD_DEFAULT);

            // Ejecutar INSERT y manejar errores con excepciones
            $result = $db->execute(
                "INSERT INTO usuarios (nombre, email, password, rol_id, estado) 
             VALUES (?, ?, ?, ?, ?)",
                [$nombre, $email, $hashedClave, $rol_id, $estado]
            );

            // Si hay error, la propia biblioteca lanzará una excepción (ajusta según tu driver)
            // Si no lanza excepción, verifica manualmente:
            if (!$result) {
                throw new Exception("Error al insertar: Consulta fallida");
            }

            // Obtener ID (depende de tu driver, ejemplo para SQLite Cloud):
            $result = $db->execute("SELECT LAST_INSERT_ROWID() as id");
            $nuevoId = $result->value(0, 0);

            return new Usuario($nuevoId, $nombre, $email, $hashedClave, $rol_id, $estado);
        } catch (Exception $e) {
            // Captura el mensaje de la excepción
            error_log("Error al crear usuario: " . $e->getMessage()); // <- Mensaje real aquí
            return null;
        }
    }

    public static function actualizar($id, $nombre, $email, $password = null, $rol_id, $estado)
    {
        try {
            $db = conectarBD();
            $params = [$nombre, $email, $rol_id, $estado];
            $query = "UPDATE usuarios SET nombre = ?, email = ?, rol_id = ?, estado = ?";

            if ($password) {
                $hashedClave = password_hash($password, PASSWORD_DEFAULT);
                $query .= ", password = ?"; // Nombre de columna correcto
                $params[] = $hashedClave; // Añadir al array, no reemplazarlo
            }

            $query .= " WHERE id = ?";
            $params[] = $id;

            $db->execute($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Marca un usuario como inactivo (estado = 0) para preservar su historial.
     * @param int $id ID del usuario.
     * @return bool true si se inactiva correctamente, false en caso de error.
     */
    public static function inactivar($id)
    {
        try {
            $db = conectarBD();
            $usuario = self::buscarPorId($id);
            if ($usuario && $usuario->getEstado() == 1) {
                $db->execute("UPDATE usuarios SET estado = 0 WHERE id = ?", [$id]);
            } else {
                throw new Exception("El usuario ya está inactivo o no existe.");
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al inactivar usuario: " . $e->getMessage());
            return false;
        }
    }


    public static function buscarPorId($id)
    {
        try {
            $db = conectarBD();
            $result = $db->execute("SELECT id, nombre, email, password, rol_id, estado FROM usuarios WHERE id = ? LIMIT 1", [$id]);

            if ($result && count($result) > 0) {
                $fila = $result[0];
                return new Usuario(
                    $fila['id'],
                    $fila['nombre'],
                    $fila['email'],
                    $fila['password'],
                    $fila['rol_id'],
                    $fila['estado']
                );
            }
            return null;
        } catch (Exception $e) {
            error_log("Error al buscar por ID: " . $e->getMessage());
            return null;
        }
    }

    public static function listar($soloActivos = true)
    {
        try {
            $db = conectarBD();
            $query = "SELECT id, nombre, email, rol_id, estado FROM usuarios";

            if ($soloActivos) {
                $query .= " WHERE estado = 1";
            }

            $result = $db->execute($query);
            $usuarios = [];

            foreach ($result as $fila) {
                $usuarios[] = new Usuario(
                    $fila['id'],
                    $fila['nombre'],
                    $fila['email'],
                    "", // No exponer la contraseña
                    $fila['rol_id'],
                    $fila['estado']
                );
            }
            return $usuarios;
        } catch (Exception $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [];
        }
    }
}
