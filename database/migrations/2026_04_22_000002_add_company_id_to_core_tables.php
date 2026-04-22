<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add company_id to each child table (idempotent so a partially-run
        //    migration can be safely re-applied on shared hosting).
        foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
            if (!Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('company_id')->nullable()->after('user_id');
                    $t->index('company_id');
                });
            }
        }

        // 2) Settings.user_id was unique. On MySQL a foreign key uses that index,
        //    so we must add a plain index first, then drop the unique. On SQLite
        //    (no FK name resolution issue) we just drop the unique directly.
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $this->ensureIndex('settings', 'user_id', 'settings_user_id_index');
            $this->dropIndexIfExists('settings', 'settings_user_id_unique');

            $this->ensureIndex('cashflows', ['user_id', 'period'], 'cashflows_user_id_period_index');
            $this->dropIndexIfExists('cashflows', 'cashflows_user_id_period_unique');
        } else {
            $this->dropIndexIfExists('settings', 'settings_user_id_unique');
            $this->dropIndexIfExists('cashflows', 'cashflows_user_id_period_unique');
        }

        // 3) Backfill: for each user, ensure a default "My Company" exists and
        //    attach any rows that still have NULL company_id.
        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            $companyId = DB::table('companies')->where('user_id', $userId)->value('id');
            if (!$companyId) {
                $companyId = DB::table('companies')->insertGetId([
                    'user_id'    => $userId,
                    'name'       => 'My Company',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
                DB::table($table)
                    ->where('user_id', $userId)
                    ->whereNull('company_id')
                    ->update(['company_id' => $companyId]);
            }
        }

        // 4) Add new uniqueness on company scope (skip silently if already there).
        if (!$this->indexExists('settings', 'settings_company_id_unique')) {
            Schema::table('settings', function (Blueprint $t) {
                $t->unique('company_id', 'settings_company_id_unique');
            });
        }
        if (!$this->indexExists('cashflows', 'cashflows_company_id_period_unique')) {
            Schema::table('cashflows', function (Blueprint $t) {
                $t->unique(['company_id', 'period'], 'cashflows_company_id_period_unique');
            });
        }

        // Note: company_id is left nullable at the schema level to avoid needing
        // doctrine/dbal for a ->change() to nullable(false). Models always
        // populate it on create.
    }

    public function down(): void
    {
        $this->dropIndexIfExists('settings', 'settings_company_id_unique');
        $this->dropIndexIfExists('cashflows', 'cashflows_company_id_period_unique');

        foreach (['debts', 'cashflows', 'payments', 'settings'] as $table) {
            if (Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    try { $t->dropIndex($table . '_company_id_index'); } catch (\Throwable $e) {}
                    $t->dropColumn('company_id');
                });
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $db = DB::connection()->getDatabaseName();
            $row = DB::selectOne(
                'SELECT 1 AS found FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$db, $table, $index]
            );
            return (bool) $row;
        }
        if ($driver === 'sqlite') {
            $row = DB::selectOne('SELECT 1 AS found FROM sqlite_master WHERE type = "index" AND name = ?', [$index]);
            return (bool) $row;
        }
        // Fallback: assume present so we don't duplicate.
        return true;
    }

    private function ensureIndex(string $table, string|array $columns, string $name): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }
        Schema::table($table, function (Blueprint $t) use ($columns, $name) {
            $t->index((array) $columns, $name);
        });
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (!$this->indexExists($table, $index)) {
            return;
        }
        Schema::table($table, function (Blueprint $t) use ($index) {
            try { $t->dropUnique($index); } catch (\Throwable $e) {
                try { $t->dropIndex($index); } catch (\Throwable $e2) {}
            }
        });
    }
};
