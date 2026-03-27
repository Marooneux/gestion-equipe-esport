<?php

namespace R301\Vue\Component;

class SelectResultat extends Select {
    public function __construct(
        ?string $description,
        ?string $selectedValue = null
    ) {
        $values = [];
        foreach (['VICTOIRE', 'DEFAITE', 'NUL'] as $name) {
            $values[$name] = $name;
        }

        parent::__construct($values, "resultat", $description, $selectedValue);
    }
}
