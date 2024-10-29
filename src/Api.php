<?php

declare(strict_types=1);

use FrameworkX\App;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Src\Application\Usecase\CancelReservation;
use Src\Application\Usecase\DeleteAllReservations;
use Src\Application\Usecase\GetReservation;
use Src\Application\Usecase\MakeReservation;
use Src\Infra\PostgresDatabaseAdapter;
use Src\Infra\Repository\Database\ReservationRepositoryDatabase;
use Src\Infra\Repository\Database\RoomRepositoryDatabase;

require_once __DIR__ . '/../vendor/autoload.php';

$connection = new PostgresDatabaseAdapter();
$reservationRepository = new ReservationRepositoryDatabase($connection);
$roomRepository = new RoomRepositoryDatabase($connection);

$app = new App();

$app->get('/', static fn () => Response::plaintext('Hello world!'));

$app->post('/make_reservation', static function (ServerRequestInterface $request) use ($reservationRepository, $roomRepository) {
    $input = json_decode((string) $request->getBody());
    try {
        $makeReservation = new MakeReservation($reservationRepository, $roomRepository);
        $reservation = $makeReservation->execute($input);
        return Response::json($reservation);
    } catch (\Throwable $th) {
        return Response::json(['message' => $th->getMessage()])->withStatus(422, $th->getMessage());
    }
});

$app->post('/cancel_reservation', static function (ServerRequestInterface $request) use ($reservationRepository) {
    $input = json_decode((string) $request->getBody());
    $cancelReservation = new CancelReservation($reservationRepository);
    $cancelReservation->execute($input->reservationId);
    return Response::plaintext('Reservation cancelled');
});

$app->get('/reservations/{reservationId}', static function (ServerRequestInterface $request) use ($reservationRepository) {
    $reservationId = $request->getAttribute('reservationId');
    $getReservation = new GetReservation($reservationRepository);
    $reservation = $getReservation->execute($reservationId);
    return Response::json($reservation);
});

$app->post('/delete_all_reservations', static function () use ($reservationRepository) {
    $deleteReservations = new DeleteAllReservations($reservationRepository);
    $deleteReservations->execute();
    return Response::plaintext('Reservations deleted');
});

$app->run();
