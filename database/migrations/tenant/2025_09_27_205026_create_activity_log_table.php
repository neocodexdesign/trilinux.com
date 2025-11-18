<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        $connection = config('activitylog.database_connection'); // normalmente null => conexão tenant
        $table = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->create($table, function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('log_name')->nullable();
            $t->text('description');
            // subject_type, subject_id (com índice "subject")
            $t->nullableMorphs('subject', 'subject');
            // coluna 'event' já incluída
            $t->string('event')->nullable();
            // causer_type, causer_id (com índice "causer")
            $t->nullableMorphs('causer', 'causer');
            $t->json('properties')->nullable();
            // coluna 'batch_uuid' já incluída
            $t->uuid('batch_uuid')->nullable();
            $t->timestamps();
            $t->index('log_name');
        });
    }

    public function down()
    {
        $connection = config('activitylog.database_connection');
        $table = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->dropIfExists($table);
    }
}