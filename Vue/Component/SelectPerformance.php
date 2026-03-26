<?php

namespace R301\Vue\Component;

class SelectPerformance extends Select {

    public function __construct(
        ?string $description,
        ?string $selectedValue = null
    ) {
        $values = [];
        foreach (['EXCELLENTE', 'BONNE', 'MOYENNE', 'MAUVAISE', 'CATASTROPHIQUE'] as $name) {
            $values[$name] = $name;
        }

        parent::__construct($values, "performance", $description, $selectedValue);
    }
}
