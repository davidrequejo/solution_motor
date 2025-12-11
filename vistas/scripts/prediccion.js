$(document).ready(function () {


    const ventasMensuales = [200000, 254300, 183255, 230586, 235000, 288400, 270000, 260000, 300000];
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre'];

    const productosBase = [
    {codigo: 'AG03443838', nombre: 'Herbicida Total', stock: 2000, compra: 3000, venta: 4500, aprobacion: 70},
    {codigo: 'AG43844048', nombre: 'Fungicida Forte', stock: 800, compra: 2500, venta: 4000, aprobacion: 75},
    {codigo: 'AG09759438', nombre: 'Insecticida Max', stock: 1000, compra: 1500, venta: 2050, aprobacion: 65},
    {codigo: 'AG06640443', nombre: 'Nutriente Plus', stock: 1000, compra: 2000, venta: 3050, aprobacion: 78},
    {codigo: 'AG03490477', nombre: 'Controlador Bio', stock: 900, compra: 1800, venta: 3500, aprobacion: 72}
    ];

    const productosAdicionales = [
    {codigo: 'AG03440379', nombre: 'Fertilizante K1'},
    {codigo: 'AG03440458', nombre: 'Regulador PH'},
    {codigo: 'AG17440436', nombre: 'Acelerador Cres'},
    {codigo: 'AG03740434', nombre: 'Absorbente Max'},
    {codigo: 'AG09550438', nombre: 'Revitalizante Eco'}
    ];

    const colores = {
    borderColor: 'rgba(13, 110, 253, 1)',
    backgroundColor: 'rgba(13, 110, 253, 0.5)'
    };

    const ctx = document.getElementById('ventasChart').getContext('2d');
    const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [{
        label: 'Ventas (S/)',
        data: ventasMensuales,
        backgroundColor: colores.backgroundColor,
        borderColor: colores.borderColor,
        borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        onClick: (e, activeEls) => {
        if (activeEls.length > 0) {
            const index = activeEls[0].index;
            mostrarTabla(index);
        }
        },
        scales: {
        y: {
            beginAtZero: true
        }
        },
        plugins: {
        legend: { display: false }
        }
    }
    });

    function mostrarTabla(mesIndex) {
    const tabla = document.getElementById('tablaProductos');
    const cuerpo = document.getElementById('tbodyProductos');
    cuerpo.innerHTML = '';

    let productos = [...productosBase];
    while (productos.length < 10) {
        const adicional = productosAdicionales[Math.floor(Math.random() * productosAdicionales.length)];
        productos.push({
        codigo: adicional.codigo,
        nombre: adicional.nombre,
        stock: Math.floor(Math.random() * 2000),
        compra: Math.floor(Math.random() * 3000 + 1000),
        venta: Math.floor(Math.random() * 3000 + 4000),
        aprobacion: Math.floor(Math.random() * 30 + 50)
        });
    }

    productos.forEach((prod, idx) => {
        cuerpo.innerHTML += `
        <tr>
            <td>${idx + 1}</td>
            <td>${prod.codigo}</td>
            <td>${prod.nombre}</td>
            <td>${prod.stock}</td>
            <td>S/ ${prod.compra.toFixed(2)}</td>
            <td>S/ ${prod.venta.toFixed(2)}</td>
            <td>${prod.aprobacion}%</td>
        </tr>
        `;
    });
    tabla.style.display = 'block';
    }

    function dibujarLineaConFlecha() {
    const svg = document.getElementById('lineaTrazo');
    svg.innerHTML = '<defs><marker id="arrowhead" markerWidth="10" markerHeight="7" refX="0" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#0d6efd"/></marker></defs>';

    const puntos = chart.getDatasetMeta(0).data.map(d => d.getCenterPoint());
    const pathData = puntos.map((pt, i) => `${i === 0 ? 'M' : 'L'} ${pt.x} ${pt.y}`).join(' ');

    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", pathData);
    path.setAttribute("class", "arrow-line");
    svg.appendChild(path);
    }
    requestAnimationFrame(dibujarLineaConFlecha);
    setTimeout(dibujarLineaConFlecha, 500);
    window.addEventListener('resize', () => setTimeout(dibujarLineaConFlecha, 500));

});