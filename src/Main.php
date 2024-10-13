<?php

declare(strict_types=1);

namespace Src;

use FrameworkX\App;
use PDO;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();

$app->post('/make_reservation', function (ServerRequestInterface $request) {
    $connection = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', '123456');
    $input = json_decode((string) $request->getBody());
    if (!filter_var($input->email, FILTER_VALIDATE_EMAIL)) {
        $output = ['message' => 'Invalid email'];
        return Response::json($output)->withStatus(422);
    }
    $reservationId = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $statement = $connection->prepare('SELECT * FROM sdp.rooms WHERE room_id = ?');
    $statement->execute([$input->roomId]);
    $room = $statement->fetchAll(PDO::FETCH_ASSOC);
    $reservations = $connection->prepare("SELECT * FROM sdp.reservations WHERE room_id = ? AND (checkin_date, checkout_date) OVERLAPS (?, ?) AND status = 'active'");
    $reservations->execute([$input->roomId, $input->checkinDate, $input->checkoutDate]);
    $reservations = $reservations->fetchAll(PDO::FETCH_ASSOC);
    $isAvailable = count($reservations) === 0;
    if (!$isAvailable) {
        $output = ['status' => 'error', 'message' => 'Room is not available'];
        return Response::json($output)->withStatus(422);
    }
    $price = 0;
    $duration = 0;
    if ($room[0]['type'] === 'day') {
        $duration = (new \DateTime($input->checkinDate))->diff(new \DateTime($input->checkoutDate))->days;
        $price = $duration * $room[0]['price'];
    }
    if ($room[0]['type'] === 'hour') {
        $duration = (new \DateTime($input->checkinDate))->diff(new \DateTime($input->checkoutDate))->h;
        $price = $duration * $room[0]['price'];
    }
    $statement = $connection->prepare('INSERT INTO sdp.reservations (reservation_id, room_id, email, checkin_date, checkout_date, price, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $statement->execute([$reservationId, $input->roomId, $input->email, $input->checkinDate, $input->checkoutDate, $price, 'active']);
    return Response::json([
        'reservationId' => $reservationId,
        'isAvailable' => $isAvailable,
        'duration' => $duration,
        'price' => $price
    ]);
});

$app->post('/cancel_reservation', function (ServerRequestInterface $request) {
    $connection = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=postgres', 'postgres', '123456');
    $input = json_decode((string) $request->getBody());
    $statement = $connection->prepare("UPDATE sdp.reservations SET status = 'cancelled' WHERE reservation_id = ?");
    $statement->execute([$input->reservationId]);
    return Response::plaintext('Reservation cancelled');
});

$app->get('/', function () {
    return Response::plaintext(
        "Hello wörld!\n"
    );
});

$app->run();
