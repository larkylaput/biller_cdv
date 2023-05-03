<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Biller\Cdv\Factory\BillerCdv;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CdvCheckController extends BaseController
{
    public function validate(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required',
                'value' => 'required',
                'amount' => ['required', 'numeric'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
        
        $class = "App\Biller\Cdv\Validators\BillerCode".$validated['code'];
        if (!class_exists($class)) {
            return response()->json([
                'message' => "Biller CDV Class not found"
            ], 400);
        }


        $cdv = new BillerCdv(new $class);
        $status = $cdv->validate($validated['value'], $validated['amount']);
        return response()->json([
            'data' => [
                'result' => $status
            ]
        ]);
    }
}
