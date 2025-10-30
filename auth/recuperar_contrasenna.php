<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<title>Leap Works - Recuperar Contraseña</title>
	<link href="../public/css/styles.css" rel="stylesheet" />
	<link rel="icon" type="image/x-icon" href="../public/images/favicon.ico" />
	<script data-search-pseudo-elements defer
		src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous">
	</script>
	<title>Leap Works Sistema</title>
</head>

<body class="body-animation">
	<style>
	.logo {
		padding: 0 5px 5px 5px;
		height: auto;
		width: 180px;
	}

	#titulo {
		font-size: 1.5em;
		font-weight: bold;
		color: #282828;
		font-family: 'Source Sans 3', sans-serif;
		padding-bottom: 25px;
		margin: 50px 0 25px 0;
	}

	@import url('https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;500;600;700;900&display=swap');

	* {
		font-family: 'Source Sans 3', sans-serif;
		margin: 0;
	}

	.body-animation {
		background: linear-gradient(-45deg, #b45b1f, #d49c21);
		background-size: 400% 400%;
		animation: gradient 15s ease infinite;
		height: 100vh;
	}

	@keyframes gradient {
		0% {
			background-position: 0% 50%;
		}

		50% {
			background-position: 100% 50%;
		}

		100% {
			background-position: 0% 50%;
		}
	}
	</style>
	<div id="layoutAuthentication">
		<div id="layoutAuthentication_content">
			<main>
				<div class="container-xl px-4">
					<div class="row justify-content-center">
						<div class="col-lg-5">
							<div class="card shadow-lg border-0 rounded-lg mt-5">
								<div class="card-header justify-content-center text-center">
									<span id="titulo">Recuperar Contraseña</span><br></br>
									<img src="../public/images/Logo.jpg" class='logo' alt="Logo Principal" />
								</div>
								<div class="card-body">
									<form id="recuperaContrasenna">
										<div class="mb-3">
											<label class="mb-1" htmlFor="inputPassword">Escribe tu nueva
												contraseña</label>
											<input type="hidden" id="emailRecuperacion"
												value="<?php echo (empty($_GET['email']))? '' : $_GET['email']; ?>" />
											<input type="hidden" id="tokenRecuperacion"
												value="<?php echo (empty($_GET['tokenkey']))? '' : $_GET['tokenkey']; ?>" />
											<input class="form-control" autocomplete="new-password" value=""
												id="contrasennaRecuperacion" type="password"
												placeholder="Escribe tu nueva contraseña" name="password" />
										</div>
										<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
											<button class="btn btn-dark" type="submit" value="Cambiar">Cambiar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<div id="layoutAuthentication_footer">
			<footer class="footer-admin mt-auto footer-dark">
				<div class="container-xl px-4 text-center">
					<div class="row">
						<div class="col-md-12 small">©2023 Leap Works</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"
		integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
	<script src="../public/scripts/bootstrap5.js" type="text/javascript"></script>
	<script src="../public/scripts/bootbox.min.js" type="text/javascript" charset="UTF-8"></script>
	<script src="./auth.js"></script>
</body>

</html>