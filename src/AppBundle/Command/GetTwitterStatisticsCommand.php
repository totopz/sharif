<?php

namespace AppBundle\Command;

use AppBundle\Entity\TwitterStatistics;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetTwitterStatisticsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:get-twitter-statistics')
            ->setDescription('Get Twitter statistics')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitterUserId = $this->getContainer()->getParameter('twitter_statistics_user_id');

        $twitterServices = $this->getContainer()->get('app.twitter_services');

        $twitterStatistics = new TwitterStatistics();
        $twitterStatistics->setTwitterUserId($twitterUserId);
        $twitterStatistics->setCreatedAt(new \DateTime());

        try {
            $followersCount = $twitterServices->getFollowersCount($twitterUserId);

            $twitterStatistics->setFollowersCount($followersCount);
        } catch (\Twig_Error $e) {
            // nothing here - error is logged
            // can echo some message to the console
        }

        try {
            $likesCount = $twitterServices->getLikesCount($twitterUserId);

            $twitterStatistics->setLikesCount($likesCount);
        } catch (\Twig_Error $e) {
            // nothing here - error is logged
            // can echo some message to the console
        }

        try {
            $retweetsCount = $twitterServices->getRetweetsCount($twitterUserId);

            $twitterStatistics->setRetweetsCount($retweetsCount);
        } catch (\Twig_Error $e) {
            // nothing here - error is logged
            // can echo some message to the console
        }

        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $entityManager->persist($twitterStatistics);
        $entityManager->flush();

        $output->writeln('Done');
    }

}
