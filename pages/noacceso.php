<!--Contenido-->
	<!-- Content Wrapper. Contains page content -->
	<style>
		* {position: relative;margin: 0;padding: 0;box-sizing: border-box;font-family: 'Lato', sans-serif;}body {height: 100vh;display: flex;flex-direction: column;justify-content: center;align-items: center;background: linear-gradient(to bottom right, #EEE, #AAA);}h1 {margin: 40px 0 20px;}.lock {border-radius: 5px;width: 43px;height: 30px;background-color: #333;animation: dip 1s;animation-delay: (2s - .5);&::before,&::after {content: '';position: absolute;border-left: 7px solid #333;height: 35px;width: 30px;left: calc(50% - 15px);}&::before {top: -20px;border: 8px solid #333;border-bottom-color: transparent;border-radius: 15px 15px 0 0;height: 30px;animation: lock 2s, spin 2s;}&::after {top: -10px; border-right: 5px solid transparent;animation: spin 2s;}}@keyframes lock {0% {top: -35px;}65% {top: -35px;}100% {top: -20px;}}@keyframes spin {0% {transform: scaleX(-1);left: calc(50% - 30px);}65% {transform: scaleX(1);left: calc(50% - 15px);}}@keyframes dip {0% {transform: translateY(0px);}50% {transform: translateY(10px);}100% {transform: translateY(0px);}}
	</style>
	<div class="container-fluid" >        
		<!-- Main content -->
		<section class="content mt-10">
			<div class="row pt-4">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header with-border">
							<div class="lock"></div>
							<h1 class="box-title">No tienes permiso para acceder a esta página</h1><br/>
							<p>Por favor contacta al administrador si consideras que esto es un error.</p>
						</div>
						<script type="text/javascript">
							setTimeout(function(){ location.reload(); }, 60000);
						</script>
						<!-- /.box-header -->
						<!-- centro -->
						<!--Fin centro -->
					</div><!-- /.box -->
				</div><!-- /.col -->
			</div><!-- /.row -->
		</section><!-- /.content -->
	</div><!-- /.container-fluid -->
<!--Fin-Contenido-->