<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('permission_id')->constrained('permissions');
            $table->timestamps();
        });

        \App\Models\RolePermission::insert(
            \App\Models\Permission::all()->map(function ($permission) {
                $role = \App\Models\Role::where('slug', 'admin')->first();

                return [
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

        // developer pode somente criar as versoes e ver os deploys
        \App\Models\RolePermission::insert(
            \App\Models\Permission::whereIn('slug', [
                'view-version',
                'create-version',
                'edit-version',
                'destroy-version',
                'view-deploy',
                'create-deploy',
                'edit-deploy',
                'destroy-deploy',
            ])->get()->map(function ($permission) {
                $role = \App\Models\Role::where('slug', 'developer')->first();

                return [
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

        \App\Models\RolePermission::insert(
            \App\Models\Permission::whereIn('slug', [
                'view-deploy',
                'approve-deploy',
                'reject-deploy',
            ])->get()->map(function ($permission) {
                $role = \App\Models\Role::where('slug', 'client')->first();

                return [
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
