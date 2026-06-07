<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Service;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with('services')->latest()->get();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('staff.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $staff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        $staff->services()->sync($request->services ?? []);

        return redirect()->route('staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit(Staff $staff)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        $staff->load('services');

        return view('staff.edit', compact('staff', 'services'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        $staff->services()->sync($request->services ?? []);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->services()->detach();

        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully.');
    }
}
