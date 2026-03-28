<?php

namespace App\Http\Controllers;

use App\Models\RepairLog;
use App\Models\FireExtinguisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RepairLogController extends Controller
{
    private function generateRepairNo(): string
    {
        return 'REP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', RepairLog::class);

        $query = RepairLog::with(['fireExtinguisher', 'repairedBy'])->latest();

        if (!$request->user()->hasRole('admin')) {
            $query->where('repaired_by', $request->user()->id);
        }

        $repairLogs = $query->paginate(15);
        return view('repair-logs.index', compact('repairLogs'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', RepairLog::class);

        $extinguishers = FireExtinguisher::where('status', 'active')->get();
        $extinguisher = null;
        if ($request->has('extinguisher_id')) {
            $extinguisher = FireExtinguisher::findOrFail($request->extinguisher_id);
        }
        return view('repair-logs.create', compact('extinguisher', 'extinguishers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', RepairLog::class);

        $validated = $request->validate([
            'extinguisher_id' => 'required|exists:fire_extinguishers,id',
            'problem' => 'required|string|max:2000',
            'vendor_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            RepairLog::create([
                'repair_no' => $this->generateRepairNo(),
                'extinguisher_id' => $validated['extinguisher_id'],
                'problem' => $validated['problem'],
                'repaired_by' => auth()->id(),
                'status' => 'pending',
                'vendor_name' => $validated['vendor_name'] ?? null,
            ]);

            $extinguisher = FireExtinguisher::findOrFail($validated['extinguisher_id']);
            $extinguisher->status = 'under_repair';
            $extinguisher->save();
        });

        return redirect()->route('repair-logs.index')->with('success', 'สร้างใบบันทึกการซ่อมสำเร็จ');
    }

    public function show(RepairLog $repairLog)
    {
        $this->authorize('view', $repairLog);
        $repairLog->load(['fireExtinguisher', 'repairedBy', 'inspection']);
        return view('repair-logs.show', compact('repairLog'));
    }

    public function edit(RepairLog $repairLog)
    {
        $this->authorize('update', $repairLog);
        return view('repair-logs.edit', compact('repairLog'));
    }

    public function update(Request $request, RepairLog $repairLog)
    {
        $this->authorize('update', $repairLog);

        $validated = $request->validate([
            'action_taken' => 'nullable|string|max:2000',
            'repair_cost' => 'nullable|numeric',
            'status' => 'required|in:pending,in_progress,completed',
            'vendor_name' => 'nullable|string|max:255',
            'repaired_date' => 'nullable|date',
            'repair_result' => 'nullable|in:repaired,unrepairable',
        ]);

        if ($validated['status'] === 'completed' && !$repairLog->completed_date) {
            $validated['completed_date'] = now();
            
            // Revert extinguisher status to active or disposed
            $extinguisher = $repairLog->fireExtinguisher;
            if ($request->repair_result === 'unrepairable') {
                $extinguisher->status = 'disposed';
            } else {
                $extinguisher->status = 'active';
            }
            $extinguisher->save();
        }

        unset($validated['repair_result']);
        $repairLog->update($validated);

        return redirect()->route('repair-logs.show', $repairLog->id)->with('success', 'อัปเดตข้อมูลการซ่อมสำเร็จ');
    }

    public function complete(Request $request, RepairLog $repairLog)
    {
        $this->authorize('complete', $repairLog);

        $validated = $request->validate([
            'action_taken' => 'nullable|string|max:2000',
        ]);

        $repairLog->update([
            'status' => 'completed',
            'completed_date' => now(),
            'action_taken' => $validated['action_taken'] ?? 'Completed',
        ]);

        $extinguisher = $repairLog->fireExtinguisher;
        $extinguisher->status = 'active';
        $extinguisher->save();

        return redirect()->route('repair-logs.show', $repairLog->id)->with('success', 'ปิดงานซ่อมสำเร็จ ถังดับเพลิงพร้อมใช้งาน');
    }
}
