<?php

namespace Nubix\Notifications;

use Aws\Sns\SnsClient;
use Aws\Credentials\Credentials;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Nubix\Notifications\Channels\SmsChannel;

class SmsChannelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('sms', function ($app) {
                $credentials = null;

                $accessKey = $this->app['config']['services.sns.key'];
                $secret = $this->app['config']['services.sns.secret'];

                if (null !== $accessKey && null !== $secret) {
                    $credentials = new Credentials($accessKey, $secret);
                }

                return new SmsChannel(
                    new SnsClient([
                        'version' => '2010-03-31',
                        'credentials' => $credentials,
                        'region' => $this->app['config']['services.sns.region'],
                        'endpoint' => $this->app['config']['services.sns.endpoint'],
                    ])
                );
            });
        });
    }
}
