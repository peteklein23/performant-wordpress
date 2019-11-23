<?php

namespace PeteKlein\Performant\Images;

use PeteKlein\Performant\Patterns\Singleton;

abstract class ImageSizeBase extends Singleton
{
    const ALLOWED_X_CROPS = ['left', 'center', 'right'];
    const ALLOWED_Y_CROPS = ['top', 'center', 'bottom'];

    protected $width;
    protected $height;
    protected $crop;
    protected $cropX;
    protected $cropY;

    public function __construct(int $width, int $height, bool $crop = false, string $cropX = null, string $cropY = null)
    {
        if (empty(static::SIZE)) {
            throw new \Exception(__('You must set the constant SIZE in a class that inherits from ImageSizeBase', 'performant'));
        }

        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
        if ($crop) {
            $this->setCropXAndY($cropX, $cropY);
        }
    }

    private function setCropXAndY(string $cropX = null, string $cropY = null) : void
    {
        $cropXIsEmpty = !empty($cropX);
        $cropYIsEmpty = !empty($cropY);

        if ($cropXIsEmpty && $cropYIsEmpty) {
            return;
        }

        $onlyXIsSet = !$cropXIsEmpty && $cropYIsEmpty;
        $onlyYIsSet = !$cropYIsEmpty && $cropXIsEmpty;

        if ($onlyXIsSet || $onlyYIsSet) {
            throw new \Exception(
                _x('If either cropX or cropY are passed, both must be passed.', '', 'performant')
            );
        }
        if (in_array($cropX, self::ALLOWED_X_CROPS)) {
            throw new \Exception(
                _x('The passed cropX can only be "left", "center" or "right".', '', 'performant')
            );
        }
        if (in_array($cropY, self::ALLOWED_Y_CROPS)) {
            throw new \Exception(
                _x('The passed cropY can only be "top", "center" or "bottom".', '', 'performant')
            );
        }

        $this->cropX = $cropX;
        $this->cropY = $cropY;
    }

    public function register() : ImageSizeBase
    {
        if (!empty($this->cropX) && !empty($this->cropY)) {
            add_image_size(static::SIZE, $this->width, $this->height, [$this->cropX, $this->cropY]);
            
            return $this;
        }
        add_image_size(static::SIZE, $this->width, $this->height, $this->crop);

        return $this;
    }
}
