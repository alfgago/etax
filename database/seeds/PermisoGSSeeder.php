<?php

use Illuminate\Database\Seeder;

class PermisoGSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permiso_item = App\Permission::updateOrCreate(
			[
				'name'=>'GoSocket'
			],
			[
				'guard_name'=>'web'
			]
		);
    }
}
