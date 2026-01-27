<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class KurirController extends Controller
{
  public function index(Request $request)
  {
    try {
        $query = DB::table('kurir');

        // Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $search = explode(' ', $search);

            for ($i = 0; $i < count($search); $i++) {
                $search[$i] = '%' . $search[$i] . '%';
                $query = $query->orWhereAny([
                    'nama_kurir',
                    'no_telepon',
                    'alamat',
                    'status',
                ], 'ilike', $search[$i]);
            }
        }

        if ($request->has('level')) {
            $level = $request->input('level');
            $level = explode(',', $level);
            $query = $query->whereIn('level', $level);
        }

        // Ordering
        if ($request->has('order')) {
            $orderBy = $request->input('order');
            $orderMode = $request->input('order_mode', 'asc');
            $query = $query->orderBy($orderBy, $orderMode);
        }
        else {
            $query = $query->orderBy('nama_kurir', 'asc');
        }

        return $query->simplePaginate(10, ['*'], 'page', $request->input('page', 1));
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function show()
  {
    try {
        return DB::table('kurir')->get();
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function store(Request $request)
  {
    try {
        $request->validate([
            'nama_kurir' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'required|string|max:500',
            'level' => 'required|integer|in:1,2,3,4,5',
            'status' => 'required|in:active,inactive',
        ]);
    
        DB::table('kurir')->insert([
            'nama_kurir' => $request->nama_kurir,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'level' => $request->level,
            'status' => $request->status,
            'created_at' => now(),
        ]);
        return response()->json(['message' => 'Kurir created successfully'], 200);
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function update(Request $request)
  {
    try {
        $request->validate([
            'nama_kurir' => 'string|max:255',
            'no_telepon' => 'string|max:15',
            'alamat' => 'string|max:500',
            'level' => 'integer|in:1,2,3,4,5',
            'status' => 'in:active,inactive',
        ]);

        $kurir = DB::table('kurir')->where('id', $request->id);
        $selectedKurir = $kurir->first();
        if (!$selectedKurir) {
            return response()->json(['error' => 'Kurir not found'], 404);
        }
        $kurir->update([
            'nama_kurir' => $request->nama_kurir ?? $selectedKurir->nama_kurir,
            'no_telepon' => $request->no_telepon ?? $selectedKurir->no_telepon,
            'alamat' => $request->alamat ?? $selectedKurir->alamat,
            'level' => $request->level ?? $selectedKurir->level,
            'status' => $request->status ?? $selectedKurir->status,
            'updated_at' => now(),
        ]);
        return response()->json(['message' => 'Kurir updated successfully'], 200);
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function destroy(Request $request)
  {
    try {
        $kurir = DB::table('kurir')->where('id', $request->id);
        $selectedKurir = $kurir->first();
        if (!$selectedKurir) {
            return response()->json(['error' => 'Kurir not found'], 404);
        }
        $kurir->delete();

        return response()->json(['message' => 'Kurir deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }
}