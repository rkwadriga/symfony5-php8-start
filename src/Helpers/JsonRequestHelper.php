<?php declare(strict_types=1);
/**
 * Created 2021-09-27
 * Author Dmitry Kushneriov
 */

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonRequestHelper
{
    public function getParams(Request $request): array
    {
        if ($request->getContentType() !== 'json' || empty($request->getContent())) {
            return [];
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        return $data;
    }
}