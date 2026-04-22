<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->foreignId('company_id')->nullable()->after('user_id');
                $t->index('company_id');
            });
        }

        // Settings.user_id was unique (one setting per user). With multi-company we
        // switch the uniqueness to company_id, so drop the user_id unique index.
        Schema::table('settings', function (Blueprint $t) {
            try {
                $t->dropUnique('settings_user_id_unique');
            } catch (\Throwable $e) {
                // older installs may not have the named index; ignore.
            }
        });

        // Cashflows had a (user_id, period) unique. Drop it so each company
        // can have its own monthly period row.
        Schema::table('cashflows', function (Blueprint $t) {
            try {
                $t->dropUnique('cashflows_user_id_period_unique');
            } catch (\Throwable $e) {
                // ignore if absent
            }
        });

        // Backfill: for each user, create a default "My Company" and attach existing rows.
        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            $companyId = DB::table('companies')->insertGetId([
                'user_id'    => $userId,
                'name'       => 'My Company',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
                DB::table($table)->where('user_id', $userId)->update(['company_id' => $companyId]);
            }
        }

        // Enforce one setting per company, one cashflow row per company+period.
        Schema::table('settings', function (Blueprint $t) {
            $t->unique('company_id', 'settings_company_id_unique');
        });
        Schema::table('cashflows', function (Blueprint $t) {
            $t->unique(['company_id', 'period'], 'cashflows_company_id_period_unique');
        });

        // Note: we leave company_id nullable at the schema level to avoid needing
        // doctrine/dbal for a column change on shared-hosting MySQL. Models always
        // populate it on create.
    }

    public function down(): void
    {
        foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->dropIndex([$table . '_company_id_index']);
                $t->dropColumn('company_id');
            });
        }
    }
};
