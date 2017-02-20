<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * @ApiDoc(
     *     description="Get Twitter statistics"
     * )
     * @QueryParam(
     *     name="start",
     *     requirements="^\d+$",
     *     allowBlank=false,
     *     strict=true,
     *     description="Start UNIX timestamp"
     * )
     * @QueryParam(
     *     name="end",
     *     requirements="^\d+$",
     *     allowBlank=false,
     *     strict=true,
     *     description="End UNIX timestamp"
     * )
     * @Get("/api/twitter/statistics")
     */
    public function getAction(ParamFetcher $paramFetcher)
    {
        $startTimestamp = $paramFetcher->get('start');
        $endTimestamp = $paramFetcher->get('end');

        $startDateTime = new \DateTime('@' . $startTimestamp);
        $endDateTime = new \DateTime('@' . $endTimestamp);

        $statistics = $this->getDoctrine()
            ->getRepository('AppBundle:TwitterStatistics')
            ->getStatistics($startDateTime, $endDateTime);

        return new JsonResponse([
            'data' => $statistics,
        ]);
    }
}
