<?php

namespace App\Http\Controllers;

use App\Services\Messaging\InboundEmailService;
use Illuminate\Http\Request;

class InboundEmailController extends Controller
{
    public function store(Request $request, InboundEmailService $inboundEmail)
    {
        $message = $inboundEmail->handle($request);

        return response()->json([
            'status' => true,
            'message' => 'Inbound email stored.',
            'message_id' => $message->id,
        ]);
    }
}
