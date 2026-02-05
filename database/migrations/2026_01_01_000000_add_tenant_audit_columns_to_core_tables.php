<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
                $table->index(['tenant_id', 'email']);
            }

            if (! Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('roles', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('roles', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'name']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('permissions', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('permissions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('permissions', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'name']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            if (! Schema::hasColumn('role_user', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('role_user', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('role_user', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'model_id', 'model_type'], 'role_user_tenant_model_idx');
        });

        Schema::table('permission_role', function (Blueprint $table) {
            if (! Schema::hasColumn('permission_role', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('permission_role', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('permission_role', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'role_id', 'permission_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('clients', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('clients', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'document']);
        });

        Schema::table('pets', function (Blueprint $table) {
            if (! Schema::hasColumn('pets', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('pets', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('pets', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['tenant_id', 'client_id']);
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'client_id']);
            $table->dropIndex(['tenant_id', 'name']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'phone']);
            $table->dropIndex(['tenant_id', 'document']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'role_id', 'permission_id']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropIndex('role_user_tenant_model_idx');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'name']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'name']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'email']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropSoftDeletes();
        });
    }
};
