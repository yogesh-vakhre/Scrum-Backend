<?php 
namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiHelpers
{

    /**
     * Show the Success Response.
     *
     * @param array $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\Response
     */
    protected function onSuccess($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Show the Error Response.
     *
     * @param int $code
     * @param string $message
     * @param array $errorMessages
     * @return \Illuminate\Http\Response
     */
    protected function onError(int $code, string $message = '', $errorMessages = []): JsonResponse
    {
        $response = [
            'status' => $code,
            'message' => $message,
        ];

        if(!empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }

        return response()->json( $response, $code);
    }
}