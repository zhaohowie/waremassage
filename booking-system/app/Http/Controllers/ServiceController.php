<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $categories = \App\Models\ServiceCategory::with(['services' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $uncategorizedServices = \App\Models\Service::whereNull('service_category_id')
            ->orderBy('name')
            ->get();

        return view('services.index', compact('categories', 'uncategorizedServices'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'nullable|exists:service_categories,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'cleanup_time' => 'required|integer|min:0',
            'description' => 'nullable',
            'color' => 'required',
        ]);

        Service::create([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'cleanup_time' => $request->cleanup_time,
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'service_category_id' => 'nullable|exists:service_categories,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'cleanup_time' => 'required|integer|min:0',
            'description' => 'nullable',
            'color' => 'required',
        ]);

        $service->update([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'cleanup_time' => $request->cleanup_time,
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
