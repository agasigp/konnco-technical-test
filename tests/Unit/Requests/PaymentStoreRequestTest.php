<?php

use App\Http\Requests\PaymentStoreRequest;

beforeEach(function () {
    $this->paymentRequest = new PaymentStoreRequest();
});

test('test rules method', function () {
    $this->assertEquals(
        [
            'amount' => 'required|gt:0'
        ],
        $this->paymentRequest->rules()
    );
});

test('test authorize method', function () {
    $this->assertTrue($this->paymentRequest->authorize());
});
