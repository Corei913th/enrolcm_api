<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EligibilityResultResource extends JsonResource
{
    /**
     * Transform the eligibility result array into a JSON resource.
     *
     * Expected input structure:
     * [
     *   'eligible' => bool,
     *   'reasons' => array,
     *   'payment_status' => array (optional),
     *   'documents_status' => array (optional),
     *   'academic_criteria' => array (optional)
     * ]
     */
    public function toArray($request)
    {
        return [
            'eligible' => $this->resource['eligible'] ?? false,
            'reasons' => $this->resource['reasons'] ?? [],

            // Payment status details
            'payment_status' => [
                'valid' => $this->resource['payment_status']['valid'] ?? null,
                'status' => $this->resource['payment_status']['status'] ?? null,
                'reason' => $this->resource['payment_status']['reason'] ?? null,
            ],

            // Documents status details
            'documents_status' => [
                'valid' => $this->resource['documents_status']['valid'] ?? null,
                'missing' => $this->resource['documents_status']['missing'] ?? [],
                'pending' => $this->resource['documents_status']['pending'] ?? [],
                'rejected' => $this->resource['documents_status']['rejected'] ?? [],
            ],

            // Academic criteria details
            'academic_criteria' => [
                'eligible' => $this->resource['academic_criteria']['eligible'] ?? null,
                'reasons' => $this->resource['academic_criteria']['reasons'] ?? [],
            ],
        ];
    }
}
