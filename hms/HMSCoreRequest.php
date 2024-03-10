<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-03-10
 * Time: 10:19:18
 * https://www.Maatify.dev
 */

namespace Maatify\HMS;

class HMSCoreRequest
{
    private static self $instance;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}