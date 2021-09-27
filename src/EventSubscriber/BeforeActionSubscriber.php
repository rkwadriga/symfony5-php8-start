<?php declare(strict_types=1);
/**
 * Created 2021-09-26
 * Author Dmitry Kushneriov
 */

namespace App\EventSubscriber;

use function json_last_error;
use function json_last_error_msg;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Helpers\JsonRequestHelper;

class BeforeActionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JsonRequestHelper $jsonRequestHelper
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'convertJsonStringToArray',
        ];
    }

    public function convertJsonStringToArray(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $requestParams = $this->jsonRequestHelper->getParams($request);
        $request->request->replace($requestParams);
    }
}