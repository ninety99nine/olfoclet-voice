<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BaseController
{
    protected function prepareOutput(
        mixed $output,
        int $status = Response::HTTP_OK
    ): JsonResponse|View|RedirectResponse|StreamedResponse|BinaryFileResponse|ResourceCollection {

        if ($output instanceof View ||
            $output instanceof RedirectResponse ||
            $output instanceof StreamedResponse ||
            $output instanceof BinaryFileResponse ||
            $output instanceof ResourceCollection) {
            return $output;
        }

        return response()->json($output, $status);
    }
}
