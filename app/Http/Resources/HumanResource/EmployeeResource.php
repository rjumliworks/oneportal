<?php

namespace App\Http\Resources\HumanResource;

use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hashids = new Hashids('krad',10);
        $code = $hashids->encode($this->id);

        return [
            'id' => $this->id,
            'code' => $code,
            'email' => $this->email,
            'username' => $this->username,
            'profile' => $this->profile,
            'organization' => $this->organization,
            'information' => $this->information,
            'academics' => $this->academics,
            'credentials' => $this->credentials,
            'contracts' => $this->contracts,
            'deductions' => $this->deductions,
            'created_at' => $this->created_at,
            'avatar' => ($this->profile->avatar === 'avatar.jpg') ? '/images/avatars/'.$this->profile->avatar : '/storage/profile-pictures/'.$this->profile->avatar,
 
        ];
    }
}
