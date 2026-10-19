<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    // GET /api/barang  (opsional: ?kategori=... atau ?cari=...)
    public function index(Request $request): JsonResponse
    {
        $query = Barang::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->cari . '%');
            });
        }

        return response()->json($query->latest()->paginate(10), 200);
    }

    // GET /api/barang/{barang}
    public function show(Barang $barang): JsonResponse
    {
        return response()->json(['data' => $barang], 200);
    }

    // POST /api/barang
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barang,kode_barang'],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:20'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'numeric', 'min:0'],
            'lokasi_rak' => ['nullable', 'string', 'max:50'],
        ]);

        $barang = Barang::create($data);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $barang,
        ], 201);
    }

    // PUT/PATCH /api/barang/{barang}
    public function update(Request $request, Barang $barang): JsonResponse
    {
        $data = $request->validate([
            'kode_barang' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('barang', 'kode_barang')->ignore($barang->id)],
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'kategori' => ['sometimes', 'nullable', 'string', 'max:100'],
            'satuan' => ['sometimes', 'required', 'string', 'max:20'],
            'stok' => ['sometimes', 'required', 'integer', 'min:0'],
            'harga' => ['sometimes', 'required', 'numeric', 'min:0'],
            'lokasi_rak' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $barang->update($data);

        return response()->json([
            'message' => 'Barang berhasil diubah.',
            'data' => $barang->fresh(),
        ], 200);
    }

    // DELETE /api/barang/{barang}
    public function destroy(Barang $barang): JsonResponse
    {
        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus.'], 200);
    }
}
