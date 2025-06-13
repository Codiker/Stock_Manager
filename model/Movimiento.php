<?php

class Movimiento {
    private ?int $id;
    private int $producto_id;
    private int $cantidad;
    private string $tipo; 
    private string $fecha;
    private string $estado;
    private string $created_at;
    private ?string $updated_at;
    public ?string $producto_nombre = null;

    public function __construct(
        ?int $id,
        int $producto_id,
        int $cantidad,
        string $tipo,
        string $fecha,
        string $estado,
        string $created_at,
        ?string $updated_at
    ) {
        $this->id = $id;
        $this->producto_id = $producto_id;
        $this->cantidad = $cantidad;
        $this->tipo = $tipo;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function getProductoId(): int { return $this->producto_id; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getTipo(): string { return $this->tipo; }
    public function getFecha(): string { return $this->fecha; }
    public function getEstado(): string { return $this->estado; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getUpdatedAt(): ?string { return $this->updated_at; }
     public function getProductoNombre(): string {
        return $this->producto_nombre ?? '';
    }
}