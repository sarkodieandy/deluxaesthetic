<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clinic\StorePractitionerBlockedDateRequest;
use App\Http\Requests\Admin\Clinic\StorePractitionerScheduleRequest;
use App\Http\Requests\Admin\Clinic\UpdatePractitionerScheduleRequest;
use App\Models\Branch;
use App\Models\PractitionerBlockedDate;
use App\Models\PractitionerProfile;
use App\Models\PractitionerSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /** @var list<string> */
    public const DAY_LABELS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function index(Request $request): View
    {
        $practitionerId = $request->integer('practitioner_profile_id') ?: null;
        $branchId = $request->integer('branch_id') ?: null;

        $schedules = PractitionerSchedule::query()
            ->with(['practitioner.user', 'branch'])
            ->when($practitionerId, fn ($q) => $q->where('practitioner_profile_id', $practitionerId))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('practitioner_profile_id')
            ->orderBy('branch_id')
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->paginate(30)
            ->withQueryString();

        $blockedDates = PractitionerBlockedDate::query()
            ->with('practitioner.user')
            ->when($practitionerId, fn ($q) => $q->where('practitioner_profile_id', $practitionerId))
            ->where('ends_on', '>=', now()->toDateString())
            ->orderBy('starts_on')
            ->limit(50)
            ->get();

        return view('admin.schedules.index', [
            'schedules' => $schedules,
            'blockedDates' => $blockedDates,
            'practitioners' => PractitionerProfile::query()->with('user')->where('is_active', true)->orderBy('sort_order')->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'dayLabels' => self::DAY_LABELS,
            'filters' => [
                'practitioner_profile_id' => $practitionerId,
                'branch_id' => $branchId,
            ],
        ]);
    }

    public function store(StorePractitionerScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        PractitionerSchedule::create([
            'practitioner_profile_id' => (int) $data['practitioner_profile_id'],
            'branch_id' => (int) $data['branch_id'],
            'day_of_week' => (int) $data['day_of_week'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.schedules.index', $this->filterQuery($request))
            ->with('status', 'Working hours added. Online booking will offer slots in this window.');
    }

    public function edit(PractitionerSchedule $schedule): View
    {
        $schedule->load(['practitioner.user', 'branch']);

        return view('admin.schedules.edit', [
            'schedule' => $schedule,
            'practitioners' => PractitionerProfile::query()->with('user')->where('is_active', true)->orderBy('sort_order')->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'dayLabels' => self::DAY_LABELS,
        ]);
    }

    public function update(UpdatePractitionerScheduleRequest $request, PractitionerSchedule $schedule): RedirectResponse
    {
        $data = $request->validated();

        $schedule->update([
            'practitioner_profile_id' => (int) $data['practitioner_profile_id'],
            'branch_id' => (int) $data['branch_id'],
            'day_of_week' => (int) $data['day_of_week'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Working hours updated.');
    }

    public function destroy(PractitionerSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Working hours removed.');
    }

    public function storeBlocked(StorePractitionerBlockedDateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        PractitionerBlockedDate::create([
            'practitioner_profile_id' => (int) $data['practitioner_profile_id'],
            'starts_on' => $data['starts_on'],
            'ends_on' => $data['ends_on'],
            'reason' => $data['reason'] ?? null,
        ]);

        return redirect()
            ->route('admin.schedules.index', $this->filterQuery($request))
            ->with('status', 'Blocked date range saved. No online slots will be offered on those days.');
    }

    public function destroyBlocked(PractitionerBlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('status', 'Blocked date removed.');
    }

    /**
     * @return array<string, int>
     */
    private function filterQuery(Request $request): array
    {
        return array_filter([
            'practitioner_profile_id' => $request->integer('practitioner_profile_id') ?: null,
            'branch_id' => $request->integer('branch_id') ?: null,
        ]);
    }
}
