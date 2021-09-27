<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\HttpException;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("account", name="account_create", methods={"PUT"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();

            $user = new User();
            $faker = Factory::create();
            $user->setSalt(hash('sha256', $faker->uuid));
            $user->setEmail($request->get('username'));
            $user->setName($request->get('name'));
            $user->setPassword($this->passwordEncoder->hashPassword($user, $request->get('password')));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user);
        } catch (\Exception $e) {
            throw new HttpException($e);
        }
    }
}