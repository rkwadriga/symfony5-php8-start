<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\HttpException;

class AccountController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("account", name="app_account_create", methods={"PUT"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            dd($request);
        } catch (\Exception $e) {
            throw new HttpException($e);
        }
    }
}