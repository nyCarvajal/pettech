<?php

namespace App\Http\Controllers\Groomer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\GroomingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroomerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroomingSession::class);

        $date = $request->input('date', now()->toDateString());
        $user = $request->user();

        $appointments = Appointment::query()
            ->with(['customer', 'pet', 'groomingSession.stageLogs'])
            ->where('service_type', 'grooming')
            ->where('assigned_to_user_id', $user->id)
            ->whereDate('start_at', $date)
            ->orderBy('start_at')
            ->get();

        $appointments->each(function (Appointment $appointment) use ($user): void {
            if ($appointment->groomingSession) {
                return;
            }

            $appointment->setRelation('groomingSession', GroomingSession::create([
                'appointment_id' => $appointment->id,
                'tenant_id' => $appointment->tenant_id ?? $user->tenant_id,
                'groomer_user_id' => $user->id,
                'current_stage' => GroomingSession::STAGES[0],
            ]));
        });

        $appointments->load('groomingSession.stageLogs');

        $kanban = collect(GroomingSession::STAGES)
            ->mapWithKeys(fn (string $stage) => [
                $stage => $appointments->filter(
                    fn (Appointment $appointment) => $appointment->groomingSession?->current_stage === $stage
                )->values(),
            ]);

        return view('groomer.dashboard', [
            'appointments' => $appointments,
            'kanban' => $kanban,
            'date' => $date,
            'stages' => GroomingSession::STAGES,
        ]);
    }

    public function advance(Request $request, GroomingSession $groomingSession): RedirectResponse
    {
        $this->authorize('update', $groomingSession);

        $this->changeStage($groomingSession, 1, $request->user()->id);

        return back()->with('status', 'Etapa actualizada correctamente.');
    }

    public function rollback(Request $request, GroomingSession $groomingSession): RedirectResponse
    {
        $this->authorize('update', $groomingSession);

        $this->changeStage($groomingSession, -1, $request->user()->id);

        return back()->with('status', 'Etapa actualizada correctamente.');
    }

    private function changeStage(GroomingSession $session, int $step, int $userId): void
    {
        $currentIndex = array_search($session->current_stage, GroomingSession::STAGES, true);
        $currentIndex = $currentIndex === false ? 0 : $currentIndex;

        $targetIndex = max(0, min(count(GroomingSession::STAGES) - 1, $currentIndex + $step));
        $targetStage = GroomingSession::STAGES[$targetIndex];

        if ($targetStage === $session->current_stage) {
            return;
        }

        $now = now();

        $session->update([
            'current_stage' => $targetStage,
            'started_at' => $session->started_at ?? ($targetStage !== 'received' ? $now : null),
            'finished_at' => $targetStage === 'ready' ? $now : null,
        ]);

        $session->stageLogs()->create([
            'stage' => $targetStage,
            'changed_at' => $now,
            'changed_by' => $userId,
        ]);
    }
}
