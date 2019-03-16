<?php
require '../vendor/autoload.php';
$con = new mysqli("localhost","heshan", "pwd",  "odi_live_score");

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
// login

$app->post('/login', function ($request, $response, $args) {

    $username = $request->getParsedBodyParam('username', '');
    $password = $request->getParsedBodyParam('password', '');

    $payload = ['logged' => false];

    if ($username == "admin" && $password == "root") {
        setSession("admin", "1", "admin");
        $payload = ['logged' => true];
        return $response->withStatus(200)->withJson($payload);
    }

    return $response->withStatus(200)->withJson($payload);
});

//update-score

$app->post('/update-score', function ($request, $response, $args) {
    global $con;

    $team = $request->getParsedBodyParam('team');
    $description = $request->getParsedBodyParam('description');
    $score = $request->getParsedBodyParam('score');
    $wickets = $request->getParsedBodyParam('wickets');
    $overs = $request->getParsedBodyParam('overs');

    $con->query("UPDATE score SET runs = '$score', wickets = '$wickets', overs = '$overs' WHERE team = '$team'");
    $con->query("UPDATE match_status SET description = '$description' WHERE id = 1");

    return $response->withStatus(201)->withJson(["id"=>$con->insert_id]);
});

//get-score

$app->get('/get-score/{team}', function ($request, $response, $args){
    global $con;
    $team = $args['team'];
    $_score = $con->query("SELECT * FROM score WHERE team = '$team'");
    $score = $_score->fetch_assoc();
    return $response->withStatus(200)->withJson($score);

});

//get-description

$app->get('/get-description', function ($request, $response, $args){
    global $con;
    $_des = $con->query("SELECT * FROM match_status");
    $des = $_des->fetch_assoc();
    return $response->withStatus(200)->withJson($des);

});
try {
    $app->run();
} catch (\Slim\Exception\MethodNotAllowedException $e) {
} catch (\Slim\Exception\NotFoundException $e) {
} catch (Exception $e) {
    echo 'error';
}

