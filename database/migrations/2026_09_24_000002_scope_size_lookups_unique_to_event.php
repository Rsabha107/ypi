<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sizes became event-scoped, so type+code is only unique within an event.
     */
    public function up(): void
    {
        if (! Schema::hasTable('size_lookups')) {
            return;
        }

        if ($this->hasIndex('uniq_type_code')) {
            DB::statement('ALTER TABLE `size_lookups` DROP INDEX `uniq_type_code`');
        }

        if (! $this->hasIndex('uniq_event_type_code')) {
            DB::statement('ALTER TABLE `size_lookups` ADD UNIQUE `uniq_event_type_code` (`event_id`, `type`, `code`)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('size_lookups')) {
            return;
        }

        if ($this->hasIndex('uniq_event_type_code')) {
            DB::statement('ALTER TABLE `size_lookups` DROP INDEX `uniq_event_type_code`');
        }

        if (! $this->hasIndex('uniq_type_code')) {
            DB::statement('ALTER TABLE `size_lookups` ADD UNIQUE `uniq_type_code` (`type`, `code`)');
        }
    }

    private function hasIndex(string $name): bool
    {
        return ! empty(DB::select('SHOW INDEX FROM `size_lookups` WHERE Key_name = ?', [$name]));
    }
};
