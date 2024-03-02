<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {

        $name = getenv("NAME");
        $link = getenv("LINK");
        $color = getenv("COLOR");

        echo "<h1 style=\"color: ". $color . "\">" . $name . "</h1>";
        echo "<a href=\"" . $link . "\">Go to " . $link .  "</a><br><br>";
        echo "Jaeger: <a target=\"_blank\" href=\"http://localhost:16686\">http://localhost:16686</a><br><br>";

        $extensions = get_loaded_extensions();

        // Loop through the extensions and print each one
        echo "Loaded extensions are: <ul>";
        foreach ($extensions as $extension) {
            echo "<li>" . $extension . "</li>";
        }
        echo "</ul>";

        $response->getBody()->write("");
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
