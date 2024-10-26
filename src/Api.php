<?php

declare(strict_types=1);

use FrameworkX\App;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use Src\CancelReservation;
use Src\GetReservation;
use Src\MakeReservation;

require_once __DIR__ . '/../vendor/autoload.php';

$reservationRepository = new Src\ReservationRepositoryDatabase();
$roomRepository = new Src\RoomRepositoryDatabase();

$app = new App();

$app->get('/', fn() => Response::plaintext("Hello world!"));

$app->post('/make_reservation', function (ServerRequestInterface $request) use ($reservationRepository, $roomRepository) {
    $input = json_decode((string) $request->getBody());
    try {
        $makeReservation = new MakeReservation($reservationRepository, $roomRepository);
        $reservation = $makeReservation->execute($input);
        return Response::json($reservation);
    } catch (\Throwable $th) {
        return Response::json(['message' => $th->getMessage()])->withStatus(422, $th->getMessage());
    }
});

$app->post('/cancel_reservation', function (ServerRequestInterface $request) use ($reservationRepository) {
    $input = json_decode((string) $request->getBody());
    $cancelReservation = new CancelReservation($reservationRepository);
    $cancelReservation->execute($input->reservationId);
    return Response::plaintext('Reservation cancelled');
});

$app->get('/reservations/{reservationId}', function (ServerRequestInterface $request) use ($reservationRepository) {
    $reservationId = $request->getAttribute('reservationId');
    $getReservation = new GetReservation($reservationRepository);
    $reservation = $getReservation->execute($reservationId);
    return Response::json($reservation);
});

$app->run();
