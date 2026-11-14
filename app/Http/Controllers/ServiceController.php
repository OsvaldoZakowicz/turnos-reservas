<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('services/ListServices', [
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Service::create($validated);

        return to_route('services.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $validated = $request->validated();

        $service->name = $validated['name'];
        $service->description = $validated['description'];
        $service->duration_minutes = $validated['duration_minutes'];
        $service->price = $validated['price'];
        $service->is_active = $validated['is_active'];

        $service->save();

        return to_route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return to_route('services.index');
    }
}
