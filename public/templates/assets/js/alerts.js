
const BASE_URL = window.location.origin + '/App_InventariosV.1/Stock_Manager';

// Notificaciones con SweetAlert2
function showNotification(type, message) {
    const config = {
        icon: type,
        title: type === 'success' ? 'Éxito' : 'Error',
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    };
    return Swal.fire(config);
}
function handleUrlNotifications() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) showNotification('success', success);
    if (error) showNotification('error', error);
}

function setupProductHandlers() {

    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            fetch(`${BASE_URL}/controller/productController.php?id=${id}`)
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
                })
                .catch(error => {
                    console.error('Error al cargar producto:', error);
                    showNotification('error', 'Error al cargar datos del producto');
                });
        });
    });

    document.querySelector("#formEditarProducto")?.addEventListener("submit", (e) => {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);

        fetch(`${BASE_URL}/controller/productController.php`, {
            method: "POST",
            body: data
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                showNotification('success', response.message).then(() => {
                    location.reload();
                });
            } else {
                showNotification('error', response.message);
            }
        })
        .catch(error => {
            console.error('Error al guardar:', error);
            showNotification('error', 'Error en la conexión');
        });
    });
}

// Inicialización
document.addEventListener("DOMContentLoaded", () => {
    // Verificar que SweetAlert2 esté cargado
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado');
        return;
    }

    handleUrlNotifications();
    setupProductHandlers();
});