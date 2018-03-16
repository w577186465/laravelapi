<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class QueryKeyword extends Resource {
	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'id' => $this->id,
			'keyword' => $this->keyword,
		];
	}
}
