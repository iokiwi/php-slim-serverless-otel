<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use OpenTelemetry\API\Trace\TracerProviderInterface;
use OpenTelemetry\Context\Context;
use GuzzleHttp\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use OpenTelemetry\API\Trace as API;
// use OpenTelemetry\Context\Context;
use OpenTelemetry\SDK\Trace\TracerProvider;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/foo', function (Request $request, Response $response) {
        $response->getBody()->write("bar");
        return $response;
    });

    $app->get('/ping', function (Request $request, Response $response) {
        $count = (int)($request->getQueryParams()['count'] ?? '1');

        $client = new Client();
        $url = getenv("LINK_INTERNAL") . "/foo";

        $response->getBody()->write("pong " . $count);

        if ($count > 0) {

            $res = $client->request('GET', $url, [
                "count" => $count - 1
            ]);
            echo $res->getBody() . " ";
        }
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {

        $name = getenv("NAME");
        $link = getenv("LINK");
        $color = getenv("COLOR");

        echo "<h1 style=\"color: ". $color . "\">" . $name . "</h1>";
        echo "<a href=\"" . $link . "/foo\">Go to " . $link .  "</a><br><br>";
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
