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
    public function prepareRequest(Request $request): Request
    {
        if ($request->getContentType() !== 'json' || empty($request->getContent())) {
            return $request;
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }

        $request->request->add($data);

        return $request;
    }
}