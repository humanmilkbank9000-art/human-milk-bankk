<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Infant;

class UserController extends Controller
{
    public function search(Request $request)
    {
        // Admin gate
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $q = trim((string)$request->get('q', ''));
        $type = $request->get('user_type'); // optional: donor|requester
        if (strlen($q) < 2) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = User::query();
        if ($type && in_array($type, ['donor','requester'])) {
            $query->where('user_type', $type);
        }

        $query->where(function($sub) use ($q) {
            $sub->where('first_name', 'LIKE', "%$q%")
                ->orWhere('last_name', 'LIKE', "%$q%")
                ->orWhere('contact_number', 'LIKE', "%$q%")
                ->orWhere('address', 'LIKE', "%$q%");
        });

        $results = $query->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(20)
            ->get(['user_id','first_name','last_name','contact_number','address','user_type']);

        return response()->json([
            'data' => $results,
            'total' => $results->count(),
        ]);
    }

    /**
     * List infants for a given user (admin-only).
     */
    public function infants(Request $request, $userId)
    {
        if (!Session::has('account_id') || Session::get('account_role') !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $uid = (int) $userId;
        if ($uid <= 0) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $infants = Infant::where('user_id', $uid)
            ->orderByDesc('date_of_birth')
            ->orderByDesc('created_at')
            ->get(['infant_id','first_name','middle_name','last_name','sex','date_of_birth','birth_weight']);

        // Normalize output for UI autofill
        $data = $infants->map(function ($i) {
            return [
                'infant_id' => $i->infant_id,
                'first_name' => $i->first_name,
                'last_name' => $i->last_name,
                'sex' => $i->sex, // male|female
                'date_of_birth' => optional($i->date_of_birth)->format('Y-m-d'),
                'birth_weight' => $i->birth_weight,
            ];
        });

        return response()->json(['data' => $data, 'total' => $data->count()]);
    }
}
