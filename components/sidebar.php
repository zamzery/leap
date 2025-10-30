<!-- Page Wrapper -->
<div id="layoutSidenav">
	<!-- Sidebar -->
	<div id="layoutSidenav_nav">
		<nav class="sidenav shadow-right sidenav-dark">
			<div class="sidenav-menu">
				<div class="nav accordion" id="accordionSidenav">
					<!--  Sidenav Menu Heading (Cuenta)
					 * * Note: * * Visible only on and above the sm breakpoint -->
					<div class="sidenav-menu-heading d-sm-none">Cuenta</div>
					<!--  Sidenav Link (Alertas)
					 * * Note: * * Visible only on and above the sm breakpoint -->
					<a class="nav-link d-sm-none" href="alertas">
						<div class="nav-link-icon"><i class="fa-solid fa-bell"></i></div> Alertas
					</a>

					<div class="sidenav-menu-heading">Principal</div>
					<?php 
				if ($_SESSION['escritoriover']==1){
					echo '<a class="nav-link" href="../pages/escritorio.php">
						<div class="nav-link-icon"><i class="fa-solid fa-chart-simple"></i></div>
						Escritorio
					</a>';
				}

				if ($_SESSION['pedidosver']==1){
					echo '<a class="nav-link" href="../pages/pedidos.php">
						<div class="nav-link-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
						Pedidos
					</a>';
				}

				if ($_SESSION['pagosver']==1 || $_SESSION['gastosver']==1){
					echo '<div class="sidenav-menu-heading">Pagos Recibidos y Gastos</div>';
				}

				if ($_SESSION['pagosver']==1){
					echo '<a class="nav-link" href="../pages/pagos_recibidos.php">
						<div class="nav-link-icon"><i class="fa-solid fa-dollar-sign"></i></div>
						Pagos Recibidos
					</a>';
				}

				if ($_SESSION['gastosver']==1){
					echo '<a class="nav-link" href="../pages/pagos_efectuados.php">
						<div class="nav-link-icon"><i class="fa-solid fa-wallet"></i></div>
						Pagos Efectuados
					</a>';
				}

				if ($_SESSION['facturasver']==1 || $_SESSION['adminfacturaver']==1){
					echo '<div class="sidenav-menu-heading">Facturación</div>';
				}
			
				if ($_SESSION['facturasver']==1){
					echo '<a class="nav-link" href="../pages/facturas.php"><div class="nav-link-icon"><i class="fa-solid fa-file-invoice"></i></div> Facturas</a>';
				}
			
				if ($_SESSION['complementosver']==1){
					echo '<a class="nav-link" href="../pages/complementos.php"><div class="nav-link-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div> Complementos</a>';
				}
			
					if ($_SESSION['adminfacturaver']==1){
						echo '<button class="btn btn-link nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuthSocial" aria-expanded="false" aria-controls="pagesCollapseAuthSocial">
								<div class="nav-link-icon"><i class="fa-solid fa-gear"></i></div>
								Admin. Facturas
								<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
							</button>
							<div class="collapse" id="pagesCollapseAuthSocial" data-bs-parent="#accordionSidenavPagesAuth">
								<nav class="sidenav-menu-nested nav">';
					}

						if ($_SESSION['adminfacturaver']==1 && $_SESSION['clavefacturacionver']==1){
							echo '<a class="nav-link" href="../pages/clavesfacturacion.php"><div class="nav-link-icon"><i class="fa-solid fa-tags"></i></div> Clave Facturación</a>';
						}

						if ($_SESSION['adminfacturaver']==1 && $_SESSION['unidadmedidaver']==1){
							echo '<a class="nav-link" href="../pages/unidadesmedida.php"><div class="nav-link-icon"><i class="fa-solid fa-ruler"></i></div> Unidades Medida</a>';
						}

						if ($_SESSION['adminfacturaver']==1 && $_SESSION['metodopagover']==1){
							echo '<a class="nav-link" href="../pages/metodospago.php"><div class="nav-link-icon"><i class="fa-solid fa-money-check-dollar"></i></div> Método Pago</a>';
						}

					if ($_SESSION['adminfacturaver']==1){
						echo '</nav>
							</div>';
					}

				if ($_SESSION['administrarver']==1){
					echo '<div class="sidenav-menu-heading">Administración del sistema</div>
					<button class="btn btn-link nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#administrarCollapse" aria-expanded="false" aria-controls="administrarCollapse">
						<div class="nav-link-icon"><i class="fa-solid fa-gear"></i></div>
						Administrar
						<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
					</button>
					<div class="collapse" id="administrarCollapse" data-bs-parent="#accordionSidenavAdministrar">
						<nav class="sidenav-menu-nested nav">';
				}

					if ($_SESSION['administrarver']==1 && $_SESSION['productosver']==1){
							echo '<a class="nav-link" href="../pages/productos.php"><div class="nav-link-icon"><i class="fa-solid fa-basket-shopping"></i></div> Productos</a>';
						}

					if ($_SESSION['administrarver']==1 && $_SESSION['clientesver']==1){
						echo '<a class="nav-link" href="../pages/clientes.php">
							<div class="nav-link-icon"><i class="fa-solid fa-user-plus"></i></div>
							Clientes
						</a>';
					}

					if ($_SESSION['administrarver']==1 && $_SESSION['cargosver']==1){
						echo '<a class="nav-link" href="../pages/cargos.php">
							<div class="nav-link-icon"><i class="fa-solid fa-id-badge"></i></div>
							Cargo Usuario
						</a>';
					}

					if ($_SESSION['administrarver']==1 && $_SESSION['usuariosver']==1){
						echo '<a class="nav-link" href="../pages/users.php">
							<div class="nav-link-icon"><i class="fa-solid fa-user"></i></div>
							Usuarios
						</a>';
					}

				if ($_SESSION['administrarver']==1){
						echo '</nav>
					</div>';
				}

				if ($_SESSION['reportesver']==1){
					echo '<div class="sidenav-menu-heading">Reportes</div>
					<button class="btn btn-link nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#reportesCollapse" aria-expanded="false" aria-controls="reportesCollapse">
						<div class="nav-link-icon"><i class="fa-solid fa-chart-line"></i></div>
						Reportes
						<div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
					</button>
					<div class="collapse" id="reportesCollapse" data-bs-parent="#accordionSidenavReportes">
						<nav class="sidenav-menu-nested nav">';
				}

					if ($_SESSION['reportesver']==1 && $_SESSION['reporteproductosver']==1){
						echo '<a class="nav-link" href="../pages/reportes_productos.php">
							<div class="nav-link-icon"><i class="fa-solid fa-chart-pie"></i></div>
							Reporte de Productos
						</a>';
					}

					if ($_SESSION['reportesver']==1 && $_SESSION['balancever']==1){
						echo '<a class="nav-link" href="../pages/balances.php">
							<div class="nav-link-icon"><i class="fa-solid fa-scale-balanced"></i></div>
							Balance
						</a>';
					}

				if ($_SESSION['reportesver']==1){
						echo '</nav>
					</div>';
				}
			?>
				</div>
			</div>
			<div class="sidenav-footer">
				<div class="sidenav-footer-content">
					<div class="sidenav-footer-subtitle">Conectado como:</div>
					<div class="sidenav-footer-title">
						<?php echo (empty($_SESSION['nombre']))? '' : $_SESSION['nombre']; ?></div>
				</div>
			</div>
		</nav>
	</div>
	<div id="layoutSidenav_content">
		<main>
			<!-- End of Sidebar -->

			<!-- Main Content -->