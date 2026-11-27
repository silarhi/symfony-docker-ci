<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * How long the CDN keeps a public page, in seconds, and serves it stale while it fetches it again. A logged-in
     * visitor has a session, which keeps the page private (App\EventListener\PublicCacheListener).
     */
    public const int SHARED_MAX_AGE = 300;
}
