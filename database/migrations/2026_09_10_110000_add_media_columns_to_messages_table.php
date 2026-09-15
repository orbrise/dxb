<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('message');
            }
            if (!Schema::hasColumn('messages', 'attachment_type')) {
                // image | audio | video | file
                $table->string('attachment_type', 16)->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('messages', 'attachment_mime')) {
                $table->string('attachment_mime', 128)->nullable()->after('attachment_type');
            }
            if (!Schema::hasColumn('messages', 'attachment_size')) {
                $table->unsignedInteger('attachment_size')->nullable()->after('attachment_mime');
            }
            if (!Schema::hasColumn('messages', 'attachment_duration')) {
                // seconds — for voice notes / video
                $table->unsignedSmallInteger('attachment_duration')->nullable()->after('attachment_size');
            }
            if (!Schema::hasColumn('messages', 'attachment_original_name')) {
                $table->string('attachment_original_name')->nullable()->after('attachment_duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            foreach ([
                'attachment_path',
                'attachment_type',
                'attachment_mime',
                'attachment_size',
                'attachment_duration',
                'attachment_original_name',
            ] as $col) {
                if (Schema::hasColumn('messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
