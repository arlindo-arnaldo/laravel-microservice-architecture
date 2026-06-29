<?php

trait ApiResponse
{


    public function sucess(array $data, int $code)
    {
        return response()->json([
            'succss' => true,
            'data' => $data
        ]);
    }

    public function error(string $message, int $code = 500)
    {
        return response()->json([
            'success' => false,
            'error' => $message
        ]);
    }
}
