<?php
$service_url = '';
if (isset($_ENV["API_URL"]) && !empty($_ENV["API_URL"])) {
	$service_url = $_ENV["API_URL"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Car Dealers</title>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>
<body>
	<main class="container mt-5">
		<div class="section text-center">
			<h3>Car Dealers Admin Service</h3>
		</div>

		<div class="section" style="display: block; width: 600px; margin: 0 auto;">
			<form method="POST" id="run-db-migration">
				<div class="my-4">
					<h4>Run DB Migrations</h4>
					<p>Runs the database migration. Must be triggered when using with a brand new database or adding new migrations to the API service.</p>
				</div>

				<input class="btn btn-dark mb-5" type="submit" data-label="Run Migrations" data-label-processing="Processing..." value="Run Migrations">
			</form>
		</div>

		<div class="section" style="display: block; width: 600px; margin: 0 auto;">
			<form method="POST" id="add-car-offer">
				<div class="my-4">
					<h4>Add a new offer</h4>

					<div class="form-group border border-light border-1 rounded p-2 mb-3">
						<div class="row">
							<label class="col-sm-3 col-form-label" for="derivative">Derivative</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="derivative" name="derivative" value="" placeholder="A 180 d SE 5dr Auto Hatchback [2020.5]" required>
							</div>
						</div>
						<div class="row mt-2">
							<label class="col-sm-3 col-form-label" for="model">Model</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="model" name="model" value="" placeholder="A class" required>
							</div>
						</div>
						<div class="row mt-2">
							<label class="col-sm-3 col-form-label" for="transmission">Transmission</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="transmission" name="transmission" value="" placeholder="manual or automatic" required>
							</div>
						</div>
						<div class="row mt-2">
							<label class="col-sm-3 col-form-label" for="fuel_type">Fuel type</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="fuel_type" name="fuel_type" value="" placeholder="gasoline or diesel" required>
							</div>
						</div>
						<div class="row mt-2">
							<label class="col-sm-3 col-form-label" for="price">Price</label>
							<div class="col-sm-9">
								<input class="form-control" type="text" id="price" name="price" value="" placeholder="3500" required>
							</div>
						</div>
					</div>
				</div>

				<input class="btn btn-dark mb-5" type="submit" data-label="Add Car Offer" data-label-processing="Processing..." value="Add Car Offer">
			</form>
		</div>
	</main>

	<script
		src="https://code.jquery.com/jquery-3.5.1.min.js"
		integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
		crossorigin="anonymous"></script>
	<script>
	jQuery(document).ready(() => {
		const $dbMigrationsForm = jQuery('form#run-db-migration');
		const $dbMigrationsSubmitBtn = jQuery('.btn', $dbMigrationsForm);
		let isDbMigrationsFormSubmitting = false;
		$dbMigrationsForm.on('submit', event => {
			event.preventDefault();

			if (isDbMigrationsFormSubmitting ) return;
			isDbMigrationsFormSubmitting = true;

			const agreedToRunDbMigrations = confirm('Are you sure you want to run the database migrations `php artisan migrate`?');
			if (!agreedToRunDbMigrations) {
				isDbMigrationsFormSubmitting = false;
				return;
			}

			$dbMigrationsSubmitBtn.text($dbMigrationsSubmitBtn.data('label-processing'));

			jQuery.ajax({
				type: "POST",
				dataType: "JSON",
				url: '<?php echo $service_url ?>/api/db/migrate',
				data: $dbMigrationsForm.serializeArray(),
				success: function(data){
					alert('Success!');

					isDbMigrationsFormSubmitting = false;
					$dbMigrationsSubmitBtn.text($dbMigrationsSubmitBtn.data('label'));
				},
				error: function() {
					alert('Something went wrong. Please try again later.');
					isDbMigrationsFormSubmitting = false;
				}
			});
		});

		const $carsOffersForm = jQuery('form#add-car-offer');
		const $carsOffersSubmitBtn = jQuery('.btn', $carsOffersForm);
		let isCarsOffersFormSubmitting = false;
		$carsOffersForm.on('submit', event => {
			event.preventDefault();

			if (isCarsOffersFormSubmitting ) return;
			isCarsOffersFormSubmitting = true;

			$carsOffersSubmitBtn.text($carsOffersSubmitBtn.data('label-processing'));

			jQuery.ajax({
				type: "POST",
				dataType: "JSON",
				url: '<?php echo $service_url ?>/api/car',
				data: $carsOffersForm.serializeArray(),
				success: function(data){
					alert('Success!');

					$carsOffersForm.trigger("reset");

					isCarsOffersFormSubmitting = false;
					$carsOffersSubmitBtn.text($carsOffersSubmitBtn.data('label'));
				},
				error: function() {
					alert('Something went wrong. Please try again later.');
					isCarsOffersFormSubmitting = false;
				}
			});
		});
	});
	</script>
</body>
</html>
