<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
        
        
        Schema::create('company_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('permission');
            $table->timestamps();
			$table->softDeletes();
        });
		
		$roles_arr = array('Super Admin','Admin');
		
		foreach($roles_arr as $key => $arr){
			$role = Spatie\Permission\Models\Role::create([
            'name' => $arr            
			]);
		}
		
		$permissions_arr = array('role-list','role-create','role-edit','role-delete','product-list','product-create','product-edit','product-delete','user-list','user-create','user-edit','user-delete','permission-list','permission-create','permission-edit','permission-delete','team-list','team-create','team-edit','team-delete','plan-create','plan-edit','plan-delete','plan-list','plan-cancel');
		
		$admin_permissions_arr = array('user-list','team-list','team-create','team-edit','team-delete','plan-list','plan-cancel');
		
		$super_admin = Spatie\Permission\Models\Role::where('name','Super Admin')->first();
		$admin = Spatie\Permission\Models\Role::where('name','Admin')->first();
		
		foreach($permissions_arr as $key => $arr){
			$permission = Spatie\Permission\Models\Permission::create([
            'name' => $arr            
			]);
		}
		
		$super_admin->syncPermissions($permissions_arr);
		$admin->syncPermissions($admin_permissions_arr);
		
		$user = App\User::find(1);		
		$user->assignRole(array('Admin'));
					
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
        Schema::drop($tableNames['company_permissions']);
    }
}
