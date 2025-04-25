<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 10;

        $tips = Tip::select('id', 'title', 'thumbnail', 'url')
            ->paginate($limit);

        $tips->getCollection()->transform(function ($tip) {
            $tip->thumbnail = $tip->thumbnail ? url('tips/' . $tip->thumbnail) : '';
            return $tip;
        });

        return response()->json([
            'status' => true,
            'message' => 'Tips retrieved successfully',
            'data' => $tips
        ]);
    }
}
