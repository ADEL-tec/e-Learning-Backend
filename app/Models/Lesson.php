<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Traits\DefaultDatetimeFormat;

class Lesson extends Model
{

    use DefaultDatetimeFormat;

    protected $casts = [
        'video' => 'json',
    ];

    public  function setVideoAttribute($value)
    {
        $newVideo = [];
        foreach ($value as $k => $v) {
            $oldVideo = [];
            if (empty($v['old_url'])) {
                if (!empty($v['url']) || $v['url']) {
                    $oldVideo['url'] = $v['url'];
                } else {
                    $oldData =    json_decode($this->attributes['video'], true);
                    $oldVideo['url'] = $oldData[$k]['url'];
                }
            } else {
                $oldVideo['url'] = $v['old_url'];
            }

            if (empty($v['old_thumbnail'])) {
                if (!empty($v['thumbnail']) || $v['thumbnail']) {
                    $oldVideo['thumbnail'] =  $v['thumbnail'];
                } else {
                    $oldData =    json_decode($this->attributes['video'], true);
                    $oldVideo['thumbnail'] = $oldData[$k]['thumbnail'];
                }
            } else {
                $oldVideo['thumbnail'] = $v['old_thumbnail'];
            }
            $oldVideo['name'] = $v['name'];

            array_push($newVideo, $oldVideo);
        }
        $this->attributes['video'] = json_encode(array_values($newVideo));
    }

    public function getVideoAttribute($value)
    {
        $result = json_decode($value, true);
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $result[$key]['url'] = empty($value['url']) ? "" :  env('APP_URL') . 'uploads/' . $value['url'];
                $result[$key]['thumbnail'] = empty($value['thumbnail']) ? "" : env('APP_URL') . 'uploads/' . $value['thumbnail'];
            }
        }
        return $result;
    }

    public function getThumbnailAttribute($value)
    {
        return env('APP_URL') . 'uploads/' . $value;
    }
}
