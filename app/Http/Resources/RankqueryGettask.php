<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RankqueryGettask extends Resource {
	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'id' => $this->id,
			'ywkid' => $this->ywkid,
			'site' => $this->site,
			'task_type' => $this->task_type,
			'keywords' => QueryKeyword::collection($this->keywords),
		];
	}
}
