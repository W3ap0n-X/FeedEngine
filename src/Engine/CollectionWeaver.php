<?php
namespace Qck\FeedEngine\Engine;

class CollectionWeaver {
    /**
     * Interleaves multiple arrays into one.
     * * @param array $collections An array of arrays (e.g., [[A1, A2], [B1, B2], [C1, C2]])
     * @return array The "Weaved" result [A1, B1, C1, A2, B2, C2]
     */
    public static function weave(array $collections): array {
        $result = [];
        $max_length = max(array_map('count', $collections));

        for ($i = 0; $i < $max_length; $i++) {
            foreach ($collections as $collection) {
                if (isset($collection[$i])) {
                    $result[] = $collection[$i];
                }
            }
        }

        return $result;
    }
}