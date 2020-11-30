<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>
<body>
	<main class="container mt-5">
		<div class="section">
			<h3>Car Dealers Client Service</h3>
		</div>

		<div id="vue-table" class="mb-3">
			<div class="row flex-nowrap justify-content-between align-items-center my-2">
				<div class="col">
					<p>Page {{currentPage}} of {{totalPages}} -- Total items: {{totalElements}}</p>
				</div>
				<div class="col">
					<select v-model="elementsPerPage" class="form-control">
						<option v-for="n in 10" v-bind:value="n*5">{{n*5}} Rows Per Page</option>
					</select>
				</div>
			</div>
			<div class="my-2">
				<div class="form-check form-check-inline" v-for="col in columns">
					<input class="form-check-input" type="checkbox" v-bind:id="col.field" v-bind:value="col.field" v-model="visibleColumns" :checked="col.visible">
					<label class="form-check-label" v-bind:for="col.field">{{col.title}}</label>
				</div>
			</div>
			<table class="table table-bordered">
				<thead class="thead-dark">
					<tr>
						<th
							v-if="visibleColumns.includes(col.field)"
							v-for="col in columns"
							v-on:click="sortTable(col.field)"
							scope="col">
							{{col.title}}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="row in getRows()">
						<td
							v-if="visibleColumns.includes(col.field)"
							v-for="col in columns">
							{{row[col.field]}}
						</td>
					</tr>
				</tbody>
			</table>
			<ul class="pagination justify-content-center my-1">
				<li class="page-item"
					v-show="currentPage > 1"
					v-on:click="changePage(currentPage - 1)">
					<a class="page-link rounded-0 bg-light text-dark" href="#">
						Previous
					</a>
				</li>
				<li class="page-item"
					v-show="currentPage > 1">
					<span class="page-link rounded-0 bg-light text-dark">
						...
					</span>
				</li>
				<li class="page-item"
					v-for="pageNumber in numPages()"
					v-if="Math.abs(pageNumber - currentPage) < 3"
					v-bind:class="[pageNumber == currentPage ? 'active' : '']"
					v-on:click="changePage(pageNumber)">
					<a v-bind:class="[pageNumber == currentPage ? 'page-link rounded-0 bg-dark text-white' : 'page-link rounded-0 bg-light text-dark']" href="#">
						{{pageNumber}}
					</a>
				</li>
				<li class="page-item"
					v-show="currentPage < totalPages">
					<span class="page-link rounded-0 bg-light text-dark">
						...
					</span>
				</li>
				<li class="page-item"
					v-show="currentPage < totalPages"
					v-on:click="changePage(currentPage + 1)">
					<a class="page-link rounded-0 bg-light text-dark" href="#">
						Next
					</a>
				</li>
			</ul>
		</div>
	</main>

	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
	<script>
	if (document.getElementById('vue-table')) {
		new Vue({
			el: '#vue-table',
			data: {
				currentPage: 1,
				elementsPerPage: 5,
				ascending: false,
				sortColumn: '',
				filterBy: '',
				filterByString: '',
				showPrevPageButton: false,
				showNextPageButton: false,
				visibleColumns: [
					// 'id',
					'derivative',
					// 'model',
					// 'transmission',
					// 'fuel_type',
					'price',
					'created_at'
				],
				columnsMapping: [
					{
						title: 'Derivative',
						field: 'derivative'
					},
					{
						title: 'Model',
						field: 'model'
					},
					{
						title: 'Transmission',
						field: 'transmission'
					},
					{
						title: 'Fuel Type',
						field: 'fuel_type'
					},
					{
						title: 'Price',
						field: 'price'
					},
					{
						title: 'Created',
						field: 'created_at'
					}
				],
				rows: [
					{
						'id': '',
						'derivative': '',
						'model': '',
						'transmission': '',
						'fuel_type': '',
						'price': '',
						'created_at': '',
					}
				]
			},
			created: function() {
				this.loadCarsData();
			},
			computed: {
				totalPages: function () {
					return Math.ceil(this.totalElements / this.elementsPerPage);
				},
				totalElements: function () {
					return this.rows.length;
				},
				columns: function () {
					return this.columnsMapping.filter(column => Object.keys(this.rows[0]).includes(column.field)) || [];
				},
				selectedColumns: function () {
					return this.columns
						.filter(column => this.visibleColumns.includes(column.field))
						.map(column => column.field);
				}
			},
			methods: {
				loadCarsData: function() {
					const url = 'http://localhost:5080/api/cars';
					axios.get(url)
						.then(response => {
							this.rows = response.data;
						}).catch( error => {
							console.log(error);
						});
				},
				filterRows: function () {},
				sortTable: function sortTable(col) {
					if (this.sortColumn === col) {
						this.ascending = !this.ascending;
					} else {
						this.ascending = true;
						this.sortColumn = col;
					}

					var ascending = this.ascending;

					this.rows.sort(function (a, b) {
						if (a[col] > b[col]) {
							return ascending ? 1 : -1;
						} else if (a[col] < b[col]) {
							return ascending ? -1 : 1;
						}
						return 0;
					});
				},
				numPages: function () {
					return Math.ceil(this.rows.length / this.elementsPerPage);
				},
				getRows: function () {
					var start = (this.currentPage - 1) * this.elementsPerPage;
					var end = start + this.elementsPerPage;
					return this.rows.slice(start, end) || [];
				},
				changePage: function (page) {
					this.currentPage = page;
				}
			}
		});
	}
	</script>
</body>
</html>
