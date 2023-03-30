<?php

namespace App\Http\Controllers\Public;

trait ResponseMessage
{
    public function RespondSuccess($message,$data = null)
    {
        return response()->json([
            'type' => 'success',
            'message' => $message,
            'data'=> $data
        ]);
    }

    public function RespondError($message, $data = null)
    {
        return response()->json([
            'type' => 'error',
            'message' => $message,
            'data'=> $data
        ]);
    }

    public function RespondInfo($message , $data = null)
    {
        return response()->json([
            'type' => 'info',
            'message' => $message,
            'data'=> $data
        ]);
    }

}
