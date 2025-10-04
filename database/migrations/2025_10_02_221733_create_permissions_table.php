<?php

use App\Models\Permission;
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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('guard_name');
            $table->timestamps();
        });

        collect([
            // Users
            ['name' => 'View Any Users', 'slug' => 'users-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Users', 'slug' => 'users-view', 'guard_name' => 'web'],
            ['name' => 'Create Users', 'slug' => 'users-create', 'guard_name' => 'web'],
            ['name' => 'Update Users', 'slug' => 'users-update', 'guard_name' => 'web'],
            ['name' => 'Delete Users', 'slug' => 'users-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Users', 'slug' => 'users-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Users', 'slug' => 'users-forceDelete', 'guard_name' => 'web'],

            // Cliente
            ['name' => 'View Any Cliente', 'slug' => 'client-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Cliente', 'slug' => 'client-view', 'guard_name' => 'web'],
            ['name' => 'Create Cliente', 'slug' => 'client-create', 'guard_name' => 'web'],
            ['name' => 'Update Cliente', 'slug' => 'client-update', 'guard_name' => 'web'],
            ['name' => 'Delete Cliente', 'slug' => 'client-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Cliente', 'slug' => 'client-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Cliente', 'slug' => 'client-forceDelete', 'guard_name' => 'web'],

            // Host
            ['name' => 'View Any Host', 'slug' => 'host-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Host', 'slug' => 'host-view', 'guard_name' => 'web'],
            ['name' => 'Create Host', 'slug' => 'host-create', 'guard_name' => 'web'],
            ['name' => 'Update Host', 'slug' => 'host-update', 'guard_name' => 'web'],
            ['name' => 'Delete Host', 'slug' => 'host-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Host', 'slug' => 'host-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Host', 'slug' => 'host-forceDelete', 'guard_name' => 'web'],

            // Version
            ['name' => 'View Any Version', 'slug' => 'version-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Version', 'slug' => 'version-view', 'guard_name' => 'web'],
            ['name' => 'Create Version', 'slug' => 'version-create', 'guard_name' => 'web'],
            ['name' => 'Update Version', 'slug' => 'version-update', 'guard_name' => 'web'],
            ['name' => 'Delete Version', 'slug' => 'version-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Version', 'slug' => 'version-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Version', 'slug' => 'version-forceDelete', 'guard_name' => 'web'],

            // Deploy
            ['name' => 'View Any Deploy', 'slug' => 'deploy-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Deploy', 'slug' => 'deploy-view', 'guard_name' => 'web'],
            ['name' => 'Create Deploy', 'slug' => 'deploy-create', 'guard_name' => 'web'],
            ['name' => 'Update Deploy', 'slug' => 'deploy-update', 'guard_name' => 'web'],
            ['name' => 'Delete Deploy', 'slug' => 'deploy-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Deploy', 'slug' => 'deploy-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Deploy', 'slug' => 'deploy-forceDelete', 'guard_name' => 'web'],
            ['name' => 'Approve Deploy', 'slug' => 'deploy-approve', 'guard_name' => 'web'],
            ['name' => 'Reject Deploy', 'slug' => 'deploy-reject', 'guard_name' => 'web'],

            // Client Config
            ['name' => 'View Any Client Config', 'slug' => 'client-config-viewAny', 'guard_name' => 'web'],
            ['name' => 'View Client Config', 'slug' => 'client-config-view', 'guard_name' => 'web'],
            ['name' => 'Create Client Config', 'slug' => 'client-config-create', 'guard_name' => 'web'],
            ['name' => 'Update Client Config', 'slug' => 'client-config-update', 'guard_name' => 'web'],
            ['name' => 'Delete Client Config', 'slug' => 'client-config-delete', 'guard_name' => 'web'],
            ['name' => 'Restore Client Config', 'slug' => 'client-config-restore', 'guard_name' => 'web'],
            ['name' => 'Force Delete Client Config', 'slug' => 'client-config-forceDelete', 'guard_name' => 'web'],
        ])->map(function ($data) {
            Permission::create(
                $data
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
