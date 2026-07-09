<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Person;

class CheckInController extends Controller
{
    public function create()
    {
        return view('admin.scanner');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $pivotRow = DB::table('attendee_edition')
            ->where('token', $validated['token'])
            ->first();

        if ($pivotRow === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Entrada no encontrada.',
            ], 404);
        }

        $attendee = Person::find($pivotRow->attendee_id);

        $revokedRights = [];
        if (!$pivotRow->auth_for_ad) {
            $revokedRights[] = 'Publicidad';
        }
        if (!$pivotRow->auth_for_comms) {
            $revokedRights[] = 'Comunicaciones';
        }
        if (!$pivotRow->auth_image_rights) {
            $revokedRights[] = 'Imagen';
        }

        if ($pivotRow->attendance) {
            return response()->json([
                'status'              => 'warning',
                'message'             => 'Esta entrada ya ha sido escaneada.',
                'not_accepted_rights' => $revokedRights,
            ]);
        }

        DB::table('attendee_edition')
            ->where('edition_id', $pivotRow->edition_id)
            ->where('attendee_id', $pivotRow->attendee_id)
            ->update([
                'attendance'    => true,
                'checked_in_at' => now(),
            ]);

        return response()->json([
            'status'              => 'success',
            'message'             => 'Bienvenido/a ' . $attendee->name . '!',
            'not_accepted_rights' => $revokedRights,
        ]);
    }
}
