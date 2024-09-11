<?php

namespace Willow\Fields;

class Resolver
{
    public function __invoke(array $unresolvedData, ?array $fullReference = null): array
    {
        foreach ($unresolvedData as $key => $value) {
            if (is_array($value)) {
                $unresolvedData[$key] = $this->__invoke($value, $fullReference ?? $unresolvedData);
            } elseif ($value instanceof FieldHelper) {
                $unresolvedData[$key] = $value->resolve($fullReference ?? $unresolvedData);
            }
        }
        return $unresolvedData;
    }
}
