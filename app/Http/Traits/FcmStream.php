<?php

namespace App\Http\Traits;

use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class FcmStream {

    public static function sendMessageNotification($thread, $title, $body, $token){
        $option_builder = new OptionsBuilder();
        $option_builder->setTimeToLive(60 * 20);

        $notification_builder = new PayloadNotificationBuilder($title);
        $notification_builder->setBody($body)->setSound('default');

        $data_builder = new PayloadDataBuilder();
        $data_builder->addData(['thread' => $thread]);

        $option = $option_builder->build();
        $notification = $notification_builder->build();
        $data = $data_builder->build();

        $downstream_response = FCM::sendTo($token, $option, $notification, $data);

        $downstream_response->numberSuccess();
        $downstream_response->numberFailure();
        $downstream_response->numberModification();
    }

};