<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>(string)$this->id,
            "type"=>"Projects",
            "attributes"=>[
                "id"=>$this->id,
                "nombre"=>$this->titulo,
                "email"=>$this->url,
                "password"=>$this->horas,
            ],
            "links"=>[
                'self'=>url("api/projects/".$this->id)
            ]

        ];
    }
    public function with(Request $request)
    {
        return ["jsonapi"=>["version"=>"1.0"]];
    }
}
