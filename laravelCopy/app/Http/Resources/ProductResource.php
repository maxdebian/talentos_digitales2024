<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        //return parent::toArray($request);
        return [
            'id'            =>  $this->id,
            'description'   =>  $this->description,
            'image'         =>  $this->product_image,
            'price'         =>  ($this->cost_price + (($this->cost_price*$this->increase)/100)),
            'productStock'  =>  is_null($this->stock) ? 0 : $this->stock,
            'enabled'       =>  $this->enabled


/*
            'created_at'    =>  Carbon::parse($this->created_at)->format('m/d/Y'), */
        ];
    }


}
