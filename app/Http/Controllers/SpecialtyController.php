<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    /**
     * Listagem de especialidades com suporte a busca e paginação.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $specialties = Specialty::withCount('doctors')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.specialties.index', compact('specialties', 'search'));
    }

    /**
     * Exibe o formulário de criação de especialidade.
     */
    public function create()
    {
        return view('admin.specialties.create');
    }

    /**
     * Armazena uma nova especialidade no banco de dados.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:specialties,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Specialty::create($validated);

        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Especialidade cadastrada com sucesso!');
    }

    /**
     * Exibe o formulário de edição de uma especialidade existente.
     */
    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    /**
     * Atualiza os dados da especialidade no banco de dados.
     */
    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:specialties,name,' . $specialty->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $specialty->update($validated);

        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Especialidade atualizada com sucesso!');
    }

    /**
     * Remove a especialidade do banco de dados com verificação de segurança.
     */
    public function destroy(Specialty $specialty)
    {
        // Impede exclusão caso haja médicos vinculados a esta especialidade
        if ($specialty->doctors()->exists()) {
            return redirect()
                ->route('admin.specialties.index')
                ->with('error', 'Não é possível excluir uma especialidade que possui médicos vinculados.');
        }

        $specialty->delete();

        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Especialidade removida com sucesso!');
    }
}