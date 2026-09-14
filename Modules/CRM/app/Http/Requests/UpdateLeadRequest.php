<?php

namespace Modules\CRM\Http\Requests;

class UpdateLeadRequest extends StoreLeadRequest
{
    protected function prepareForValidation(): void
    {
        $this->mergeLeadPayload();
    }
}
