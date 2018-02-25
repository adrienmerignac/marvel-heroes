<?php

namespace App\Tests\Services;

class ResponseTest {
    public function getBody() {
        return json_encode([
            "data" => [
                "total" => 100,
                "count" => 3,
                "results" => [
                    0 => [
                        "id" => 1,
                        "name" => "Spiderman"
                    ],
                    1 => [
                        "id" => 2,
                        "name" => "Batman"
                    ],
                    2 => [
                        "id" => 3,
                        "name" => "Superman"
                    ]
                ]
            ]
        ]);
    }
}