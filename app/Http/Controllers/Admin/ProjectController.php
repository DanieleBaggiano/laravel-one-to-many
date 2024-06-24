<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = \App\Models\Project::with('type')->get();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = \App\Models\Type::all();
        return view('admin.projects.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'slug' => 'required|unique:projects,slug',
            'type_id' => 'nullable|exists:types,id',
        ]);

        $slug = Str::slug($request->slug);

        $originalSlug = $slug;
        $counter = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $slug,
            'type_id' => $request->type_id,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = \App\Models\Project::findOrFail($id);
        $types = \App\Models\Type::all();
        return view('admin.projects.edit', compact('project', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'slug' => 'required|unique:projects,slug,' . $id,
            'type_id' => 'nullable|exists:types,id',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $request->slug,
            'type_id' => $request->type_id,
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}
