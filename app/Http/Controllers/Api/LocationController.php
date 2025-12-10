<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function provinsis()
    {
        // Log::info('locations.provinsis.request');
        try {
            $items = DB::table('loc_provinsis')
                ->orderBy('name')
                ->get(['id as id','name as name']);
            // Log::info('locations.provinsis.response', ['count' => $items->count()]);
            return response()->json(['success'=>true,'data'=>$items]);
        } catch (\Throwable $e) {
            // Log::error('locations.provinsis.error', ['message' => $e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Error fetching provinsis'], 500);
        }
    }

    public function kabkotas(Request $request, int $provinsiId)
    {
        // Log::info('locations.kabkotas.request', ['provinsi_id' => $provinsiId]);
        try {
            $items = DB::table('loc_kabkotas')
                ->where('province_id', $provinsiId)
                ->orderBy('name')
                ->get(['id as id','name as name']);
            // Log::info('locations.kabkotas.response', ['provinsi_id' => $provinsiId, 'count' => $items->count()]);
            return response()->json(['success'=>true,'data'=>$items]);
        } catch (\Throwable $e) {
            // Log::error('locations.kabkotas.error', ['provinsi_id' => $provinsiId, 'message' => $e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Error fetching kabkotas'], 500);
        }
    }

    public function kecamatans(Request $request, int $kabkotaId)
    {
        // Log::info('locations.kecamatans.request', ['kabkota_id' => $kabkotaId]);
        try {
            $items = DB::table('loc_kecamatans')
                ->where('regency_id', $kabkotaId)
                ->orderBy('name')
                ->get(['id as id','name as name']);
            // Log::info('locations.kecamatans.response', ['kabkota_id' => $kabkotaId, 'count' => $items->count()]);
            return response()->json(['success'=>true,'data'=>$items]);
        } catch (\Throwable $e) {
            // Log::error('locations.kecamatans.error', ['kabkota_id' => $kabkotaId, 'message' => $e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Error fetching kecamatans'], 500);
        }
    }

    public function desas(Request $request, int $kecamatanId)
    {
        // Log::info('locations.desas.request', ['kecamatan_id' => $kecamatanId]);
        try {
            $items = DB::table('loc_desas')
                ->where('district_id', $kecamatanId)
                ->orderBy('name')
                ->get(['id as id','name as name']);
            // Log::info('locations.desas.response', ['kecamatan_id' => $kecamatanId, 'count' => $items->count()]);
            return response()->json(['success'=>true,'data'=>$items]);
        } catch (\Throwable $e) {
            // Log::error('locations.desas.error', ['kecamatan_id' => $kecamatanId, 'message' => $e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Error fetching desas'], 500);
        }
    }
}
