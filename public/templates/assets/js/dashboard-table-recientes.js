document.addEventListener('DOMContentLoaded', function() {
    fetch('../getProductosRecientes.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#tablaRecientes tbody');
            tbody.innerHTML = '';
            data.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${p.nombre}</td>
                    <td>$${Number(p.precio).toFixed(2)}</td>
                    <td>${p.stock}</td>
                    <td>${p.categoria}</td>
                    <td>${p.fecha}</td>
                `;
                tbody.appendChild(tr);
            });
            new simpleDatatables.DataTable("#tablaRecientes",{
                labels: {
                    placeholder: "Buscar...",
                    perPage: " productos por página",
                    noRows: "No se encontraron productos recientes",
                    info: "Mostrando {start} a {end} de {rows} productos recientes"
                }
            });
        });
}); 