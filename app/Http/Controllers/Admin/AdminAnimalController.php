<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;

class AdminAnimalController extends Controller
{
    public function index(Request $request)
    {
        $editAnimal = null;
        if ($request->filled('edit')) {
            $editAnimal = Animal::find($request->query('edit'));
        }

        $animals = Animal::orderByDesc('animal_id')->get();
        return view('admin.animals', compact('animals', 'editAnimal'));
    }

    public function store(Request $request)
    {
        $nama = $request->input('nama') ?? $request->input('name');
        $request->merge(['nama' => $nama, 'age' => (string) $request->input('age')]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'string', 'max:20'],
            'age' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,adopted,unavailable'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $imagePath = ($validated['image'] ?? null) ?: 'assets/img/meng.png';
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $dir = public_path('assets/img/uploads/animals');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'animal-' . time() . '-' . mt_rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $imagePath = 'assets/img/uploads/animals/' . $filename;
        }

        Animal::create([
            'nama' => $validated['nama'],
            'species' => $validated['species'],
            'breed' => $validated['breed'],
            'gender' => $validated['gender'],
            'age' => $validated['age'],
            'status' => $validated['status'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? '',
        ]);

        return redirect()->route('admin.animals.index')->with('success', 'Data hewan adopsi berhasil ditambahkan.');
    }

    public function update(Request $request, Animal $animal)
    {
        $nama = $request->input('nama') ?? $request->input('name');
        $request->merge(['nama' => $nama, 'age' => (string) $request->input('age')]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'string', 'max:20'],
            'age' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,adopted,unavailable'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $imagePath = ($validated['image'] ?? null) ?: $animal->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $dir = public_path('assets/img/uploads/animals');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'animal-' . time() . '-' . mt_rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $imagePath = 'assets/img/uploads/animals/' . $filename;
        }

        $animal->update([
            'nama' => $validated['nama'],
            'species' => $validated['species'],
            'breed' => $validated['breed'],
            'gender' => $validated['gender'],
            'age' => $validated['age'],
            'status' => $validated['status'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? $animal->description,
        ]);

        return redirect()->route('admin.animals.index')->with('success', 'Data hewan adopsi berhasil diperbarui.');
    }

    public function destroy(Animal $animal)
    {
        $animal->delete();
        return redirect()->route('admin.animals.index')->with('success', 'Data hewan adopsi berhasil dihapus.');
    }
}
