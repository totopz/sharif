<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Endroid\Twitter\Twitter;
use Monolog\Logger;

class TwitterServices
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Twitter
     */
    private $twitter;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(EntityManager $em, Twitter $twitter, Logger $logger)
    {
        $this->em = $em;
        $this->twitter = $twitter;
        $this->logger = $logger;
    }

    /**
     * Get user follower count
     *
     * @param int $twitterUserId
     * @throws \Twig_Error
     * @return int
     */
    public function getFollowersCount($twitterUserId)
    {
        $followersCount = 0;
        $cursor = -1;

        do {
            $apiCall = '/followers/ids';

            $requestParams = [
                'cursor' => $cursor,
                'user_id' => $twitterUserId,
                'stringify_ids' => true,
            ];

            $response = $this->twitter->query($apiCall, 'GET', 'json', $requestParams);

            if (empty($response)) {
                $this->logger->warning('Empty twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Empty twitter API response');
            }

            $response = json_decode($response->getContent(), true);

            if ($response === null || empty($response['ids'])) {
                $this->logger->warning('Invalid twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Invalid twitter API response');
            }

            $cursor = $response['next_cursor_str'];

            $followersCount += count($response['ids']);
        } while ($cursor != 0);

        return $followersCount;
    }

    /**
     * Get likes count
     *
     * @param int $twitterUserId
     * @throws \Twig_Error
     * @return int
     */
    public function getLikesCount($twitterUserId)
    {
        $likesCount = 0;
        $maxTweetId = 1;

        do {
            $apiCall = '/favorites/list';

            $requestParams = [
                'user_id' => $twitterUserId,
                'include_entities' => false,
                'count' => 200,
                'since_id' => $maxTweetId,
            ];

            $response = $this->twitter->query($apiCall, 'GET', 'json', $requestParams);

            if (empty($response)) {
                $this->logger->warning('Empty twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Empty twitter API response');
            }

            $response = json_decode($response->getContent(), true);

            if ($response === null || is_array($response) == false) {
                $this->logger->warning('Invalid twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Invalid twitter API response');
            }

            foreach ($response as $tweet) {
                if ($tweet['id_str'] > $maxTweetId) {
                    $maxTweetId = $tweet['id_str'];
                }

                $likesCount++;
            }
        } while (count($response) > 0);

        return $likesCount;
    }

    /**
     * Get retweets count
     *
     * @param int $twitterUserId
     * @throws \Twig_Error
     * @return int
     */
    public function getRetweetsCount($twitterUserId)
    {
        $retweetsCount = 0;
        $maxTweetId = 1;

        do {
            $apiCall = '/statuses/user_timeline';

            $requestParams = [
                'user_id' => $twitterUserId,
                'trim_user' => true,
                'exclude_replies' => true,
                'count' => 200,
                'since_id' => $maxTweetId,
            ];

            $response = $this->twitter->query($apiCall, 'GET', 'json', $requestParams);

            if (empty($response)) {
                $this->logger->warning('Empty twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Empty twitter API response');
            }

            $response = json_decode($response->getContent(), true);

            if ($response === null || is_array($response) == false) {
                $this->logger->warning('Invalid twitter API response', [
                    'apiCall' => $apiCall,
                    'requestParams' => $requestParams,
                ]);

                throw new \Twig_Error('Invalid twitter API response');
            }

            foreach ($response as $tweet) {
                if ($tweet['id_str'] > $maxTweetId) {
                    $maxTweetId = $tweet['id_str'];
                }

                $retweetsCount += $tweet['retweet_count'];
            }
        } while (count($response) > 0);

        return $retweetsCount;
    }
}