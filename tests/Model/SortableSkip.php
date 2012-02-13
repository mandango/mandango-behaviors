<?php

namespace Model;

/**
 * Model\SortableSkip document.
 */
class SortableSkip extends \Model\Base\SortableSkip
{
    private $skip;

    public function setSkip($skip)
    {
        $this->skip = $skip;

        return $this;
    }

    public function sortableSkip()
    {
        return $this->skip;
    }
}
