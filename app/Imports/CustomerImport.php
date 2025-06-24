<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToCollection, WithHeadingRow, WithValidation
{
    public $insertedCustomers = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $customer = Customer::create([
                'user_id'       => Auth::id() ?? 1,
                'contact_type'  => $row['contact_type'],
                'company_name'  => $row['company_name'],
                'owner_name'    => $row['owner_name'],
                'address'       => $row['address'],
                'city'          => $row['city'],
                'zip_code'      => $row['zip_code'],
                'phone'         => $row['phone'],
                'email'         => $row['email'],
                'website'       => $row['website'],
                'tag_id'        => isset($row['tag_id']) ? json_encode(array_map('intval', explode(',', str_replace(['[', ']', '"'], '', $row['tag_id'])))) : null,
                'description'   => $row['description'],
                'image'         => $row['image'] ?? null,
                'longitude'     => $row['longitude'] ?? null,
                'latitude'      => $row['latitude'] ?? null,
                'status'        => $row['status'] ?? 'active',
            ]);

            $this->insertedCustomers[] = $customer;
        }
    }

    public function rules(): array
    {
        return [
            '*.contact_type' => 'required|in:customer,prospect,inactive',
            '*.email' => 'required|email|unique:customers,email',
            '*.company_name' => 'required|string|max:200',
            '*.owner_name' => 'required|string|max:100',
            '*.address' => 'required|string|max:200',
            '*.city' => 'required|string|max:100',
            '*.zip_code' => 'required|max:25',
            '*.phone' => 'required|max:20',
            '*.website' => 'required|url|max:300',
            '*.description' => 'required|string|max:600',
            '*.longitude' => 'nullable|numeric',
            '*.latitude' => 'nullable|numeric',
        ];
    }
}
