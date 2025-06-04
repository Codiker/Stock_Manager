<?php
declare(strict_types=1);
class Producto
{
    private ?int $id;
    private string $nombre;
    private string $descripcion;
    private float $precio;
    private int $stock;
    private int $categoria_id;
    private bool $activo;
    private string $estado;
    private string $created_at;
    private ?string $updated_at;

    // Propiedad adicional para la categoría o-o
    public ?string $categoria_nombre = null;
 
    public function __construct(
        ?int $id = null,
        string $nombre = '',
        string $descripcion = '',
        float $precio = 0.0,
        int $stock = 0,
        int $categoria_id = 0,
        bool $activo = true,
        string $estado = 'disponible',
        string $created_at = '',
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->categoria_id = $categoria_id;
        $this->activo = $activo;
        $this->estado = $estado;
        $this->created_at = $created_at ?: date('Y-m-d H:i:s');
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNombre(): string
    {
        return $this->nombre;
    }
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }
    public function getPrecio(): float
    {
        return $this->precio;
    }
    public function getStock(): int
    {
        return $this->stock;
    }
    public function getCategoriaId(): int
    {
        return $this->categoria_id;
    }
    public function isActivo(): bool
    {
        return $this->activo;
    }
    public function getEstado(): string
    {
        return $this->estado;
    }
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setEstado(string $estado): void
    {
        $estadosValidos = ['disponible', 'agotado', 'bajo', 'descontinuado'];
        if (!in_array($estado, $estadosValidos)) {
            throw new InvalidArgumentException("Estado no válido.");
        }
        $this->estado = $estado;
    }

    public function validar(): array
    {
        $errores = [];
        if (empty($this->nombre)) $errores['nombre'] = 'El nombre es obligatorio';
        if ($this->precio <= 0) $errores['precio'] = 'El precio debe ser mayor a 0';
        if ($this->stock < 0) $errores['stock'] = 'El stock no puede ser negativo';
        if ($this->categoria_id <= 0) $errores['categoria_id'] = 'Seleccione una categoría válida';
        return $errores;
    }
}