<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
	private $models = [
		RolesTableSeeder::class,
		UsersTableSeeder::class,
		StationsTableSeeder::class,
		#CustomersTableSeeder::class,
		#Items
		#Notes
		#OrdersTableSeeder::class,
		#ProductsTableSeeder::class,
		TemplatesTableSeeder::class,
		BatchRoutesTableSeeder::class,
		MasterCategoriesTableSeeder::class,
		#CategoriesTableSeeder::class,
		#SubCategoriesTableSeeder::class,
		StoresTableSeeder::class,
		StatusesTableSeeder::class,
		SettingsTableSeeder::class,
		RulesTableSeeder::class,
		DepartmentsTableSeeder::class,
	];

	public function run ()
	{
		Model::unguard();
		foreach ( $this->models as $table => $model ) {
			$this->call($model);
		}
		Model::reguard();
	}
}
