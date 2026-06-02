var chartTopProductos = null;
var chartComparativo = null;
var chartOrigen = null;
var tablaComparativo = null;

var coloresReporte = [
	"#0d6efd", "#198754", "#dc3545", "#fd7e14", "#6f42c1",
	"#20c997", "#0dcaf0", "#6c757d", "#d63384", "#ffc107"
];

function init() {
	var hoy = new Date();
	var inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

	$("#fechaInicio").val(formatoFecha(inicio));
	$("#fechaFin").val(formatoFecha(hoy));

	$.post("../ajax/reporte_producto.php?op=select_productos", function (r) {
		$("#productos").html(r);
		$("#productos").selectpicker("refresh");
		cargarReporte();
	});

	$("#formFiltros").on("submit", function (e) {
		e.preventDefault();
		cargarReporte();
	});

	$("#btnLimpiarProductos").on("click", function () {
		$("#productos").val([]);
		$("#productos").selectpicker("refresh");
		cargarReporte();
	});

	tablaComparativo = $("#tablaComparativo").DataTable({
		dom: "f<'row'<'col-sm-2'B><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",
		buttons: [
			{extend: "excelHtml5", title: "Reporte de Productos", className: "btn btn-sm btn-primary"},
			{extend: "pdf", title: "Reporte de Productos", className: "btn btn-sm btn-primary"}
		],
		"columnDefs": [
			{"className": "text-right", "targets": [2, 3]},
		],
		"iDisplayLength": 25,
		"order": [[0, "asc"], [1, "asc"]]
	});
}

function formatoFecha(fecha) {
	var mes = String(fecha.getMonth() + 1).padStart(2, "0");
	var dia = String(fecha.getDate()).padStart(2, "0");
	return fecha.getFullYear() + "-" + mes + "-" + dia;
}

function formatoNumero(valor) {
	return new Intl.NumberFormat("es-MX", {
		minimumFractionDigits: 0,
		maximumFractionDigits: 2
	}).format(valor || 0);
}

function formatoMoneda(valor) {
	return new Intl.NumberFormat("es-MX", {
		style: "currency",
		currency: "MXN"
	}).format(valor || 0);
}

function cargarReporte() {
	var filtros = {
		fechaInicio: $("#fechaInicio").val(),
		fechaFin: $("#fechaFin").val(),
		productos: $("#productos").val() || []
	};

	$.ajax({
		url: "../ajax/reporte_producto.php?op=datos",
		type: "POST",
		dataType: "json",
		data: filtros,
		success: function (data) {
			actualizarKpis(data.resumen);
			renderTopProductos(data.top);
			renderOrigen(data.origen);
			renderComparativo(data.comparativo, filtros.productos.length);
			renderTabla(data.comparativo);
		},
		error: function (e) {
			console.log(e.responseText);
			bootbox.alert("No se pudo cargar el reporte de productos.");
		}
	});
}

function actualizarKpis(resumen) {
	$("#kpiUnidades").html(formatoNumero(resumen.unidades));
	$("#kpiImporte").html(formatoMoneda(resumen.importe));
	$("#kpiProductos").html(formatoNumero(resumen.productos));
	$("#kpiDias").html(formatoNumero(resumen.dias));
}

function destruirGrafica(grafica) {
	if (grafica) {
		grafica.destroy();
	}
}

function recortarEtiqueta(texto) {
	if (!texto) {
		return "";
	}
	return texto.length > 34 ? texto.substring(0, 31) + "..." : texto;
}

function renderTopProductos(top) {
	destruirGrafica(chartTopProductos);
	chartTopProductos = null;

	var hayDatos = top && top.length > 0 && typeof Chart !== "undefined";
	$("#chartTopProductos").toggleClass("d-none", !hayDatos);
	$("#emptyTopProductos").toggleClass("d-none", hayDatos);
	if (!hayDatos) {
		return;
	}

	chartTopProductos = new Chart(document.getElementById("chartTopProductos"), {
		type: "bar",
		data: {
			labels: top.map(function (item) { return recortarEtiqueta(item.nombreProducto); }),
			datasets: [{
				label: "Unidades",
				data: top.map(function (item) { return item.unidades; }),
				backgroundColor: "#0d6efd"
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			indexAxis: "y",
			plugins: {
				legend: {display: false},
				tooltip: {
					callbacks: {
						afterLabel: function (context) {
							return "Importe: " + formatoMoneda(top[context.dataIndex].importe);
						}
					}
				}
			},
			scales: {
				x: {beginAtZero: true}
			}
		}
	});
}

function renderOrigen(origen) {
	destruirGrafica(chartOrigen);
	chartOrigen = null;

	var hayDatos = origen && origen.length > 0 && typeof Chart !== "undefined";
	$("#chartOrigen").toggleClass("d-none", !hayDatos);
	$("#emptyOrigen").toggleClass("d-none", hayDatos);
	if (!hayDatos) {
		return;
	}

	chartOrigen = new Chart(document.getElementById("chartOrigen"), {
		type: "doughnut",
		data: {
			labels: origen.map(function (item) { return item.origen; }),
			datasets: [{
				data: origen.map(function (item) { return item.importe; }),
				backgroundColor: coloresReporte
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				tooltip: {
					callbacks: {
						label: function (context) {
							return context.label + ": " + formatoMoneda(context.parsed);
						}
					}
				}
			}
		}
	});
}

function renderComparativo(comparativo, productosSeleccionados) {
	destruirGrafica(chartComparativo);
	chartComparativo = null;

	var hayDatos = comparativo && comparativo.length > 0 && productosSeleccionados > 0 && typeof Chart !== "undefined";
	$("#chartComparativo").toggleClass("d-none", !hayDatos);
	$("#emptyComparativo").toggleClass("d-none", hayDatos);
	if (!hayDatos) {
		return;
	}

	var periodos = [];
	var productos = {};
	comparativo.forEach(function (item) {
		if (periodos.indexOf(item.periodo) === -1) {
			periodos.push(item.periodo);
		}
		productos[item.productoID] = item.nombreProducto;
	});

	var datasets = Object.keys(productos).map(function (productoID, index) {
		return {
			label: recortarEtiqueta(productos[productoID]),
			data: periodos.map(function (periodo) {
				var encontrado = comparativo.find(function (item) {
					return String(item.productoID) === String(productoID) && item.periodo === periodo;
				});
				return encontrado ? encontrado.unidades : 0;
			}),
			borderColor: coloresReporte[index % coloresReporte.length],
			backgroundColor: coloresReporte[index % coloresReporte.length],
			tension: .25
		};
	});

	chartComparativo = new Chart(document.getElementById("chartComparativo"), {
		type: "line",
		data: {
			labels: periodos,
			datasets: datasets
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				tooltip: {
					callbacks: {
						label: function (context) {
							return context.dataset.label + ": " + formatoNumero(context.parsed.y) + " unidades";
						}
					}
				}
			},
			scales: {
				y: {beginAtZero: true}
			}
		}
	});
}

function renderTabla(comparativo) {
	tablaComparativo.clear();
	comparativo.forEach(function (item) {
		tablaComparativo.row.add([
			item.periodo,
			item.nombreProducto,
			formatoNumero(item.unidades),
			formatoMoneda(item.importe)
		]);
	});
	tablaComparativo.draw();
}

init();
