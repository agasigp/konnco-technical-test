<?php

use Illuminate\Validation\Rule;
use App\Http\Requests\PaymentUpdateRequest;

beforeEach(function () {
    $this->paymentRequest = new PaymentUpdateRequest();
});

test('test rules method', function () {
    $this->assertEquals(
        [
            'amount' => 'required|gt:0',
            'status' => [
                'required',
                Rule::in(['completed', 'failed']),
            ]
        ],
        $this->paymentRequest->rules()
    );
});
