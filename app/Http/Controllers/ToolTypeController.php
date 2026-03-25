<?php

namespace App\Http\Controllers;

use App\Models\ToolType;
use App\Models\ToolChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ToolTypeController extends Controller
{
    public function index()
    {
        $toolTypes = ToolType::withCount('checklistItems')->latest()->get();
        return view('tool-types.index', compact('toolTypes'));
    }

    public function create()
    {
        return view('tool-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tool_types,slug',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        $slug = $request->slug ?: Str::slug($request->name, '_');

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (ToolType::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '_' . $counter++;
        }

        ToolType::create([
            'slug' => $slug,
            'name' => $request->name,
            'icon' => $request->icon ?: 'bi-wrench',
            'color' => $request->color ?: 'primary',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tool-types.index')
            ->with('success', 'เพิ่มประเภทเครื่องมือเรียบร้อยแล้ว');
    }

    public function edit(ToolType $toolType)
    {
        $toolType->load(['checklistItems' => function ($q) {
            $q->orderBy('sort_order');
        }]);
        return view('tool-types.edit', compact('toolType'));
    }

    public function update(Request $request, ToolType $toolType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        $toolType->update([
            'name' => $request->name,
            'icon' => $request->icon ?: 'bi-wrench',
            'color' => $request->color ?: 'primary',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tool-types.edit', $toolType)
            ->with('success', 'แก้ไขประเภทเครื่องมือเรียบร้อยแล้ว');
    }

    public function destroy(ToolType $toolType)
    {
        // Check if there are tools using this type
        $toolCount = \App\Models\Tool::where('type', $toolType->slug)->count();
        if ($toolCount > 0) {
            return redirect()->route('tool-types.index')
                ->with('error', "ไม่สามารถลบได้ มีเครื่องมือที่ใช้ประเภทนี้อยู่ {$toolCount} รายการ");
        }

        $toolType->delete();

        return redirect()->route('tool-types.index')
            ->with('success', 'ลบประเภทเครื่องมือเรียบร้อยแล้ว');
    }

    // Checklist Item Management
    public function storeChecklistItem(Request $request, ToolType $toolType)
    {
        $request->validate([
            'item_code' => 'required|string|max:50',
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:500',
        ]);

        $maxOrder = $toolType->checklistItems()->max('sort_order') ?? 0;

        $toolType->checklistItems()->create([
            'item_code' => $request->item_code,
            'category' => $request->category,
            'item_name' => $request->item_name,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('tool-types.edit', $toolType)
            ->with('success', 'เพิ่มรายการตรวจสอบเรียบร้อยแล้ว');
    }

    public function updateChecklistItem(Request $request, ToolType $toolType, ToolChecklistItem $item)
    {
        $request->validate([
            'item_code' => 'required|string|max:50',
            'category' => 'required|string|max:255',
            'item_name' => 'required|string|max:500',
        ]);

        $item->update([
            'item_code' => $request->item_code,
            'category' => $request->category,
            'item_name' => $request->item_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tool-types.edit', $toolType)
            ->with('success', 'แก้ไขรายการตรวจสอบเรียบร้อยแล้ว');
    }

    public function destroyChecklistItem(ToolType $toolType, ToolChecklistItem $item)
    {
        $item->delete();

        return redirect()->route('tool-types.edit', $toolType)
            ->with('success', 'ลบรายการตรวจสอบเรียบร้อยแล้ว');
    }
}
