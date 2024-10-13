<?php

declare(strict_types=1);

namespace Tests;

use function Minicli\PestCurlyPlugin\curly;

describe('mainTest', function () {
    test('Não deve reservar um quarto com email invalido', function () {
        $input = [
            'roomId' => 'aa354842-59bf-42e6-be3a-6188dbb5fff8',
            'email' => 'john.doe',
            'checkinDate' => '2024-03-03T10:00:00',
            'checkoutDate' => '2024-03-08T10:00:00',
        ];
        $response  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response['code'])->toBe(422);
        $output = json_decode($response['body'], true);
        expect($output['message'])->toBe('Invalid email');
    });

    test('Deve reservar um quarto por dia', function () {
        $input = [
            'roomId' => 'aa354842-59bf-42e6-be3a-6188dbb5fff8',
            'email' => 'john.doe@gmail.com',
            'checkinDate' => '2024-03-03T10:00:00',
            'checkoutDate' => '2024-03-08T10:00:00',
        ];
        $response  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response['code'])->toBe(200);
        $output = json_decode($response['body'], true);
        expect($output['reservationId'])->not()->toBeEmpty();
        expect($output['duration'])->toBe(5);
        expect($output['price'])->toBe(5000);
        curly()->post('http://localhost:8000/cancel_reservation', $output);
    });

    test('Não deve reservar um quarto por dia em um período já reservado', function () {
        $input = [
            'roomId' => 'aa354842-59bf-42e6-be3a-6188dbb5fff8',
            'email' => 'john.doe@gmail.com',
            'checkinDate' => '2024-03-03T10:00:00',
            'checkoutDate' => '2024-03-08T10:00:00',
        ];
        $response  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response['code'])->toBe(200);
        $output = json_decode($response['body'], true);
        $response2  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response2)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response2['code'])->toBe(422);
        $output2 = json_decode($response2['body'], true);
        expect($output2['message'])->toBe('Room is not available');
        curly()->post('http://localhost:8000/cancel_reservation', $output);
    });

    test('Deve reservar um quarto por hora', function () {
        $input = [
            'roomId' => 'd5f5c6cb-bf69-4743-a288-dafed2517e38',
            'email' => 'john.doe@gmail.com',
            'checkinDate' => '2024-03-03T10:00:00',
            'checkoutDate' => '2024-03-03T12:00:00',
        ];
        $response  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response['code'])->toBe(200);
        $output = json_decode($response['body'], true);
        expect($output['reservationId'])->not()->toBeEmpty();
        expect($output['duration'])->toBe(2);
        expect($output['price'])->toBe(200);
        curly()->post('http://localhost:8000/cancel_reservation', $output);
    });

    test('Não deve reservar um quarto por hora em um período já reservado', function () {
        $input = [
            'roomId' => 'd5f5c6cb-bf69-4743-a288-dafed2517e38',
            'email' => 'john.doe@gmail.com',
            'checkinDate' => '2024-03-03T10:00:00',
            'checkoutDate' => '2024-03-03T12:00:00',
        ];
        $response  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response['code'])->toBe(200);
        $output = json_decode($response['body'], true);
        $response2  = curly()->post('http://localhost:8000/make_reservation', $input);
        expect($response2)->toBeArray()->toHaveKeys(['code', 'body']);
        expect($response2['code'])->toBe(422);
        $output2 = json_decode($response2['body'], true);
        expect($output2['message'])->toBe('Room is not available');
        curly()->post('http://localhost:8000/cancel_reservation', $output);
    });
});
