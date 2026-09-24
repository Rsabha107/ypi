<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lookup tables become event-scoped. A NULL event_id means the row is
     * shared/global and stays visible to every event.
     *
     * No FK constraint: legacy rows in these tables hold '0000-00-00' timestamps,
     * which make MySQL reject the table rebuild an ALTER ... ADD CONSTRAINT requires.
     */
    private array $tables = ['participant_types', 'nationalities', 'size_lookups'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'event_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->unsignedBigInteger('event_id')->nullable()->after('id');
                $blueprint->index('event_id', "{$table}_event_id_index");
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'event_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropIndex("{$table}_event_id_index");
                $blueprint->dropColumn('event_id');
            });
        }
    }
};
