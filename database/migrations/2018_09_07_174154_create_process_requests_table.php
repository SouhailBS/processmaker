<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_requests', function (Blueprint $table) {
            //Columns
            $table->increments('id');
            $table->unsignedInteger('process_id');
            $table->unsignedInteger('process_collaboration_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('parent_request_id')->nullable();
            $table->string('participant_id')->nullable();
            // The callable id is the text id of the bpmn element
            $table->string('callable_id');
            $table->enum('status', ['DRAFT', 'ACTIVE', 'COMPLETED', 'ERROR', 'CANCELED']);
            $table->json('data');
            $table->string('name');
            $table->json('errors')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamps();

            //Indexes
            $table->index('process_id');
            $table->index('user_id');
            $table->index('process_collaboration_id');
            $table->index('parent_request_id');
            $table->index('participant_id');

            //Foreign keys
            //If the collaboration is deleted the request stays without collaboration
            $table->foreign('process_collaboration_id')
                ->references('id')->on('process_collaborations')
                ->onDelete('set null');
            //A process can not be deleted if it has requests
            $table->foreign('process_id')
                ->references('id')->on('processes')
                ->onDelete('restrict');
            //An user can not be deleted if it has requests
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            //A request delete child requests in cascade
            $table->foreign('parent_request_id')
                ->references('id')->on('process_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_requests');
    }
}
