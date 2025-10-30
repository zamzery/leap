<?php 
if (strlen(session_id()) < 1)
	session_start();
?>
<!DOCTYPE html>
<html lang="es_ES">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="Sistema Leap Works" content="">
	<meta name="Leap Works" content="Sistema Leap Works">
	<link rel="icon" type="image/x-icon" href="../public/images/favicon.ico" />
	<meta name="referrer" content="origin" />

	<title>Leap Works</title>
	<!-- Fuentes tipográficas del sistema-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;500;600;700;900&display=swap"
		rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
		integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Custom styles for this template-->
	<link href="../public/css/styles.css" rel="stylesheet" type="text/css" />
	<link href="../public/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
	<link href="../public/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
	<link href="../public/css/tempusdominus.css" rel="stylesheet" type="text/css">
	<link href="../public/css/featherlight.min.css" rel="stylesheet" type="text/css">
	<link href="../public/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
	<link href="../public/vendor/datatables/reorder.css" rel="stylesheet">
</head>

<style>
.logo_navbar {
	width: auto;
	height: 45px;
}
</style>

<body class="nav-fixed">
	<!-- Navbar -->
	<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white"
		id="sidenavAccordion">
		<button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0 mr-4"
			id="sidebarToggle">
			<i class="fa-solid fa-bars"></i>
		</button>
		<button onclick="location.reload()" type="button" class="ml-2 btn btn-link"><img src="../public/images/Logo.jpg"
				class='logo_navbar' alt="Logo Principal" /></button>
		<ul class="navbar-nav align-items-center ms-auto">
			<ul class='navbar-nav bg-gradient-primary sidebar sidebar-dark accordion'>
				<!-- <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
					<a href="../pages/todos.php">
						<button class="btn btn-icon btn-transparent-dark" id="todo-link"><i
								class="fa-solid fa-list-check"></i></button>
					</a>
				</li> -->
				<li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
					<button type="button" class="btn btn-icon btn-transparent-dark dropdown-toggle"
						id="navbarDropdownUserImage" data-bs-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false"><img class="img-fluid"
							src="<?php echo (empty($_SESSION['avatar']))? '../public/files/avatars/default.jpg' : '../public/files/avatars/'.$_SESSION['avatar'];  ?>"
							alt="" /></button>
					<div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
						aria-labelledby="navbarDropdownUserImage">
						<h6 class="dropdown-header d-flex align-items-center">
							<img class="dropdown-user-img"
								src="../public/files/avatars/<?php echo (empty($_SESSION['avatar']))? 'default.jpg' :  $_SESSION['avatar']; ?>"
								alt="" />
							<div class="dropdown-user-details">
								<div class="dropdown-user-details-name">
									<?php echo (empty($_SESSION['nombre']))? '' :  $_SESSION['nombre']; ?></div>
								<div class="dropdown-user-details-email">
									<?php echo (empty($_SESSION['email']))? '' :  $_SESSION['email']; ?></div>
							</div>
						</h6>
						<div class="dropdown-divider"></div>

						<button class="btn btn-link dropdown-item">
							<div class="dropdown-item-icon">
								<a class="nav-link" href="../pages/usuario_mismo.php">
									<div class="nav-link-icon text-primary">
										<i class="fa-solid fa-user-gear fa-lg"></i> <span class="text-primary">
											Configurar Cuenta</span>
									</div>
								</a>
							</div>
						</button>
						<button class="btn btn-link dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
							<div class="dropdown-item-icon">
								<i data-feather="log-out" class="fa-solid fa-right-from-bracket"
									style="color:#ff0000;"></i> <span style="color:#ff0000;">Cerrar Sesión </span>
							</div>
						</button>
					</div>
				</li>
			</ul>
		</ul>
	</nav>
	</div>
	<div id="layoutSidenav_content">
		<main>
			<?php
	require '../components/sidebar.php';
	?>