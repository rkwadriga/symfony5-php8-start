<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

/**
 * Class AbstractController
 * @package App\Controller
 *
 * @method \App\Entity\User getUser()
 */
abstract class AbstractController extends BaseController
{

}