<?php

namespace App\Http\Controllers;

use App\Models\Ecole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class EcoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ecole::query();

        // Filtrage par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }

        $ecoles = $query->orderBy('nom')->paginate($request->get('per_page', 15));

        return response()->json($ecoles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:ecoles,code',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'directeur' => 'nullable|string|max:255',
            'statut' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $ecole = Ecole::create($validated);

        return response()->json([
            'message' => 'École créée avec succès',
            'data' => $ecole
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ecole $ecole): JsonResponse
    {
        return response()->json($ecole);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ecole $ecole): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('ecoles')->ignore($ecole->id)],
            'adresse' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'directeur' => 'nullable|string|max:255',
            'statut' => 'nullable|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $ecole->update($validated);

        return response()->json([
            'message' => 'École mise à jour avec succès',
            'data' => $ecole
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ecole $ecole): JsonResponse
    {
        $ecole->delete();

        return response()->json([
            'message' => 'École supprimée avec succès'
        ]);
    }
}
