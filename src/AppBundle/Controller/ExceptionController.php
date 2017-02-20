<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionController extends Controller
{
    public function showAction(\Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Requested API call does not exist',
            ]);
        }

        return new JsonResponse([
            'error' => true,
            'message' => $exception->getMessage(),
        ]);
    }
}
