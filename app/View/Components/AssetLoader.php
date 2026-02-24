<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AssetLoader extends Component
{
    public $type;
    public $src;
    public $critical;
    public $async;
    public $defer;
    public $preload;

    public function __construct(
        $type = 'css',
        $src = '',
        $critical = false,
        $async = false,
        $defer = false,
        $preload = false
    ) {
        $this->type = $type;
        $this->src = $src;
        $this->critical = $critical;
        $this->async = $async;
        $this->defer = $defer;
        $this->preload = $preload;
    }

    public function render()
    {
        return view('components.asset-loader');
    }

    public function getAssetUrl()
    {
        return local_or_cdn_asset($this->src);
    }

    public function getVersionedUrl()
    {
        return versioned_asset($this->src);
    }
}
