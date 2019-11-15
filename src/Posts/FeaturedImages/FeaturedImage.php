<?php

namespace PeteKlein\Performant\Posts\FeaturedImages;

class FeaturedImage
{
    public $url;
    public $title;
    public $caption;
    public $alt;
    public $description;
    public $height;
    public $width;

    public function __construct(
        string $url,
        string $title = '',
        ?string $caption = '',
        ?string $alt = '',
        ?string $description = '',
        $height = 0,
        $width = 0
    ) {
        $this->url = $url;
        $this->title = !empty($title) ? $title : '';
        $this->caption = !empty($caption) ? $caption : '';
        $this->alt = !empty($alt) ? $alt : '';
        $this->description = !empty($description) ? $description : '';
        $this->height = $height;
        $this->width = $width;
    }
}
