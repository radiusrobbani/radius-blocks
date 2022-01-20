<?php

namespace RT\RadiusBlocks\Abstracts;

abstract class Block
{
    /**
     * The plugin name. Used for option names.
     *
     * @var string
     */
    protected $name = '';

    /**
     * ID of the class extending the settings API. Used in option names.
     *
     * @var string
     */
    protected $attributes = [];

    /**
     * Get iD of the class extending the settings API. Used in option names.
     *
     * @return  string
     */
    public function getAttributes() {
        return is_array($this->attributes) ?
            $this->attributes :
            [];
    }

    /**
     * Returns true if the block type is dynamic, or false otherwise. A dynamic
     * block is one which defers its rendering to occur on-demand at runtime.
     *
     * @return bool Whether block type is dynamic.
     * @since 5.0.0
     *
     */
    public function is_dynamic() {
        return is_callable([$this, 'render_callback']);
    }

    /**
     * Get the plugin name. Used for option names.
     *
     * @return  string
     */
    public function getName() {
        return $this->name;
    }
}
