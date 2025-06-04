<?php
declare(strict_types=1);
class Categoria
{
    private ?int $id;
    private string $nombre;
    private string $descripcion;
    private string $created_at;
    private ?string $updated_at = null;

    public function __construct(
        ?int $id = null,
        string $nombre = '',
        string $descripcion = '',
        string $created_at = '',
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
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

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
}