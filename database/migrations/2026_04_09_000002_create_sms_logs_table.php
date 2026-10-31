<?php
 
 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;
 
 return new class extends Migration {
     public function up(): void
     {
         Schema::create('sms_logs', function (Blueprint $table) {
             $table->id();
             $table->string('phone')->nullable();
             $table->text('content')->nullable();
             $table->string('brandname')->nullable();
             $table->string('request_id')->nullable();
             $table->string('status')->nullable();
             $table->text('response')->nullable();
             $table->timestamps();
         });
     }
 
     public function down(): void
     {
         Schema::dropIfExists('sms_logs');
     }
 };
