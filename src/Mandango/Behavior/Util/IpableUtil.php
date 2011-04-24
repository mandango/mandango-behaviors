<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\Behavior\Util;

/**
 * IpableUtil.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class IpableUtil
{
    /**
     * Returns the IP from $_SERVER['REMOTE_ADDR'] if exists, or 127.0.0.1 if it does not exists.
     *
     * @return string The IP.
     */
    static public function getIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }
}
