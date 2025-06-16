document.addEventListener("DOMContentLoaded", function () {
  // Gráfica de área: Ventas diarias
  fetch("/getVentasDiarias.php")
    .then((res) => res.json())
    .then((data) => {
      const ctx = document.getElementById("myAreaChart").getContext("2d");
      new Chart(ctx, {
        type: "line",
        data: {
          labels: Object.keys(data),
          datasets: [
            {
              label: "Ventas",
              data: Object.values(data),
              fill: true,
              backgroundColor: "rgba(2,117,216,0.2)",
              borderColor: "rgba(2,117,216,1)",
            },
          ],
        },
        options: {
          scales: {
            x: { title: { display: true, text: "Día" } },
            y: { beginAtZero: true },
          },
        },
      });
    });

  // Gráfica de barra: Productos por categoría
  fetch("../getProductosPorCategoria.php")
    .then((res) => res.json())
    .then((data) => {
      const ctx = document.getElementById("myBarChart").getContext("2d");
      const categorias = Object.keys(data);
      const cantidades = Object.values(data);

      // Paleta de colores para las barras
      const colores = [
        "rgba(54, 162, 235, 0.7)",
        "rgba(255, 99, 132, 0.7)",
        "rgba(255, 206, 86, 0.7)",
        "rgba(75, 192, 192, 0.7)",
        "rgba(153, 102, 255, 0.7)",
        "rgba(255, 159, 64, 0.7)",
        "rgba(199, 199, 199, 0.7)",
      ];
      // Si hay más categorías que colores, repite la paleta
      const backgroundColors = categorias.map(
        (_, i) => colores[i % colores.length]
      );

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: categorias,
          datasets: [
            {
              label: "Cantidad de productos",
              data: cantidades,
              backgroundColor: backgroundColors,
              borderColor: backgroundColors.map((c) => c.replace("0.7", "1")),
              borderWidth: 1,
            },
          ],
        },
        options: {
          plugins: {
            legend: { display: false },
            title: {
              display: true,
              text: "Productos por Categoría",
              font: { size: 18 },
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed.y} productos`;
                },
              },
            },
          },
          scales: {
            x: {
              title: { display: true, text: "Categoría", font: { size: 14 } },
            },
            y: {
              beginAtZero: true,
              title: { display: true, text: "Cantidad", font: { size: 14 } },
              ticks: { stepSize: 1 },
            },
          },
        },
      });
    });
});
