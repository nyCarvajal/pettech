<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Services\Appointment\AppointmentService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;
use PHPUnit\Framework\TestCase;

class AppointmentServiceConflictTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = new DB();
        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:'], 'tenant');
        $db->setAsGlobal();
        $db->bootEloquent();

        Model::setConnectionResolver($db->getDatabaseManager());

        $schema = $db->getConnection('tenant')->getSchemaBuilder();
        $schema->create('appointments', function ($table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->string('service_type')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_detects_overlap_for_same_groomer(): void
    {
        Appointment::query()->create([
            'tenant_id' => 1,
            'code' => 'APT-1',
            'service_type' => 'grooming',
            'start_at' => '2026-02-10 10:00:00',
            'end_at' => '2026-02-10 11:00:00',
            'assigned_to_user_id' => 8,
            'status' => 'confirmed',
            'created_by' => 1,
        ]);

        $service = new AppointmentService();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('cita solapada');

        $service->ensureNoOverlap(1, 8, '2026-02-10 10:30:00', '2026-02-10 11:30:00');
    }

    public function test_allows_non_overlapping_slot(): void
    {
        Appointment::query()->create([
            'tenant_id' => 1,
            'code' => 'APT-2',
            'service_type' => 'grooming',
            'start_at' => '2026-02-10 10:00:00',
            'end_at' => '2026-02-10 11:00:00',
            'assigned_to_user_id' => 8,
            'status' => 'confirmed',
            'created_by' => 1,
        ]);

        $service = new AppointmentService();

        $service->ensureNoOverlap(1, 8, '2026-02-10 11:00:00', '2026-02-10 12:00:00');

        $this->assertTrue(true);
    }
}
