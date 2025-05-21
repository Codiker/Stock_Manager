alerts.js
// alerts.js

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: success,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    if (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
});




document.addEventListener("DOMContentLoaded", () => {
    // Botón de editar
    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            fetch(`../../controller/ProductoController.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.querySelector("#edit-id").value = p.id;
                        document.querySelector("#edit-nombre").value = p.nombre;
                        document.querySelector("#edit-descripcion").value = p.descripcion;
                        document.querySelector("#edit-precio").value = p.precio;
                        document.querySelector("#edit-stock").value = p.stock;
                        document.querySelector("#edit-categoria_id").value = p.categoria_id;
                        document.querySelector("#edit-activo").value = p.activo ? "1" : "0";
                        document.querySelector("#edit-estado").value = p.estado;
                        new bootstrap.Modal(document.querySelector("#modalEditarProducto")).show();
                    }
                });
        });
    });

    // Enviar formulario
    document.querySelector("#formEditarProducto").addEventListener("submit", (e) => {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);

        fetch("../../controller/ProductoController.php", {
            method: "POST",
            body: data
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                Swal.fire("Éxito", response.message, "success").then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Error", response.message, "error");
            }
        });
    });
});