<?php

/*
 * This file is part of the CsaGuzzleBundle package
 *
 * (c) Charles Sarrazin <charles@sarraz.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Csa\Bundle\GuzzleBundle\GuzzleHttp\Subscriber;

use GuzzleHttp\Event\AbstractRetryableEvent;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Csa Guzzle Stopwatch integration
 *
 * @author Charles Sarrazin <charles@sarraz.in>
 */
class StopwatchSubscriber implements SubscriberInterface
{
    private $stopwatch;

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    public function getEvents()
    {
        return [
            'before'   => ['onBefore', RequestEvents::EARLY],
            'complete' => ['onFinish', RequestEvents::EARLY],
            'error'    => ['onFinish', RequestEvents::EARLY],
        ];
    }

    public function onBefore(BeforeEvent $event)
    {
        $this->stopwatch->start($event->getRequest()->getUrl(), 'guzzle');
    }

    public function onFinish(AbstractRetryableEvent $event)
    {
        $url = $event->getRequest()->getUrl();

        if (!$this->stopwatch->isStarted($url)) {
            return;
        }

        $this->stopwatch->stop($url);
    }
}
