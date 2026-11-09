<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // restrict: no se borra un servicio con historial de reservas
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status', 20)->default(BookingStatus::Pending->value);
            $table->text('notes')->nullable();
            $table->timestamps();

            // consulta de superposicion: estado y rango de horas
            $table->index(['status', 'start_time', 'end_time']);
            // historial del cliente
            $table->index(['user_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
