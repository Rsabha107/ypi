<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'show_uniform_section')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('show_uniform_section')->default(true)->after('active_flag');
            });
        }

        // Seed every existing event from the old global setting so behaviour does not change.
        if (Schema::hasTable('settings')) {
            $global = DB::table('settings')->where('key', 'show_uniform_section')->value('value');

            if ($global !== null) {
                DB::table('events')->update(['show_uniform_section' => (int) $global === 1]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'show_uniform_section')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('show_uniform_section');
            });
        }
    }
};
