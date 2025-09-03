<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Enum\User\UserTypeEnum;
use App\Enum\User\UserGenderEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DataEntry\CountryResource;
use App\Http\Resources\Global\Other\BasicResource;
use App\Http\Resources\Global\Other\BasicUserResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_code' => $this->phone_code,
            'phone' => $this->phone,
            'type' => $this->type,
            'display_type' => UserTypeEnum::resolve($this->type),
            'is_active' => $this->is_active,
            'avatar' => $this->avatar,
            'roles' => $this->whenLoaded('roles', fn() => RoleResource::collection($this->roles), []),
            'creator' => $this->whenLoaded('creator', fn() => new BasicUserResource($this->creator), ['id' => $this->created_by]),
            'created_at' => $this->created_at,
        ];
    }
}
