<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Ghostwriter\Draft\Application\Interface\RouterInterface;
// use Ghostwriter\Draft\Value\RuntimeException;
// use Ghostwriter\Draft\Value\UserInterface;
use Illuminate\Routing\Router as IlluminateRouter;
use Override;

final class Router extends IlluminateRouter implements RouterInterface
{
    //    public $user;
    //
    //    #[Override]
    //    public function user(): UserInterface
    //    {
    //        $user = $this->user;
    //        if ($user instanceof UserInterface) {
    //            return $user;
    //        }
    //
    //        throw new RuntimeException('No user was provided.');
    //    }
}
